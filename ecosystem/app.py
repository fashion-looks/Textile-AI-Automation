"""Small standard-library HTTP API for the Fashion Looks ecosystem."""
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
import json
import logging
import os
from urllib.parse import urlparse
from .catalog import inventory

logging.basicConfig(level=os.getenv("LOG_LEVEL", "INFO"))
HOST = os.getenv("HOST", "127.0.0.1")
PORT = int(os.getenv("PORT", "8000"))

class Handler(BaseHTTPRequestHandler):
    def _send(self, status, payload):
        data = json.dumps(payload, ensure_ascii=False).encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(data)))
        self.send_header("X-Content-Type-Options", "nosniff")
        self.end_headers()
        self.wfile.write(data)

    def do_GET(self):
        path = urlparse(self.path).path
        if path == "/health":
            return self._send(200, {"ok": True, "service": "fashion-looks-ecosystem"})
        if path == "/api/categories":
            return self._send(200, {"count": 35, "categories": inventory()})
        if path == "/":
            return self._send(200, {"name": "Fashion Looks A-Z Ecosystem", "routes": ["/health", "/api/categories"]})
        return self._send(404, {"error": "not_found"})

    def log_message(self, fmt, *args):
        logging.info("%s - %s", self.address_string(), fmt % args)

def main():
    server = ThreadingHTTPServer((HOST, PORT), Handler)
    logging.info("Starting on http://%s:%s", HOST, PORT)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        pass
    finally:
        server.server_close()

if __name__ == "__main__":
    main()
