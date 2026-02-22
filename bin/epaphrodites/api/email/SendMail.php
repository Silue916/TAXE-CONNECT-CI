<?php

namespace Epaphrodites\epaphrodites\api\email;

use Epaphrodites\epaphrodites\api\email\ini\config;
use Epaphrodites\epaphrodites\api\email\ini\usingMethod;

class SendMail extends config
{

    use usingMethod;
    
    /**
     * Send mail
     * 
     * @param array $contacts
     * @param string $msgHeader
     * @param string $msgContent
     * @param string|null $file
     */
    public function sendEmail(
        array $contacts, 
        string $msgHeader, 
        string $msgContent, 
        string|null $file = null
    ) 
    {
        if(__EMAIL_METHOD__ == 'python'){

            return $this->sendEmailByPython($contacts, $msgHeader, $msgContent, $file);
        }else{

            return $this->sendEmailByPhp($contacts, $msgHeader, $msgContent, $file);
        }
    }
}
