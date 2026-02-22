<?php

namespace Epaphrodites\epaphrodites\api\email\ini;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Epaphrodites\epaphrodites\translate\PythonCodesTranslate;

class config
{
    public object $mail;

    public function __construct()
    {
        // Initialize the PHPMailer object
        $this->mail = new PHPMailer(true);
    }

    public function settings():bool
    {
        try {
            // Enable SMTP debugging (optional)
            #$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;

            // Use SMTP for sending emails
            $this->mail->isSMTP();

            // Get email configuration from the ini file
            $config = static::configIni();

            // Set SMTP server details
            $this->mail->Host = $config['EMAIL']['SERVER'];

            $this->mail->SMTPAuth = true;

            $this->mail->Username = $config['EMAIL']['USER'];

            $this->mail->Password = $config['EMAIL']['PASSWORD'];

            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $this->mail->Port = $config['EMAIL']['PORT'];

            // Set the sender's email and name
            $this->mail->setFrom($config['EMAIL']['USER'], $config['EMAIL']['TITLE']);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function content(string $msgHeader, string $msgContent)
    {
        // Set email format to HTML
        $this->mail->isHTML(true);

        // Set email subject and content
        $this->mail->Subject = $msgHeader;

        $this->mail->Body = $msgContent;
    }

    private static function configIni()
    {
        // Load email configuration from an ini file
        $ini = _DIR_CONFIG_INI_ . "email.ini";

        $content = parse_ini_file($ini, true);

        return $content;
    }

    public function pip():object{

        return new PythonCodesTranslate;
    }
}
