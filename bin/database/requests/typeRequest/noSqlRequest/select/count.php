<?php

namespace Epaphrodites\database\requests\typeRequest\noSqlRequest\select;

use Epaphrodites\database\query\Builders;

class count extends Builders
{

    /**
     * Get total number of users db
     * @return int
     */
    public function noSqlCountAllUsers():int
    {
        $result = $this->db(1)
            ->selectCollection('usersaccount')
            ->countDocuments([]);

        return $result;
    }

    /**
     * Get total number of users db
     * @return int
     */
    public function noSqlRedisCountAllUsers():int
    {

        $result = $this->key('usersaccount')->all()->count()->redisGet();

        return $result;
    }    

    /** 
     * Get total number of users db per group
     * @return int
     */
    public function noSqlCountUsersByGroup(
        int $Group
    ):int
    {

        $result = $this->db(1)
            ->selectCollection('usersaccount')
            ->countDocuments(['usersgroup' => $Group]);

        return $result;
    }

    /** 
     * Get total number of users db per group
     * @return int
     */
    public function noSqlRedisCountUsersByGroup(
        int $Group
    ):int
    {

        $result = $this->key('usersaccount')->search(['usersgroup'])->param([$Group])->all()->count()->redisGet();

        return $result;
    }    

    /** 
     * Get total number of users recent actions
     * @return int
     */
    public function noSqlCountUsersRecentActions():int
    {
        $result = $this->db(1)
            ->selectCollection('history')
            ->countDocuments([]);

        return $result;
    }  
    
    /** 
     * Get total number of users recent actions
     * @return int
     */
    public function noSqlRedisCountUsersRecentActions():int
    {

        $result = $this->key('history')->all()->count()->redisGet();

        return $result;
    }      
}