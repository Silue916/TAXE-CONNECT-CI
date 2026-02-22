<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\AddControllerPage;
use Epaphrodites\epaphrodites\Console\Stubs\StubsControllerFunction;

class AddControllerViewPages extends AddControllerPage{

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
    */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        # Get console arguments
        $controller = $input->getArgument('controller');
        $name = preg_replace('/[-*+\/@#$%^&=~`!?;:.,|\\<>[\]{}]/', '_', $input->getArgument('path'));

        $FileName = OutputDirectory::Files('controlleur') . "/{$controller}.php";

        if(file_exists($FileName)===true){

            StubsControllerFunction::generate($FileName, $name);
            $output->writeln("<info>Your function path {$name} has been generated successfully!!!✅</info>");
            return static::SUCCESS;            

        }else{
            $output->writeln("<error>Sorry this controller '{$controller}' don't exist❌</error>");
            return static::FAILURE;
        }
    }
}