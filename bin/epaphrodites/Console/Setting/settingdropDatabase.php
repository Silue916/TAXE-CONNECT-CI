<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
        
class settingdropDatabase extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Command to drop a database')
                ->setHelp('This to help you to drop a database.')
                ->addArgument('database', InputArgument::REQUIRED, 'Your database name')
                ->addArgument('order', InputArgument::REQUIRED, 'Your database order config');
    }
}        
        