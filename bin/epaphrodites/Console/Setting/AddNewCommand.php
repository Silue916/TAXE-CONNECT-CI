<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class AddNewCommand extends Command
{

    protected function configure()
    {
        $this->setDescription('Add new command')
             ->setHelp('This command allows you to Add command.')
             ->addArgument('cmd', InputArgument::REQUIRED, 'add new command)')
             ->addArgument('file', InputArgument::REQUIRED, 'add file name');
    }
}