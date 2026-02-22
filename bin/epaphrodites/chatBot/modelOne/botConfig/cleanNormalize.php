<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

use Normalizer;

trait cleanNormalize
{
    /**
     * Cleans and normalizes a text by removing special characters and converting to lowercase,
     * then splits it into an array of words.
     *
     * @param string $text The input text to be cleaned and normalized.
     * @return array An array containing the cleaned and normalized words.
     */
    private function cleanAndNormalize(
        string $text
    ): array{
        $cleanText = $this->cleanText($this->wordNormalizer($text));

        return $this->splitTextIntoWords($cleanText);
    }

    /**
     * Cleans the text by removing special characters and converting to lowercase.
     *
     * @param string $text The text to clean.
     * @return string The cleaned text.
     */
    private function cleanText(
        string $text
    ): string{
        $cleanedText = preg_replace("/(?<=\s|^)'(\w+)/", '$1', $text);
        return strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleanedText));
    }
    
    /**
     * @param string $word
     * @return string
     */
    private function wordNormalizer(
        string $word
    ):string {

        $normalize = Normalizer::normalize($word, Normalizer::FORM_D);
    
        $normalize = preg_replace('/\p{Mn}/u', '', $normalize);

        return json_encode($normalize, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Splits the cleaned text into an array of words.
     *
     * @param string $cleanText The cleaned text.
     * @return array An array containing the words.
     */
    private function splitTextIntoWords(
        string $cleanText
    ): array{
        $arrayDatas = explode(" ", $cleanText);

        return $this->filterWords($arrayDatas);
    }

    /**
     * Filters out common words from the array of words.
     *
     * @param array $words The array of words to filter.
     * @return array The filtered array of words.
     */
    private function filterWords(
        array $words
    ): array{
        $wordsToRemove = $this->getWordsToRemove();
        return array_diff($words, $wordsToRemove);
    }

    /**
     * Get the common words to remove from the text.
     *
     * @return array The array of common words to remove.
     */
    private function getWordsToRemove(): array
    {
        return array_merge( $this->frenchWord() , $this->englishWord());
    }

    /**
     * @return array
    */    
    private function frenchWord():array
    {
        return 
        [
            'le', 'la', 'les', 'des', 'une', 'un', 'l\'', 'a', 'ce', 'cette', 'ces', 'celui', 'celle', 'ceux', 'celles', 'un', 'sur', 'es' , 'est' , 'sont' , 'sommes',
            'une', 'des', 'du', 'de', 'la', 'le', 'les', 'leur', 'leurs', 'eux', 'elle', 'elles', 'on', 'moi' ,'je' , 'toi', 'tu', 
            'nous', 'vous', 'se', 'me', 'te', 'lui', 'leur', 'y', 'en', 'où',
        ];
    }

    /**
     * @return array
    */
    private function englishWord():array
    {   
        return [
            'the', 'of', 'a', 'to', 'this', 'these', 'that', 'those', 'on', 'are', 'is', 'some','their', 'him', 'them', 'her', 'them', 'one', 'me', 'i', 'you', 
            'oneself', 'we', 'them', 'there', 'where', 'it'
        ];
    } 
}