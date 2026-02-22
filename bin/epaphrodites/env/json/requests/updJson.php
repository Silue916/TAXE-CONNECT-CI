<?php

namespace Epaphrodites\epaphrodites\env\json\requests;

trait updJson
{

    private array $whereConditions;

    /**
     * @param array $conditions
     * @return self
     */
    public function where(
        array $conditions
    ):self{
        $this->whereConditions = $conditions;
        return $this;
    }
    
    /**
     * @param array $datas
     * @return bool
     */
    public function update(
        array $datas
    ): bool{

        if (empty($datas)) {
            return false;
        }
    
        $jsonFileDatas = static::loadJsonFile($this->file);
    
        $isUpdated = false;
    
        foreach ($jsonFileDatas as &$item) {

            $isMatch = true;
    
            foreach ($this->whereConditions as $key => $value) {
                if (!array_key_exists($key, $item) || $item[$key] !== $value) {
                    $isMatch = false;
                    break;
                }
            }
    
            if ($isMatch) {
                $item = array_merge($item, $datas);
                $isUpdated = true;
            }
        }
    
        $this->whereConditions = [];
    
        if ($isUpdated) {
            static::saveJson($this->file, $jsonFileDatas);
            return true;
        }
    
        return false;
    } 
}