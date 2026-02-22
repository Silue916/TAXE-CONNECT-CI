<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class AddNewDatabase extends Command
{

    protected function configure()
    {
        $this->setDescription('Add a new database')
             ->setHelp('This command allows you to Add a new Database')
             ->addArgument('database', InputArgument::REQUIRED, 'Select database name to create')
             ->addArgument('order', InputArgument::OPTIONAL, 'Select database param order');
    }
}