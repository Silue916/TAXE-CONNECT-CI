<?php

namespace Epaphrodites\epaphrodites\Console\Models;
        
use Epaphrodites\database\seeders\databaseSeeding;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\settingseeding;
        
class modelseeding extends settingseeding{
        
    private static array $Autorise = ['sql' , 'nosql'];
    
    /**
    * @param \Symfony\Component\Console\Input\InputInterface $input
    * @param \Symfony\Component\Console\Output\OutputInterface $output
    */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){

        $seeding = new databaseSeeding;

        $databaseType = strtolower($input->getArgument('type'));

        if (in_array($databaseType, static::$Autorise)) {
            
            if ($databaseType == "nosql") {
                $seeding->noSqlRun();
            } elseif ($databaseType == "sql") {
                $seeding->sqlRun();
            } else {
                $output->writeln("<error>Invalid database type</error>");
                return static::FAILURE;
            }

            $output->writeln("<info>Your {$databaseType} seeder has been created successfully!!!✅</info>");
            return static::SUCCESS;
        } else {
            $output->writeln("<error>Invalid database type</error>");
            return static::FAILURE;
        }
    }
}
        