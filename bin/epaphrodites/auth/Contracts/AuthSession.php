<?php

namespace Epaphrodites\epaphrodites\auth\Contracts;

interface AuthSession
{

    /**
     * @return string|null
     */
    public function login():string|null;

    /**
     * @return string|null
    */    
    public function id():string|null;

    /**
     * @return int|null
    */    
    public function type():int|null;

    /**
     * @return string|null
     */    
    public function nameSurname():string|null;

    /**
     * @return string|null
     */    
    public function email():string|null;

    /**
     * @return string|null
     */    
    public function contact():string|null;

    /**
     * @return string|null
     */    
    public function otp():string|null;

    /**
     * @return int|null
     */    
    public function otpVerification():int|null;

    /**
     * @return bool
     */    
    public function otpAuthentification():bool;   
    
    /**
     * @return int|null
     */    
    public function otpConfirmation():int|null;    

    /**
     * @return mixed
     */    
    public function token_csrf():mixed;
}