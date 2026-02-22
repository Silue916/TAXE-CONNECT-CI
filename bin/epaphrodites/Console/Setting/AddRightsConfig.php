<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class AddRightsConfig extends Command
{

    protected function configure()
    {
        $this->setDescription('Add a new user right')
             ->setHelp('This command allows you to Add a new user right.')
             ->addArgument('moduleKey', InputArgument::REQUIRED, 'App Module key')
             ->addArgument('path', InputArgument::REQUIRED, 'Locate path of file')
             ->addArgument('libelle', InputArgument::REQUIRED, 'Libelle of right');
    }
}