<?php

namespace Database\Epaphrodites\config\getConnexion\etablishConnexion;

use PDO;
use PDOException;

trait oracledb{

    private ?PDO $pdo = null;

    /**
     * Connexion Oracledb
     * @param integer $db
     * @return object
    */
    private function setOracledbConnexion(
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
                    static::ORACLE_DNS($db),
                    static::DB_USER($db),
                    static::DB_PASSWORD($db),
                    static::oracleOptions()
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
     * @param integer $db
     * @return object
    */   
    public function oracledb(
        int $db = 1,
        bool $state = false
    ):object|array{

        $this->ifExtensionExist('pdo_oci');
        return $this->setOracledbConnexion($db, $state);
    }   
}