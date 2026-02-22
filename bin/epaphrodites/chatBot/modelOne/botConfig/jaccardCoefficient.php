<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

use Normalizer;

trait jaccardCoefficient
{

    /**
     * Calculates the Jaccard coefficient between two arrays.
     *
     * @param array $questionArray The first array.
     * @param array $AnswersArray The second array.
     * @return float The Jaccard coefficient value.
     */
    private function calculateJaccardCoefficient(
        array $initQuestionArray, 
        array $AnswersArray
    ): float{

        // Convert arrays to sets to remove duplicates
        $AnswersArray = array_unique($AnswersArray);
        $initQuestionArray = array_unique($initQuestionArray);        

        $mainKeyword = $this->extractBracketValues($AnswersArray);

        $othersKeyword = $this->extractBracesValues($AnswersArray);

        $questionArray = $this->filterUsersQuestion( $initQuestionArray , array_merge( $othersKeyword , $mainKeyword ));
        
        // Calculate the weighted intersection of the two arrays 
        $intersection = $this->calculateSimilarWords($questionArray , $AnswersArray , 0.90);
        
        // Calculate the weighted intersection of the main keyword and the AnswersArray
        (int) $mainKeywordIntersec = $this->getMainKeyCoefficient($mainKeyword , $initQuestionArray);
        
        // Calculate the weighted intersection of the main keyword and the AnswersArray
        (int) $otherKeywordIntersec = $this->getMainKeyCoefficient($othersKeyword , $initQuestionArray);
    
        $mainKeywordCoefficient = $mainKeywordIntersec * 0.27;

        $otherKeywordCoefficient = $otherKeywordIntersec * 0.33;

        $mainKeyword = !is_null($mainKeyword)&&!in_array("", $mainKeyword) ? 1: 0;
        $othersKeyword = !is_null($othersKeyword)&&!in_array("", $othersKeyword) ? 1: 0;
      
        $union = count($AnswersArray) - ($mainKeyword+$othersKeyword);

        // Calculate the Jaccard coefficient
        $jaccardCoefficient = ($union !== 0) ? $intersection / $union : 0;

        $jaccardCoefficient = $mainKeywordCoefficient + $jaccardCoefficient + $otherKeywordCoefficient;

        return $jaccardCoefficient;
    }
   
    /**
     * @param mixed $mainKeyword
     * @param mixed $initQuestionArray
     * @return int
    */
    private function getMainKeyCoefficient( 
        mixed $mainKeyword, 
        mixed $initQuestionArray 
    ):int{
        $intersection = $this->calculateSimilarWords( $initQuestionArray , $mainKeyword , 0.90);

        $intersection = $intersection > 0 ? 1 : 0;

        return $intersection;
    }

    /**
     * @param array $userQuestions
     * @param array $wordToRemove
     * @param float $threshold
     * @return int
     */
    private function calculateSimilarWords(
        array $userQuestions, 
        array $wordsToRemove, 
        float $threshold = 0.8
    ): int {
        $countRemovedWords = 0;
    
        foreach ($userQuestions as $question) {
            foreach ($wordsToRemove as $wordToRemove) {
                similar_text(mb_strtolower($question), mb_strtolower($wordToRemove), $similarity);
                if ($similarity >= $threshold * 100) {
                    $countRemovedWords++;
                    break 1;
                }
            }
        }
    
        return $countRemovedWords;
    }
    
    /**
     * @param array $botAnswers
     * @return array
    */
    private function extractBracketValues(
        array $botAnswers
    ):array{

        $extractedValues = [];
        $pattern = '/\[(.*?)\]/';
        $botAnswersString = implode(' ', $botAnswers);
    
        preg_match_all($pattern, $botAnswersString, $matches);
    
        foreach ($matches[1] as $match) {
            $values = explode(',', $match);
            foreach ($values as $value) {
                if (!empty($value)) {
                    $extractedValues[] = $value;
                }
            }
        }
        $arrayToRemove = implode(' ' , $extractedValues);
        $extractedValues = explode(' ' , $arrayToRemove);
        
        return $extractedValues;
    }

    /**
     * @param array $botAnswers
     * @return array
    */
    private function extractBracesValues(
        array $botAnswers
    ):array{

        $extractedValues = [];
        $pattern = '/\{(.*?)\}/';
        $botAnswersString = implode(' ', $botAnswers);
    
        preg_match_all($pattern, $botAnswersString, $matches);
    
        foreach ($matches[1] as $match) {
            $values = explode(',', $match);
            foreach ($values as $value) {
                if (!empty($value)) {
                    $extractedValues[] = $value;
                }
            }
        }
        $arrayToRemove = implode(' ' , $extractedValues);
        $extractedValues = explode(' ' , $arrayToRemove);
        
        return $extractedValues;
    }    

    /**
     * @param array $initQuestionArray
     * @param array $arrayToRemove
     * @return array
    */
    private function filterUsersQuestion(
        array $initQuestionArray, 
        array $arrayToRemove
    ): array{
        
        return array_diff($initQuestionArray, $arrayToRemove);
    }
    
    /**
     * @param string $sentence
     * @return array
    */    
    private function normalizeWords(
        string $sentence
    ): array{

        $sentence = strtolower(preg_replace('/[^\p{L}\s]+/u', '', $this->wordNormalizer($sentence)));
       
        $sentence = preg_replace('/\p{M}/u', '', $sentence);

        return explode(' ', $sentence);
    }
}
