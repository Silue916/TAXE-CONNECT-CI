<?php

namespace Epaphrodites\database\query\buildChaines;

trait buildQueryChaines
{

    /**
     * select query chaine
     * @param string|null $propriety
     * @return mixed
     */
    public function SQuery(
        string|null $propriety = null
    ):mixed{

        if ($propriety === NULL&&!isset($this->sumCase)) {
            $propriety = '*';
        }    
        
        if($this->sumCase){

            $propriety .= ",{$this->sumCase}";
        }    
        
        $propriety = ltrim($propriety, ',');

        /* 
        * Select initial query chaine
        */
        $query = "SELECT {$propriety} FROM {$this->table}";

        /* 
         * Add join if exist
        */
        if ($this->join) {

            $query .= " {$this->join}";
        }

        /* 
         * Add join left if exist
        */
        if ($this->joinLeft) {

            $query .= " {$this->joinLeft}";
        }

        /* 
         * Add join right if exist
        */
        if ($this->joinRight) {

            $query .= " {$this->joinRight}";
        }  
        
        /* 
         * Add join right if exist
        */
        if ($this->joinFull) {

            $query .= " {$this->joinFull}";
        }           

        /**
         * Add where if exist
         */
        if ($this->where) {
            $query .= " WHERE {$this->where}";
        }

        /** 
         * Add LIKE if exist
         */
        if ($this->like) {
            $query .= " WHERE {$this->like} LIKE ?";
        }

        /**
         * Add match if exist
         */
        if ($this->match) {
            $query .= " WHERE MATCH ({$this->match}) AGAINST (?)";
        }

        /** 
         * Add BETWEEN if exist
         */
        if ($this->between) {
            $query .= " WHERE {$this->between} BETWEEN ? AND ? ";
        }

        /** 
         * Add BETWEEN DATE TIME if exist
         */
        if ($this->between_date) {
            $query .= " WHERE {$this->between_date} BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') ";
        }        

        /** 
         * Add AND if exist
         */
        if ($this->and) {
            $query .= "{$this->and}";
        }

        /** 
         * Add JSON AND if exist
         */
        if ($this->andJSON) {
            $query .= "{$this->andJSON}";
        }

        /** 
         * Add IS NOT NULL OR IS NULL if exist
         */
        if ($this->is) {
            $query .= " {$this->is}";
        }

        /** 
         * Add OR if exist
         */
        if ($this->or) {
            $query .= "{$this->or}";
        }

        /** 
         * Add GROUP BY if exist
         */
        if ($this->group) {
            $query .= " {$this->group}";
        }

        /** 
         * Add ORDER BY if exist
         */
        if ($this->order) {
            $query .= " {$this->order}";
        }

        /** 
         * Add HAVING if exist
         */
        if ($this->having) {
            $query .= " {$this->having}";
        }

        /** 
         * Add LIMIT if exist
         */
        if ($this->limit) {
            $query .= " {$this->limit}";
        }

        /** 
         * Add OFFSET if exist
         */
        if ($this->offset) {
            $query .= " {$this->offset}";
        }

        // Init variables
        $this->initQueryChaine();
        
        return $this->selectBuildRequest($query);
    }

    /**
     * insert query chaine
     *
     * @return string
     */
    public function IQuery(): string
    {

        /** 
         * Insert initial query chaine
         */
        $Iquery = "INSERT INTO {$this->table} ";

        /**
         * Add DATAS if exist
         */
        if ($this->insert) {
            $Iquery .= "( {$this->insert} )";
        }

        /**
         * Add VALUES if exist
         */
        if ($this->values) {
            $Iquery .= " VALUES( {$this->values} )";
        }

        return $this->executeBuildRequest($Iquery);
    }

