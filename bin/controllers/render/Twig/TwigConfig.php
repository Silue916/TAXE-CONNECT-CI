<?php

namespace Epaphrodites\controllers\render\Twig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Epaphrodites\epaphrodites\env\config\ResponseSequence;
use Epaphrodites\epaphrodites\define\config\traits\currentFunctionNamespaces;
use Epaphrodites\epaphrodites\define\config\traits\currentVariableNameSpaces;

class TwigConfig extends ResponseSequence{

    use currentVariableNameSpaces , currentFunctionNamespaces;

    /**
     * Twig path Environment
     * @var \Twig\Environment $twigEnvironment
     * @return mixed
    */    
    private function getTwigEnvironement(): Environment
    {

        $twigEnvironment = new Environment ( (new FilesystemLoader ( _DIR_VIEWS_ ) ) , [ 'cache' =>false ]);
        
        $twigEnvironment->addExtension(static::initConfig()['extension']);

        // Expose project public links to Twig templates.
        $twigEnvironment->addGlobal(
            '_TAXECONNECT_SITE_URL_',
            defined('_TAXECONNECT_SITE_URL_') ? _TAXECONNECT_SITE_URL_ : 'https://taxeconnect.ci'
        );
        $twigEnvironment->addGlobal(
            '_TAXECONNECT_DOCS_URL_',
            defined('_TAXECONNECT_DOCS_URL_') ? _TAXECONNECT_DOCS_URL_ : 'https://taxeconnect.ci/docs'
        );
        $twigEnvironment->addGlobal(
            '_TAXECONNECT_GITHUB_URL_',
            defined('_TAXECONNECT_GITHUB_URL_') ? _TAXECONNECT_GITHUB_URL_ : 'https://github.com/taxeconnect-ci/taxeconnect-ci'
        );
        $twigEnvironment->addGlobal(
            '_TAXECONNECT_SUPPORT_URL_',
            defined('_TAXECONNECT_SUPPORT_URL_') ? _TAXECONNECT_SUPPORT_URL_ : 'https://taxeconnect.slack.com'
        );
        $twigEnvironment->addGlobal(
            '_TAXECONNECT_VIDEOS_URL_',
            defined('_TAXECONNECT_VIDEOS_URL_') ? _TAXECONNECT_VIDEOS_URL_ : 'https://www.youtube.com/@taxeconnectci'
        );

        return $twigEnvironment;
    }

    /**
     * Get Twig Environment instance
     * 
     * @return \Twig\Environment
     */    
    public function getTwigEnvironmentInstance(): Environment
    {

        return $this->getTwigEnvironement();
    }
}
