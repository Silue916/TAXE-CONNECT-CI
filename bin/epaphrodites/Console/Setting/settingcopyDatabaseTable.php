<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
        
class settingcopyDatabaseTable extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Run the database table copy')
             ->setHelp('This is help.')
             ->addOption('c', 'c', InputOption::VALUE_NONE, 'Copy databse to another');
    }
}        
        