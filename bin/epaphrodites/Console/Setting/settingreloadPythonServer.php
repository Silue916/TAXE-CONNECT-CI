<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
        
class settingreloadPythonServer extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Start or reload python server')
                ->setHelp('This is help.')
                ->addOption('reload', 'r', InputOption::VALUE_NONE, 'Reload python server')
                ->addOption('kill', 'k', InputOption::VALUE_NONE, 'Kill python server')
                ->addOption('start', 's', InputOption::VALUE_NONE, 'Start python server');
    }
}        
        