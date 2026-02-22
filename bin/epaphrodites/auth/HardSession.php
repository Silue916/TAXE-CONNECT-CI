<?php

namespace Epaphrodites\epaphrodites\auth;

use Epaphrodites\epaphrodites\env\config\GeneralConfig;

class HardSession extends GeneralConfig{

    /**
     * @param mixed $id
     * @param mixed $login
     * @param mixed $email
     * @param mixed $contact
     * @param mixed $usersGroup
     * @param mixed $nameSurname
     * @return void
    */
    public function StartSession( $id , $login , $nameSurname , $contact , $email , $usersGroup, $sessionOTP, $otpVerification )
    {

        // Set id session value
        $this->SetSession( _AUTH_ID_ , $id);

        // Set login session value
        $this->SetSession( _AUTH_LOGIN_ , $login);
        
        // Set name and surname session value
        $this->SetSession( _AUTH_NAME_ , $nameSurname);

        // Set contact session value 
        $this->SetSession( _AUTH_CONTACT_ , $contact);

        // Set email session value
        $this->SetSession( _AUTH_EMAIL_ , $email);

        // Set usersGroup user session value
        $this->SetSession( _AUTH_TYPE_ , $usersGroup );

        // Set Session OTP
        $this->SetSession( _AUTH_OTP_ , $sessionOTP );

        // Set session otp verification
        $this->SetSession( _AUTH_VERIFY_ , $otpVerification );
    }
}