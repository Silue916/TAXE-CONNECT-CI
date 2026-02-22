<?php

namespace Epaphrodites\database\config\Contracts;

interface DatabaseRequest
{

    /**
     * SQL request to select  
     * 
     * @param string|null $SqlChaine
     * @param string|null $param
     * @param array|null $datas
     * @param bool|false $closeConnection
     * @param int|1 $bd
     * @return array|null
    */
    public function select(
        string $SqlChaine, 
        array $datas = [], 
        bool $param = false , 
        bool $closeConnection = false , 
        int $db = 1
    ):array|NULL;

    /**
     * SQL run request  
     * 
     * @param string|null $SqlChaine
     * @param string|null $param
     * @param array|null $datas
     * @param bool|false $closeConnection
     * @param int|1 $bd
     * @return bool
    */    
    public function runRequest(
        string $SqlChaine, 
        array $datas = [], 
        bool $param = false , 
        bool $closeConnection = false , 
        int $db = 1
    ):bool|NULL;
}