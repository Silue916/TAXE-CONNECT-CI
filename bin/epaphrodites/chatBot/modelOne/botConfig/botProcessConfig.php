<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

use Epaphrodites\epaphrodites\auth\session_auth;
use Epaphrodites\epaphrodites\define\config\traits\currentFunctionNamespaces;

trait botProcessConfig
{

    use currentFunctionNamespaces;

  /**
     * @param string $userMessage
     * @return array
     */
    private function chatProcessConfig(
        string $userMessage
    ): array{
        $result =[];
        
        if (!empty($userMessage)) {

            // Find and store the response for the user message
            $response = $this->findResponse($userMessage);

            // Add the new response to existing data
            $existingData[] = $response;

            // Save the updated data to the JSON file
            $this->saveJson($existingData);
        }

        $login = $this->getBotUsersConnected();

        // Load existing JSON data, if any
        $existingData = $this->loadJsonFile();

        foreach ($existingData as $key => $value) {
            if ($value['login'] === $login) {
                $result[] = $existingData[$key];
            }
        }

        // Return the updated data including the new response
        return $result;
    }

    /**
     * @param string $userMessage
     * @param string $botName
     * @return array
     */
    private function herediaBotConfig(
        string $userMessage, 
        string $botName
    ): array{
        $hyppoCampusDatas = "customize/user{$botName}";
        $result =[];
        
        if (!empty($userMessage)) {

            // Find and store the response for the user message
            $response = $this->findHerediaResponse($userMessage , $botName);
            
            // Add the new response to existing data
            $existingData[] = $response;

            // Save the updated data to the JSON file
            $this->saveJson($existingData , $hyppoCampusDatas);
        }

        $login = $this->getBotUsersConnected();

        // Load existing JSON data, if any
        $existingData = $this->loadJsonFile($hyppoCampusDatas);
        
        foreach ($existingData as $key => $value) {
            if ($value['login'] === $login) {
                $result[] = $existingData[$key];
            }
        }

        // Return the updated data including the new response
        return $result;
    }

   /**
     * @param string $userMessage
     * @param bool $learn
     * @return array
     */
    private function herediaChatBotProcessConfig(
        string $userMessage, 
        bool $learn
    ):array{
        $result =[];
        
        $login = $this->getBotUsersConnected();

        if (!empty($userMessage)) {

            static::initConfig()['python']->executePython('lunchBotModelTwo',[ 'login' => $login, 'learn'=>$learn, 'userMessages'=>$userMessage ]);
        }

        // Load existing JSON data, if any
        $existingData = $this->loadOthersJsonFile('modelTwo/hippocampusModelTwo');

        foreach ($existingData as $key => $value) {
            if ($value['login'] === $login) {
                $result[] = $existingData[$key];
            }
        }

        return $result;
    } 
    
    /**
     * @param string $userMessage
     * @param bool $learn
     * @return array
     */
    private function noellaChatBotProcessConfig(
        string $userMessage, 
    ):array{
        $result =[];
        
        $login = $this->getBotUsersConnected();

        if (!empty($userMessage)) {

            static::initConfig()['python']->executePython('lunchBotModelThree',[ 'login' => $login, 'userMessages'=>$userMessage ]);
        }

        // Load existing JSON data, if any
        $existingData = $this->loadOthersJsonFile('modelThree/hippocampusModelThree');

        foreach ($existingData as $key => $value) {
            if ($value['login'] === $login) {
                $result[] = $existingData[$key];
            }
        }

        return $result;
    }  
    
    /**
     * @return string
     */
    protected function getBotUsersConnected(): string
    {
        $sessionAuth = new session_auth();
    
        $login = $sessionAuth->login();
    
        return $login ?? session_id();
    }
}