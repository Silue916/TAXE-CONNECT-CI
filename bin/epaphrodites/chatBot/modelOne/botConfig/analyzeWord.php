<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

trait analyzeWord
{

    /**
     * @param string $userQUestions
     * @param string $name
     * @return string
    */    
    private function getUsersRequest( 
        string $userQuestion, 
        string $name
    ): ?string {

        $userQuestion = explode(" ", $userQuestion);
        
        $indexGetRequest = array_search($name, $userQuestion, true);
        
        if ($indexGetRequest === false) {
            return "";
        }
        
        if ($indexGetRequest === count($userQuestion) - 1) {
            return "";
        }
        
        return $userQuestion[$indexGetRequest + 1];
    }

    /**
     * @param array $userQUestions
     * @param array $name
     * @return string
    */
    private function getMainResultName( 
        array $userQUestions = [], 
        array $name = []
    ):string{

        $getMain = "";

        (string) $userQUestions = implode( ' ' , $userQUestions);

        foreach ($name as $names) {
            $getMain = $this->getUsersRequest($userQUestions, $names);

            if (!empty($getMain)) { return $getMain; }
        }

        return $getMain;
    }
}