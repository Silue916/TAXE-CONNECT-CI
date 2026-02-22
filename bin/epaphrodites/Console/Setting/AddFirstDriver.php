<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class AddFirstDriver extends Command
{

    protected function configure()
    {
        $this->setDescription('Create the first driver')
             ->setHelp('This command allows you to Update first driver');
    }
}