    /**
     * Update query chaine
     *  @return mixed
     */
    public function UQuery(): string
    {

        $query = "";

        /** 
         * Update inital query chaine
         */
        $query = "UPDATE {$this->table} ";

        /** 
         * Add join if exist
         */
        if ($this->join) {

            $query .= " {$this->join}";
        }

        /** 
         * Add SET if exist
         */
        if ($this->set) {
            $query .= " SET {$this->set} ";
        }

        /** 
         * Add SET DATE if exist
         */
        if ($this->set_date) {
            $query .= " {$this->set_date} ";
        }        

        /** 
         * Add SET if exist
         */
        if ($this->set_i) {
            $query .= " SET {$this->set_i} ";
        }

        /** 
         * Add REPLACE if exist
         */
        if ($this->replace) {
            $query .= " SET {$this->replace}";
        }

        /** 
         * Add WHERE if exist
         */
        if ($this->where) {
            $query .= " WHERE {$this->where} ";
        }

        /** 
         * Add IS NOT NULL OR IS NULL if exist
         */
        if ($this->is) {
            $query .= " {$this->is}";
        }

        /** 
         * Add match if exist
         */
        if ($this->match) {
            $query .= " WHERE MATCH ({$this->match}) AGAINST (?)";
        }

        /** 
         * Add BETWEEN if exist
         */
        if ($this->between) {
            $query .= " WHERE {$this->between} BETWEEN ? AND ? ";
        }

        /** 
         * Add BETWEEN DATE TIME if exist
         */
        if ($this->between_date) {
            $query .= " WHERE {$this->between_date} BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') ";
        }        

        /** 
         * Add LIKE if exist
         */
        if ($this->like) {
            $query .= " WHERE {$this->like} LIKE ? ";
        }

        /* 
        *Add AND if exist
        */
        if ($this->and) {
            $query .= " {$this->and}";
        }

        /** 
         * Add JSON AND if exist
         */
        if ($this->andJSON) {
            $query .= "{$this->andJSON}";
        }        

        /** 
         * Add OR if exist
         */
        if ($this->or) {
            $query .= "{$this->or}";
        }

        /** 
         * Add ORDER BY if exist
         */
        if ($this->order) {
            $query .= " {$this->order}";
        }

        /** 
         * Add HAVING if exist
         */
        if ($this->having) {
            $query .= " {$this->having}";
        }

        /** 
         * Add LIMIT if exist
         */
        if ($this->limit_i) {
            $query .= " {$this->limit_i}";
        }

        /** 
         * Add OFFSET if exist
         */
        if ($this->offset) {
            $query .= " {$this->offset}";
        }        

        // Init variables
        $this->initQueryChaine();

        return $this->executeBuildRequest($query);
    }

    /**
     * Delete query chaine
     * @return mixed
     */
    public function DQuery(): string
    {

        /** 
         * Update inital query chaine
         */
        $query = "DELETE FROM {$this->table} ";

        /* 
            Add WHERE if exist
        */
        if ($this->where) {
            $query .= " WHERE {$this->where} ";
        }

        /** 
         * Add LIKE if exist
         */
        if ($this->like) {
            $query .= " WHERE {$this->like} LIKE ? ";
        }

        /** 
         * Add IS NOT NULL OR IS NULL if exist
         */
        if ($this->is) {
            $query .= " {$this->is}";
        }

        /** 
         * Add match if exist
         */
        if ($this->match) {
            $query .= " WHERE MATCH ({$this->match}) AGAINST (?)";
        }

        /** 
         * Add BETWEEN if exist
         */
        if ($this->between) {
            $query .= " WHERE {$this->between} BETWEEN ? AND ? ";
        }

        /** 
         * Add BETWEEN DATE TIME if exist
         */
        if ($this->between_date) {
            $query .= " WHERE {$this->between_date} BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') ";
        }                

        /** 
         * Add AND if exist
         */
        if ($this->and) {
            $query .= " {$this->and}";
        }

        /** 
         * Add JSON AND if exist
         */
        if ($this->andJSON) {
            $query .= "{$this->andJSON}";
        }

        /** 
         * Add OR if exist
         */
        if ($this->or) {
            $query .= "{$this->or}";
        }

        /** 
         * Add HAVING if exist
         */
        if ($this->having) {
            $query .= " {$this->having}";
        }

        /** 
         * Add LIMIT if exist
         */
        if ($this->limit_i) {
            $query .= " {$this->limit_i}";
        }

        /** 
         * Add OFFSET if exist
         */
        if ($this->offset) {
            $query .= " {$this->offset}";
        }        

        // Init variables
        $this->initQueryChaine();
        
        return $this->executeBuildRequest($query);
    }

