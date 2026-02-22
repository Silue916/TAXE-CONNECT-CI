<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Epaphrodites\epaphrodites\Console\Setting\AddModulesConfig;
use Epaphrodites\epaphrodites\Console\Stubs\AddRightsModulesStub;
use Symfony\Component\Console\Input\InputInterface;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\EpaphMozart\ModulesConfig\Lists\GetModulesList;

class AddRightsModules extends AddModulesConfig
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
        $key = $input->getArgument('key');
        $libelle = str_replace( '_' , ' ' , $input->getArgument('libelle'));
        
        # Get module list and verify key
        $list = GetModulesList::GetModuleList();
        $IfKeyExist = array_key_exists($key,$list);

        # Select oupout file
        $GetFilesNames = OutputDirectory::Files('modulelist') . '.php';

        if (file_exists($GetFilesNames)===true&&$IfKeyExist===false){

            AddRightsModulesStub::generate($GetFilesNames, $key , strtoupper($libelle));
            $output->writeln("<info>The Module has been successfully created!!!✅</info>");

            return static::SUCCESS;
        }else{

            $output->writeln("<error>Sorry, the module Key '{$key}' of '{$libelle}' already exists❌</error>");
            return static::FAILURE;
        }
    }
}