<?php

namespace Epaphrodites\epaphrodites\define\lang\eng;

class SetEnglishTextMessages
{
    private $AllMessages;

    public function SwithAnswers($MessageCode)
    {

        $this->AllMessages[] =
        [
            'language' => 'english',
            '403-title' => 'ERROR 403',
            '404-title' => 'ERROR 404',
            '419-title' => 'ERROR 419',
            '500-title' => 'ERROR 500',
            'session_name' => _SESSION_,
            'token_name' => _CRSF_TOKEN_,
            'back' => "Return to homepage",
            '403' => "Restricted access!!!",
            '500' => 'Internal server error',
            '404' => "Oops! No page found!!!",
            'author' => 'TaxeConnect CI team',
            'mdpnotsame' => "Incorrect password",
            'fileempty' => "No file selected!!!",
            'site-title' => 'HOME | TAXECONNECT CI',
            'description' => 'Digital platform for municipal tax collection',
            '419' => "Your session has expired!!!",
            'error_text' => 'TaxeConnect CI error',
            'denie' => "Processing not possible!!!",
            'noformat' => "The file format is incorrect!",
            'version' => 'TAXECONNECT CI V1.0',
            'login-wrong' => "Login or password incorrect",
            'connexion' => "Please reconnect again, please!",
            'false-mail' => 'Sorry, this email is incorrect.',
            'user-exist' => 'Sorry, this user already exists.',
            'succes' => "Processing completed successfully!!!",
            'no-identic' => "Sorry, the passwords do not match.",
            'error' => 'Sorry, an error occurred during processing',
            'file-header' => 'Please check the header of your file',
            'vide' => "Please fill in all fields correctly, please!!!",
            'no-data' => "Sorry, no information matches your request.",
            'erreur' => "Sorry, an issue occurred during processing!!!",
            'done' => "Congratulations, your registration was successful!!!",
            'rightexist' => "Sorry, this right already exists for this user.",
            'tailleauto' => "The file size exceeds the allowed limit of 500 KB.",
            'send' => "Congratulations, your message has been successfully sent!!!",
            'errorsending' => "Sorry, an issue occurred while sending your message!!!",
            'denie_action' => "Processing not possible!!! You do not have authorization to perform this action.",
            'keywords' => "TaxeConnect CI, municipal taxes, digital payment, mobile money, QR proof, Cote d'Ivoire;",
        ];

        return isset($this->AllMessages[0][$MessageCode]) ? $this->AllMessages[0][$MessageCode] : "";
    }
}
