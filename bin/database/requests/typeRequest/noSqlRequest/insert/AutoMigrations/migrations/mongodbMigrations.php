<?php

namespace Epaphrodites\database\requests\typeRequest\noSqlRequest\insert\AutoMigrations\migrations;

trait mongodbMigrations{

/**
   * Create user if not exist
   */
  private function CreateMongoUserIfNotExist()
  {

    $this->db(1)->createCollection("usersaccount");
  }

  /**
   * Create recently users actions if not exist
   */
  private function createHistoryMongoIfNotExist()
  {

    $this->db(1)->createCollection('history');
  }

  /**
   * Create auth_secure if not exist
   */
  private function CreateAuthSecureMongoIfNotExist()
  {

    $this->db(1)->createCollection('secure');
  }  
}