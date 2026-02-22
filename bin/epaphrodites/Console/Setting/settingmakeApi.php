<?php

namespace Epaphrodites\epaphrodites\Console\Setting;
        
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
        
class settingmakeApi extends Command
{
        
    protected function configure()
    {
        $this->setDescription('Make a new API')
                ->setHelp('This is help.')
                ->addArgument('name', InputArgument::REQUIRED, 'Your API name');
    }
}        
        