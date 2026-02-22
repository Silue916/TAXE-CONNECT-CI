<?php

namespace Epaphrodites\epaphrodites\auth;

use Epaphrodites\epaphrodites\heredia\SettingHeredia;
use Epaphrodites\epaphrodites\constant\epaphroditeClass;


class SetUsersCookies extends epaphroditeClass{

    public SettingHeredia $setting;

    /**
     * @return void
     */
    public function __construct(){

        $this->setting = new SettingHeredia;
    }
    
    /**
     * Set cookies
     *
     * @param string $cookieValue
     * @return void
     */
    public function set_user_cookies(
        string $cookieValue
    ):void
    {
        setcookie(static::class('msg')->answers('token_name'), $cookieValue, $this->setting->coookies());

        $_COOKIE[static::class('msg')->answers('token_name')] = $cookieValue;
    }
}