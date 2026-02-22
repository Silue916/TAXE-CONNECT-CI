<?php

namespace Epaphrodites\database\query\buildChaines;

trait copyQueryChaine {

    use eklouBuildMethod;

    private int $sourceDb;
    private int $targetDb;
    private array $sourceTables = [];
    
    /**
     * @param int $database
     * @return mixed
     */
    public function dbSource(
        int $database
    ) {
        $this->sourceDb = $database;
        return $this;
    }
    
    /**
     * @param int $database
     * @return mixed
     */
    public function dbTarget(
        int $database
    ) {
        $this->targetDb = $database;
        return $this;
    }
    
    /**
     * @param array $tables
     * @return mixed
     */
    public function table(array $tables) {
        $this->sourceTables = $tables;
        $this->execute();
        return $this;
    }

    /**
     * function to copy Tables
     * 
     * @param int $sourceDb
     * @param callable $callback
     * @return void
     */
    public function copyTables(
        int $sourceDb, 
        callable $callback
    ):void {
        $this->dbSource($sourceDb);
        $callback($this);
    }    

    public function execute(){

        $driver = $this->driver($this->sourceDb);

        $sourceConn = $this->getdbConnexion($this->sourceDb);
        $targetConn = $this->getdbConnexion($this->targetDb);

         match ($driver) {
            'pgsql' => $this->postresMethod($sourceConn, $targetConn),
            'mysql' => $this->mysqlMethod($sourceConn, $targetConn),
        };
    }

    /**
     * get database connexion
     * 
     * @param int $db
     * @return array|object
     */
    private function getdbConnexion(
        int $db = 1
    ):array|object {
            
        $connexion = (new \Epaphrodites\database\config\process\getDatabase)->GetConnexion($db);

        return $connexion;
    }
}