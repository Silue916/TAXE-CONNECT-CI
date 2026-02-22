<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\insert\AutoMigrations\seeders;

trait sqlSeeder{
   
  /**
   * Create user if not exist
   * @return void
   */
  private function CreateFirstUserIfNotExist():void
  {

    $this->table('usersaccount')
      ->insert('login, password')
      ->values( ' ? , ? ' )
      ->param(['admin', $this->Guard->CryptPassword('admin')])
      ->IQuery();
  } 
}