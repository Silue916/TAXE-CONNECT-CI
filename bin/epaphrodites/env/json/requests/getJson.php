<?php

namespace Epaphrodites\epaphrodites\env\json\requests;

trait getJson
{
    
    /**
     * Request to select datas from json
     * 
     * @param array $datas
     * @return array
     */
    public function get(array $datas = []): array
    {
        $jsonFileDatas = static::loadJsonFile($this->file);
    
        if (empty($datas)) {
            return $jsonFileDatas;
        }
    
        return array_filter($jsonFileDatas, function ($value) use ($datas) {
            foreach ($datas as $key => $val) {
                if (!isset($value[$key]) || $value[$key] !== $val) {
                    return false;
                }
            }
            return true;
        });
    }
}