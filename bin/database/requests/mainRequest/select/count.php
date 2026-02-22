<?php

namespace Epaphrodites\database\requests\mainRequest\select;

use Epaphrodites\database\requests\typeRequest\sqlRequest\select\count as CountCount;

final class count extends CountCount
{

  /**
   * Request to count all contributors
   *
   * @return int
   */
  public function CountAllContributors(): int
  {
      return match (_FIRST_DRIVER_) {

        'mongodb' => 0,
        'redis' => 0,

        default => $this->sqlCountAllContributors(),
      };
  }

  /**
   * Request to count all users
   * 
   * @return int
   */
  public function CountAllUsers(): int
  {

      return match (_FIRST_DRIVER_) {

        'mongodb' => $this->noSqlCountAllUsers(),
        'redis' => $this->noSqlRedisCountAllUsers(),

        default => $this->sqlCountAllUsers(),
      };

  }

  /**
   * Request to count all users per group
   * 
   * @param int $usersGroup
   * @return int
   */
  public function CountUsersByGroup(
    int|string $usersGroup
  ):int
  {

      return match (_FIRST_DRIVER_) {

        'mongodb' => $this->noSqlCountUsersByGroup($usersGroup),
        'redis' => $this->noSqlRedisCountUsersByGroup($usersGroup),

        default => $this->sqlCountUsersByGroup($usersGroup),
      };
  }

  /**
   * Request to count all recent actions
   * @return int
   */
  public function countUsersRecentActions(): int
  {

      return match (_FIRST_DRIVER_) {

        'mongodb' => $this->noSqlCountUsersRecentActions(),
        'redis' => $this->noSqlRedisCountUsersRecentActions(),

        default => $this->sqlCountUsersRecentActions(),
      };
  }
}
