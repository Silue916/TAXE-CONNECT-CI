<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\CsrfToken\traits;

trait buildOutput
{

    /**
     * Build token crsf input field
     * @return void 
     * */    
    private function buildInputField():void
    {

        echo "<input type='hidden' name='".CSRF_FIELD_NAME."' value='". htmlspecialchars($this->getValue(), ENT_QUOTES, 'UTF-8')."' required />";
    }  
    
    /**
     * @return void
     */
    private function buildMeta():void
    {
        echo "<meta name='".CSRF_FIELD_NAME."' content='". htmlspecialchars($this->getValue(), ENT_QUOTES, 'UTF-8')."'>";
    }     

}