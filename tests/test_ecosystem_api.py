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
        request = Request(
            self.base + "/api/purchase-orders/assess",
            data=json.dumps(payload).encode("utf-8"),
            headers={"Content-Type": "application/json"},
            method="POST",
        )
        with urlopen(request) as response:
            body = json.load(response)
        self.assertEqual(response.status, 200)
        self.assertEqual(body["assessment"]["balance_qty"], 3500)
        self.assertEqual(body["assessment"]["status"], "in_progress")

    def test_assessment_rejects_invalid_quantities(self):
        payload = {
            "po_number": "FL-PO-1003",
            "supplier": "Example Mill",
            "order_qty": 100,
            "received_qty": 101,
            "committed_delivery_date": "2099-12-31",
        }
        request = Request(
            self.base + "/api/purchase-orders/assess",
            data=json.dumps(payload).encode("utf-8"),
            headers={"Content-Type": "application/json"},
            method="POST",
        )
        with self.assertRaises(HTTPError) as error:
            urlopen(request)
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
