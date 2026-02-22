<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Stubs\newCommandSubs;
use Epaphrodites\epaphrodites\Console\Setting\AddNewCommand;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;

class AddCommands extends AddNewCommand{


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
    */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        # Get console arguments
        $command = $input->getArgument('cmd');
        $fileName = $input->getArgument('file');

        $modelFile = OutputDirectory::Files('console') . "/Models/model{$fileName}.php";
        $commadFile = OutputDirectory::Files('console') . "/Commands/command{$fileName}.php";
        $settingFile = OutputDirectory::Files('console') . "/Setting/setting{$fileName}.php";

        if(file_exists($modelFile)===false&&file_exists($commadFile)===false&&file_exists($settingFile)===false){

            newCommandSubs::generate($commadFile , $modelFile , $settingFile ,$command , $fileName);
            $output->writeln("<info>Your command {$command} has been generated successfully!!!✅</info>");
            return static::SUCCESS;            

        }else{
            $output->writeln("<error>Sorry this command '{$command}' exist already❌</error>");
            return static::FAILURE;
        }
    }
}