import os
import sys
import argparse
import json
import logging
import importlib.util
import threading
import time
from http.server import BaseHTTPRequestHandler, HTTPServer

def setup_logging():
    handlers = [logging.StreamHandler(sys.stdout)]
    
    log_locations = [
        'pythonServer.log',
        os.path.join(os.path.expanduser("~"), "pythonServer.log"),
        os.path.join(os.path.dirname(__file__), "pythonServer.log"),
        os.path.join(os.environ.get('TEMP', '/tmp'), "pythonServer.log")
    ]
    
    for log_path in log_locations:
        try:
            handlers.append(logging.FileHandler(log_path))
            break
        except PermissionError:
            continue
    else:
        print("Unable to create a log file, using console output only.")
    
    logging.basicConfig(
        level=logging.WARNING,
        format='%(asctime)s - %(levelname)s - %(message)s',
        handlers=handlers
)
    
setup_logging()

logger = logging.getLogger(__name__)

def load_router():

    try:
        router_path = os.path.join(os.path.dirname(__file__), '../../../..', 'bin/epaphrodites/python/config/routes.py')
        spec = importlib.util.spec_from_file_location("routes", router_path)
        routes_module = importlib.util.module_from_spec(spec)
        spec.loader.exec_module(routes_module)
        return routes_module.Router()
    except Exception as e:
        logger.error(f"Failed to load router: {e}")

        sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../..')))
        from bin.epaphrodites.python.config.routes import Router
        return Router()

class StreamingHandler:
    def __init__(self, request_handler):
        self.request_handler = request_handler
        self.is_streaming = False
        
    def start_stream(self, status_code=200, content_type="text/event-stream; charset=utf-8"):
        self.request_handler.send_response(status_code)
        self.request_handler.send_header("Content-Type", content_type)
        self.request_handler.send_header("Cache-Control", "no-cache")
        self.request_handler.send_header("Connection", "keep-alive")
        self.request_handler.end_headers()
        self.is_streaming = True
        
    def write_chunk(self, data, event_type="message", event_id=None):
        if not self.is_streaming:
            raise RuntimeError("Stream not started")
            
        sse_data = ""
        if event_id:
            sse_data += f"id: {event_id}\n"
        if event_type:
            sse_data += f"event: {event_type}\n"
            
        if isinstance(data, (dict, list)):
            data = json.dumps(data, ensure_ascii=False, separators=(',', ':'))
            
        sse_data += f"data: {data}\n\n"
        
        try:
            chunk_data = sse_data.encode('utf-8')
            self.request_handler.wfile.write(chunk_data)
            self.request_handler.wfile.flush()
        except BrokenPipeError:
            logger.debug("Client disconnected during streaming")
            self.is_streaming = False
            raise
        
    def end_stream(self):
        if self.is_streaming:
            try:
                self.request_handler.wfile.write(b"event: complete\ndata: {}\n\n")
                self.request_handler.wfile.flush()
                self.is_streaming = False
            except BrokenPipeError:
                logger.debug("Client disconnected during stream termination")
            except Exception as e:
                logger.error(f"Error ending stream: {e}")

class CustomHandler(BaseHTTPRequestHandler):
    
    router = load_router()
    
    MAX_CONTENT_LENGTH = 10_000_000
    
    STREAM_TIMEOUT = 1800

    def do_GET(self): self.handle_method("GET")
    def do_POST(self): self.handle_method("POST")
    def do_PUT(self): self.handle_method("PUT")
    def do_DELETE(self): self.handle_method("DELETE")
    def do_PATCH(self): self.handle_method("PATCH")

    def handle_method(self, method):
        body = None
        
        if method in ["POST", "PUT", "PATCH"]:
            try:
                content_length = int(self.headers.get('Content-Length', 0))
                
                if content_length > self.MAX_CONTENT_LENGTH:
                    self.send_error(413, "Request too large")
                    return
                    
                if content_length > 0:
                    body = self.rfile.read(content_length).decode('utf-8')
                    
            except UnicodeDecodeError:
                self.send_error(400, "Invalid encoding")
                return
            except ValueError:
                self.send_error(400, "Invalid Content-Length")
                return
            except Exception as e:
                logger.error(f"Error reading request body: {str(e)}")
                self.send_error(400, "Bad request")
                return

        stream_handler = StreamingHandler(self)
        
        try:
            handler, params = self.router.resolve(method, self.path)
            
            response = handler(self, stream_handler=stream_handler, body=body, *params)
            
            if response is not None and not stream_handler.is_streaming:
                status_code, response_data = response
                self.send_json_response(status_code, response_data)
                
        except BrokenPipeError:

            logger.debug("Client disconnected during streaming")
            
        except Exception as e:
            logger.error(f"Handler error: {str(e)}")
            if not stream_handler.is_streaming:
                self.send_json_response(500, {"error": "Internal server error"})
        
        finally:

            if stream_handler.is_streaming:
                try:
                    stream_handler.end_stream()
                except:
                    pass

    def send_json_response(self, status_code, response):

        self.send_response(status_code)
        
        if isinstance(response, (dict, list)):
            self.send_header("Content-Type", "application/json")
            data = json.dumps(response, separators=(',', ':')).encode("utf-8")
        else:
            self.send_header("Content-Type", "text/plain")
            data = str(response).encode("utf-8")
        
        self.send_header("Content-Length", str(len(data)))
        self.send_header("Connection", "close")
        self.end_headers()
        self.wfile.write(data)

    def log_message(self, format, *args):

        pass

class ThreadedHTTPServer(HTTPServer):
    
    def __init__(self, server_address, RequestHandlerClass, bind_and_activate=True):
        super().__init__(server_address, RequestHandlerClass, bind_and_activate)
        self.daemon_threads = True 

class Server:
    
    def __init__(self, host='127.0.0.1', port=5001):
        self.host = host
        self.port = port
        self.httpd = None

    def start(self):

        try:
            self.httpd = ThreadedHTTPServer((self.host, self.port), CustomHandler)
            logger.info(f"Streaming server starting on {self.host}:{self.port}")
            self.httpd.serve_forever()
            
        except OSError as e:
            if e.errno == 98:
                logger.error(f"Port {self.port} already in use")
            else:
                logger.error(f"Failed to start server: {e}")
            sys.exit(1)
            
        except KeyboardInterrupt:
            logger.info("Shutdown requested")
            
        except Exception as e:
            logger.error(f"Unexpected server error: {e}")
            
        finally:
            self.stop()

    def stop(self):
        
        if self.httpd:
            self.httpd.server_close()
            logger.info("Server stopped")

def parse_arguments():

    parser = argparse.ArgumentParser(description="Production HTTP server with streaming support")
    parser.add_argument("--host", default="127.0.0.1", help="Host address")
    parser.add_argument("--port", type=int, default=5001, help="Port number")
    parser.add_argument("--debug", action="store_true", help="Enable debug logging")
    return parser.parse_args()

def main():

    args = parse_arguments()
    
    if args.debug:
        logging.getLogger().setLevel(logging.DEBUG)
        logger.debug("Debug mode enabled")
    
    server = Server(host=args.host, port=args.port)
    
    try:
        server.start()
    except KeyboardInterrupt:
        pass

if __name__ == "__main__":
    main()