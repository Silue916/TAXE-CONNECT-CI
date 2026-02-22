<?php

namespace Epaphrodites\database\query\buildChaines;
use Epaphrodites\database\config\process\envLoader as EnvLoader;

use Epaphrodites\database\config\ini\GetConfig;

trait gearQueryChains
{
    use copyQueryChaine;
    
    private ?string $tableName = null;
    private array $columns = [];
    private ?array $dropColumn = null;
    private int $db;

    /**
     * @param int $db
     */
    public function db(
        int $db = 1
    ): self
    {
        $this->db = $db;
        return $this;
    }

    /**
     * Create a new table with the specified name and callback.
     * @param string $tableName The name of the table to be created.
     * @param callable $callback  The callback function to define table columns and properties.
     * @return array The generated SQL for creating the table.
     */
    public function createTable(
        string $tableName, 
        callable $callback
    ): array
    {
        $this->reset();
        $this->tableName = $tableName;
        $callback($this);
        return $this->generateSQL();
    }

    /**
     * Create a new table with the specified name and callback.
     * @param string   $tableName The name of the table to be created.
     * @param callable $callback  The callback function to define table columns and properties.
     * @return array The generated SQL for creating the table.
     */
    public function createColumn(
        string $tableName, 
        callable $callback
    ): array
    {
        $this->reset();
        $this->tableName = $tableName;
        $callback($this);
        return $this->generateColumn();
    }

    /**
     * Drop a table with the specified name and optional callback for additional configurations.
     * @param string $tableName The name of the table to be dropped.
     * @param callable $callback  Optional callback function for additional configurations.
     * @return array The generated SQL for dropping the table or columns.
     */
    public function dropTable(
        string $tableName, 
        callable|null $callback = null
    ): array
    {
        $this->reset();
        $this->tableName = $tableName;
        if ($callback !== null) {
            $callback($this);
        }
        return $this->dropTableColumn();
    }

    /**
     * Specify a column to be dropped from the table.
     * @param string $column The name of the column to be dropped.
     * @return $this
     */
    public function dropColumn(
        string $column, 
        ?array $option = []
    ): self
    {
        $this->dropColumn[] = compact('column', 'option');
        return $this;
    }

    /**
     * Add a new column to the table.
     *
     * @param string $columnName The name of the new column.
     * @param string $type The data type of the new column.
     * @param array $options Additional options for the new column.
     *
     * @return $this
     */
    public function addColumn(
        string $columnName, 
        ?string $type = '', 
        ?array $options = []
    ): self
    {
        $this->columns[] = compact('columnName', 'type', 'options');

        return $this;
    }

    /**
     * Add an index to the specified column(s) of the table.
     * @param string|array $columns The name(s) of the column(s) to add the index to.
     * @param string|null $indexName Optional name for the index. If not provided, a default name will be generated.
     * @return $this
     */
    public function addIndex(
        string $columns, 
        string|null $indexName = null
    ): self
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $indexName = $indexName ?: 'idx_' . implode('_', $columns);

        $this->columns[] = compact('indexName', 'columns');

