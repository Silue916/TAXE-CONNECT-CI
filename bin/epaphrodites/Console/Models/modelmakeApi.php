<?php

namespace Epaphrodites\epaphrodites\Console\Models;
        
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\settingmakeApi;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Epaphrodites\epaphrodites\Console\Stubs\StubsControllerFunction;
        
class modelmakeApi extends settingmakeApi{
        
        
    /**
    * @param \Symfony\Component\Console\Input\InputInterface $input
    * @param \Symfony\Component\Console\Output\OutputInterface $output
    */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        # Get console arguments
        $apiName = preg_replace('/[-*+\/@#$%^&=~`!?;:.,|\\<>[\]{}]/', '_', $input->getArgument('name'));
        $controllerName = OutputDirectory::Files('controlleur') . "/api.php";

         if(file_exists($controllerName)===true){

            StubsControllerFunction::generate($controllerName, $apiName, true);
            $output->writeln("<info>Your API {$apiName} has been generated successfully!!!✅</info>");
            return static::SUCCESS;            

         }else{
            $output->writeln("<error>Sorry this controller '{$controllerName}' don't exist❌</error>");
            return static::FAILURE;
        }
    }
}
        