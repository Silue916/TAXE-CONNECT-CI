
class RoutesConfig:
    def __init__(self, router, controller):
        self.router = router
        self.controller = controller
    
    def register_routes(self):
        self.router.add_route("POST", r"^/hello$", self.controller.helloEpaphrodites)
        self.router.add_route("POST", r"^/send$", self.controller.sendAndGetData)