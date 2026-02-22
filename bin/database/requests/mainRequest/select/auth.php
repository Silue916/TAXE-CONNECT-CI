<?php

namespace Epaphrodites\database\requests\mainRequest\select;

use Epaphrodites\database\requests\typeRequest\sqlRequest\select\auth as SelectAuth;

final class auth extends SelectAuth
{

  /**
   * Selection of queries based on the type of driver
   * 
   * @param string $login
   * @return array|bool
  */
  public function checkUsers(
    string $login
  ):array|bool{

    return match (_FIRST_DRIVER_) {

          'mongodb' => $this->findNosqlUsers($login),
          'redis' => $this->findNosqlRedisUsers($login),
          'oracle' => $this->findOracleUsers($login),

          default => $this->findSqlUsers($login),
    };
  }
}