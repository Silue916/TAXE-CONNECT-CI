<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\loadSave;

use Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException;

trait dropJson
{

    /**
     * save JSON file.
     * @param string $login
     * @param string $jsonFiles
     * @return bool|null Returns the decoded JSON data as an bool or NULL if there's an issue.
     * @throws epaphroditeException If there's an error in file reading, JSON decoding, or the file is not found.
     */
    private function cleanJsonFile(
        string $login, 
        string $jsonFiles = 'main/userHippocampusModelOne'
    ): bool|null{
        // JSON file path
        $jsonFilePath = _DIR_JSON_DATAS_ . "/modelOne/{$jsonFiles}.json";

        // Load the content of the JSON file
        $jsonDatas = !empty(file_get_contents($jsonFilePath)) ? file_get_contents($jsonFilePath) : "[]";

        // Check for JSON encoding errors
        if ($jsonDatas === false) {
            throw new EpaphroditeException('Erreur lors de la lecture du fichier JSON');
        }

        $jsonDatas = json_decode($jsonDatas, true);

        // Check for JSON decoding errors
        if ($jsonDatas === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new EpaphroditeException('JSON decoding error : ' . json_last_error_msg());
        }

        // Iterate through the array and remove elements corresponding to the login
        foreach ($jsonDatas as $key => $value) {
            if ($value['login'] === $login) {
                unset($jsonDatas[$key]);
            }
        }

        $newJsonString = json_encode($jsonDatas, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // Check for JSON encoding errors after modification
        if ($newJsonString === false) {
            throw new EpaphroditeException('Error encoding JSON after modification');
        }

        $bytesWritten = file_put_contents($jsonFilePath, $newJsonString);

        // Check for file writing errors
        if ($bytesWritten === false) {
            throw new EpaphroditeException('Failed to write to JSON file');
        }

        return true;
    }
}
