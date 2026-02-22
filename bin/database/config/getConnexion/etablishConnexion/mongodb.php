<?php

namespace Database\Epaphrodites\config\getConnexion\etablishConnexion;

trait mongodb
{
    private $connection;

    /**
     * Connexion MongoDB
     * @param integer $db
     * @return object
     */
    private function setMongoDBConnexion(
        int $db = 1,
        bool $state = false
    ):object
    {

        $param = [
            "username" => static::DB_USER($db),
            "password" => static::DB_PASSWORD($db)
        ];

        $options = empty(static::DB_USER($db)) && empty(static::DB_PASSWORD($db)) ? [] : $param;

        // Try to connect to database to etablish connexion
        try {
            $this->connection = new \MongoDB\Client("mongodb://" . static::noDB_HOST($db) . ":" . static::noDB_PORT($db), $options);
            return $this->connection->selectDatabase(static::DB_DATABASE($db));

            // If impossible send error message      
        } catch (\Exception $e) {
            
            throw static::getError($e->getMessage());
        }
    }

    /**
     * Connexion MongoDB
     * @param string $dbName
     * @param int $db
     * @param bool $actionType
     * @return bool
     */
    private function setMongoDBConnexionWithoutDatabase(
        string $dbName, 
        int $db, 
        bool $requestAction
    ):bool
    {

        if($requestAction==true) {
                   

        $param = [
            "username" => static::DB_USER($db),
            "password" => static::DB_PASSWORD($db)
        ];

        $options = empty(static::DB_USER($db)) && empty(static::DB_PASSWORD($db)) ? [] : $param;

        if(!class_exists('\MongoDB\Client')){
            throw new \Exception("Mongodb class database not found.");
        }

        // Try to connect to database to etablish connexion
        try {

            $etablishConnexion = new \MongoDB\Client("mongodb://" . static::noDB_HOST($db) . ":" . static::noDB_PORT($db), $options);

            $listDatabases = $etablishConnexion->listDatabases();

            foreach ($listDatabases as $databaseInfo) {

                if ($databaseInfo->getName() === $dbName) {
                    return false;
                }
            }

            $database = $etablishConnexion->$dbName;
            $database->createCollection('collection');

            return true;

            // If impossible send error message      
        } catch (\Exception $e) {

            return false;
        }
        }else{
            return false;
        }
    }       

    /**
     * @param integer $db
     * @return object
    */    
    public function MongoDB(
        int $db = 1,
        bool $state = false
    ):object|array{

        $this->ifExtensionExist('mongodb');
        return $this->setMongoDBConnexion($db);
    }

    /**
     * @param string $dbName
     * @param int $db
     * @param bool $actionType
     * @return bool
     */
    public function etablishMongoDB(
        string $dbName, 
        int $db, 
        bool $actionType
    ):bool
    {

        $this->ifExtensionExist('mongodb');
        return $this->setMongoDBConnexionWithoutDatabase($dbName, $db, $actionType);
    }
}