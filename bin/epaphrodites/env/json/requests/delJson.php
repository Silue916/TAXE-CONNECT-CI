<?php

namespace Epaphrodites\epaphrodites\env\json\requests;

trait delJson
{

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $jsonFileDatas = static::loadJsonFile($this->file);

        $isDeleted = false;

        foreach ($jsonFileDatas as $key => $item) {
            
            $isMatch = true;

            foreach ($this->whereConditions as $conditionKey => $conditionValue) {
                if (!array_key_exists($conditionKey, $item) || $item[$conditionKey] !== $conditionValue) {
                    $isMatch = false;
                    break;
                }
            }

            if ($isMatch) {
                unset($jsonFileDatas[$key]);
                $isDeleted = true;
            }
        }

        $this->whereConditions = [];

        if ($isDeleted) {
            static::saveJson($this->file, $jsonFileDatas);
            return true;
        }

        return false;
    }
}