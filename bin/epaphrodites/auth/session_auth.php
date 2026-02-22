<?php

namespace Epaphrodites\epaphrodites\auth;

use Epaphrodites\epaphrodites\auth\Contracts\AuthSession;
use Epaphrodites\epaphrodites\define\config\traits\currentVariableNameSpaces;

class session_auth implements AuthSession
{

    use currentVariableNameSpaces;

    protected $msg;
    protected $config;
    protected $result;

    /**
     * Initialize object properties when an instance is created
     * @return void
     */
    public function __construct()
    {
        $this->initializeObject();
    }

    /**
     * Initialize each property using values retrieved from static configurations
     * @return void
     */
    private function initializeObject():void{

        $this->msg = $this->getObject( static::$initNamespace , 'msg' );
        $this->config = $this->getObject( static::$initGuardsConfig , 'session' );
    }

    /**
     * 
     * User session login data
     * @return string
     */
    public function login():string|null
    {
        
        return !empty($this->config->GetSessions(_AUTH_LOGIN_)) ? $this->config->GetSessions(_AUTH_LOGIN_) : NULL;
    }

    /**
     * 
     * User session iduser data
     * @return string
     */
    public function id():string|null
    {

        return !empty($this->config->GetSessions(_AUTH_ID_)) ? $this->config->GetSessions(_AUTH_ID_)  : NULL;
    }

    /**
     * 
     * User session usersGroup user
     * @var int $usersGroup
     * @return int
     */
    public function type():int|null
    {

        return !empty($this->config->GetSessions(_AUTH_TYPE_)) 
                                    ? $this->config->GetSessions(_AUTH_TYPE_)
                                    : NULL;
    }

    /**
     * 
     * User session name and surname
     * @return string
     */
    public function nameSurname():string|null
    {

        return !empty($this->config->GetSessions(_AUTH_NAME_)) 
                                    ? $this->config->GetSessions(_AUTH_NAME_)
                                    : NULL;
    }

    /**
     * 
     * User session email
     * @return string
     */
    public function email():string|null
    {

        return !empty($this->config->GetSessions(_AUTH_EMAIL_)) 
                                    ? $this->config->GetSessions(_AUTH_EMAIL_)
                                    : NULL;
    }

    /**
     * 
     * User session contact
     * @return string
     */
    public function contact():string|null
    {
        
        return !empty($this->config->GetSessions(_AUTH_CONTACT_))
                                    ? $this->config->GetSessions(_AUTH_CONTACT_) 
                                    : NULL;
    }

    /**
     * 
     * User session otp
     * @return string
     */
    public function otp():string|null
    {
        
        return !empty($this->config->GetSessions(_AUTH_OTP_))
                                    ? $this->config->GetSessions(_AUTH_OTP_) 
                                    : NULL;
    } 
    
    /**
     * 
     * User session otp activation
     * @return int|null
     */
    public function otpVerification():int|null
    {
        
        return !empty($this->config->GetSessions(_AUTH_VERIFY_))
                                    ? $this->config->GetSessions(_AUTH_VERIFY_) 
                                    : NULL;
    }   
    
    /**
     * 
     * User session otp Authentification
     * @return bool
     */
    public function otpAuthentification():bool
    {

        return !empty($this->config->GetSessions(_AUTH_CONFIRM_))
                                    ? $this->config->GetSessions(_AUTH_CONFIRM_) 
                                    : false;
    }      

    /**
     * 
     * User session otp confirmation
     * @return int|null
     */
    public function otpConfirmation():int|null
    {
        
        return !empty($this->config->GetSessions(_AUTH_VERIFY_))
                                    ? $this->config->GetSessions(_AUTH_VERIFY_) 
                                    : NULL;
    } 

    /**
     * 
     * User cookies token_csrf data
     * @var mixed $token_csrf
     * @return mixed
     */
    public function token_csrf():mixed
    {
        return !empty($_COOKIE[$this->msg->answers('token_name')]) 
                                        ? $_COOKIE[$this->msg->answers('token_name')]
                                        : NULL;
    }

    /**
     * 
     * Destroy user session
     * @return mixed
     */
    public function deconnexion()
    {

        if ($this->login() !== NULL && $this->id() !== NULL) {

            session_unset();
            session_destroy();
        }
    }
}