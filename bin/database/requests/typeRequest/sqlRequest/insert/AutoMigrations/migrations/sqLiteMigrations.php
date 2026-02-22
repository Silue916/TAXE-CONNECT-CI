<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\insert\AutoMigrations\migrations;

trait sqLiteMigrations
{

    /**
     * Create user if not exist
     * @return void
     */
    private function CreateSqLiteUserIfNotExist():void
    {

        $this->multiChaine(
            [
                "CREATE TABLE IF NOT EXISTS usersaccount (
                _id INTEGER PRIMARY KEY AUTOINCREMENT,
                login TEXT NOT NULL,
                password TEXT NOT NULL,
                namesurname TEXT DEFAULT NULL,
                contact TEXT DEFAULT NULL,
                email TEXT DEFAULT NULL,
                usersgroup INTEGER NOT NULL DEFAULT 1,
                otp INTEGER NOT NULL DEFAULT 0,
                state INTEGER NOT NULL DEFAULT 1)",
                "CREATE INDEX IF NOT EXISTS 
                    login_index ON usersaccount (login)"
            ])->setMultiQuery();
    }

    /**
     * Create history if not exist
     * @return void
     */
    private function createHistorySqLiteIfNotExist():void
    {

        $this->multiChaine(
            [
                "CREATE TABLE IF NOT EXISTS history (
                _id INTEGER PRIMARY KEY AUTOINCREMENT, 
                actions TEXT NOT NULL, 
                dates TIMESTAMP, 
                label TEXT NOT NULL)",
                "CREATE INDEX IF NOT EXISTS 
                    actions_index ON history (actions)"
            ])->setMultiQuery();
    }

    /**
     * Create secure if not exist
     * @return void
     */
    private function CreateAuthSecureSqLiteIfNotExist():void
    {

        $this->multiChaine(
            [
                "CREATE TABLE IF NOT EXISTS secure (
                _id INTEGER PRIMARY KEY AUTOINCREMENT, 
                auth TEXT NOT NULL, 
                token TEXT NOT NULL, 
                createat TIMESTAMP)",
                "CREATE INDEX IF NOT EXISTS 
                    auth_index ON secure (auth)"
            ])->setMultiQuery();
    }
}