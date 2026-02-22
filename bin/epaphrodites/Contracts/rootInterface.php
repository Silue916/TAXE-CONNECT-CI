<?php

namespace Epaphrodites\epaphrodites\Contracts;

interface rootInterface{

    /**
     * target
     *
     * @param string $target
     * @return self
     */
    public function target(string $target):self;

    /**
     * Find content
     *
     * @param array $InitContent
     * @param bool|false $switch
     * @return self
     */
    public function content( array $InitContent = [] , bool $switch = false ):self;
    
    /**
     * run page
     *
     * @return void
    */    
    public function get():void;
    
    /**
     * Verify if loyaut exist in content array
     * @param array $content
     * @return bool
     */
    public function CheckLayout(array $content = []):bool;
    
    /**
     * @param array $content
     * @param bool $Switch
     * @return array
     */
    public function GetLayouts(bool $Switch , array $content = []):array;    

}