<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\select;

use Epaphrodites\database\requests\typeRequest\noSqlRequest\select\select as SelectSelect;

class select extends SelectSelect
{

    /**
     * Request to get contributors list (For: mySql/sqlServer/Postgres/sqLite)
     */
    public function defaultSqlListOfContributors(
        int $currentPage,
        int $numLines
    ): array {
        return $this->table('contributors')
            ->orderBy('name', 'ASC')
            ->limit((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();
    }

    /**
     * Request to get contributors list (For: oracle)
     */
    public function oracleListOfContributors(
        int $currentPage,
        int $numLines
    ): array {
        $result = $this->table('contributors')
            ->orderBy('name', 'ASC')
            ->offset((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();

        return static::initNamespace()['env']->dictKeyToLowers($result);
    }

    /**
     * Request to get contributors list (For: sqlServer)
     */
    public function sqlServerListOfContributors(
        int $currentPage,
        int $numLines
    ): array {
        return $this->table('contributors')
            ->orderBy('name', 'ASC')
            ->offset((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();
    }

    /**
     * Request to get users list (For: mySql/sqlServer/Postgres/sqLite)
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @return array
     */
    public function defaultSqlListeOfAllUsers(
        int $currentPage, 
        int $numLines
    ):array{

        $result = $this->table('usersaccount')
            ->orderBy('usersgroup', 'ASC')
            ->limit((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();

        return $result;
    }

    /**
     * Request to get users list (For: oracle)
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @return array
     */
    public function oracleListeOfAllUsers(
        int $currentPage,
        int $numLines
    ):array{

        $result = $this->table('usersaccount')
            ->orderBy('usersgroup', 'ASC')
            ->offset((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();

        return static::initNamespace()['env']->dictKeyToLowers($result);
    } 

    /**
     * Request to get users list (For: sqlServer)
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @return array
     */
    public function sqlServerListeOfAllUsers(
        int $currentPage,
        int $numLines
    ):array{

        $result = $this->table('usersaccount')
            ->orderBy('usersgroup', 'ASC')
            ->offset((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();

        return $result;
    }    

    /**
     * Request to get list of users recents actions (For: mySql/sqlServer/Postgres/sqLite)
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @return array
     */
    public function defaultSqlListOfRecentActions(
        int $currentPage,
        int $numLines
    ):array{

        $result = $this->table('history')
            ->orderBy('dates', 'ASC')
            ->limit((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();

        return $result;
    }  
    
    /**
     * Request to get list of users recents actions (For: oracle)
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @return array
     */
    public function oracleListOfRecentActions( 
        int $currentPage,
        int $numLines
    ):array{

        $result = $this->table('history')
            ->orderBy('dates', 'ASC')
            ->offset((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();

        return static::initNamespace()['env']->dictKeyToLowers($result);
    }     

    /**
     * Request to get list of users recents actions (For: sqlServer)
     *
     * @param integer $currentPage
     * @param integer $numLines
     * @return array
     */
    public function sqlServerListOfRecentActions( 
        int $currentPage,
        int $numLines
    ):array{

        $result = $this->table('history')
            ->orderBy('dates', 'ASC')
            ->offset((($currentPage - 1) * $numLines), $numLines)
            ->SQuery();

        return $result;
    }     
}
