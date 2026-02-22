<?php

namespace Epaphrodites\database\seeders;

use Epaphrodites\database\query\Builders;

class databaseSeeding extends Builders{

    /**
     * Seed the application's database (sql/nosql).
     */    
    public function noSqlRun(): void
    {
         $document =
          [
            'login' => 'admin',
            'password' => static::initConfig()['guard']->CryptPassword('admin'),
            'namesurname' => NULL,
            'contact' => NULL,
            'email' => NULL,
            'state'=> 1,
            'group'=> 1,
          ];
      
          $this->db(1)->selectCollection('usersaccount')
                        ->insertOne($document);        
     }

    /**
     * Seed the application's database.
     */    
     public function sqlRun(): void
     {
        $this->table('usersaccount')
            ->insert('login , namesurname , contact' , 'email')
            ->values( ' ? , ? , ? , ? ' )
            ->param([ 'epaphrodites' , 'EPAPHRODITES FRAMEWORK' , '0000000000', 'infos@epaphrodite.org' ])
            ->sdb(1)
            ->IQuery(); 
     }    
}