<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class AddPythonFilesCommand extends Command
{

    protected function configure()
    {
        $this->setDescription('Add python files')
             ->setHelp('This command allows you to Add View pas.')
             ->addArgument('file', InputArgument::REQUIRED, 'python file name)');
    }
}