<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\insert;

use Epaphrodites\database\query\Builders;

class setting extends Builders
{

    /**
     * To record recent actions
     * 
     * @param string|null $action
     * @return bool
     */
    public function ActionsRecente(
        string|null $label = null
    ): bool
    {

        return  _FIRST_DRIVER_ === "oracle" 
                        ?  $this->oracleHistory($label)
                        :  $this->othersSqlHistory($label);
    }

    /**
     * Add history for mysql/Postgres/sqlserver/sqlite
     * @param string|null $label
     * @return bool
    */    
    public function othersSqlHistory(
        string|null $label = null
    ):bool{

        $this->table('history')
            ->insert('actions , dates , label')
            ->values(' ? , ? , ? ')
            ->param([static::initNamespace()['session']->login(), date("Y-m-d H:i:s"), $label])
            ->IQuery();

        return true;        
    }    

    /**
     * Add history for oracle
     * @param string|null $label
     * @return bool
    */
    public function oracleHistory(
        string|null $label = null
    ):bool{

        $this->table('history')
            ->insert('actions, label, dates')
            ->values("?, ?, TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')")
            ->param([static::initNamespace()['session']->login(), $label, date("Y-m-d H:i:s")])
            ->IQuery();

        return true;        
    }

    /**
     * To record recent actions
     * 
     * @param string|null $action
     * @return bool
     */
    public function noSqlActionsRecente(
        string|null $action = null
    ): bool
    {

        $document =
            [
                'actions' => static::initNamespace()['session']->login(),
                'dates' => date("Y-m-d H:i:s"),
                'label' => $action,
            ];

        $this->db(1)->selectCollection('history')->insertOne($document);

        return true;
    }

    /**
     * To record recent actions
     * 
     * @param string|null $action
     * @return bool
     */
    public function noSqlRedisActionsRecente(
        string|null $action = null
    ): bool
    {

        $login = static::initNamespace()['session']->login();

        $document =
            [
                'actions' => $login,
                'dates' => date("Y-m-d H:i:s"),
                'label' => $action,
            ];

        $this->key('history')->id('_id')->index($login)->param($document)->lastIndex()->addToRedis();

        return true;
    }
}