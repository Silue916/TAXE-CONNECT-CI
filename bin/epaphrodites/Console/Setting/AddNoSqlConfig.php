<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class AddNoSqlConfig extends Command
{

    protected function configure()
    {
        $this->setDescription('Add noSQL request')
             ->setHelp('This command allows you to Add noSQL request.')
             ->addArgument('directory', InputArgument::REQUIRED, 'Request directory (insert/select/update/delete)')
             ->addArgument('typeRequest', InputArgument::REQUIRED, 'Request type (insert/select/count/update/delete)')
             ->addArgument('fileLocate', InputArgument::REQUIRED, 'Locate request file')
             ->addArgument('functionName', InputArgument::REQUIRED, 'Name of function');
    }
}