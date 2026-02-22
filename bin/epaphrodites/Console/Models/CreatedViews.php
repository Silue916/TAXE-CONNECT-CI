<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Stubs\AllViewsStub;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Epaphrodites\epaphrodites\Console\Setting\CreateViewsConfig;

class CreatedViews extends CreateViewsConfig
{

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
    */    
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        $directory = $input->getArgument('directory');
        $initFileNames = $input->getArgument('viewName');

        $directory = explode('@', $directory);
        $directory = implode('/', $directory);

        if(is_dir(OutputDirectory::Files("views")."/$directory")!==false){
            
            $fileName = OutputDirectory::Files("views") . "/$directory/{$initFileNames}" . _MAIN_EXTENSION_ . _FRONT_;
            
            if(file_exists($fileName)===false){

                AllViewsStub::generate($fileName, $fileName , $directory);
                $output->writeln("<info>The view file {$initFileNames} has been successfully created!!!✅</info>");
                return static::SUCCESS;
            }else{
                $output->writeln("<error>Sorry, this view file {$initFileNames} does not exist.❌</error>");
                return static::FAILURE;
            }
        }else{
            $output->writeln("<error>Sorry, this view directory {$directory} does not exist.❌</error>");
            return static::FAILURE;
        }
    }
}