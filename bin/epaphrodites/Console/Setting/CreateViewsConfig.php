<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class CreateViewsConfig extends Command
{

    protected function configure()
    {
        $this->setDescription('Create a front views')
             ->setHelp('This command allows you to create a new view.')
             ->addArgument('directory', InputArgument::REQUIRED, 'File directory')
             ->addArgument('viewName', InputArgument::REQUIRED, 'Name of view');
    }
}