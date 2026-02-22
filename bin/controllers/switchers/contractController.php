<?php

namespace Epaphrodites\controllers\switchers;

interface contractController
{

    /**
     * @param mixed $class
     * @param mixed $pages
     * @return mixed
     */
    public function SwitchApiControllers( 
        object $class, 
        string $pages
    ):mixed;

    /**
     * @param mixed $class
     * @param mixed $pages
     * @param bool $switch
     * @return mixed
     */    
    public function SwitchControllers(
        object $class, 
        string $pages, 
        bool $switch = false , 
        string $views = ""
    ):mixed;
}