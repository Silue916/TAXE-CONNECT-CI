<?php

namespace Epaphrodites\database\requests\mainRequest\select;

use Epaphrodites\database\requests\typeRequest\sqlRequest\select\get_id as GetId;

final class get_id extends GetId
{

    /**
     * Request to select user right by user usersGroup
     * 
     * @param int $usersGroup
     * @return array
     */
    public function getUsersRights(
      int|string $usersGroup
    ):array{

        return static::initConfig()['listright']->getUsersRights($usersGroup);
    }

  /**
   * Request to check users by login
   * 
   * @param string $login
   * @return array
   */
  public function GetUsersDatas(
    string $login
  ):array{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlGetUsersDatas($login),
      'redis' => $this->noSqlRedisGetUsersDatas($login),
      'oracle' => $this->sqlGetOracleUsersDatas($login),

      default => $this->sqlGetUsersDatas($login),
    };    
  }

  /**
   * Request to check users per group
   *
   * @param int $currentPage
   * @param int $numLine
   * @param int $UsersGroup
   * @return array
   */
  public function GetUsersByGroup(
    int $currentPage, 
    int $numLine, 
    int|string $UsersGroup
  ):array{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlGetUsersByGroup($currentPage , $numLine , $UsersGroup),
      'redis' => $this->noSqlGetUsersByGroup($currentPage , $numLine , $UsersGroup),
      'oracle' => $this->oracleGetUsersByGroup($currentPage , $numLine , $UsersGroup),
      'sqlserver' => $this->sqlServerGetUsersByGroup($currentPage , $numLine , $UsersGroup),

      default => $this->defaultSqlGetUsersByGroup($currentPage , $numLine , $UsersGroup),
    };        
  }

  /**
   * Request to select users actions list by login
   * 
   * @param string|null $login
   * @return array
   */
  public function getUsersRecentsActions(
    string|null $login = null
  ):array{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlGetUsersRecentsActions($login),
      'redis' => $this->noSqlGetUsersRecentsActions($login),
      'oracle' => $this->sqlGetOracleRecentsActions($login),

      default => $this->sqlGetUsersRecentsActions($login),
    };      
  }
}