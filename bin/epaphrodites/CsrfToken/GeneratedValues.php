<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\CsrfToken;

use Epaphrodites\epaphrodites\CsrfToken\encryptToken\encryptTokenValue;

class GeneratedValues extends encryptTokenValue
{
    
    /**
     * Get user token value
     * 
     * @return string
     */
    public function getvalue(): string
    {
        return $this->GenerateurTokenValues(70);
    }
}
