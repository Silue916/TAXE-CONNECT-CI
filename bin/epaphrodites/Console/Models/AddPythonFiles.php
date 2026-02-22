<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Stubs\pythonStubs;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Epaphrodites\epaphrodites\Console\Setting\AddPythonFilesCommand;

class AddPythonFiles extends AddPythonFilesCommand{


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
    */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        # Get console arguments
        $fileName = str_replace( '@' , '/' , $input->getArgument('file'));

        $fileInit = $fileName;

        (string) $functionName = $fileName;
        $getFunction = explode('/', $fileName);
        if(count($getFunction)==2){
           (string) $functionName = $getFunction[1];
        }

        $FileName = OutputDirectory::Files('python') . "/{$fileName}.py";

        if(file_exists($FileName)===false){

            pythonStubs::generate($FileName , $functionName , $fileInit);
            $output->writeln("<info>Your python file {$fileName} has been generated successfully!!!✅</info>");
            return static::SUCCESS;            

        }else{
            $output->writeln("<error>Sorry this file '{$fileName}' exist already</error>");
            return static::FAILURE;
        }
    }
}