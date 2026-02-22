<?php

namespace Database\Epaphrodites\config\getConnexion\etablishConnexion;

use PDO;
use PDOException;

trait SqlServer{
    
    private ?PDO $pdo = null;

    /**
     * Connexion Sql Serveur
     * @param integer $db
     * @return object
    */
    private function setSqlServerConnexion(
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
                    static::SQL_SERVER_DNS($db) . "Database=" . static::DB_DATABASE($db) , 
                    static::DB_USER($db), 
                    static::DB_PASSWORD($db) , 
                    static::sqlServerOption()
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
    private function setSqlServerConnexionWithoutDatabase(
        string $dbName, 
        int $db, 
        bool $actionType
    ):bool
    {

        $requestAction = $actionType ? "CREATE" : "DROP";

        // Try to connect to database to etablish connexion
        try {

            $etablishConnexion = new PDO(
                static::SQL_SERVER_DNS($db) , 
                static::DB_USER($db), 
                static::DB_PASSWORD($db) , 
                static::sqlServerOption()
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
    public function SqlServer(
        int $db = 1,
        bool $state = false
    ):object|array{

        $this->ifExtensionExist('pdo_sqlsrv');
        return $this->setSqlServerConnexion($db, $state);
    }  
    
    /**
     * @param string $dbName
     * @param int $db
     * @param bool $actionType
     * @return bool
    */   
    public function etablishSqlServer(
        string $dbName, 
        int $db, 
        bool $actionType 
    ):bool
    {
        $this->ifExtensionExist('pdo_sqlsrv');
        return $this->setSqlServerConnexionWithoutDatabase($dbName , $db, $actionType);
    }      
}