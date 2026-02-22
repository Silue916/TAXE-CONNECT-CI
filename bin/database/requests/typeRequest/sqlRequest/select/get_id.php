<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\select;

use Epaphrodites\database\requests\typeRequest\noSqlRequest\select\get_id as SelectGet_id;

class get_id extends SelectGet_id
{  

    /**
     * Request to get users by group
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @param integer $usersGroup
     * @return array
     */
    public function defaultSqlGetUsersByGroup(
        int $currentPage, 
        int $numLines, 
        int|string $usersGroup
    ):array
    {

        $result = $this->table('usersaccount')
            ->where('usersgroup')
            ->limit((($currentPage - 1) * $numLines), $numLines)
            ->orderBy('login', 'ASC')
            ->param([$usersGroup])
            ->SQuery();

        return static::initNamespace()['env']->dictKeyToLowers($result);
    }

    /**
     * Request to get users by group (For: Oracle)
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @param integer $usersGroup
     * @return array
     */
    public function oracleGetUsersByGroup(
        int $currentPage, 
        int $numLines, 
        int $usersGroup
    ):array
    {

        $result = $this->table('usersaccount')
            ->where('usersgroup')
            ->offset((($currentPage - 1) * $numLines), $numLines)
            ->orderBy('login', 'ASC')
            ->param([$usersGroup])
            ->SQuery();

        return static::initNamespace()['env']->dictKeyToLowers($result);
    }        

    /**
     * Request to get users by group (For: sqlServer)
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @param integer $usersGroup
     * @return array
     */
    public function sqlServerGetUsersByGroup(
        int $currentPage, 
        int $numLines, 
        int $usersGroup
    ):array
    {

        $result = $this->table('usersaccount')
            ->where('usersgroup')
            ->offset((($currentPage - 1) * $numLines), $numLines)
            ->orderBy('login', 'ASC')
            ->param([$usersGroup])
            ->SQuery();

        return $result;
    }    

    /** 
     * Request to select users by login (For: Oracle)
     *
     * @param string|null $login
     * @return array
     */
    public function sqlGetOracleUsersDatas(
        string|null $login = null
    ):array
    {

        $login = static::initNamespace()['env']->no_space($login);

        $result = $this->table('usersaccount')
                        ->where('login')
                        ->param([$login])
                        ->SQuery();

        return static::initNamespace()['env']->dictKeyToLowers($result);
    }

    /** 
     * Request to select users by login
     *
     * @param string|null $login
     * @return array
     */
    public function sqlGetUsersDatas(
        string|null $login = null
    ):array
    {

        $login = static::initNamespace()['env']->no_space($login);

        $result = $this->table('usersaccount')
                        ->where('login')
                        ->param([$login])
                        ->SQuery();

        return $result;
    }

   /** 
     * Request to select users actions list by login (For: Oracle)
     * 
     * @param string|null $login
     * @return array
     */
    public function sqlGetOracleRecentsActions(
        string|null $login = null
    ):array
    {

        $login = static::initNamespace()['env']->no_space($login);

        $result = $this->table('history')
            ->like('actions')
            ->param([$login])
            ->SQuery();

        return static::initNamespace()['env']->dictKeyToLowers($result);
    }    

   /** 
     * Request to select users actions list by login (For: Mysql/Postges/sqlServer/sqLite)
     * 
     * @param string|null $login
     * @return array
     */
    public function sqlGetUsersRecentsActions(
        string|null $login = null
    ):array
    {

        $login = static::initNamespace()['env']->no_space($login);

        $result = $this->table('history')
            ->like('actions')
            ->param([$login])
            ->SQuery();

        return $result;
    }    
}