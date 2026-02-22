<?php

namespace Epaphrodites\epaphrodites\Console\commands;

use Epaphrodites\epaphrodites\Console\Models\createNewDatabase;

class CommandAddDatabase extends createNewDatabase{

    protected static $defaultName = 'create:db';
}