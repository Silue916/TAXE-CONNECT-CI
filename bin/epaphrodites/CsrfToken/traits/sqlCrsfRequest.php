<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\CsrfToken\traits;

use DateTime;
use DateInterval;

trait sqlCrsfRequest
{

    /**
     * Update crsfToken life into database (For: mysl/sqlServer/postgres/sqlite)
     *
     * @param string $cookies
     * @return void
     */
    private function UpdateUserCrsfToken(
        ?string $cookies = null
    ): void{
        $this->table('secure')
            ->set(['token', 'createat'])
            ->where('auth')
            ->param([$cookies,  date("Y-m-d H:i:s"), md5(static::initNamespace()['session']->login())])
            ->UQuery();
    }

    /**
     * Update crsfToken life into database (For: oracle)
     *
     * @param string $cookies
     * @return void
     */
    private function UpdateOracleUserCrsfToken(
        ?string $cookies = null
    ): void{
        
        $this->table('secure')
            ->set(['token'])
            ->setDate(['createat'])
            ->where('auth')
            ->param([$cookies, date("Y-m-d H:i:s"), md5(static::initNamespace()['session']->login())])
            ->UQuery();
    }    

    /**
     * Add crsfToken life into database (For: mysl/sqlServer/postgres/sqlite)
     *
     * @param string $cookies
     * @return bool
     */
    private function CreateUserCrsfToken(
        ?string $cookies = null
    ): bool{

        $this->table('secure')
            ->insert('auth , token , createat')
            ->values("?, ?, ?")
            ->param([md5(static::initNamespace()['session']->login()), $cookies, date("Y-m-d H:i:s")])
            ->IQuery();

        return false;
    }

    /**
     * Add crsfToken life into database (For: oracle)
     *
     * @param string $cookies
     * @return bool
     */
    private function CreateOracleUserCrsfToken(?string $cookies = null): bool
    {

        $this->table('secure')
            ->insert('auth, token, createat')
            ->values("?, ?, TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')")
            ->param([md5(static::initNamespace()['session']->login()), $cookies, date("Y-m-d H:i:s")])
            ->IQuery();

        return false;
    }    

    /**
     *  Check token date (For: mysl/sqlServer/postgres/sqlite/oracle)
     * @return string|int
     */
    public function CheckUserCrsfToken(): string|int
    {
       
        $addDay = 1;
        
        $currentDate = new DateTime(date('Y-m-d'));
        
        $endOfDay = clone $currentDate;
        $endOfDay->add(new DateInterval("P{$addDay}D"));
        $endOfDay = $endOfDay->format('Y-m-d H:i:s');
        
        $startOfDay = clone $currentDate;
        $startOfDay->sub(new DateInterval("P{$addDay}D"));
        $startOfDay = $startOfDay->format('Y-m-d H:i:s');

        $crsfLogin = md5(static::initNamespace()['session']->login());

        $result = (_FIRST_DRIVER_ == "oracle") 
                ? $this->getOracleTokenLife($startOfDay, $endOfDay, $crsfLogin) 
                : $this->getSqlTokenLife($startOfDay, $endOfDay, $crsfLogin);

        return !empty($result) ? $result[0]['token'] : 0;
    }

    /**
     * Get csrf value
     *
     * @return string|int
     */
    public function secure(): string|int
    {
       
        $login = static::initNamespace()['session']->login();
        
        $login = $login !== null ? md5($login) : NULL;

        $result = $this->table('secure')
            ->where('auth')
            ->param([$login])
            ->SQuery();

        $result = static::initNamespace()['env']->dictKeyToLowers($result);

        return !empty($result) ? $result[0]['token'] : 0;
    }

    /**
     * Request to get crsfToken life (For: oracle)
     * 
     * @return array
     */    
    private function getOracleTokenLife($startOfDay, $endOfDay, $crsfLogin){

        $result = $this->table('secure')
                    ->betweenDate('createat')
                    ->and(['auth'])
                    ->param([$startOfDay, $endOfDay, $crsfLogin])
                    ->SQuery('token');

        return !empty($result) ? $result = static::initNamespace()['env']->dictKeyToLowers($result): $result;
    }

    /**
     * Request to get crsfToken life (For: mysl/sqlServer/postgres/sqlite)
     * 
     * @return array
     */
    private function getSqlTokenLife($startOfDay, $endOfDay, $crsfLogin){

        $result = $this->table('secure')
                    ->between('createat')
                    ->and(['auth'])
                    ->param([$startOfDay, $endOfDay, $crsfLogin])
                    ->SQuery('token');

        return $result;
    }    
}