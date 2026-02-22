<?php

namespace Epaphrodites\epaphrodites\Kernel;

use Epaphrodites\controllers\render\Http\ConfigHttp;
use Epaphrodites\controllers\switchers\GetControllers;
use Epaphrodites\epaphrodites\EpaphMozart\templatesConfig\ConfigDashboardPages;

class runKernel extends ConfigHttp
{

    private ?string $GetUrl = null;
    private object $Switchers;
    private object $InterfaceManager;

    private $someValue;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->Switchers = new GetControllers;
        $this->InterfaceManager = new ConfigDashboardPages;
    }

    /**
     * Run app
     * @return void
     */
    private function Start(): void
    {

        /**
         * @return string
         */
        $this->GetUrl = (string) $this->HttpResponses();

        /**
         * Get user authentification page or destroy session
         * @var mixed $GetUrl
         */
        if ($this->GetUrl === _LOGIN_ || $this->GetUrl === _LOGOUT_) {

            static::class('session')->deconnexion();

            $this->GetUrl = (string) $this->InterfaceManager->login();
        }

        /**
         * Get user authentification page or destroy session
         */
        if ($this->GetUrl === _DASHBOARD_ && static::class('session')->id() === NULL) {

            static::class('session')->deconnexion();

            $this->GetUrl = (string) $this->InterfaceManager->main();
        }

        /**
         * Get user dashbord page
         * @return string
         */
        if ($this->GetUrl === _DASHBOARD_ && static::class('session')->token_csrf() !== NULL && static::class('session')->id() !== NULL && static::class('session')->login() !== NULL) {

            $this->GetUrl = (string) $this->InterfaceManager->admin(static::class('session')->type(), $this->GetUrl);
        }

        /**
         * Force users to users to save his informations
         * @return string
         */
        if (static::class('session')->id() !== NULL && static::class('session')->login() !== NULL && empty(static::class('session')->nameSurname()) && empty(static::class('session')->email()) && empty(static::class('session')->contact())) {

            $this->GetUrl = (string) $this->InterfaceManager->identification();
        }

        /**
         * Force users to users to verificate OTP
         * @return string
         */
        if ( !empty(static::class('session')->email()) && static::class('session')->otpVerification() == 1 && _OTP_METHOD_ == true && static::class('session')->otpAuthentification() == false ) {

            $this->GetUrl = (string) $this->InterfaceManager->otpVerification();
        }

        /**
         * Splitting the URL returned by the GetUrl method into an array
         * @return array
         */
        $getUrl = explode('/', $this->GetUrl);

        /**
         * Return true user page
         * @param null|array $provider
         * @param null|string $paths
         * @return void
         */
        $this->Switchers->SwitchMainControllers($getUrl, $this->provider($getUrl));
    }

    /**
     * Method to initiate the application execution.
     * @return void
     */
    public static function Run(): void
    {

        // Create an instance of the current class.
        $app = new self();

        // Call the Start method of the instance.
        $app->Start();
    }

    /**
     * Get Home value
     * @return string
     */
    public function getHome():string
    {
        return _HOME_;
    }
}
