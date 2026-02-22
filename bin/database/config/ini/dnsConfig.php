<?php

namespace Epaphrodites\database\config\ini;

class dnsConfig extends GetConfig{

    /**
     * @return string
     */
    protected static function ORACLE_DNS(
        int $db
    ):string{

        $host = static::ORACLE_HOST($db);
        $port = static::ORACLE_PORT($db);
        $sid = static::ORACLE_CONNEXION($db);

        return "oci:dbname=(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP) {$host} {$port} ) ) {$sid})";
    }

    /**
     * @return string
     */
    protected static function SQL_SERVER_DNS(
        int $db
    ):string{

        return "sqlsrv:".static::SQL_SERVER_DB_HOST($db) . static::SQL_SERVER_DB_PORT($db);
    }

    /**
     * @return string
     */    
    protected static function MYSQL_DNS(
        int $db
    ):string{

        return "mysql:" . static::DB_HOST($db) . ';' . static::DB_PORT($db);
    }   
    
    /**
     * @return string
     */    
    protected static function POSTGRES_SQL_DNS(
        int $db
    ):string{

        return "pgsql:" . static::DB_HOST($db) . ';' . static::DB_PORT($db);
    }  
    
    /**
     * @return string
     */    
    protected static function SQLITE_DNS(
        int $db
    ):string{

        return 'sqlite:' . static::DB_SQLITE($db);
    }
}
