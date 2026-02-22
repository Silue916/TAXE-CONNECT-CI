<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\CsrfToken;

use Epaphrodites\database\query\Builders;
use Epaphrodites\epaphrodites\CsrfToken\traits\sqlCrsfRequest;
use Epaphrodites\epaphrodites\CsrfToken\traits\noSqlCrsfRequest;
use Epaphrodites\epaphrodites\CsrfToken\contracts\validateTokenInterface;

class csrf_secure extends Builders implements validateTokenInterface
{
    public $getCrsf;

    use sqlCrsfRequest, noSqlCrsfRequest;

    /**
     * Setter function csrf
     * @param string $key
     * @return bool|string|null
     */
    public function get_csrf(?string $key = null){

        $this->getCrsf = $this->getTokenCrsf($key);

        return $this->getCrsf;
    }

    /**
     * Get rooting csrf 
     * @param string $key
     * @return bool|string|null
     */
    private function getTokenCrsf(?string $key=null):bool|string|null{
        
        return match (_FIRST_DRIVER_) {

            'redis' => empty($this->noSqlRedisSecure()) ? $this->noSqlRedisCreateUserCrsfToken($key) : $this->noSqlRedisUpdateUserCrsfToken($key),
            'mongodb' => empty($this->noSqlSecure()) ? $this->noSqlCreateUserCrsfToken($key) : $this->noSqlUpdateUserCrsfToken($key),
            'oracle' => empty($this->secure()) ? $this->CreateOracleUserCrsfToken($key) : $this->UpdateOracleUserCrsfToken($key),

            default => empty($this->secure()) ? $this->CreateUserCrsfToken($key) : $this->UpdateUserCrsfToken($key)
        };
    }
    
}
