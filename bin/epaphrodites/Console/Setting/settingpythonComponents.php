<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
        
class settingpythonComponents extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Install python Components')
                ->setHelp('This is help.')
                ->addArgument('install', InputArgument::OPTIONAL, 'installation');
    }
}        
        