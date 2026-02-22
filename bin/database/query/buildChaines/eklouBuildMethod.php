<?php

namespace Epaphrodites\database\query\buildChaines;

trait eklouBuildMethod{

    /**
     * @param object $sourceConn
     * @param object $targetConn
     * @throws \Exception
     * @return void
     */
    private function postresMethod(
        object $sourceConn, 
        object $targetConn
    ):void {
        try {
           
            $query = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";
            $allTables = $sourceConn->query($query)->fetchAll(\PDO::FETCH_COLUMN);
    
            $tables = empty($this->sourceTables) ? $allTables : array_intersect($allTables, $this->sourceTables);
    
            if (empty($tables)) {
                throw new \Exception("Aucune table correspondante à copier.");
            }
    
            foreach ($tables as $table) {
                $columnsQuery = "
                    SELECT column_name, data_type, is_nullable, column_default, character_maximum_length
                    FROM information_schema.columns
                    WHERE table_schema = 'public' AND table_name = '$table'
                ";
                $columns = $sourceConn->query($columnsQuery)->fetchAll(\PDO::FETCH_ASSOC);
    
                if (empty($columns)) {
                    throw new \Exception("Impossible de récupérer les colonnes de la table '$table'.");
                }
    
                $createTableQuery = "CREATE TABLE IF NOT EXISTS \"$table\" (\n";
                $columnDefinitions = [];
                foreach ($columns as $column) {
                    $definition = "\"{$column['column_name']}\" {$column['data_type']}";
                    if ($column['character_maximum_length']) {
                        $definition .= "({$column['character_maximum_length']})";
                    }
                    if ($column['is_nullable'] === 'NO') {
                        $definition .= " NOT NULL";
                    }
                    if ($column['column_default'] && !str_contains($column['column_default'], 'nextval')) {
                        $definition .= " DEFAULT {$column['column_default']}";
                    }
                    $columnDefinitions[] = $definition;
                }
                $createTableQuery .= implode(",\n", $columnDefinitions) . "\n);";
    
                $targetConn->exec("DROP TABLE IF EXISTS \"$table\" CASCADE");
    
                $targetConn->exec($createTableQuery);
    
                $sequencesQuery = "
                    SELECT column_name, column_default
                    FROM information_schema.columns
                    WHERE table_schema = 'public' AND table_name = '$table' AND column_default LIKE 'nextval(%'
                ";
                $sequences = $sourceConn->query($sequencesQuery)->fetchAll(\PDO::FETCH_ASSOC);
    
                foreach ($sequences as $sequence) {
                    preg_match("/nextval\\('([^']+)'::regclass\\)/", $sequence['column_default'], $matches);
                    if (!empty($matches[1])) {
                        $sequenceName = $matches[1];
    
                        $sequenceCreateQuery = "CREATE SEQUENCE IF NOT EXISTS $sequenceName AS integer";
                        $targetConn->exec($sequenceCreateQuery);
    
                        $targetConn->exec("ALTER TABLE \"$table\" ALTER COLUMN \"{$sequence['column_name']}\" SET DEFAULT nextval('$sequenceName'::regclass)");
                    }
                }
    
                $selectQuery = "SELECT * FROM \"$table\"";
                $result = $sourceConn->query($selectQuery);
                $rows = $result->fetchAll(\PDO::FETCH_ASSOC);
    
                if (count($rows) > 0) {

                    $columns = array_keys($rows[0]);
                    $columnList = implode(", ", array_map(fn($col) => "\"$col\"", $columns));
                    $placeholders = implode(", ", array_fill(0, count($columns), "?"));
    
                    $insertQuery = "INSERT INTO \"$table\" ($columnList) VALUES ($placeholders)";
                    $insertStmt = $targetConn->prepare($insertQuery);
    
                    foreach ($rows as $row) {
                        $insertStmt->execute(array_values($row));
                    }
                }
    
                $primaryKeyQuery = "
                    SELECT kcu.column_name
                    FROM information_schema.table_constraints tc
                    JOIN information_schema.key_column_usage kcu
                    ON tc.constraint_name = kcu.constraint_name
                    WHERE tc.table_schema = 'public' AND tc.table_name = '$table' AND tc.constraint_type = 'PRIMARY KEY'
                ";
                $primaryKeyColumns = $sourceConn->query($primaryKeyQuery)->fetchAll(\PDO::FETCH_COLUMN);
    
                if (!empty($primaryKeyColumns)) {
                    
                    $targetConn->exec("ALTER TABLE \"$table\" DROP CONSTRAINT IF EXISTS \"{$table}_pkey\"");
                    $primaryKeyColumnsList = implode(", ", array_map(fn($col) => "\"$col\"", $primaryKeyColumns));
                    $targetConn->exec("ALTER TABLE \"$table\" ADD PRIMARY KEY ($primaryKeyColumnsList)");
                }
    
                $indexQuery = "
                    SELECT indexdef
                    FROM pg_indexes
                    WHERE schemaname = 'public' AND tablename = '$table'
                ";
                $indexes = $sourceConn->query($indexQuery)->fetchAll(\PDO::FETCH_COLUMN);
    
                foreach ($indexes as $index) {
                    try {
                        $targetConn->exec($index);
                    } catch (\Exception $e) {
                        
                        echo "Avertissement lors de la création de l'index : " . $e->getMessage() . "\n";
                    }
                }
            }
    
            echo "Tables copiées avec succès";
        } catch (\Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
    
    private function mysqlMethod(
        object $sourceConn, 
        object $targetConn
    ): void {
        
        try {
            
            $targetConn->beginTransaction();
    
            $query = "SELECT table_name FROM information_schema.tables WHERE table_schema = '{$this->sourceDb}'";
            $allTables = $sourceConn->query($query)->fetchAll(\PDO::FETCH_COLUMN);
    
            $tables = empty($this->sourceTables) ? $allTables : array_intersect($allTables, $this->sourceTables);
    
            if (empty($tables)) {
                throw new \Exception("Aucune table correspondante à copier.");
            }
    
            foreach ($tables as $table) {

                $columnsQuery = "
                    SELECT column_name, data_type, is_nullable, column_default, character_maximum_length, extra
                    FROM information_schema.columns
                    WHERE table_schema = '{$this->sourceDb}' AND table_name = '$table'
                ";
                $columns = $sourceConn->query($columnsQuery)->fetchAll(\PDO::FETCH_ASSOC);
    
                if (empty($columns)) {
                    throw new \Exception("Impossible de récupérer les colonnes de la table '$table'.");
                }
    
                $createTableQuery = "CREATE TABLE IF NOT EXISTS `$table` (\n";
                $columnDefinitions = [];
                foreach ($columns as $column) {
                    $definition = "`{$column['column_name']}` {$column['data_type']}";
                    if ($column['character_maximum_length']) {
                        $definition .= "({$column['character_maximum_length']})";
                    }
                    if ($column['is_nullable'] === 'NO') {
                        $definition .= " NOT NULL";
                    }
                    if ($column['column_default'] !== null) {
                        $definition .= " DEFAULT '{$column['column_default']}'";
                    }
                    if (strpos($column['extra'], 'auto_increment') !== false) {
                        $definition .= " AUTO_INCREMENT";
                    }
                    $columnDefinitions[] = $definition;
                }
                $createTableQuery .= implode(",\n", $columnDefinitions) . "\n);";
    
                $targetConn->exec("DROP TABLE IF EXISTS `$table`");
                $targetConn->exec($createTableQuery);
    
                $selectQuery = "SELECT * FROM `$table`";
                $result = $sourceConn->query($selectQuery);
                $rows = $result->fetchAll(\PDO::FETCH_ASSOC);
    
                if (count($rows) > 0) {
                    $columns = array_keys($rows[0]);
                    $columnList = implode(", ", array_map(fn($col) => "`$col`", $columns));
                    $placeholders = implode(", ", array_fill(0, count($columns), "?"));
    
                    $insertQuery = "INSERT INTO `$table` ($columnList) VALUES ($placeholders)";
                    $insertStmt = $targetConn->prepare($insertQuery);
    
                    foreach (array_chunk($rows, 1000) as $batch) {
                        foreach ($batch as $row) {
                            $insertStmt->execute(array_values($row));
                        }
                    }
                }
    
                $primaryKeyQuery = "
                    SELECT column_name
                    FROM information_schema.key_column_usage
                    WHERE table_schema = '{$this->sourceDb}' AND table_name = '$table' AND constraint_name = 'PRIMARY'
                ";
                $primaryKeyColumns = $sourceConn->query($primaryKeyQuery)->fetchAll(\PDO::FETCH_COLUMN);
    
                if (!empty($primaryKeyColumns)) {
                    $primaryKeyColumnsList = implode(", ", array_map(fn($col) => "`$col`", $primaryKeyColumns));
                    $targetConn->exec("ALTER TABLE `$table` ADD PRIMARY KEY ($primaryKeyColumnsList)");
                }
    
                $indexQuery = "
                    SELECT index_name, GROUP_CONCAT(column_name ORDER BY seq_in_index ASC) AS column_names
                    FROM information_schema.statistics
                    WHERE table_schema = '{$this->sourceDb}' AND table_name = '$table'
                    GROUP BY index_name
                ";
                $indexes = $sourceConn->query($indexQuery)->fetchAll(\PDO::FETCH_ASSOC);
    
                foreach ($indexes as $index) {
                    $indexName = $index['index_name'];
                    $columnNames = $index['column_names'];
                    if ($indexName !== 'PRIMARY') {
                        $targetConn->exec("CREATE INDEX `$indexName` ON `$table` ($columnNames)");
                    }
                }
            }
    
            $targetConn->commit();
            echo "Succefuly copy {$this->sourceDb} to {$this->targetDb}.";
        } catch (\Exception $e) {
          
            $targetConn->rollBack();
            echo "Error : " . $e->getMessage();
        }
    }    
}