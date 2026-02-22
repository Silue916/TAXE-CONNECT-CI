<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\select;

use Epaphrodites\database\requests\typeRequest\noSqlRequest\select\auth as SelectAuth;

class auth extends SelectAuth
{

  /**
   * Verify if usersaccount table exist in database (For mySql/postgresSql/sqLite/sqlServer/oracle)
   * @return bool
   */
  protected function if_table_exist(): bool
  {

    $result = $this->table('usersaccount')->except()->SQuery();

    return count($result) > 0 ? true : false;

  }

  /**
   * Request to select all users of database (For mySql/postgresSql/sqLite/sqlServer)
   * 
   * @param string $login
   * @return array|bool
   */
  public function findSqlUsers(
    string $login
  ):array|bool{


    if ($this->if_table_exist() == true) {

      $result = $this->table('usersaccount')
          ->like('login')
          ->param([$login])
          ->except()
          ->SQuery();

      return $result;
      
    } else {
      
      static::firstSeederGeneration();

      return false;
    }
  }

  /**
   * Request to select all users of database (For oracle)
   * 
   * @param string $login
   * @return array|bool
   */
  public function findOracleUsers(
    string $login
  ):array|bool{

    if ($this->if_table_exist() == true) {

      $result = $this->table('usersaccount')
          ->like('login')
          ->param([$login])
          ->except()
          ->SQuery();

      return static::initNamespace()['env']->dictKeyToLowers($result);
    } else {
      
      static::firstSeederGeneration();

      return false;
    }
  }  
}
