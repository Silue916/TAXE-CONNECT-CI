<?php

namespace Database\Epaphrodites\config\getConnexion\etablishConnexion;

use Redis;

trait redisdb
{

    /**
     * Connexion Redis
     * @param integer $db
     * @return array|object
     */
    private function setRedisDBConnexion(
        int $db
    ):array|object
    {
        $dbPrefix = static::DB_DATABASE($db);
        $password = static::DB_PASSWORD($db);
        $redis = new Redis();

        try {
            // Try to connect to the Redis server
            $connected = $redis->connect(static::noDB_HOST($db), static::noDB_PORT($db));

            if (!$connected) {
                throw static::getError('Failed to connect to Redis server.');
            }

            // Authenticate with the Redis server
            $authenticated = $password ? $redis->auth($password) : true;

            if (!$authenticated) {
                throw static::getError('Failed to authenticate with Redis server.');
            }

            // Return the Redis connection
            return [ "db" => $dbPrefix , "connexion" => $redis];
            
        } catch (\Exception $e) {
            throw static::getError('Redis Connection Error: ' . $e->getMessage());
        }
    }

    /**
     * @param integer $db
     * @return array|object
     */
    public function RedisDB(
        int $db = 1,
        bool $state = false
    ):array|object
    {

        $this->ifExtensionExist('redis');
        return $this->setRedisDBConnexion($db);
    }
}