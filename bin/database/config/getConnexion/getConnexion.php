<?php

namespace Epaphrodites\database\config\getConnexion;

use Epaphrodites\database\config\ini\dnsConfig;
use Epaphrodites\database\config\Contracts\DriversConnexion;
use Database\Epaphrodites\config\getConnexion\etablishConnexion\mysql;
use Database\Epaphrodites\config\getConnexion\etablishConnexion\SqLite;
use Database\Epaphrodites\config\getConnexion\etablishConnexion\redisdb;
use Database\Epaphrodites\config\getConnexion\etablishConnexion\mongodb;
use Database\Epaphrodites\config\getConnexion\etablishConnexion\oracledb;
use Database\Epaphrodites\config\getConnexion\etablishConnexion\SqlServer;
use Database\Epaphrodites\config\getConnexion\etablishConnexion\getExtension as Extension;
use Database\Epaphrodites\config\getConnexion\etablishConnexion\postgreSQL;

class getConnexion extends dnsConfig implements DriversConnexion
{

    use mysql, oracledb, postgreSQL, mongodb, SqLite, SqlServer, redisdb, Extension;
}
