import json
import threading
import unittest
from urllib.error import HTTPError
from urllib.request import Request, urlopen
from http.server import ThreadingHTTPServer

from ecosystem.app import Handler, MAX_REQUEST_BYTES


class EcosystemApiTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.server = ThreadingHTTPServer(("127.0.0.1", 0), Handler)
        cls.thread = threading.Thread(target=cls.server.serve_forever, daemon=True)
        cls.thread.start()
        cls.base = "http://127.0.0.1:%s" % cls.server.server_port

    @classmethod
    def tearDownClass(cls):
        cls.server.shutdown()
        cls.server.server_close()
        cls.thread.join(timeout=2)

    def post_json(self, path, payload):
        request = Request(
            self.base + path,
            data=json.dumps(payload).encode("utf-8"),
            headers={"Content-Type": "application/json"},
            method="POST",
        )
        return urlopen(request)

    def test_health(self):
        with urlopen(self.base + "/health") as response:
            self.assertEqual(response.status, 200)
            self.assertTrue(json.load(response)["ok"])

    def test_catalog_has_35_categories(self):
        with urlopen(self.base + "/api/categories") as response:
            payload = json.load(response)
        self.assertEqual(payload["count"], 35)
        self.assertEqual(len(payload["categories"]), 35)
        self.assertEqual([x["id"] for x in payload["categories"]], list(range(1, 36)))

    def test_purchase_order_assessment_endpoint(self):
        payload = {
            "po_number": "FL-PO-1002",
            "supplier": "Example Mill",
            "order_qty": 5000,
            "received_qty": 1500,
            "committed_delivery_date": "2099-12-31",
        }
        with self.post_json("/api/purchase-orders/assess", payload) as response:
            body = json.load(response)
        self.assertEqual(response.status, 200)
        self.assertEqual(body["assessment"]["balance_qty"], 3500)
        self.assertEqual(body["assessment"]["status"], "in_progress")

    def test_batch_assessment_returns_valid_and_invalid_rows(self):
        payload = {
            "purchase_orders": [
                {
                    "po_number": "FL-PO-2001",
                    "supplier": "Mill One",
                    "order_qty": 1000,
                    "received_qty": 250,
                    "committed_delivery_date": "2099-12-31",
                },
                {
                    "po_number": "FL-PO-2002",
                    "supplier": "Mill Two",
                    "order_qty": 100,
                    "received_qty": 101,
                    "committed_delivery_date": "2099-12-31",
                },
                {
                    "po_number": "FL-PO-2003",
                    "supplier": "Mill Three",
                    "order_qty": 500,
                    "received_qty": 0,
                    "committed_delivery_date": "2000-01-01",
                },
            ]
        }
        with self.post_json("/api/purchase-orders/assess-batch", payload) as response:
            body = json.load(response)["batch"]
        self.assertEqual(body["total"], 3)
        self.assertEqual(body["valid"], 2)
        self.assertEqual(body["invalid"], 1)
        self.assertEqual(body["results"][0]["assessment"]["balance_qty"], 750)
        self.assertEqual(body["results"][1]["status"], "invalid")
        self.assertEqual(body["results"][2]["status"], "overdue")

    def test_batch_assessment_rejects_empty_or_oversized_item_list(self):
        for orders in ([], [{}] * 51):
            with self.subTest(count=len(orders)):
                with self.assertRaises(HTTPError) as error:
                    self.post_json("/api/purchase-orders/assess-batch", {
                        "purchase_orders": orders
                    })
                self.assertEqual(error.exception.code, 400)
                self.assertEqual(
                    json.load(error.exception)["error"],
                    "invalid_purchase_order_batch",
                )

    def test_supplier_performance_endpoint(self):
        payload = {
            "purchase_orders": [
                {
                    "po_number": "FL-SP-1",
                    "supplier": "Mill A",
                    "order_qty": 1000,
                    "received_qty": 1000,
                    "committed_delivery_date": "2000-01-02",
                    "actual_delivery_date": "2000-01-01",
                },
                {
                    "po_number": "FL-SP-2",
                    "supplier": "Mill A",
                    "order_qty": 1000,
                    "received_qty": 1000,
                    "committed_delivery_date": "2000-01-02",
                    "actual_delivery_date": "2000-01-05",
                },
                {
                    "po_number": "FL-SP-3",
                    "supplier": "Mill A",
                    "order_qty": 500,
                    "received_qty": 250,
                    "committed_delivery_date": "2000-01-02",
                },
            ]
        }
        with self.post_json("/api/suppliers/performance", payload) as response:
            body = json.load(response)["supplier_performance"]
        self.assertEqual(body["total_suppliers"], 1)
        supplier = body["suppliers"][0]
        self.assertEqual(supplier["on_time_delivery_rate_pct"], 50.0)
        self.assertEqual(supplier["average_late_days"], 3.0)
        self.assertEqual(supplier["overdue_open_pos"], 1)

    def test_supplier_performance_endpoint_rejects_duplicate_pos(self):
        order = {
            "po_number": "FL-SP-1",
            "supplier": "Mill A",
            "order_qty": 100,
            "received_qty": 100,
            "committed_delivery_date": "2000-01-02",
            "actual_delivery_date": "2000-01-02",
        }
        with self.assertRaises(HTTPError) as error:
            self.post_json("/api/suppliers/performance", {
                "purchase_orders": [order, {**order, "supplier": "Mill B"}]
            })
        self.assertEqual(error.exception.code, 400)
        self.assertEqual(
            json.load(error.exception)["error"],
            "invalid_supplier_performance_request",
        )

    def test_certification_expiry_batch_endpoint(self):
        payload = {
            "warning_days": 30,
            "certificates": [
                {
                    "company": "Example Textile Mill",
                    "standard": "GOTS",
                    "certificate_number": "GOTS-API-001",
                    "issue_date": "2026-01-01",
                    "expiry_date": "2026-12-01",
                },
                {
                    "company": "Example Textile Mill",
                    "standard": "GRS",
                    "certificate_number": "GRS-API-002",
                    "issue_date": "2025-01-01",
                    "expiry_date": "2026-11-01",
                },
            ],
        }
        with self.post_json("/api/certifications/assess-batch", payload) as response:
            body = json.load(response)["certification_expiry"]
            self.assertEqual(response.headers["Cache-Control"], "no-store")
            self.assertEqual(response.headers["X-Content-Type-Options"], "nosniff")
        self.assertEqual(response.status, 200)
        self.assertEqual(body["summary"]["expiring_soon"], 1)
        self.assertEqual(body["summary"]["expired"], 1)

    def test_certification_expiry_endpoint_rejects_invalid_warning_window(self):
        with self.assertRaises(HTTPError) as error:
            self.post_json("/api/certifications/assess-batch", {
                "warning_days": 500,
                "certificates": [{
                    "company": "Example Mill",
                    "standard": "GOTS",
                    "certificate_number": "GOTS-API-003",
                    "issue_date": "2026-01-01",
                    "expiry_date": "2027-01-01",
                }],
            })
        self.assertEqual(error.exception.code, 400)
        self.assertEqual(
            json.load(error.exception)["error"],
            "invalid_certification_expiry_request",
        )

    def test_assessment_rejects_invalid_quantities(self):
        payload = {
            "po_number": "FL-PO-1003",
            "supplier": "Example Mill",
            "order_qty": 100,
            "received_qty": 101,
            "committed_delivery_date": "2099-12-31",
        }
        with self.assertRaises(HTTPError) as error:
            self.post_json("/api/purchase-orders/assess", payload)
        self.assertEqual(error.exception.code, 400)
        self.assertEqual(json.load(error.exception)["error"], "invalid_purchase_order")

    def test_assessment_requires_json_content_type(self):
        request = Request(
            self.base + "/api/purchase-orders/assess",
            data=b"{}",
            headers={"Content-Type": "text/plain"},
            method="POST",
        )
        with self.assertRaises(HTTPError) as error:
            urlopen(request)
        self.assertEqual(error.exception.code, 415)

    def test_assessment_rejects_oversized_body(self):
        payload = json.dumps({"padding": "x" * MAX_REQUEST_BYTES}).encode("utf-8")
        request = Request(
            self.base + "/api/purchase-orders/assess",
            data=payload,
            headers={"Content-Type": "application/json"},
            method="POST",
        )
        with self.assertRaises(HTTPError) as error:
            urlopen(request)
        self.assertEqual(error.exception.code, 413)

    def test_unknown_route_is_404(self):
        with self.assertRaises(HTTPError) as error:
            urlopen(self.base + "/missing")
        self.assertEqual(error.exception.code, 404)


if __name__ == "__main__":
    unittest.main()
