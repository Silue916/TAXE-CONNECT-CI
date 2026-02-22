<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Stubs\migrationStubs;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Epaphrodites\epaphrodites\Console\Setting\settingMigration;

class modelMigration extends settingMigration{
 
    /**
    * @param \Symfony\Component\Console\Input\InputInterface $input
    * @param \Symfony\Component\Console\Output\OutputInterface $output
    */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        # Get console arguments
        $action = $input->getArgument('type');
       
        $results = $this->setUsersRequest($action);

        if($results === true ){

            $output->writeln("<info>The schema has been successfully created!!!✅</info>");
            return static::SUCCESS;
        }else{
            $output->writeln("<error>Sorry, check your request before starting the migration ❌</error>");
            return static::FAILURE;
        }
    }
    
    private function setUsersRequest(string $actions):bool
    {
        $result = explode( '_' , $actions);
 
        if(count($result)>=3){

            $type = end($result);
            $action = reset($result);
            $tableName = implode('_', array_slice($result, 1, -1));
            $positionTo = strpos($tableName, "_to_");
            
            if ($positionTo !== false) {
                $columnName = substr($tableName, 0, $positionTo);
                $oldTable = substr($tableName, $positionTo + strlen("_to_"));
            }

            $schemaFileName = OutputDirectory::Files('schema') . "/makeGearShift.php";

            if($action === "create" && $type === "table"){

                $fileName = OutputDirectory::Files('schema') . "/schema/makeUpGearShift.php";
                migrationStubs::generateMigration( $this->transformToFunction($actions) , $tableName , $fileName , $schemaFileName);
                return true;
            }

            if($action === "drop"&&$type === "table"){

                $fileName = OutputDirectory::Files('schema') . "/schema/makeDownGearShift.php";
                migrationStubs::dropMigration( $this->transformToFunction($actions) , $tableName , $fileName , $schemaFileName);
                return true;
            }  
            
            if($action === "add"&&$type === "table"&&$positionTo !== false && !empty($oldTable) && !empty($columnName)){

                $fileName = OutputDirectory::Files('schema') . "/schema/makeUpGearShift.php";
                migrationStubs::addColumn( $this->transformToFunction($actions) , $oldTable , $columnName , $fileName , $schemaFileName );
                return true;
            }                
        }
        
        return false;
    }
    
   /**
     *  @param string $initPage
     * @return string
     */
    private function transformToFunction($initPage): string
    {

        $parts = explode('_', $initPage);

        $camelCaseParts = array_map(function ($part) {
            return ucfirst($part);
        }, $parts);

        $camelCaseString = lcfirst(implode('', $camelCaseParts));

        $contract = explode('/', $camelCaseString);

        $parts = count($contract) > 1 ? $contract[1] : $contract[0];

        return $parts;
    }    
}
        