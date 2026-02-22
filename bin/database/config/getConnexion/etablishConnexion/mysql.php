<?php

namespace Database\Epaphrodites\config\getConnexion\etablishConnexion;

use PDO;
use PDOException;

trait mysql{

    // At the top of your class
    private ?PDO $pdo = null;

    /**
     * Establishes or closes the MySQL connection.
     *
     * @param int $db    The database ID.
     * @param bool $state If true, closes the connection. If false, connects (if not already).
     * @return object|null The PDO connection object or null if closed.
     */
    private function setMysqlConnexion(
        int $db, 
        bool $state = false
    ): ?object{
        
        if ($state == true) {
            // Close the connection by setting the PDO instance to null
            $this->pdo = null;
            return null;
        }

        // If no active connection, establish a new one
        if ($this->pdo == null) {
            try {
                $this->pdo = new PDO(
                    static::MYSQL_DNS($db) . 'dbname=' . static::DB_DATABASE($db),
                    static::DB_USER($db),
                    static::DB_PASSWORD($db),
                    static::dbOptions()
                );
            } catch (PDOException $e) {
                // Throw a formatted error message if connection fails
                throw new PDOException(static::getError($e->getMessage()));
            }
        }

        // Return the existing or newly created connection
        return $this->pdo;
    }

    /**
     * Connexion to Mysql
     * @param string $dbName
     * @param int $db
     * @param bool $actionType
     * @return bool
    */
    private function setMysqlConnexionWithoutDatabase(
        string $dbName , 
        int $db, 
        bool $actionType
    ):bool
    {

        $requestAction = $actionType ? "CREATE" : "DROP";

        // Try to connect to database to etablish connexion
        try {

            $etablishConnexion =  new PDO(
                static::MYSQL_DNS($db),
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
     * Mysql database connexion
     * @param integer $db
     * @return object
     */
    public function Mysql(
        int $db = 1,
        bool $state = false
    ):object|array{

        $this->ifExtensionExist('pdo_mysql');
        return $this->setMysqlConnexion($db, $state);
    }

    /**
     * Connexion to Mysql
     * @param string $dbName
     * @param int $db
     * @param bool $actionType
     * @return bool
    */
    public function etablishMysql(
        string $dbName, 
        int $db, 
        bool $actionType
    ):bool{

        $this->ifExtensionExist('pdo_mysql');
        return $this->setMysqlConnexionWithoutDatabase($dbName, $db, $actionType);
    }    
}