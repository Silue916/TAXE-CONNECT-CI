<?php

namespace Epaphrodites\epaphrodites\CsrfToken\errors;

use Epaphrodites\controllers\render\errors;

class tokenError extends errors{
    
    /**
     * @return void
     */
    public function send():void
    {
         $this->sendTologin();
    }
}