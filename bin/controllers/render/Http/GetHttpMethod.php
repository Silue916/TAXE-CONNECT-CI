<?php

namespace Epaphrodites\controllers\render\Http;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;

class GetHttpMethod extends epaphroditeClass
{

 /**
     * Switch URL for HTTP methods
     *
     * @return string
     */
    protected function SwitchUrlHttp():string{

       // Set cookies and start user session
       static::initConfig()['setting']->session_if_not_exist();

       // Get the URL path
       $urlPath = urldecode(parse_url($_SERVER['REQUEST_URI']??'', PHP_URL_PATH));

       // Get the base URL path
       $baseURL = '/' . ltrim(_DOMAINE_, '/');

       // Remove the base URL from the full URL path
       $httpResponses = str_replace($baseURL, '', $urlPath);

       // Return the processed URL
       return empty(_DOMAINE_) ? $urlPath : ltrim($httpResponses, '/');
    }

}