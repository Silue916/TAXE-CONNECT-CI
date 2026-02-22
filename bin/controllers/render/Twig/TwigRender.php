<?php

namespace Epaphrodites\controllers\render\Twig;

use Epaphrodites\epaphrodites\Contracts\twigRender as ContractsTwigRender;
use Epaphrodites\epaphrodites\ErrorsExceptions\ErrorHandler;

class TwigRender extends TwigConfig implements ContractsTwigRender{

    use ErrorHandler;
    /**
     * Twig render
     *
     * @param string|null $view
     * @param array|[] $array
     * @return void
     */ 
    public function render( 
      string|null $view = null, 
      array $array = [] 
    ):void
    {

        try {
            echo $this->getTwigEnvironmentInstance()
                ->render(
                    $view . _FRONT_ , 
                    $array 
                );
        } catch (\Throwable $e) {
            if (!_PRODUCTION_) {
            echo $this->getTwigEnvironmentInstance()
                ->render( 
                    ErrorHandler::setView() . _FRONT_ ,
                    ErrorHandler::getErrors($e) 
                );
            }
        }
    }    
}