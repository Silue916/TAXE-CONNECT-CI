<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\CsrfToken;

use Epaphrodites\epaphrodites\CsrfToken\traits\buildOutput;
use Epaphrodites\epaphrodites\CsrfToken\GeneratedValues;


class token_csrf extends GeneratedValues{   

    use buildOutput;

    /**
     * csrf verification process...
     * @return bool
     */
    private function process():bool{
       
        return static::initConfig()['crsf']->isValidToken();
    }

    /**
     * Forcing token process...
     * @return bool
     */
    private function forcingProcess():bool{
       
        return static::initConfig()['crsf']->forcingValidToken();
    }    

    /**
     * Get Token csrf input field
     * @return void 
     * */    
    public function input_field():void{

        $this->buildInputField();
    }  

    /**
     * @return void
     */
    public function xCrsfToken():void{

        $this->buildMeta();
    }

    /**
     * Check if CSRF token exists and is valid
     * @return bool
     */
    public function tocsrf():bool{

        return (isset($_POST[CSRF_FIELD_NAME])||isset($_GET[CSRF_FIELD_NAME])) ? $this->process() : true;
    }

    /**
     * Check if CSRF token exists and is valid
     * @return bool
     */    
    public function toForceCrsf():bool{

        return $this->forcingProcess();
    }
}