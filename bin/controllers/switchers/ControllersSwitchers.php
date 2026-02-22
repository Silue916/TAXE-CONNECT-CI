<?php

namespace Epaphrodites\controllers\switchers;

class ControllersSwitchers extends MergeControllers
{

    /**
     * @param mixed $controller
     * @param mixed $provider
     * @param null|bool $switch
     * @return bool
     */
    public static function GetController(
        $controller, 
        $provider, 
        bool $switch = false
    ):bool
    {
     
        static::class('crsf')->tocsrf() === false
                     ? static::class('errors')->error_403() 
                     : NULL;

        if ($switch === false) {
            return $controller === $provider[0] ? true : false;
        } else {

            return $controller === $provider[0] && static::class('session')->id() !== NULL  
                        ? true 
                        : false;
        }
    }
}
