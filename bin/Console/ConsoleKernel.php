<?php

namespace Epaphrodites\Console;

class ConsoleKernel
{

    /**
     * Console commades list
     * @return array
    */
    public function GetConsolesCommands():array
    {
        return [
            new \Epaphrodites\epaphrodites\Console\commands\CommandsUsers,
            new \Epaphrodites\epaphrodites\Console\commands\commandmakeApi,
            new \Epaphrodites\epaphrodites\Console\commands\commandseeding,
            new \Epaphrodites\epaphrodites\Console\commands\commandgearshift,
            new \Epaphrodites\epaphrodites\Console\commands\commandMigration,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddPython,
            new \Epaphrodites\epaphrodites\Console\commands\CommandRunServer,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddRights,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddCommand,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddModules,
            new \Epaphrodites\epaphrodites\Console\commands\CommandCreatFront,
            new \Epaphrodites\epaphrodites\Console\commands\CommandController,
            new \Epaphrodites\epaphrodites\Console\commands\CommandUpdateUser,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddDatabase,
            new \Epaphrodites\epaphrodites\Console\commands\commanddropDatabase,
            new \Epaphrodites\epaphrodites\Console\commands\CommandFirstDrivers,
            new \Epaphrodites\epaphrodites\Console\commands\CommandRequestFiles,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddSqlRequest,
            new \Epaphrodites\epaphrodites\Console\commands\commandForEnvManagers,
            new \Epaphrodites\epaphrodites\Console\commands\commandaddCExtensions,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddNoSqlRequest,
            new \Epaphrodites\epaphrodites\Console\commands\commandpythonComponents,
            new \Epaphrodites\epaphrodites\Console\commands\commandcopyDatabaseTable,
            new \Epaphrodites\epaphrodites\Console\commands\commandreloadPythonServer,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddChatbotFunction,
            new \Epaphrodites\epaphrodites\Console\commands\CommandAddControllerFunction,
        ];
    }    
}