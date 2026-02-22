<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
        
class settingseeding extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Command to make a new seeding')
                ->setHelp('Make a seeder.')
                ->addArgument('type', InputArgument::REQUIRED, 'Database type');
    }
}        
        