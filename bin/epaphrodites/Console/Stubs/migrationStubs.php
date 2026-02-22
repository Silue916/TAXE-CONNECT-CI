<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class migrationStubs{

    /**
     * @param string $schemaName
     * @param string $table
     * @param string $fileName
     * @return void
     */      
    public static function generateMigration(string $schemaName, string $table , string $fileName , string $schemaFileName):void
    {

        $dateTime = date("d/m/Y H:i:s");

$stub = "    /**
    * Create table $table
    * create $dateTime
    */
    public function {$schemaName}()
    {
        return \$this->createTable('$table', function (\$table) {

               \$table->addColumn('_id', 'INTEGER', ['PRIMARY KEY']);
               \$table->addColumn('name', 'VARCHAR(100)');
               \$table->db(1);
        });
    }     
}";
        static::generateSchema($fileName, $stub);  
        static::addSchema($schemaFileName, "\t\t\t\$this->{$schemaName}()," , true);  
    }

    /**
     * @param string $schemaName
     * @param string $table
     * @param string $fileName
     * @return void
     */  
    public static function dropMigration(string $schemaName, string $table , string $fileName , string $schemaFileName):void
    {

        $dateTime = date("d/m/Y H:i:s");

$stub = "    /**
    * Drop $table
    * drop $dateTime
    */
    public function {$schemaName}()
    {
        return \$this->dropTable('$table', function (\$table) {
               \$table->dropColumn('name');
               \$table->db(1);
        });
    }     
}";

    static::generateSchema($fileName, $stub);    
    static::addSchema($schemaFileName, "\t\t\t\$this->{$schemaName}()," , false);
    }    

    /**
     * @param string $schemaName
     * @param string $table
     * @param string $columnName
     * @param string $fileName
     * @return void
     */    
    public static function addColumn(string $schemaName , string $table , string $columnName , string $fileName , string $schemaFileName):void
    {

        $dateTime = date("d/m/Y H:i:s");

$stub = "    /**
    * Add Column $columnName
    * create $dateTime
    */
    public function {$schemaName}()
    {
        return \$this->createColumn('$table', function (\$table) {
               \$table->addColumn('{$columnName}', 'VARCHAR(100)');
               \$table->db(1);
        });
    }     
}";
        static::generateSchema($fileName, $stub);  
        static::addSchema($schemaFileName, "\t\t\t\$this->{$schemaName}()," , true);        
    }       
    
    /**
     * @param string $fileName
     * @param string $newFunctionContent
     * @return void
     */
    public static function generateSchema(string $fileName, string $newFunctionContent):void
    {
        $fileContent = file_get_contents($fileName);
    
        $lastClassBracketPosition = strrpos($fileContent, '}');
    
        if ($lastClassBracketPosition !== false) {
            $fileContent = substr($fileContent, 0, $lastClassBracketPosition);
        }
    
        $fileContent .= "\n" . $newFunctionContent;
    
        file_put_contents($fileName, $fileContent, LOCK_EX);
    }

    /**
     * @param string $fileName
     * @param string $newFunctionContent
     * @param bool $isUp
     * @return void
     */  
    public static function addSchema(string $fileName, string $newFunctionContent, bool $isUp): void
    {
        $fileContent = file_get_contents($fileName);
    
        $lastMethodPosition = $isUp ? strrpos($fileContent, 'public final function up') : strrpos($fileContent, 'public final function down');
    
        if ($lastMethodPosition !== false) {
            $openingBracketPosition = strpos($fileContent, '[', $lastMethodPosition);
    
            if ($openingBracketPosition !== false) {
                $fileContent = substr_replace($fileContent, "\n" . $newFunctionContent, $openingBracketPosition + 1, 0);
            }
        }
    
        file_put_contents($fileName, $fileContent, LOCK_EX);
    }
}