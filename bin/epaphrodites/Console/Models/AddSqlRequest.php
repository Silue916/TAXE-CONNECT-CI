<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Epaphrodites\epaphrodites\Console\Stubs\AddSqlRequestStub;
use Symfony\Component\Console\Input\InputInterface;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\AddSqlConfig;

class AddSqlRequest extends AddSqlConfig
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
        $directory = $input->getArgument('directory');
        $type = $input->getArgument('typeRequest');
        $file = $input->getArgument('fileLocate');
        $name = $input->getArgument('functionName');

        if(!empty(OutputDirectory::Files($directory))){

            $FileName = OutputDirectory::Files($directory) . "/{$file}.php";
           
            if(file_exists($FileName)===true){
                AddSqlRequestStub::generate($FileName, $name , $type);
                $output->writeln("<info>Your request {$file} has been created successfully!!!✅</info>");
                return static::SUCCESS;
            }else{
                $output->writeln("<error>Sorry this file {$file} don't exist in {$directory} directory❌</error>");
                return static::FAILURE;  
            }
        }else{
            $output->writeln("<error>Sorry {$directory} directory don't exist❌</error>");
            return static::FAILURE;
        }
    }
}