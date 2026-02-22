<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\UsersConfig;
use Epaphrodites\database\requests\mainRequest\update\update as Update;

class UpdateUsers extends UsersConfig
{

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */    
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $UserGroup = $input->getArgument('userGroup');
        $result = (new Update)->ConsoleUpdateUsers( $username , $password , $UserGroup );

        if($result===true){
            $output->writeln("<info>Changes to the user {$username} have been successfully made!!!✅</info>");
            return static::SUCCESS;
        }else{
            $output->writeln("<error>Error during processing. Please verify that the user {$username} already exists❌</error>");
            return static::FAILURE;
        }
    }
}