<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\loadSave;

use Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException;

trait loadJson
{

    /**
     * Loads and retrieves data from a JSON file.
     * @param string $jsonFiles
     * @return array|null Returns the decoded JSON data as an array or NULL if there's an issue.
     * @throws epaphroditeException If there's an error in file reading, JSON decoding, or the file is not found.
     */
    private function loadJsonFile(
        string $jsonFiles = 'main/chatbotModelOneKnowledge'
    ): ?array{

        // Path to the JSON file
        $jsonFilePath = _DIR_JSON_DATAS_ . "/modelOne/{$jsonFiles}.json";

        // Check if the file exists
        if (file_exists($jsonFilePath)) {
            // Read the file content
            $jsonData = !empty(file_get_contents($jsonFilePath)) ? file_get_contents($jsonFilePath) : "[]";
           
            // Check if file reading is successful
            if ($jsonData !== false) {
                // Decode the JSON content
                $questionsAnswers = json_decode($jsonData, true);
             
                // Check if JSON decoding is successful and the result is an array
                if ($questionsAnswers !== null && is_array($questionsAnswers)) {
                    
                    return $questionsAnswers; // Return the decoded data
                } else {
                    // Handle an error if JSON decoding fails or the data type is not an array
                    throw new epaphroditeException("Error: Unable to decode the JSON file or the data type is not an array.");
                }
            } else {
                // Handle an error if file reading fails
                throw new epaphroditeException("Error: Unable to read the JSON file.");
            }
        } else {
            // Handle an error if the JSON file does not exist
            throw new epaphroditeException("Error: JSON file not found.");
        }
    }

    /**
     * Checking language answers
     * @param string $language
     * @param string $jsonFiles
     * @return array 
    */    
    private function getContenAccordingLanguage(
        string $language = "eng", 
        string $jsonFiles = 'main/chatbotModelOneKnowledge'
    ):array{

        $result = [];
        $jsonFilesResult = $this->loadJsonFile($jsonFiles);

        foreach ($jsonFilesResult as $key => $value) {
            if ($value['language'] == $language) {
                $result[] = $value;
            }
        }
        return $result;
    }
}
