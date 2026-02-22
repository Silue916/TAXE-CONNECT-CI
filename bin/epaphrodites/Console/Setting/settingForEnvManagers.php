<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
        
class settingForEnvManagers extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Generate or load environment variables.')
             ->setHelp('Use this command to generate or load environment variables from the configuration file.')
             ->addOption('generate', 'g', InputOption::VALUE_NONE, 'Generate environment variables from Config.ini')
             ->addOption('load', 'l', InputOption::VALUE_NONE, 'Load environment variables from the .env file');
    }
}        
        