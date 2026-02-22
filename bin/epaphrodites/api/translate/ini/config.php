<?php

namespace Epaphrodites\epaphrodites\api\translate\ini;

trait config{

    /**
     * @return array
     */
    private function setting():array{

        return [
            'key' => _YOUR_DEEPL_API_KEY,
            'uri' => 'https://api-free.deepl.com/v2/translate'
        ];
    }
}