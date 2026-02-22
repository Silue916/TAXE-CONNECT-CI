<?php

namespace Epaphrodites\epaphrodites\heredia\herediaSettingTraits;

trait session{

 /**
     * Set main params
     * 
     * @return array
     */
    public function session_params(): array
    {
        return
            [

                /*
            |--------------------------------------------------------------------------
            | Session lifetime
            |--------------------------------------------------------------------------
            |
            | Supported: "lax", "strict", "none", null
            |
            */
                'lifetime' => 86400,

                /*
            |--------------------------------------------------------------------------
            | Session Cookie Path
            |--------------------------------------------------------------------------
            |
            | Supported: "lax", "strict", "none", null
            |
            */
                'path' => '/',

                /*
            |--------------------------------------------------------------------------
            | httponly Cookies
            |--------------------------------------------------------------------------
            |
            | Supported: "false", "true"
            |
            */
                'httponly' => true,

                /*
            |--------------------------------------------------------------------------
            | Same-Site Cookies
            |--------------------------------------------------------------------------
            |
            | Supported: "lax", "strict", "none", null
            |
            */
                'samesite' => 'Strict',
            ];
    }

}