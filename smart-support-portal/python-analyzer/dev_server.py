#!/usr/bin/env python3
import json
import sys
import socketserver
from http.server import BaseHTTPRequestHandler

# Ensure app package is importable
sys.path.insert(0, '.')
from app.classifier import classify

class Handler(BaseHTTPRequestHandler):
    def do_GET(self):
        if self.path == '/health':
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(b'{"status":"ok"}')
        else:
            self.send_response(404)
            self.end_headers()

    def do_POST(self):
        if self.path == '/analyze':
            length = int(self.headers.get('content-length', 0))
            data = self.rfile.read(length)
            try:
                payload = json.loads(data.decode())
                title = payload.get('title', '')
                description = payload.get('description', '')
                priority, category, confidence = classify(title, description)
                resp = json.dumps({
                    'priority': priority,
                    'category': category,
                    'confidence': confidence
                }).encode()
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.end_headers()
                self.wfile.write(resp)
            except Exception:
                self.send_response(400)
                self.end_headers()
        else:
            self.send_response(404)
            self.end_headers()

if __name__ == '__main__':
    HOST = '127.0.0.1'
    PORT = 8000
    with socketserver.TCPServer((HOST, PORT), Handler) as httpd:
        print(f"Dev analyzer running on http://{HOST}:{PORT}")
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            pass
        finally:
            httpd.server_close()
