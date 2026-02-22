<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class AddControllerPage extends Command
{

    protected function configure()
    {
        $this->setDescription('Add view function page')
             ->setHelp('This command allows you to Add View.')
             ->addArgument('controller', InputArgument::REQUIRED, 'Select the controller)')
             ->addArgument('path', InputArgument::REQUIRED, 'Select the path');
    }
}