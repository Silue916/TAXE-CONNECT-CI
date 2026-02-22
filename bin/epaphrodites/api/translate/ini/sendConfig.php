<?php

namespace Epaphrodites\epaphrodites\api\translate\ini;

trait sendConfig{

    /**
     * @return string
     */
    private function getApiKey():string{

        return $this->setting()['key'];
    }

    /**
     * @return string
     */
    private function getUrl():string{

        return $this->setting()['uri'];
    }    
}