<?php

namespace Epaphrodites\epaphrodites\env\json\saveLoad;

use Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException;

trait saveJsonDatas
{

    /**
     * save JSON file.
     * @param array $datas
     * @return bool|null Returns the decoded JSON data as an bool.
     * @throws epaphroditeException If there's an error in file reading, JSON decoding, or the file is not found.
     */
    private static function saveJson(
        string $jsonFilePath,
        array $datas = []
    ): bool|null{
  
        // Check if the file exists
        if (file_exists($jsonFilePath)) {

            file_put_contents($jsonFilePath, json_encode($datas,JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));

            return true;
        }else{
            // Handle an error if the JSON file does not exist
            throw new epaphroditeException("Error: JSON file not found.");
        }
    }
}