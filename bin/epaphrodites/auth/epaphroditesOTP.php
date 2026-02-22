<?php

namespace Epaphrodites\epaphrodites\auth;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;

class epaphroditesOTP{

    /**
     * verificate OTP value
     * 
     * @param string $otpSending
     * @param string $sessionOTP
     * @return void
     */
    public function verificateOTP( 
        string $otpSending = 'unknown'
    ):void{

        $sessionOTP = $this->epaphroditesClass('session')->otp();

        if( md5($otpSending) == md5($sessionOTP )){

            $_SESSION[_AUTH_CONFIRM_] = $sessionOTP;

            $locate = $this->epaphroditesClass('paths')->dashboard();

            header("Location: {$locate}");
        }
    }

    /**
     * Summary of epaphroditesClass
     * 
     * @param string $className
     * @return object
     */
    private function epaphroditesClass(
        string $className
    ):object{

        $mainClass = new epaphroditeClass();

        return $mainClass::class($className);
    }
}