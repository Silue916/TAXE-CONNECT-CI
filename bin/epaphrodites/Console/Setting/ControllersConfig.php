<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class ControllersConfig extends Command
{

    protected function configure()
    {
        $this->setDescription('Create a new controller')
             ->setHelp('This command allows you to create a new controller.')
             ->addArgument('name', InputArgument::REQUIRED, 'Name of the controller');
    }
}