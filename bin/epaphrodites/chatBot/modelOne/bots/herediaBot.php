<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\bots;

use Epaphrodites\epaphrodites\auth\session_auth;
use Epaphrodites\epaphrodites\chatBot\modelOne\makeActions\botActions;
use Epaphrodites\epaphrodites\chatBot\modelOne\defaultAnswers\mainHerediaDefaultMessages;

trait herediaBot
{
    /**
     * Finds the best response based on the user's input by calculating Jaccard coefficients.
     *
     * @param string $userMessage The message input by the user.
     * @param string $jsonFiles json file path name.
     * @return array The best-matching response.
     */
    private function getHerediaResponse(
        string $userMessage, 
        string $jsonFiles
    ): array{

        $knowledgeDatas = "customize/{$jsonFiles}";
        $hyppoCampusDatas = "customize/user{$jsonFiles}";

        // Get user login
        $login = $this->getBotUsersConnected();

        // Get last answers previous is true
        $previousQuestion = $this->lastUsersQuestion($login , $hyppoCampusDatas);

        // Get last question previous is true
        $previousQuestion = !is_null($previousQuestion) ? $previousQuestion['question'] : "";

        // Clean and normalize the user's message
        $this->userWords = $this->cleanAndNormalize("{$previousQuestion} {$userMessage}");

        // Detect user language
        $mainLanguage = $this->detectMainLanguage("{$previousQuestion} {$userMessage}" , $login , $hyppoCampusDatas);
    
        // Users sentiment analysis
        $sentimentAnalyzer = $this->analyzeSentiment($userMessage, $mainLanguage);        

        // Load questions and answers from a JSON file
        $questionsAnswers = $this->getContenAccordingLanguage($mainLanguage , $knowledgeDatas);
        
        // Iterate through each question and its associated answer
        $commentsToConsider = $this->iterateQuestionAnswersAssociated($questionsAnswers , $this->userWords);
        
        [ $bestCoefficient , $makeAction , $defaultLanguage , $similarySentence , $bestAnswers ] = $this->commentToConsiders($commentsToConsider);

        // Update the best coefficient and the corresponding response
        if ($bestCoefficient >= $this->mainCoefficient&&$similarySentence>0) {

            $this->mainCoefficient = $bestCoefficient;

            $actionResponses = $makeAction == "none"&&$bestCoefficient>=0.5 ? : (new botActions)->actions($makeAction , $login , $hyppoCampusDatas, $mainLanguage);

            $response = "{$bestAnswers} {$actionResponses}";

        } elseif ($bestCoefficient > 0.1) {

            $this->previous = true;

            $getContent = $this->herediaDefaultMessageToGetMorePrecision($mainLanguage , $this->getClass() );
            
            $getAnswers = $getContent[$this->answersKey];
            $defaultLanguage = $getContent[$this->languageKey];
            $response = $this->answersChanging($getAnswers);
        }
        
        // If no response is found, get a default bot message
        if(empty($response)){

            $getContent = $this->herediaDefaultMessageWhereNoResult($mainLanguage , $this->getClass() );
            
            $getAnswers = $getContent[$this->answersKey];
            $defaultLanguage = $getContent[$this->languageKey];
            $defaultMessage = $this->answersChanging($getAnswers);
            $response = [ $this->answersKey => "{$sentimentAnalyzer}{$defaultMessage}" ];
        }else{
            $response = [ $this->answersKey => "{$sentimentAnalyzer}{$response}" ];
        }
        
        $result = $this->predictAnswers($login, $userMessage, $defaultLanguage , $response);

        // Return the response with the highest similarity coefficient
        return $result;
    }

    /** 
     * @return \Epaphrodites\epaphrodites\chatBot\modelOne\defaultAnswers\mainHerediaDefaultMessages
    */
    private function getClass():object
    {
        return new mainHerediaDefaultMessages;
    }
}