        return $this;
    }

    /**
     * Add a foreign key constraint to the specified column(s) of the table.
     * @param string|array $columns The name(s) of the column(s) to which the foreign key constraint is added.
     * @param string $reference The reference table and column(s) for the foreign key in the format 'table(column)'.
     * @param array $options Optional. Additional options for the foreign key constraint such as 'onDelete' and 'onUpdate'.
     * @return $this
     */
    public function addForeign(
        string $reference, 
        string $foreign,
        string|null $constraint = null,
        array $options = []
    ): self
    {
        if (!is_array($reference)) {
            $columns = [$reference];
        }

        $this->columns[] = compact('reference', 'foreign', 'constraint', 'options');

        return $this;
    }    

    /**
     * Generate the SQL statement for creating the table.
     * @return array The generated SQL for creating the table.
     */
    private function generateSQL(): array
    {
        
        $requests = [];
        $db = empty($this->db) ? 1 : $this->db;

        $driver = $this->driver($db);

        $sql = match ($driver) {

                'oracle' => "BEGIN EXECUTE IMMEDIATE 'CREATE TABLE {$this->tableName} (",
                'sqlserver' => "IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$this->tableName}') BEGIN CREATE TABLE {$this->tableName} (",
            
                default => "CREATE TABLE IF NOT EXISTS {$this->tableName} (",
            };

        foreach ($this->columns as $column) {
            if (isset($column['columnName'])) {
                $columnName = $column['columnName'];
                $type = $column['type'];
                $options = implode(' ', $column['options'] ?? '');
                $sql .= "{$columnName} {$type} {$options}, ";
            }
        }

        $sql = rtrim($sql, ', ');

        $sql .= match ($driver) {
            'oracle' => ")'; EXCEPTION WHEN OTHERS THEN IF SQLCODE = -955 THEN NULL; ELSE RAISE; END IF; END;",
            'sqlserver' => "); END",
            default => ")"
        };
        $requests[] = $sql;

        foreach ($this->columns as $column) {
            if (isset($column['indexName'])) {
                $indexName = $column['indexName'];
                $columns = implode(', ', $column['columns']);
                $requests[] = $this->generateIndexSQL($indexName, $columns, $db, "CREATE");
            }
        }
    
        foreach ($this->columns as $column) {
            if (isset($column['reference'], $column['foreign'])) {
                $constraint = isset($column['constraint']) 
                                ? $column['constraint'] 
                                : "fk_".$column['reference']."_".$this->tableName;
                $reference = $column['reference'];
                $foreignKey = $column['foreign'];
                $options = implode(' ', $column['options'] ?? []);

                $requests[] = "ALTER TABLE {$this->tableName} ADD CONSTRAINT {$constraint} FOREIGN KEY ($foreignKey) REFERENCES {$reference}($foreignKey) $options";
            }
        }
      
        return ['request' => $requests, 'db' => $db];
    }

    /**
     * Generate the SQL statement for adding columns and indexes to the table.
     * @return array The generated SQL for adding columns and indexes.
     */
    public function generateColumn(): array
    {
        $db = empty($this->db) ? 1 : $this->db;
    
        $driver = $this->driver($db);

        $sql = "ALTER TABLE {$this->tableName}";
    
        // Get the best request syntax according driver
        $syntax = match ($driver){
                "sqlserver" => 'ADD',
                "oracle" => 'ADD',
                default => 'ADD COLUMN'
            };

        $columnsSql = [];

        $indexColumns = [];
    
        foreach ($this->columns as $column) {

            if (isset($column['columnName'], $column['type'])) {
                $columnName = $column['columnName'];
                $type = $column['type'];
                $options = isset($column['options']) ? implode(' ', $column['options']) : '';
                $columnsSql[] = "{$sql} {$syntax} {$columnName} {$type} {$options}";
            }
            
            if (isset($column['indexName'], $column['columns'])) {
                $indexName = $column['indexName'];
                $columns = implode(', ', $column['columns']);
                $indexSql = $this->generateIndexSQL($indexName, $columns, $db, "CREATE");
                $indexColumns[] = $indexSql;
            }
        }
    
        $request = array_merge($columnsSql, $indexColumns);

        foreach ($this->columns as $column) {
            if (isset($column['reference'], $column['foreign'])) {
                $constraint = isset($column['constraint']) 
                                ? $column['constraint'] 
                                : "fk_".$column['reference']."_".$this->tableName;
                $reference = $column['reference'];
                $foreignKey = $column['foreign'];
                $options = implode(' ', $column['options'] ?? []);

                $request[] = "ALTER TABLE {$this->tableName} ADD CONSTRAINT {$constraint} FOREIGN KEY ($foreignKey) REFERENCES {$reference}($foreignKey) $options";
            }
        }
        
        return ['request' => $request, 'db' => $db];
    }
    
    /**
     * Generate the SQL statement for dropping the table or columns.
     * @return array The generated SQL for dropping the table or columns.
     */
    private function dropTableColumn(): array
    {

        $db = empty($this->db) ? 1 : $this->db;

        $driver = $this->driver($db);


        $comma = $driver !== 'sqlite' ? ',' : '';

        if (empty($this->dropColumn)) {

            $sql = $driver == "oracle" 
                            ? "DROP TABLE {$this->tableName}" 
                            : "DROP TABLE IF EXISTS {$this->tableName}";
          
            return ['request' => [$sql], 'db' => $db];
        }

        $sql = "ALTER TABLE {$this->tableName}";

        foreach ($this->dropColumn as $column) {

            $options = !empty($column['option']) ? implode(' ', $column['option']) : 'COLUMN';

            $sql .= " DROP {$options} {$column['column']}{$comma}";
        }

        $sql = rtrim($sql, $comma);

        return ['request' => [$sql], 'db' => $db];
    }

   /**
     * Generate the SQL statement for adding an index based on the database type.
     * @param string $indexName The name of the index.
     * @param string $columns The columns to include in the index.
     * @param int $db The database type.
     * @return string The generated SQL for adding the index.
     */
    private function generateIndexSQL(
        string $indexName, 
        string $columns, 
        int $db, 
        string $option = "ADD"
    ): string
    {
        switch ($this->driver($db)) {
            case 'mysql':
                return "{$option} INDEX {$indexName} ON {$this->tableName}({$columns})";
            case 'oracle':
            case 'pgsql':
                return "{$option} INDEX {$indexName} ON {$this->tableName}({$columns})";
            case 'sqlserver':
            case 'sqlite':
                return "{$option} INDEX {$indexName} ON {$this->tableName}({$columns})";
            default:
                throw new \InvalidArgumentException("Unsupported database type: {$db}");
        }
    }

    /**
     * Reset the properties of the trait.
     * @return void
     */
    private function reset(): void
    {
        $this->db = 1;
        $this->tableName = null;
        $this->columns = [];
        $this->dropColumn = null;
    }

    /**
     * Check database driver
     * @return string
    */
    public function driver($key): string
    {
        // Load env
        self::env();

        $db = max(1, (int) $key);

        return GetConfig::DB_DRIVER($db);
    }

    /**
     * Load Env variables
     * @return void
     */
    private static function env():void{

        EnvLoader::init(_ENV_EDITABLE_);
    }    
}
