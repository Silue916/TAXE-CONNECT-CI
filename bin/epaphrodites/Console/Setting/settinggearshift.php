<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
        
class settinggearshift extends Command
{
    protected function configure()
    {
        $this->setDescription('Run the database migrations')
             ->setHelp('This is help.')
             ->addOption('u', 'u', InputOption::VALUE_NONE, 'Up migration')
             ->addOption('d', 'd', InputOption::VALUE_NONE, 'Down migration');
    }
    
}        
        