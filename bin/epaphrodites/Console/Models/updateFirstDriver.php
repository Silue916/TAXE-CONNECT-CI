<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\AddFirstDriver;
use Epaphrodites\epaphrodites\Console\Setting\OutputDirectory;
use Epaphrodites\epaphrodites\Console\Stubs\stubsUpdateFirstDrivers;

class updateFirstDriver extends AddFirstDriver{

    private static array $allowedMethods = ['mysql', 'pgsql', 'sqlserver', 'sqlite', 'mongodb', 'redis'];

    /**
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
    */
    protected function execute( 
        InputInterface $input, 
        OutputInterface $output
    ){
        # Get console arguments
        $driver = _FIRST_DRIVER_;
        $checkDriver = in_array($driver , static::$allowedMethods);

        $crsfSecure = OutputDirectory::Files('crsfsecure') . '/csrf_secure.php';
        $validateToken = OutputDirectory::Files('crsfsecure') . '/validate_token.php';
        $startSession = OutputDirectory::Files('startsession') . '/StartUsersSession.php';

        if($checkDriver===true){

            (new stubsUpdateFirstDrivers)->generateRequest($crsfSecure, $startSession , $validateToken , $driver);
            $output->writeln("<info>{$driver} driver has created successfully!!!✅</info>");
            return static::SUCCESS;            

        }else{
            $output->writeln("<error>Sorry this {$driver} driver don't exist❌</error>");
            return static::FAILURE;
        }
    }
}