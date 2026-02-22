<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

class AddServerConfig extends Command
{

    protected function configure()
    {
        $this->setDescription('Run server')
             ->setHelp('This command allows you to Run server.')
             ->addOption('port' , null , InputOption::VALUE_REQUIRED, 'Port');
    }
}