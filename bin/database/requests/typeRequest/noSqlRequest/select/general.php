<?php

namespace Epaphrodites\database\requests\typeRequest\noSqlRequest\select;

use Epaphrodites\database\query\Builders;

class general extends Builders
{
    
    /**
     * Request to select all recent users actions of database (For: mongodb)
     * @return array
     */
    public function mongodbUsersHistoryActions():array
    {

        $documents = [];
        $UserConnected = static::initNamespace()['session']->login();

        $result = $this->db(1)
            ->selectCollection('history')
            ->find(['actions' => $UserConnected], [
                'limit' => 6, 
                'sort' => [
                    'dates' => (date('Y-m-d') == 'DESC') ? 1 : -1
                ]
            ]);

        foreach ($result as $document) {
            $documents[] = $document;
        }

        return $documents;
    }

    /**
     * Request to select all recent users actions of database (For: redis)
     * @return array
     */
    public function redisUsersHistoryActions():array
    {

        $UserConnected = static::initNamespace()['session']->login();

        $result = $this->key('history')->index($UserConnected)->all()->rlimit(0,6)->redisGet();

        return $result;
    }    
}