<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Stubs\ControllerStub;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Epaphrodites\epaphrodites\Console\Setting\ControllersConfig;

class CreateControllers extends ControllersConfig
{
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        $name = $input->getArgument('name');
        $fileName = OutputDirectory::Files('controlleur') . "/{$name}.php";
        $controllerMaps = OutputDirectory::Files('controllermaps') . "/controllerMap.php";
        ControllerStub::GenerateControlleurs($fileName, $name , $controllerMaps);
        $output->writeln("<info>The controller {$name} has been successfully created!!!✅</info>");

        return static::SUCCESS;
    }
}