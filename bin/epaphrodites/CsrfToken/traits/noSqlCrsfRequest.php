<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\CsrfToken\traits;

use DateTime;
use DateInterval;

trait noSqlCrsfRequest
{

    /**
     * Get csrf value
     * @return string|int
     */
    public function noSqlSecure(): string|int
    {

        $login = static::initNamespace()['session']->login();
        
        $login = $login !== null ? md5($login) : NULL;

        $documents = [];

        $result = $this->db(1)
            ->selectCollection('secure')
            ->find(['auth' => $login]);

        foreach ($result as $document) {
            $documents[] = $document;
        }
        
        return !empty($documents) ? $documents[0]['token'] : 0;
    }

    /**
     * Get csrf value
     * @return string|int
     */
    public function noSqlRedisSecure(): string|int
    {

        $result = $this->key('secure')
            ->index(md5(static::initNamespace()['session']->login()))
            ->redisGet();

        return !empty($result) ? $result[0]['token'] : 0;
    }

    /**
     * Insert token into database
     *
     * @param string|null $cookies
     * @return bool
     */
    private function noSqlCreateUserCrsfToken(?string $cookies = null): bool
    {

        $document = [
            'auth' => md5(static::initNamespace()['session']->login()),
            'token' => $cookies,
            'createat' => date("Y-m-d H:i:s"),
        ];

        $this->db(1)->selectCollection('secure')->insertOne($document);

        return false;
    }

    /**
     * Insert token into database
     *
     * @param string|null $cookies
     * @return bool
     */
    private function noSqlRedisCreateUserCrsfToken(?string $cookies = null): bool
    {

        $datas = [
            'auth' => md5(static::initNamespace()['session']->login()),
            'token' => $cookies,
            'createat' => date("Y-m-d H:i:s"),
        ];

        $this->key('secure')->id('_id')->index(md5(static::initNamespace()['session']->login()))->param($datas)->addToRedis();

        return false;
    }

    /**
     * Update token into database
     *
     * @param string $cookies
     * @return void
     */
    private function noSqlUpdateUserCrsfToken(?string $cookies = null): void
    {

        $filter = ['auth' => md5(static::initNamespace()['session']->login())];
        
        $update = [
            '$set' => [
                'token' => $cookies,
                'createat' => date("Y-m-d H:i:s"),
            ],
        ];

        $this->db(1)->selectCollection('secure')->updateOne($filter, $update);
    }

    /**
     * Update token into database
     *
     * @param string $cookies
     * @return void
     */
    private function noSqlRedisUpdateUserCrsfToken(?string $cookies = null): void
    {

        $index = md5(static::initNamespace()['session']->login());

        $datas =
            [
                'token' => $cookies,
                'createat' => date("Y-m-d H:i:s"),
            ];

        $this->key('secure')->index($index)->rset($datas)->updRedis();
    }

    /**
     * Check token date
     * @return string|int
     */
    public function noSqlCheckUserCrsfToken(): string|int
    {

        $addDay = 1;
        $currentDate = new DateTime(date('Y-m-d'));

        $endOfDay = clone $currentDate;
        $endOfDay->add(new DateInterval("P{$addDay}D"));
        $endOfDay = $endOfDay->format('Y-m-d H:i:s');

        $startOfDay = clone $currentDate;
        $startOfDay->sub(new DateInterval("P{$addDay}D"));
        $startOfDay = $startOfDay->format('Y-m-d H:i:s');

        $filter = [
            'createat' => [
                '$gte' => $startOfDay,
                '$lte' => $endOfDay,
            ],
            'auth' => md5(static::initNamespace()['session']->login()),
        ];

        $result = $this->db(1)->selectCollection('secure')->find($filter);

        foreach ($result as $document) {
            $documents[] = $document;
        }

        return !empty($documents) ? $documents[0]['token'] : 0;
    }

    /**
     * Check token date
     * @return string|int
     */
    public function noSqlRedisCheckUserCrsfToken(): string|int
    {

        $addDay = 1;
        $verifyResult = 0;
        $currentDate = new DateTime(date('Y-m-d'));

        $endOfDay = clone $currentDate;
        $endOfDay->add(new DateInterval("P{$addDay}D"));
        $endOfDay = $endOfDay->format('Y-m-d H:i:s');

        $startOfDay = clone $currentDate;
        $startOfDay->sub(new DateInterval("P{$addDay}D"));
        $startOfDay = $startOfDay->format('Y-m-d H:i:s');

        $result = $this->key('secure')
            ->index(md5(static::initNamespace()['session']->login()))
            ->redisGet();

        if (!empty($result)) {
            $mainDay = $result[0]['token'];

            $verifyResult = match (true) {
                ($mainDay >= $startOfDay && $mainDay <= $endOfDay) => $mainDay,
                default => 0
            };
        }

        return $verifyResult;
    }
}