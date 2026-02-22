<?php

namespace Epaphrodites\epaphrodites\chatBot;

use Epaphrodites\epaphrodites\chatBot\modelOne\bots\mainBot;
use Epaphrodites\epaphrodites\chatBot\modelOne\bots\herediaBot;
use Epaphrodites\epaphrodites\chatBot\modelOne\loadSave\loadJson;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\analyzeWord;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\botAssembly;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\randomArray;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\languageWords;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\initVariables;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\cleanNormalize;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\dafaultAnswers;
use Epaphrodites\epaphrodites\chatBot\modelOne\treatment\answersChecking;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\languageDetection;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\jaccardCoefficient;
use Epaphrodites\epaphrodites\chatBot\modelOne\botConfig\sentimentAnalyzer;

class chatBot {

use initVariables, loadJson, cleanNormalize, jaccardCoefficient, mainBot, herediaBot, dafaultAnswers, randomArray, languageDetection, botAssembly, analyzeWord, languageWords, answersChecking, sentimentAnalyzer;

    /**
     * @param string $userMessage
     * @return array
     */
    protected function findResponse(string $userMessage):array
    {
       return $this->getResponse($userMessage);
    }

    /**
     * @param string $userMessage
     * @param string $botName
     * @return array
     */
    protected function findHerediaResponse(string $userMessage , string $botName):array
    {

       return $this->getHerediaResponse($userMessage , $botName);
    }    
}
