import json
import threading
import unittest
from urllib.request import urlopen
from http.server import ThreadingHTTPServer
from ecosystem.app import Handler

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

    def test_unknown_route_is_404(self):
        from urllib.error import HTTPError
        with self.assertRaises(HTTPError) as error:
            urlopen(self.base + "/missing")
        self.assertEqual(error.exception.code, 404)

if __name__ == "__main__":
    unittest.main()
