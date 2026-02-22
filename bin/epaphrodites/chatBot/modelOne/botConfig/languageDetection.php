<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

trait languageDetection
{

    /**
     * @param string $login
     * @param string $jsonFiles
     * @return string
     */
    private function detectLastLang(
        string $login, 
        string $jsonFiles = 'main/userHippocampusModelOne'
    ):string{
        $jsonDatas = $this->loadJsonFile($jsonFiles);

        for ($i = count($jsonDatas) - 1; $i >= 0; $i--) {
            $value = $jsonDatas[$i];
            if ($value['login'] === $login) {
                return $value['language'];
            }
        }

        return "eng";
    }

    /**
     * @param string $userMessages
     * @param string $login
     * @return string 
    */    
    private function detectMainLanguage(
        string $userMessages, 
        string $login, 
        string $jsonFiles = 'main/userHippocampusModelOne'
    ):string{
        $languageDetected = "";

        $userMessages = $this->normalizeWords($userMessages);

        $detectLastLanguage = $this->detectLastLang($login , $jsonFiles);
        
        (int) $englishWord = $this->calculateSimilarWords($this->englishLangWord(), $userMessages, 0.80);
        
        (int) $frenchhWord = $this->calculateSimilarWords($this->frenchLangWord(), $userMessages, 0.80);

        $languageDetected = match (true) {
            empty($englishWord) && empty($frenchhWord) => $detectLastLanguage,
            default => $frenchhWord > $englishWord ? 'fr' : 'eng',
        };
                
        return $languageDetected;
    }
}