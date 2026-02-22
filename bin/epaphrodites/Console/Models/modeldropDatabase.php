<?php

namespace Epaphrodites\epaphrodites\Console\Models;
        
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\database\config\process\checkDatabase;
use Epaphrodites\epaphrodites\Console\Setting\settingdropDatabase;
        
class modeldropDatabase extends settingdropDatabase{
        
        
    /**
    * @param \Symfony\Component\Console\Input\InputInterface $input
    * @param \Symfony\Component\Console\Output\OutputInterface $output
    */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        # Get console arguments
        $database = $input->getArgument('database');
        $order = $input->getArgument('order') ? $input->getArgument('order') : 1;

        $result = (new checkDatabase)->etablishConnect($database , $order , false);

        if( $result == true ){

            $output->writeln("<info>Your database {$database} has been deleted successfully in configuration {$order}!!!✅</info>");
            return static::SUCCESS;            

        }else{
            $output->writeln("<error>Please check your configuration or the existence of this database {$database} in configuration {$order} ❌</error>");
            return static::FAILURE;
        }
    }
}
        