<?php

namespace Epaphrodites\controllers\controllers;

use Epaphrodites\controllers\switchers\MainSwitchers;

final class main extends MainSwitchers
{
    private object $visit;
    private object $session;
    private object $select;
    private object $insert;
    private string $ans = '';
    private string $alert = '';
    
    public function __construct()
    {
        $this->initializeObjects();
    }

    /**
     * @return void
     */
    private function initializeObjects(): void
    {
        $this->visit = $this->getObject(static::$initNamespace, 'visit');
        $this->session = $this->getFunctionObject(static::initNamespace(), 'session');
        $this->select = $this->getFunctionObject(static::initQuery(), 'select');
        $this->insert = $this->getFunctionObject(static::initQuery(), 'insert');
    }

    /**
     * Index page
     * 
     * @param string $html
     * @return void
     */
    public final function index(
        string $html
    ):void
    {

        $this->views($html, []);
    }
    
    /**
     * Authentification page ( login )
     * 
     * @param string $html
     * @return void
     */
    public final function login(
        string $html
    ): void
    {

        if (static::isValidMethod()) {

            $result = static::initConfig()['auth']->usersAuthManagers(
               static::getPost('__login__'),
               static::getPost('__password__')
            );

            [$this->ans, $this->alert] = static::Responses($result, [ false => ['login-wrong', 'error'] ]);
        }

        $this->views( $html,
            [
                'class' => $this->alert,
                'reponse' => $this->ans
            ]
        );
    }

    /**
     * OTP verification page (OTP)
     * 
     * @param string $html
     * @return void
     */
    public final function confirmOtpCodeSendByEmail(
        string $html
    ):void
    {

        if (static::isValidMethod(true)) {

            static::initConfig()['otp']->verificateOTP(
                static::getPost('__otp__')
            );
        }

        $this->views($html, 
        [
            'email' => $this->session->email()
        ]
    );
    }

    /**
     * Public merchant signup.
     * Signup is accepted only if commune already has an active mairie account.
     */
    public final function merchantSignup(
        string $html
    ): void {
        if (static::isPost('__signup__') && static::isValidMethod(true)) {
            $ok = $this->insert->selfRegisterMerchantAccount([
                'commune' => static::getPost('__commune__'),
                'login' => static::getPost('__login__'),
                'password' => static::getPost('__password__'),
                'owner_name' => static::getPost('__owner_name__'),
                'business_name' => static::getPost('__business_name__'),
                'phone' => static::getPost('__phone__'),
                'email' => static::getPost('__email__'),
                'activity' => static::getPost('__activity__'),
                'address' => static::getPost('__address__'),
            ]);

            if ($ok) {
                $this->ans = "Inscription validee. Votre compte commercant est cree.";
                $this->alert = "alert-success";
            } else {
                $this->ans = "Inscription impossible. Verifiez la commune (compte mairie actif requis), l'unicite du login et les champs obligatoires.";
                $this->alert = "alert-danger";
            }
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'class' => $this->alert,
                'communes' => $this->select->listOpenCommunesForMerchantSignup(),
            ]
        );
    }
}
