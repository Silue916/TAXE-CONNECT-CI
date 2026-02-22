<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class AddChatbotCommands extends Command
{

    protected function configure()
    {
        $this->setDescription('Add a new chatbot')
             ->setHelp('This command allows you to Add a new chatbot.')
             ->addArgument('name', InputArgument::REQUIRED, 'Add chatbot name)')
             ->addArgument('controller', InputArgument::OPTIONAL, 'Select the controller');
    }
}