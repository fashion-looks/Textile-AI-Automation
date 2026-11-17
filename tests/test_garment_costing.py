import json
import threading
import unittest
from http.server import ThreadingHTTPServer
from urllib.error import HTTPError
from urllib.request import Request, urlopen

from ecosystem.app import Handler
from ecosystem.garment_costing import calculate_fabric_plan


class GarmentCostingTests(unittest.TestCase):
    def setUp(self):
        self.payload = {
            "style_code": "FL-TEE-001",
            "fabric_name": "Single Jersey 180 GSM",
            "order_qty": 1000,
            "consumption_m_per_piece": 1.25,
            "wastage_percent": 5,
            "rate_per_meter": 120.50,
        }

    def test_calculates_procurement_and_cost(self):
        result = calculate_fabric_plan(self.payload)
        self.assertEqual(result["base_meters"], "1250.00")
        self.assertEqual(result["wastage_meters"], "62.50")
        self.assertEqual(result["planned_meters"], "1312.50")
        self.assertEqual(result["total_fabric_cost"], "158156.25")
        self.assertEqual(result["fabric_cost_per_piece"], "158.16")

    def test_rounds_procurement_meters_up_to_cent(self):
        payload = {**self.payload, "order_qty": 1,
                   "consumption_m_per_piece": 1.001, "wastage_percent": 0}
        result = calculate_fabric_plan(payload)
        self.assertEqual(result["planned_meters"], "1.01")

    def test_rejects_boolean_and_fractional_order_quantity(self):
        for qty in (True, 1.5, 0, 1_000_001):
            with self.subTest(qty=qty):
                with self.assertRaises(ValueError):
                    calculate_fabric_plan({**self.payload, "order_qty": qty})

    def test_rejects_non_finite_or_out_of_range_numbers(self):
        for key, value in (
            ("consumption_m_per_piece", float("nan")),
            ("wastage_percent", 101),
            ("rate_per_meter", -1),
        ):
            with self.subTest(key=key, value=value):
                with self.assertRaises(ValueError):
                    calculate_fabric_plan({**self.payload, key: value})

    def test_rejects_missing_or_oversized_text(self):
        with self.assertRaises(ValueError):
            calculate_fabric_plan({**self.payload, "style_code": " "})
        with self.assertRaises(ValueError):
            calculate_fabric_plan({**self.payload, "fabric_name": "F" * 121})


class GarmentCostingApiTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.server = ThreadingHTTPServer(("127.0.0.1", 0), Handler)
        cls.thread = threading.Thread(target=cls.server.serve_forever, daemon=True)
        cls.thread.start()
        cls.base = f"http://127.0.0.1:{cls.server.server_port}"

    @classmethod
    def tearDownClass(cls):
        cls.server.shutdown()
        cls.server.server_close()
        cls.thread.join(timeout=2)

    def post(self, body, content_type="application/json"):
        data = body if isinstance(body, bytes) else json.dumps(body).encode()
        request = Request(
            self.base + "/api/garments/fabric-plan",
            data=data,
            headers={"Content-Type": content_type},
            method="POST",
        )
        return urlopen(request)

    def test_endpoint_returns_fabric_plan_and_no_store(self):
        body = {
            "style_code": "FL-TEE-001", "fabric_name": "Jersey",
            "order_qty": 100, "consumption_m_per_piece": 1.2,
            "wastage_percent": 5, "rate_per_meter": 100,
        }
        with self.post(body) as response:
            payload = json.load(response)
            self.assertEqual(response.status, 200)
            self.assertEqual(response.headers["Cache-Control"], "no-store")
        self.assertEqual(payload["fabric_plan"]["planned_meters"], "126.00")

    def test_endpoint_rejects_wrong_content_type(self):
        with self.assertRaises(HTTPError) as error:
            self.post({}, "text/plain")
        self.assertEqual(error.exception.code, 415)

    def test_endpoint_rejects_invalid_json(self):
        with self.assertRaises(HTTPError) as error:
            self.post(b"{bad")
        self.assertEqual(error.exception.code, 400)

    def test_endpoint_rejects_oversized_body(self):
        request = Request(
            self.base + "/api/garments/fabric-plan",
            data=b" " * (16 * 1024 + 1),
            headers={"Content-Type": "application/json"},
            method="POST",
        )
        with self.assertRaises(HTTPError) as error:
            urlopen(request)
        self.assertEqual(error.exception.code, 413)

    def test_unknown_post_route_is_404(self):
        request = Request(self.base + "/missing", data=b"{}", method="POST")
        with self.assertRaises(HTTPError) as error:
            urlopen(request)
        self.assertEqual(error.exception.code, 404)


if __name__ == "__main__":
    unittest.main()
