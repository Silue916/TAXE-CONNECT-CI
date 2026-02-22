<?php

namespace Epaphrodites\epaphrodites\api\translate;

use Epaphrodites\epaphrodites\api\translate\ini\config;
use Epaphrodites\epaphrodites\api\translate\ini\sendConfig;
use Epaphrodites\epaphrodites\api\translate\ini\deeplTranslate;

class deepLp{

    use config,sendConfig, deeplTranslate;

    public function getTranslate(
        string $text, 
        string $targetLanguage = 'EN'
    ){

        return $this->translateText($text, $targetLanguage);
    }
}