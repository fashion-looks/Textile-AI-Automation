"""Small standard-library HTTP API for the Fashion Looks ecosystem."""
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
import json
import logging
import os
from urllib.parse import urlparse
from .catalog import inventory
from .garment_costing import calculate_fabric_plan

logging.basicConfig(level=os.getenv("LOG_LEVEL", "INFO"))
HOST = os.getenv("HOST", "127.0.0.1")
PORT = int(os.getenv("PORT", "8000"))
MAX_REQUEST_BYTES = 16 * 1024


class Handler(BaseHTTPRequestHandler):
    def _send(self, status, payload):
        data = json.dumps(payload, ensure_ascii=False).encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(data)))
        self.send_header("X-Content-Type-Options", "nosniff")
        self.send_header("Cache-Control", "no-store")
        self.end_headers()
        self.wfile.write(data)

    def do_GET(self):
        path = urlparse(self.path).path
        if path == "/health":
            return self._send(200, {"ok": True, "service": "fashion-looks-ecosystem"})
        if path == "/api/categories":
            return self._send(200, {"count": 35, "categories": inventory()})
        if path == "/":
            return self._send(200, {
                "name": "Fashion Looks A-Z Ecosystem",
                "routes": ["/health", "/api/categories", "/api/garments/fabric-plan"]
            })
        return self._send(404, {"error": "not_found"})

    def do_POST(self):
        if urlparse(self.path).path != "/api/garments/fabric-plan":
            return self._send(404, {"error": "not_found"})

        content_type = self.headers.get("Content-Type", "").split(";", 1)[0].strip().lower()
        if content_type != "application/json":
            return self._send(415, {"error": "application_json_required"})

        raw_length = self.headers.get("Content-Length")
        try:
            length = int(raw_length)
        except (TypeError, ValueError):
            return self._send(400, {"error": "valid_content_length_required"})
        if length <= 0:
            return self._send(400, {"error": "request_body_required"})
        if length > MAX_REQUEST_BYTES:
            return self._send(413, {"error": "request_too_large"})

        try:
            payload = json.loads(self.rfile.read(length).decode("utf-8"))
        except (UnicodeDecodeError, json.JSONDecodeError):
            return self._send(400, {"error": "invalid_json"})

        try:
            result = calculate_fabric_plan(payload)
        except ValueError as error:
            return self._send(400, {"error": "invalid_request", "message": str(error)})
        return self._send(200, {"ok": True, "fabric_plan": result})

    def log_message(self, fmt, *args):
        # Deliberately log request metadata only; never log submitted business payloads.
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
