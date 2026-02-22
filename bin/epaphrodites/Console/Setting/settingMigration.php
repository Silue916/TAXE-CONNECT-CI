<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
        
class settingMigration extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Command to make database migration')
                ->setHelp('Add type of migration.')
                ->addArgument('type', InputArgument::REQUIRED, 'Set magration type');
    }
}        
        