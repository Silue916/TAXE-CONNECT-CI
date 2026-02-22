<?php

namespace Epaphrodites\database\requests\typeRequest\noSqlRequest\insert\AutoMigrations;

use Epaphrodites\database\query\Builders;
use Epaphrodites\Epaphrodites\danho\GuardPassword;
use Epaphrodites\database\requests\typeRequest\noSqlRequest\insert\AutoMigrations\seeders\noSqlSeeders;
use Epaphrodites\database\requests\typeRequest\noSqlRequest\insert\AutoMigrations\migrations\mongodbMigrations;
use Epaphrodites\database\requests\typeRequest\noSqlRequest\insert\AutoMigrations\migrations\redisdbMigrations;

class InitNoSeederGenerated extends Builders
{

  use mongodbMigrations, noSqlSeeders;

  protected $Guard;

  public function __construct()
  {
    $this->Guard = new GuardPassword;
  }

  /** 
   * generate to Mongo tables if not exist
   */
  public function CreateMongoCollections()
  {

    $this->CreateMongoUserIfNotExist();

    $this->CreateFirstUserIfNotExist();

    $this->CreateAuthSecureMongoIfNotExist();

    $this->createHistoryMongoIfNotExist();
  }

  /** 
   * generate to Mongo tables if not exist
   */
  public function CreateRedisMigration()
  {

    $this->CreateRedisDbFirstUserIfNotExist();
  }  
}
