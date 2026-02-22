<?php

namespace Epaphrodites\epaphrodites\api\translate\ini;

trait deeplTranslate{

    private function translateText(
        string $text, 
        string $targetLanguage = 'EN'
    ): string {

        if (empty($this->getApiKey())) {
            return "API key is required.";
        }
    
        $params = [
            'auth_key' => $this->getApiKey(),
            'text' => $text,
            'target_lang' => $targetLanguage
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl() . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
        $result = curl_exec($ch);
    
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return "Translation request error: $error";
        }
    
        curl_close($ch);

        $response = json_decode($result, true);
    
        if (!empty($response['translations'][0]['text'])) {
            return $response['translations'][0]['text'];
        } else {
            return 'Error retrieving the translation.';
        }
    }
}