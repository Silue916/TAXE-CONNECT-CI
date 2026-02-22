<?php

namespace Epaphrodites\epaphrodites\api\email\ini;

use Exception;

trait usingMethod{

    /**
     * Send Email using PHP
     * 
     * @param array $contacts
     * @param string $msgHeader
     * @param string $msgContent
     * @param string|null $file
     * @throws \Exception
     */
    private function sendEmailByPhp(
        array $contacts = [], 
        string $msgHeader = '', 
        string $msgContent = '', 
        string|null $file = null
    ): bool 
    {

        if (empty($contacts)&&empty($msgContent)&&empty($msgHeader)) {
            throw new Exception("Verify your input must contain 'recipient', 'subject' and 'content");
        }

        try {
            if (!$this->settings()) {
                throw new Exception("SMTP configuration error.");
            }
    
            $this->mail->SMTPKeepAlive = true;
    
            foreach ($contacts as $contact) {
                $this->mail->clearAddresses();
                $this->mail->addAddress($contact);
    
                if (!empty($file) && file_exists($file)) {
                    $this->mail->addAttachment($file);
                }
    
                $this->content($msgHeader, $msgContent);

                if (!$this->mail->send()) {
                    throw new Exception("SMTP error: " . $this->mail->ErrorInfo);
                }

                usleep(500000); 
            }
    
            $this->mail->smtpClose();
            return true;
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Email using PYTHON
     * To use this function, you must install python 3
     * 
     * @param array $contacts
     * @param string $msgHeader
     * @param string $msgContent
     * @param string|null $file
     * @throws \Exception
     */
    private function sendEmailByPython(
        array $contacts = [], 
        string $msgHeader = '', 
        string $msgContent = '', 
        string|null $file = null
    ){

        if (empty($contacts)&&empty($msgContent)&&empty($msgHeader)) {
            throw new Exception("Verify your input must contain 'recipient', 'subject' and 'content");
        }

        return $this->pip()->executePython('sendEmail', [ "recipient" => $contacts, "content" => $msgContent, "subject" => $msgHeader, "file" => $file ]);
    } 
}