<?php

namespace Epaphrodites\epaphrodites\constant;

use Epaphrodites\epaphrodites\define\config\currentNameSpace;

class epaphroditeClass extends currentNameSpace{

    /**
     * @param string $chaine
     * @return object
     */
    public static function class(string $chaine):object{ return new static::$initNamespace[$chaine]; } 

    /**
     * @param mixed $chaine
     * @return object
     */
    public static function getTwig(string $chaine):object{ return new static::$initTwigConfig[$chaine]; }

    /**
     * @param mixed $chaine
     * @return object
     */
    public static function getGuard(string $chaine):object{ return new static::$initGuardsConfig[$chaine]; }     

    /**
     * @return string
     * @return string
     */
    public static function JsonDatas():string{ return _DIR_JSON_DATAS_ . '/usersRights/JsonDatas.json'; }
}