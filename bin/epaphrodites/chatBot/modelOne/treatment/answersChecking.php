<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\treatment;

trait answersChecking{

    /**
     * @param array $questionsAnswers
     * @param array $userWords
     * @return array
     */
    protected function iterateQuestionAnswersAssociated(
        array $questionsAnswers, 
        array $userWords
    ):array{

        $temporaryResponses = [];

        foreach ($questionsAnswers as $question => $associatedAnswer) {

            // Clean and normalize the question
            $questionWords = $this->splitTextIntoWords($associatedAnswer["key"]);
            
            // Calculate the Jaccard coefficient between user input and each question
            $coefficient = $this->calculateJaccardCoefficient($userWords, $questionWords);

            // Check the best answers
            if ($coefficient >= 0.1) {

                $temporaryResponses[] = 
                [ 
                    $this->coefficientKey => $coefficient , 
                    $this->answersKey=>$associatedAnswer[$this->answersKey] ,
                    $this->actionsKey=>$associatedAnswer[$this->actionsKey],
                    $this->similarlyKey=>$associatedAnswer[$this->similarlyKey],
                    $this->nameKey=>$associatedAnswer[$this->nameKey],
                    $this->botKey=>$associatedAnswer[$this->botKey],
                    $this->contextKey=>$associatedAnswer[$this->contextKey],
                    $this->assemblyKey=>$associatedAnswer[$this->assemblyKey],
                    $this->languageKey=>$associatedAnswer[$this->languageKey]
                ];

            }
        }

        // Select the top comments based on coefficient
        return array_slice($temporaryResponses, 0, min(count($temporaryResponses), 100));
    }

    protected function commentToConsiders($commentsToConsider):array{

        if (!empty($commentsToConsider)) {
            
            foreach ($commentsToConsider as $checkTheBestAnswers) {
                
                if ($this->maxComment === null || $checkTheBestAnswers[$this->coefficientKey] > $this->maxComment[$this->coefficientKey]) {
                    
                    $this->maxComment = $checkTheBestAnswers;
                }
            }
            
            $bestCoefficient = $this->maxComment[$this->coefficientKey] ?? 0;
            $makeAction = $this->maxComment[$this->actionsKey] ?? null;
            $defaultLanguage = $this->maxComment[$this->languageKey];
            $similarySentence = $this->calculateSimilarWords($this->userWords , $this->maxComment[$this->similarlyKey]) ?? null;
            $bestAnswers = $this->assemblyWords( $this->userWords, $this->maxComment[$this->assemblyKey] , $this->maxComment[$this->nameKey] , $this->maxComment[$this->botKey] , $this->maxComment[$this->contextKey] , $this->maxComment[$this->answersKey] , $this->maxComment[$this->similarlyKey] );

            return [ $bestCoefficient , $makeAction , $defaultLanguage , $similarySentence , $bestAnswers ];
        }    
        
        return [ 0, NULL, NULL, NULL, NULL];
    }   
    
    /**
     * @param string $login
     * @param string $userMessage
     * @param string $defaultLanguage
     * @param array $response
     * @return array
    */
    protected function predictAnswers(
        string $login, 
        string $userMessage, 
        string $defaultLanguage, 
        array $response
    ):array{

        // Build user history
        $defaultUsers = [ $this->loginKey => $login ];
        $defaultPrevious = [ $this->previousKey => $this->previous ];
        $userQuestion = [ $this->questionKey => $userMessage ];
        $userLanguage = [ $this->languageKey => $defaultLanguage ];
        $defaultDateTime = [ $this->dateKey => date("d-y-Y h:i:s") ];

        // Merge all information to form the final response
        return array_merge( $defaultDateTime , $defaultUsers , $userQuestion , $response , $userLanguage , $defaultPrevious );
    }
}