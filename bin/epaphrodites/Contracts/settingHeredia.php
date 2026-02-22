<?php

namespace Epaphrodites\epaphrodites\Contracts;

interface settingHeredia{

    /**
     * @return void
     */
    public function __construct();

    /**
     * @return array
     */
    public function MainUserInitLayouts():array;

    /**
     * @return array
     */    
    public function AdminInitMainLayouts():array;

    /**
     * @return array
     */     
    public function AdminLayout():array; 

    /**
     * @return array
     */      
    public function MainLayout():array;

    /**
     * @return array
     */      
    public function coookies():array;
    
    /**
     * @return array
     */      
    public function session_params ():array;
    
    /**
     * @return array
     */      
    public function others_options ():array;    
}