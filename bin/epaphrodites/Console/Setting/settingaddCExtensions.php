<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
        
class settingaddCExtensions extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Add your own c native extensions')
                ->setHelp('This is help.')
                ->addArgument('name', InputArgument::REQUIRED, 'Your c extension name');
    }
}        
        