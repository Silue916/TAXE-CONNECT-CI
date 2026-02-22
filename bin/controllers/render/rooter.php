<?php

namespace Epaphrodites\controllers\render;

use Epaphrodites\controllers\render\Twig\TwigRender;
use Epaphrodites\epaphrodites\Contracts\rootInterface;

class rooter extends TwigRender implements rootInterface
{

    private array $content = [];
    private string $target;
    private object $setting;

    public function __construct( object $setting)
    {
        $this->setting = $setting;
    }

    /**
     * target
     *
     * @param string $target
     * @return self
     */
    public function target(string $target):self
    {

        $this->target =  $target;

        return $this;
    }

    /**
     * Find content
     *
     * @param array $InitContent
     * @param bool|false $switch
     * @return self
     */
    public function content( 
        array $InitContent = [], 
        bool $switch = false
    ):self
    {

        $GetLayoutsContent = $this->GetLayouts($switch, $InitContent);

        $init = $switch === true ? $this->setting->AdminInitMainLayouts() : $this->setting->MainUserInitLayouts();
        
        $this->content = array_merge( $InitContent , $GetLayoutsContent , $init );

        return $this;
    }    

    /**
     * run page
     *
     * @return void
    */    
    public function get():void
    {
        
        $this->render( "{$this->target}" , $this->content );
    }

    /**
     * Verify if loyaut exist in content array
     * @param array $content
     * @return bool
     */
    public function CheckLayout(
        array $content = []
    ):bool{

        return array_key_exists( "layouts" , $content );
    }

    /**
     * @param array $content
     * @param bool $Switch
     * @return array
     */
    public function GetLayouts(
        bool $Switch, 
        array $content = []
    ):array
    {

        if($Switch===false && $this->CheckLayout($content)!==true){ $content = $this->setting->MainLayout(); }
       
        if($Switch===true && $this->CheckLayout($content)!==true){ $content =  $this->setting->AdminLayout(); }

        return $content;
    }
}