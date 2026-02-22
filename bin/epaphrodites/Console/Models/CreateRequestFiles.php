<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Epaphrodites\epaphrodites\Console\Stubs\RequestFilesStub;
use Symfony\Component\Console\Input\InputInterface;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\RequestFileConfig;

class CreateRequestFiles extends RequestFileConfig
{

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
    */    
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){

        $requestFileName = $input->getArgument('name');
        $requestFileDirectory = $input->getArgument('file');

        $FileName = OutputDirectory::Files('request') . "/mainRequest/{$requestFileDirectory}/{$requestFileName}.php";

        if(is_dir(OutputDirectory::Files('request'))."/mainRequest/{$requestFileDirectory}"!==true){
            
            if(file_exists($FileName)===false){
 
                RequestFilesStub::generate($FileName, $requestFileName , $requestFileDirectory);
                $output->writeln("<info>The request file '{$requestFileName}' has been successfully created!!!</info>");
        
                return static::SUCCESS;
            }else{
                $output->writeln("<error>Sorry this request file '{$requestFileName}' already exists.</error>");
                return static::FAILURE;
            }
        }else{
            $output->writeln("<error>Sorry this request directory '{$requestFileDirectory}' don't already.</error>");
            return static::FAILURE;
        }
    }

}