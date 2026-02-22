<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class UsersConfig extends Command
{

    protected function configure()
    {
        $this
            ->setDescription('Update user password and typ')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user')
            ->addArgument('userGroup', InputArgument::OPTIONAL, 'User group');
    }
}