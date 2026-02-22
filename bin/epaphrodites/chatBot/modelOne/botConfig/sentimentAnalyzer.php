<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

use Epaphrodites\epaphrodites\chatBot\modelOne\defaultAnswers\mainEpaphroditesDefaultMessages as msg;

trait sentimentAnalyzer
{
   
    /**
     * @param string $sentence
     * @param string $language
     * @return string
     */
    public function analyzeSentiment(
        string $sentence, 
        string $language = 'eng'
    ): string {
        $msg = new msg;
        $wordNormalizer = $this->cleanText($this->wordNormalizer($sentence));
        $words = explode(' ', strtolower($wordNormalizer));
        $vocabulary = $msg->getVocabulary($language);
        
        $scores = [
            $this->auxiliary_verbs => 0,
            $this->neutral_words => 0,
            $this->negative_words => 0,
            $this->negations => 0,
            $this->attenuators => 0,
            $this->intensifiers => 0
        ];
       
        foreach ($words as $word) {
            $scores[$this->auxiliary_verbs] += $this->isWordInArray($word, $vocabulary['auxiliaryVerbs']);
            $scores[$this->neutral_words] += $this->isWordInArray($word, $vocabulary['neutralWords']);
            $scores[$this->negative_words] += $this->isWordInArray($word, $vocabulary['negativeWords']);
            $scores[$this->negations] += $this->isWordInArray($word, array_merge($vocabulary['negations'], $vocabulary['endNegations']));
            $scores[$this->attenuators] += $this->isWordInArray($word, $vocabulary['attenuators']);
            $scores[$this->intensifiers] += $this->isWordInArray($word, $vocabulary['intensifiers']);
        }
       
        return $this->getSentiment($scores , $language , $msg);
    }

    /**
     * @param string $word
     * @param array $array
     * @return int
     */
    private function isWordInArray(
        string $word, 
        array $array
    ): int {
        return in_array($word, $array) ? 1 : 0;
    }

    /**
     * @param array $scores
     * @param string $language
     * @param object $msg
     * @return string
     */
    private function getSentiment(
        array $scores,
        string $language,
        object $msg
    ): string {
        if (($scores[$this->negations] == 0 && $scores[$this->negative_words] == 0) || 
            ($scores[$this->negative_words] != 0 && $scores[$this->auxiliary_verbs] != 0 && $scores[$this->negations] != 0) ||
            ($scores[$this->negative_words] != 0 && $scores[$this->auxiliary_verbs] == 0 && $scores[$this->negations] != 0)) {
            return '';
        } else {
            
            return $msg->apologizeSentences($language);
        }
    }    
}