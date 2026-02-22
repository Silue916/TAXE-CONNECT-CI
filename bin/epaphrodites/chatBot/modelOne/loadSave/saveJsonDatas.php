<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\loadSave;

use Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException;

trait saveJsonDatas
{

    /**
     * save JSON file.
     * 
     * @return bool|null Returns the decoded JSON data as an bool or NULL if there's an issue.
     * @throws epaphroditeException If there's an error in file reading, JSON decoding, or the file is not found.
     */
    private function saveJson(
        array $datas = [], 
        string $jsonFiles = 'main/userHippocampusModelOne'
    ): bool{
        // Chemin du fichier JSON
        $jsonFilePath = _DIR_JSON_DATAS_ . "/modelOne/{$jsonFiles}.json";

        if (file_exists($jsonFilePath)) {

            // Convert data to JSON format
            $jsonData = !empty(file_get_contents($jsonFilePath)) ? file_get_contents($jsonFilePath) : "[]";

            // Check if file reading is successful
            if ($jsonData !== false) {
                // Decode the JSON content
                $jsonData = json_decode($jsonData, true);            

            // Check for JSON encoding errors
            if ($jsonData === false) {
                throw new epaphroditeException('Error encoding JSON');
            }

            // Add new datas
            $jsonData[] = reset($datas);

            // Write data to the file
            $bytesWritten = file_put_contents($jsonFilePath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));

            // Check for file writing errors
            if ($bytesWritten === false) {
                throw new epaphroditeException('Failed to write to JSON file');
            }

            return true;

        } else {
            // Handle an error if file reading fails
            throw new epaphroditeException("Error: Unable to read the JSON file.");
        }            

        } else {
            // Handle an error if the JSON file does not exist
            throw new epaphroditeException("Error: JSON file not found.");
        }
    }
}
