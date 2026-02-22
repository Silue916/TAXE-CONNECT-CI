<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\CsrfToken\encryptToken;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;

class encryptTokenValue extends epaphroditeClass
{

    private string $token = '';

    /**
     * @return mixed
     */
    private function TokenConnected()
    {
        return isset($_COOKIE[static::class('msg')->answers('token_name')]) ? $_COOKIE[static::class('msg')->answers('token_name')] : NULL;
    }
    
    /**
     * @param int $length
     * @return mixed 
     */
    private function generateTokenValues(int $length = 32): mixed
    {
        if ($this->TokenConnected() === null) {

            $token = '';

            $alphabet = static::$Guardlatters;
    
            $alphabetLength = strlen($alphabet);
            $bytes = random_bytes($length);
    
            for ($i = 0; $i < $length; $i++) {
                $token .= $alphabet[ord($bytes[$i]) % $alphabetLength];
            }
    
            $this->token = $token;
        } else {
            $this->token = $this->TokenConnected();
        }
    
        return $this->token;
    }
    
    /**
     * @param int $length
     * @return mixed 
     */
    protected function GenerateurTokenValues(int $length = 32): mixed
    {
        return $this->generateTokenValues($length);
    }
}