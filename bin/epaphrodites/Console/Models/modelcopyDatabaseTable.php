<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Epaphrodites\database\query\Builders;
use Epaphrodites\database\gearShift\makeGearShift;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\settingcopyDatabaseTable;
        
class modelcopyDatabaseTable extends settingcopyDatabaseTable{
        
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        
        # Get console arguments
        $action = $input->getOption('c') ? 'copy' : '';

        $results = $this->makeDatabaseTableCopy($action);

        if($results === true ){

            $output->writeln("<info>All '{$action}' Database tables has been successfully copying!!!✅</info>");
            return static::SUCCESS;
        }else{
            $output->writeln("<error>Sorry, check your request before starting the copy ❌</error>");
            return static::FAILURE;
        }        
    }

   /**
     * Search for and execute PHP migrations in the specified directory.
     * @return bool Returns true if a migration was executed successfully, otherwise false.
     */
    private function makeDatabaseTableCopy($action): bool
    {
        
        if(!empty($action)){
            
            $schema = new makeGearShift;

            if($action=="copy"){
                
                foreach ( $schema->copy() as $request ){
                   
                }

                return true;  
            }
        }

        return false;
    }
}
        