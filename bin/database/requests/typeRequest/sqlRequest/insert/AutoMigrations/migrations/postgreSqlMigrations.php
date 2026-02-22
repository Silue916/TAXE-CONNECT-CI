<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\insert\AutoMigrations\migrations;

trait postgreSqlMigrations
{

    /**
     * Create user if not exist
     * @return void
     */
    private function CreatePostgeSQLUserIfNotExist():void
    {

        $this->multiChaine(
            [
                "CREATE TABLE IF NOT EXISTS 
                usersaccount (_id SERIAL PRIMARY KEY, 
                login VARCHAR(20) NOT NULL , 
                password VARCHAR(100) NOT NULL , 
                namesurname VARCHAR(150) DEFAULT NULL , 
                contact VARCHAR(10) DEFAULT NULL , 
                email VARCHAR(50) DEFAULT NULL , 
                usersgroup INT NOT NULL DEFAULT 1 , 
                otp INT NOT NULL DEFAULT 0 , 
                state INT NOT NULL DEFAULT 1)", 
                "CREATE INDEX 
                    login_index ON usersaccount (login)"
            ])->setMultiQuery();
    }

    /**
     * Create history if not exist
     * @return void
     */
    private function createHistoryPostgeSQLIfNotExist():void
    {

        $this->multiChaine(
            [
                "CREATE TABLE IF NOT EXISTS 
                history (_id SERIAL PRIMARY KEY , 
                actions VARCHAR(20)NOT NULL , 
                dates TIMESTAMP , 
                label VARCHAR(300)NOT NULL )",
                "CREATE INDEX 
                    actions_index ON history (actions)"
            ])->setMultiQuery();
    }

    /**
     * Create secure if not exist
     * @return void
     */
    private function CreateAuthSecurePostgeSQLIfNotExist():void
    {

        $this->multiChaine(
            [
                "CREATE TABLE IF NOT EXISTS 
                secure (_id SERIAL PRIMARY KEY , 
                auth VARCHAR(300)NOT NULL , 
                token VARCHAR(200) NOT NULL , 
                createat TIMESTAMP )",
                "CREATE INDEX 
                    auth_index ON secure (auth)"
            ])->setMultiQuery();
    }
}