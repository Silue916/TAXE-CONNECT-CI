<?php

namespace Epaphrodites\epaphrodites\yedidiah;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;
use Epaphrodites\epaphrodites\yedidiah\saveLoad\loadJson;
use Epaphrodites\epaphrodites\yedidiah\treatement\getAndUpdateRights;

class Authorize extends epaphroditeClass
{

    use loadJson, getAndUpdateRights;
    
    /**
     * @param string $pages
     * @return bool
    */
    public static function Authorize(
        string $pages
    ):bool{
        $action = true;

        if(static::class('session')->type()!==1){ 
            $action = static::checkAuthorization($pages) === true ? true : static::class('errors')->error_403(); 
        }

        return $action;
    }
}