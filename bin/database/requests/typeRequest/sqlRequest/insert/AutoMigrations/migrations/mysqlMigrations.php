<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\insert\AutoMigrations\migrations;

trait mysqlMigrations
{

    /**
     * Create history if not exist
     * @return void
     */
    private function createHistoryIfNotExist():void
    {

        $this->chaine("CREATE TABLE IF NOT EXISTS history (
                _id INTEGER(11) NOT NULL AUTO_INCREMENT , 
                actions VARCHAR(20)NOT NULL , 
                dates DATETIME , 
                label VARCHAR(300)NOT NULL , 
                PRIMARY KEY(_id) , 
                INDEX (actions) )")->setQuery();
    }

    /**
     * Create secure if not exist
     * @return void
     */
    private function CreateAuthSecureIfNotExist():void
    {

        $this->chaine("CREATE TABLE IF NOT EXISTS secure (
              _id INTEGER(11) NOT NULL AUTO_INCREMENT , 
              auth VARCHAR(300) NOT NULL , 
              token VARCHAR(200) NOT NULL , 
              createat DATETIME , 
              PRIMARY KEY(_id) , 
              INDEX(auth) )")->setQuery();
    }

    /**
     * Create user if not exist
     * @return void
     */
    private function CreateUserIfNotExist():void
    {

        $this->chaine("CREATE TABLE IF NOT EXISTS usersaccount (
                _id INTEGER(11) NOT NULL AUTO_INCREMENT , 
                login VARCHAR(20)NOT NULL , 
                password VARCHAR(100) NOT NULL , 
                namesurname VARCHAR(150) DEFAULT NULL , 
                contact VARCHAR(10) DEFAULT NULL , 
                email VARCHAR(50) DEFAULT NULL , 
                usersgroup INTEGER(1) NOT NULL DEFAULT '1' , 
                state INTEGER(1) NOT NULL DEFAULT '1' , 
                otp INTEGER(1) NOT NULL DEFAULT '0' , 
                PRIMARY KEY(_id) , 
                INDEX (login) )")->setQuery();
    }
}
