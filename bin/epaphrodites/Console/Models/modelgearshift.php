<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Epaphrodites\database\query\Builders;
use Epaphrodites\database\gearShift\makeGearShift;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\settinggearshift;


class modelgearshift extends settinggearshift
{

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        
        # Get console arguments
        $action = $input->getOption('u') ? 'up' : ($input->getOption('d') ? 'down' : "");

        $results = $this->makeMigration($action);

        if($results === true ){

            $output->writeln("<info>All '{$action}' migration has been successfully created!!!✅</info>");
            return static::SUCCESS;
        }else{
            $output->writeln("<error>Sorry, check your request before starting the migration ❌</error>");
            return static::FAILURE;
        }        
    }

    /**
     * Search for and execute PHP migrations in the specified directory.
     * @return bool Returns true if a migration was executed successfully, otherwise false.
     */
    private function makeMigration($action): bool
    {
        
        if(!empty($action)){

            $schema = new makeGearShift;

            if($action==="up"){

                foreach ( $schema->up() as $request ){
                      
                    $this->executeQuery($request['request'] , $request['db']);
                }

                return true;  
            }

            if($action==="down"){

                foreach ( $schema->down() as $request ){
                    
                    $this->executeQuery($request['request'] , $request['db']);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Execute the database query.
     * @param array $queryChaine
     * @return void
     */
    private function executeQuery(array $queryChaine , int $db):void
    {
       
        $database = new Builders;
      
        $database->multiChaine($queryChaine)->setMultiQuery($db, true);
    }
}