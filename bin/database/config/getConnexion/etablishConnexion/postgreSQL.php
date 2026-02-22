<?php

namespace Database\Epaphrodites\config\getConnexion\etablishConnexion;

use PDO;
use PDOException;

trait postgreSQL{

    private ?PDO $pdo = null;

    /**
     * Connexion PostgreSQL
     * @param integer $db
     * @return object
    */
    private function setPostgreSQLConnexion(
        int $db,
        bool $state = false
    ):?object
    {

        if ($state == true) {
            // Close the connection by setting the PDO instance to null
            $this->pdo = null;
            return null;
        }

        if ($this->pdo == null) { 

            // Try to connect to database to etablish connexion
            try {

                $this->pdo = new PDO(
                    static::POSTGRES_SQL_DNS($db) . "dbname=" . static::DB_DATABASE($db),
                    static::DB_USER($db),
                    static::DB_PASSWORD($db),
                    static::dbOptions()
                );

            // If impossible send error message        
            } catch (PDOException $e) {

                throw new PDOException(static::getError($e->getMessage()));
            }
        }

        // Return the existing or newly created connection
        return $this->pdo;        
    }

    /**
     * @param string $dbName
     * @param int $db
     * @param bool $actionType  
     * @return bool    
    */ 
    private function setPostgreSQLConnexionWithoutDatabase(
        string $dbName, 
        int $db, 
        bool $actionType
    ):bool
    {

       $requestAction = $actionType ? "CREATE" : "DROP";

        // Try to connect to database to etablish connexion
        try {

            $etablishConnexion = new PDO(
                static::POSTGRES_SQL_DNS($db),
                static::DB_USER($db),
                static::DB_PASSWORD($db),
                static::dbOptions()
            );

            $etablishConnexion->exec( "{$requestAction} DATABASE {$dbName}" );

            return true;
            
            // If impossible send error message        
        } catch (PDOException $e) {

            return false;
        }
    }    
    
    /**
     * @param integer $db
     * @return object
    */   
    public function PostgreSQL(
        int $db = 1,
        bool $state = false
    ):object|array{

        $this->ifExtensionExist('pdo_pgsql');
        return $this->setPostgreSQLConnexion($db, $state);
    }  
    
    /**
     * @param string $dbName
     * @param int $db
     * @param bool $actionType
     * @return bool
    */   
    public function etablishPostgreSQL(
        string $dbName, 
        int $db, 
        bool $actionType
    ):bool
    {

        $this->ifExtensionExist('pdo_pgsql');
        return $this->setPostgreSQLConnexionWithoutDatabase($dbName , $db , $actionType);
    }    
}