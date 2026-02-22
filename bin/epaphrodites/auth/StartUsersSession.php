<?php

namespace Epaphrodites\epaphrodites\auth;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;

class StartUsersSession extends epaphroditeClass
{

  private $locate;

  public function StartUsersSession(
    $authId, 
    $authLogin, 
    $authNameSurname, 
    $authContact, 
    $authEmail, 
    $authUsersGroup,
    $authOTP,
    $otpVerify
  )
  {
  
    session_status() === PHP_SESSION_ACTIVE ?: session_start();

    static::class('global')->StartSession($authId, $authLogin, $authNameSurname, $authContact, $authEmail, $authUsersGroup, $authOTP, $otpVerify);

    session_regenerate_id();

    if (static::class('secure')->get_csrf($this->key()) !== 0) {

      static::class('cookies')->set_user_cookies($this->key());
    }

    $this->locate = static::class('paths')->dashboard();

    header("Location: $this->locate ");
  }

  /**
   * Current cookies value
   */
  private function key():string
  {
      return match (_FIRST_DRIVER_) {

          'mongodb' => !empty(static::class('secure')->noSqlCheckUserCrsfToken()) ? static::class('secure')->noSqlCheckUserCrsfToken() : $_COOKIE[static::class('msg')->answers('token_name')],
          'redis' => !empty(static::class('secure')->noSqlRedisCheckUserCrsfToken()) ? static::class('secure')->noSqlRedisCheckUserCrsfToken() : $_COOKIE[static::class('msg')->answers('token_name')],

          default => !empty(static::class('secure')->CheckUserCrsfToken()) ? static::class('secure')->CheckUserCrsfToken() : $_COOKIE[static::class('msg')->answers('token_name')],
      };
  }
}