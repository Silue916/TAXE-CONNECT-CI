<?php

namespace Epaphrodites\epaphrodites\chatBot;

use Epaphrodites\epaphrodites\chatBot\modelOne\loadSave\saveJsonDatas;
use Epaphrodites\epaphrodites\chatBot\modelOne\loadSave\loadUsersAnswers;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\botProcessConfig;

class processBotAnswers extends chatBot
{

    use saveJsonDatas, loadUsersAnswers, botProcessConfig;

    /**
     * Chat bot model one init
     * @param string $userMessage
     * @return array
    */
    public final function chatBotmodelOneProcess(
        string $userMessage
    ): array
    {
        return $this->chatProcessConfig($userMessage);
    }

    /**
     * Chatbot model one customizable
     * @param string $userMessage
     * @param string $botName
     * @return array
    */
    public final function herediaBotmodelOne(
        string $userMessage, 
        string $botName
    ): array
    {
        return $this->herediaBotConfig($userMessage , $botName);
    }      

    /**
     * Chatbot model two customizable
     * @param string $userMessage
     * @param bool $learn
     * @return array
    */
    public final function chatBotmodelTwoProcess(
        string $userMessage, 
        bool $learn = true
    ): array
    {
        return $this->herediaChatBotProcessConfig($userMessage , $learn);
    }  
    
    /**
     * Chatbot model three
     * @param string $userMessage
     * @return array
    */
    public final function chatBotModelThreeProcess(
        string $userMessage
    ): array
    {
        return $this->noellaChatBotProcessConfig($userMessage);
    }     
}