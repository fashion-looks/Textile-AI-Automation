import json
import threading
import unittest
from http.server import ThreadingHTTPServer
from urllib.error import HTTPError
from urllib.request import Request, urlopen

from ecosystem.app import Handler


class GarmentSizeMixApiTests(unittest.TestCase):
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

    def post(self, payload):
        request = Request(
            self.base + "/api/garments/fabric-plan",
            data=json.dumps(payload).encode("utf-8"),
            headers={"Content-Type": "application/json"},
            method="POST",
        )
        return urlopen(request)

    def test_size_mix_endpoint_returns_plan_and_no_store(self):
        payload = {
            "style_code": "FL-TEE-002",
            "fabric_name": "Single Jersey 180 GSM",
            "order_qty": 100,
            "size_breakdown": [
                {"size": "S", "order_qty_pcs": 20, "consumption_m_per_piece": 1.1},
                {"size": "M", "order_qty_pcs": 40, "consumption_m_per_piece": 1.2},
                {"size": "L", "order_qty_pcs": 30, "consumption_m_per_piece": 1.3},
                {"size": "XL", "order_qty_pcs": 10, "consumption_m_per_piece": 1.4},
            ],
            "wastage_percent": 5,
            "rate_per_meter": 100,
        }
        with self.post(payload) as response:
            result = json.load(response)
            self.assertEqual(response.status, 200)
            self.assertEqual(response.headers["Cache-Control"], "no-store")
        self.assertTrue(result["ok"])
        self.assertEqual(result["fabric_plan"]["planned_meters"], "129.15")
        self.assertEqual(result["fabric_plan"]["consumption_basis"], "size_breakdown")

    def test_size_mix_endpoint_rejects_quantity_mismatch(self):
        payload = {
            "style_code": "FL-TEE-002",
            "fabric_name": "Jersey",
            "order_qty": 10,
            "size_breakdown": [
                {"size": "S", "order_qty_pcs": 9, "consumption_m_per_piece": 1.1}
            ],
            "wastage_percent": 5,
            "rate_per_meter": 100,
        }
        with self.assertRaises(HTTPError) as error:
            self.post(payload)
        self.assertEqual(error.exception.code, 400)


if __name__ == "__main__":
    unittest.main()
