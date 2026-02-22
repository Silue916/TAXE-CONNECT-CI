<?php

declare(strict_types=1);

namespace Epaphrodites\database\config\process;

use Epaphrodites\database\config\getConnexion\getConnexion;
use Epaphrodites\database\config\process\envLoader as EnvLoader;
use Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException;
use Epaphrodites\database\requests\typeRequest\sqlRequest\insert\AutoMigrations\InitSeederGenerated;
use Epaphrodites\database\requests\typeRequest\noSqlRequest\insert\AutoMigrations\InitNoSeederGenerated;

class checkDatabase extends getConnexion
{

    /**
     * Database connexion
     * 
     * @param int $db
     * @param bool $state
     * @throws \Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException
     * @return array|object
     */
    protected function dbConnect(
        int $db = 1,
        bool $state = false
    ):array|object
    {

        // Load env
        checkDatabase::env();

        $mainDriver = $db !== 1 ? static::DB_DRIVER($db) : _FIRST_DRIVER_;
        
        // Switch based on the database driver type
        switch ($mainDriver) {
                // If the driver is MySQL, connect to MySQL using the Mysql method
            case 'mysql':
                return $this->Mysql($db, $state);
                break;

                // If the driver is Oracle, connect to Oracle using the Oracle method
            case 'oracle':
                return $this->oracledb($db, $state);
                break;                

                // If the driver is PostgreSQL, connect to PostgreSQL using the PostgreSQL method
            case 'pgsql':
                return $this->PostgreSQL($db, $state);
                break;

                // If the driver is sqlite, connect to sqlite using the sqlite method
            case 'sqlite':
                return $this->sqLite($db, $state);
                break;

                // If the driver is SqlServer, connect to SqlServer using the MongoDB method
            case 'sqlserver':
                return $this->SqlServer($db, $state);
                break;              

                // If the driver is MongoDB, connect to MongoDB using the MongoDB method
            case 'mongodb':
                return $this->MongoDB($db, $state);
                break;

                // If the driver is MongoDB, connect to MongoDB using the MongoDB method
            case 'redis':
                return $this->RedisDB($db, $state);
                break;

            default:
                throw new epaphroditeException("Unsupported database driver");
        }
    }

    /**
     * Database connexion
     * 
     * @param string|null $dbName
     * @param int $db
     * @param bool $requestAction
     * @throws \Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException
     * @return bool
     */
    public function etablishConnect(
        string|null $dbName = null, 
        int $db = 1 , 
        bool $requestAction = true
    ){

        // Load env
        checkDatabase::env();

        $mainDriver = $db !== 1 ? static::DB_DRIVER($db) : _FIRST_DRIVER_;

        // Switch based on the database driver type
        switch ($mainDriver) {

            // If the driver is MySQL, connect to MySQL using the Mysql method
            case 'mysql':
                return $this->etablishMysql($dbName, $db , $requestAction);
                break;

            // If the driver is PostgreSQL, connect to PostgreSQL using the PostgreSQL method
            case 'pgsql':
                return $this->etablishPostgreSQL($dbName, $db, $requestAction);
                break;

            // If the driver is sqlite, connect to sqlite using the sqlite method
            case 'sqlite':
                return $this->etablishsqLite($dbName, $db, $requestAction);
                break;

            // If the driver is sqlserver, connect to sqlserver using the sqlserver method
            case 'sqlserver':
                return $this->etablishSqlServer($dbName, $db , $requestAction);
                break;         

            // If the driver is MongoDB, connect to MongoDB using the MongoDB method
            case 'mongodb':
                return $this->etablishMongoDB($dbName, $db, $requestAction);
                break;

            default:
                throw new epaphroditeException("Unsupported database driver");
        }
    }

    /**
     * Run seeder
     * 
     * @param int $db
     * @throws \Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException
     */
    public function SeederGenerated(
        int $db = 1
    )
    {

        // Load env
        checkDatabase::env();

        $mainDriver = $db !== 1 ? static::DB_DRIVER($db) : _FIRST_DRIVER_;

        // Switch based on the database driver type
        switch ($mainDriver) {

            // If the driver is MySQL, create the table using InitSeederGenerated
            case 'mysql':
                return (new InitSeederGenerated)->createTableMysql();
                break;

            // If the driver is Oracle, create the table using InitSeederGenerated
            case 'oracle':
                return (new InitSeederGenerated)->createTableOracle();
                break;                

            // If the driver is PostgreSQL, create the table using InitSeederGenerated
            case 'pgsql':
                return (new InitSeederGenerated)->createTablePostgreSQL();
                break;

            // If the driver is sqlLite, create collections using InitNoSeederGenerated
            case 'sqlite':
                return (new InitSeederGenerated)->createTableSqLite();
                break;

            // If the driver is sqlServer, create collections using InitNoSeederGenerated
            case 'sqlserver':
                return (new InitSeederGenerated)->createTableSqlServer();
                break;

            // If the driver is MongoDB, create collections using InitNoSeederGenerated
            case 'mongodb':
                return (new InitNoSeederGenerated)->createMongoCollections();
                break;

            // If the driver is MongoDB, create collections using InitNoSeederGenerated
            case 'redis':
                return (new InitNoSeederGenerated)->CreateRedisMigration();
                break;                

            default:
                throw new epaphroditeException("Unsupported database driver");
        }
    }

    /**
     * Load Env variables
     * @return void
     */
    public static function env():void{

        EnvLoader::init(_ENV_EDITABLE_);
    }
}
