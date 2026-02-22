<?php

namespace Epaphrodites\Epaphrodites\Console\Stubs;

class StubsUpdateFirstDrivers
{
    /**
     * Generates the request based on provided parameters.
     *
     * @param string $csrfSecure    The CSRF secure file path
     * @param string $startSession  The start session file path
     * @param string $validateToken  The validate token file path
     * @param string $driver        The driver name
     * @return void
     */
    public function generateRequest(string $csrfSecure, string $startSession, string $validateToken, string $driver): void
    {
        $currentCsrfSecureContent = file_get_contents($csrfSecure);
        $currentStartSessionContent = file_get_contents($startSession);
        $currentValidateTokenContent = file_get_contents($validateToken);
        
        $keySecureFileContent = preg_replace(
             '/private function getTokenCrsf\(\?string \$key=null\):bool\|string\|null\s*{[^}].*?\}\n/s',
            $this->csrfSecureFunction($driver),
            $currentCsrfSecureContent
        );

        $keySessionFileContent = preg_replace(
            '/\/\*\*.*key\(\):string\s*{[^}]*}.*?\}\n/s',
            $this->sessionKeyFunction($driver),
            $currentStartSessionContent
        );
        
        $keyValidateTokenFileContent = preg_replace(
            '/\/\*\*.*checkMainDriverRequest\(\):string\s*{[^}]*}.*?\}\n/s',
            $this->validateToken($driver),
            $currentValidateTokenContent
        );
        
        file_put_contents($csrfSecure, $keySecureFileContent);
        file_put_contents($startSession, $keySessionFileContent);
        file_put_contents($validateToken, $keyValidateTokenFileContent);
    }

    /**
     * Finds the secure request based on the driver.
     *
     * @param string $driver The driver name
     * @return string
     */
    private function findSecureRequest(string $driver): string
    {
        return match ($driver) {
            'mongo' => "empty(\$this->noSqlSecure()) ? \$this->CreateUserCrsfToken(\$key) : \$this->UpdateUserCrsfToken(\$key)",
            'redis' => "empty(\$this->noSqlRedisSecure()) ? \$this->noSqlRedisCreateUserCrsfToken(\$key) : \$this->noSqlRedisUpdateUserCrsfToken(\$key)",
            'mysql', 'pgsql', 'sqlite', 'sqlserver' => "empty(\$this->secure()) ? \$this->CreateUserCrsfToken(\$key) : \$this->UpdateUserCrsfToken(\$key)",
        };
    }

    /**
     * Finds the start session request based on the driver.
     *
     * @param string $driver The driver name
     * @return string
     */
    private function findStartSessionRequest(string $driver): string
    {
        return match ($driver) {
            'mongo' => "!empty(static::class('secure')->noSqlCheckUserCrsfToken()) ? static::class('secure')->noSqlCheckUserCrsfToken() : \$_COOKIE[static::class('msg')->answers('token_name')]",
            'redis' => "!empty(static::class('secure')->noSqlRedisCheckUserCrsfToken()) ? static::class('secure')->noSqlRedisCheckUserCrsfToken() : \$_COOKIE[static::class('msg')->answers('token_name')]",
            'mysql', 'pgsql', 'sqlite', 'sqlserver' => "!empty(static::class('secure')->CheckUserCrsfToken()) ? static::class('secure')->CheckUserCrsfToken() : \$_COOKIE[static::class('msg')->answers('token_name')]",
        };
    }
    
    private function findValidateToken(string $driver):string
    {
        return match ($driver) {
            'mongo' => "\$this->secure->noSqlSecure()",
            'redis' => "\$this->secure->noSqlRedisSecure()",
            'mysql', 'pgsql', 'sqlite', 'sqlserver' => "\$this->secure->secure()",
      };
    }
    
    /**
     * Generates session key function content based on the driver.
     *
     * @param string $driver The driver name
     * @return string
     */
    private function sessionKeyFunction(string $driver): string
    {
        return <<<PHP
        /**
         * Current cookies value
        */        
        private function key():string {
            return {$this->findStartSessionRequest($driver)};
          }\n
        PHP; 
        
    }

    /**
     * Generates CSRF secure function content based on the driver.
     *
     * @param string $driver The driver name
     * @return string
     */
    private function csrfSecureFunction(string $driver): string
    {
        return <<<PHP
        private function getTokenCrsf(?string \$key=null):bool|string|null{

            return {$this->findSecureRequest($driver)};
            }\n
        PHP;         
    }  
    
     /**
     * Generates session key function content based on the driver.
     *
     * @param string $driver The driver name
     * @return string
     */
    private function validateToken(string $driver): string
    {
        return <<<PHP
        /**
            * @return string
            */        
            private function checkMainDriverRequest():string{

            return {$this->findValidateToken($driver)};
            }\n
        PHP;        
    }   
}