    /**
     * @param int $db
     * @return bool
     */
    public function addToRedis(
        int $db = 1
    ): bool{

        $getConnexion = $this->rdb($db);

        $index = !isset($this->index) ?: ":{$this->index}";

        $key = "{$getConnexion['db']}:{$this->key}{$index}";

        $autoIncr = "";

        $order = $getConnexion['connexion']->incr("{$key}:id");

        if (isset($this->lastIndex)) {
            $autoIncr = ":{$order}";
        }

        $jsonData = json_encode(array_merge([$this->id => $order], $this->param));

        $getConnexion['connexion']->set("{$key}{$autoIncr}", $jsonData);

        return true;
    }

    /**
     * @param int $db
     * @return bool
     */
    public function checkIsExist(int $db = 1): bool
    {
        $getConnexion = $this->rdb($db);
        $index = isset($this->index) ? ":{$this->index}" : '';
        $key = "{$getConnexion['db']}:{$this->key}{$index}";
    
        if (strpos($key, '*') === false) {
            return (bool)$getConnexion['connexion']->exists($key);
        }
    
        $redis = $getConnexion['connexion'];
        $cursor = null;
        $it = null;
        
        while ($keys = $redis->scan($it, $key, 3)) {
            if (!empty($keys)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * @param int $db
     * @return bool
     */
    public function isExist(
        int $db = 1
    ): bool{
        $cursor = '0';

        $getConnexion = $this->rdb($db);

        $index = isset($this->index) ? ":{$this->index}" : '*';

        $key = "{$getConnexion['db']}:{$this->key}{$index}";

        $result = $getConnexion['connexion']->scan($cursor, $key, 3);

        return !empty($result) ? true : false;
    }

    /**
     * @param int $db
     * @return bool
     */
    public function delRedis(
        int $db = 1
    ): bool{

        $getConnexion = $this->rdb($db);

        $index = !isset($this->index) ?: ":{$this->index}";

        $key = "{$getConnexion['db']}:{$this->key}{$index}";

        $getConnexion['connexion']->del($key);

        return true;
    }

    /**
     * @param int $db
     * @return bool
     */
    public function updRedis(
        int $db = 1
    ): bool{

        $getConnexion = $this->rdb($db);

        $index = !isset($this->index) ?: ":{$this->index}";

        $key = "{$getConnexion['db']}:{$this->key}{$index}";

        $existingData = $getConnexion['connexion']->get($key);

        $existingHash = json_decode($existingData, true);

        foreach ($this->rset as $field => $value) {

            if (array_key_exists($field, $existingHash)) {

                $existingHash[$field] = $value;
            }
        }

        $updatedData = json_encode($existingHash);

        $getConnexion['connexion']->set($key, $updatedData);

        return true;
    }

    /**
     * @param int $db
     * @return int|array
     */
    public function redisGet(
        int $db = 1
    ): int|array{
        $begin = 0;
        $end = 1;

        $excludedSuffix = ':id';

        if (isset($this->rlimit)) {
            $end = $this->rlimit["end"];
            $begin = $this->rlimit["begin"];
        }

        $getConnexion = $this->rdb($db);
        $keyPattern = "{$getConnexion['db']}:{$this->key}" . ($this->index ? ":{$this->index}" : '') . ($this->all ? ":*" : '');

        $keys = $getConnexion['connexion']->keys($keyPattern);
        $data = [];
        
        foreach ($keys as $key) {
            if (!str_ends_with($key, $excludedSuffix)) {
                $getDatas = $getConnexion['connexion']->get($key);

                if ($getDatas !== false) {
                    $decodedDatas = json_decode($getDatas, true);
                    if (is_array($decodedDatas)) {
                        $id = (int)substr($key, strrpos($key, ':') + 1);
                        $data[$id] = $decodedDatas;
                    }
                }
            }
        }

        krsort($data);
        
        $paginatedData = array_slice($data, $begin, $end - $begin);

        return $this->count ? count($paginatedData) : $paginatedData;
    }

    /**
     * @param array $data
     * @param array $keysToSearch
     * @param array $params
     * @return bool
     */
    private function verifyDatas(
        array $data, 
        array $keysToSearch,
        array $params
    ): bool{
        foreach ($keysToSearch as $key) {
            if (!array_key_exists($key, $data) || !in_array($data[$key], $params, true)) {
                return false;
            }
        }

        return true;
    }
}
