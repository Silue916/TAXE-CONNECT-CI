import sys
sys.path.append('bin/epaphrodites/chatBot/mainConfig/')
from constants import _CONTROLLER_PATH_

class controllerStub:
    
    @staticmethod
    def generate_stub(file_name, class_name):
        stub = f"""<?php
namespace Epaphrodites\\controllers\\controllers;
        
use Epaphrodites\\controllers\\switchers\\MainSwitchers;
        
final class {class_name} extends MainSwitchers
{{
    private object $msg;

    /**
    * Initialize object properties when an instance is created
    * @return void
    */    
    public final function __construct()
    {{
        $this->initializeObjects();
    }}

    /**
    * Initialize each property using values retrieved from static configurations
    * @return void
    */
    private function initializeObjects(): void
    {{
        $this->msg = $this->getFunctionObject(static::initNamespace(), 'msg');
    }}       
        
    /**
     * Start exemple page
     * @param string $html
     * @return void
    */      
    public final function exemplePages(string $html): void
    {{
        $this->views( $html, [], false );
    }}     
        
}}"""

        with open( _CONTROLLER_PATH_ + file_name, "w") as file:
            file.write(stub)