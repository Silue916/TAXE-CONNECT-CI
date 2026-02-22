<?php

namespace Epaphrodites\epaphrodites\ErrorsExceptions;

use Epaphrodites\controllers\render\Twig\TwigRender;

class SwitchErrorsPages extends TwigRender
{

    /**
     * Error 404
     * @return void
     */
    public function error_404():void
    {

        static::DefaultResponses(404, true);

        $this->render('errors/404', ['back' => $this->GoBack(), 'layouts' => static::initNamespace()['layout']->errors()]);

        exit;
    }

    /**
     * Error 403
     * @return void
     */
    public function error_403():void
    {
        static::DefaultResponses(403, true);

        $this->render('errors/403', ['back' => $this->GoBack(), 'layouts' => static::initNamespace()['layout']->errors()]);

        exit;
    }

    /**
     * Error 419 
     * @return void
     */
    public function error_419():void
    {
        static::DefaultResponses(419, true);

        $this->render('errors/419', ['back' => $this->GoBack(), 'layouts' => static::initNamespace()['layout']->errors(),]);

        static::initNamespace()['session']->deconnexion();

        exit;
    }

    /**
     * Error 500
     * 
     * @return void
     */
    public function error_500($errorType):void
    {
        static::DefaultResponses(500, true);

        $this->render('errors/500', ['back' => $this->GoBack(), 'type' => $errorType, 'layouts' => static::initNamespace()['layout']->errors()]);

        exit;
    }

    /**
     * Redirects the user to the login page after performing logout.
     * @return void
     */
    public function sendToLogin():void
    {
        $loginPage = static::initNamespace()['paths']->login();

        header("Location: $loginPage");

        exit;
    }
 
    /**
     * back manager
     * @return string
     */
    private function GoBack():string
    {

        return is_null(static::initNamespace()['session']->login()) ? static::initNamespace()['paths']->gethost() : static::initNamespace()['paths']->dashboard();
    }
}
