<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\loadSave;

use Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException;

trait loadUsersAnswers
{

    /**
     * Loads and retrieves data from a JSON file.
     * 
     * @return array|null Returns the decoded JSON data as an array or NULL if there's an issue.
     * @throws epaphroditeException If there's an error in file reading, JSON decoding, or the file is not found.
     */
    private function loadJsonFile(
        string $jsonFiles = "main/userHippocampusModelOne"
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
                $usersAnswers = json_decode($jsonData, true);
               
                // Check if JSON decoding is successful and the result is an array
                if ($usersAnswers !== null && is_array($usersAnswers)) {
                    return $usersAnswers; // Return the decoded data
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
     * Loads and retrieves data from a JSON file.
     * @param string $jsonFiles
     * @return array|null Returns the decoded JSON data as an array or NULL if there's an issue.
     * @throws epaphroditeException If there's an error in file reading, JSON decoding, or the file is not found.
     */
    private function loadOthersJsonFile(
        string $jsonFiles
    ): ?array{
        
        // Path to the JSON file
        $jsonFilePath = _DIR_JSON_DATAS_ . "/{$jsonFiles}.json";
        
        // Check if the file exists
        if (file_exists($jsonFilePath)) {
            // Read the file content
            $jsonData = !empty(file_get_contents($jsonFilePath)) ? file_get_contents($jsonFilePath) : "[]";
            
            // Check if file reading is successful
            if ($jsonData !== false) {
                // Decode the JSON content
                $usersAnswers = json_decode($jsonData, true);
               
                // Check if JSON decoding is successful and the result is an array
                if ($usersAnswers !== null && is_array($usersAnswers)) {
                    return $usersAnswers; // Return the decoded data
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
     * @param string $login
     * @param string $jsonFiles
     * @return array|NULL
    */
    public function lastUsersQuestion(
        string $login, 
        string $jsonFiles = "main/userHippocampusModelOne"
    ):array|NULL {
       
        $lastMessage = NULL;
        $result = $this->loadJsonFile($jsonFiles);

        foreach (array_reverse($result) as $message) {
            if ($message['login'] === $login) {
                $lastMessage = $message;
                break;
            }
        }

        $result = is_array($lastMessage)&&$lastMessage["previous"]===true ? $lastMessage : NULL;

        return $result;
    }
}
