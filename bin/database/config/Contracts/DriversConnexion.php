<?php

namespace Epaphrodites\database\config\Contracts;

interface DriversConnexion
{

    /**
     * SqlServer connexion
     * 
     * @param int|1 $db
     * @return mixed
    */    
    public function SqlServer(
        int $db = 1
    );

    /**
     * Mysql connexion
     * 
     * @param int|1 $db
     * @return mixed
    */     
    public function Mysql(
        int $db = 1
    );

    /**
     * PostgreSQL connexion
     * 
     * @param int|1 $db
     * @return mixed
    */     
    public function PostgreSQL(
        int $db = 1
    );

    /**
     * SqlLite connexion
     * 
     * @param int|1 $db
     * @return mixed
    */     
    public function SqLite(
        int $db = 1
    );

    /**
     * MongoDB connexion
     * 
     * @param int|1 $db
     * @return mixed
    */     
    public function MongoDB(
        int $db = 1
    );
}