import sys
import os
import re

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../..')))

from bin.controllers.controllers.apiControllers import ApiControllers
from bin.controllers.controllerMap.routesConfig import RoutesConfig

class Route:
    def __init__(self, method, pattern, handler):
        self.method = method
        self.pattern = re.compile(pattern)
        self.handler = handler
    
    def matches(self, method, path):
        if self.method == method:
            match = self.pattern.match(path)
            if match:
                return True, match.groups()
        return False, ()

class Router:
    def __init__(self):
        self.routes = []
        self.controller = ApiControllers()
        self.routes_config = RoutesConfig(self, self.controller)
        self._register_routes()
    
    def add_route(self, method, pattern, handler):
        self.routes.append(Route(method, pattern, handler))
    
    def resolve(self, method, path):
        for route in self.routes:
            matched, params = route.matches(method, path)
            if matched:
                return route.handler, params
        return self.controller.not_found, []
    
    def _register_routes(self):
        self.routes_config.register_routes()