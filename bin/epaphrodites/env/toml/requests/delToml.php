<?php

namespace Epaphrodites\epaphrodites\env\toml\requests;

trait delToml
{

    /**
     * @param array $datas
     * @return bool
     */
    public function delete(
        array $datas = []
    ): bool
    {
        if (!isset($this->path)) {
            return false;
        }  
        
        $tomlFileDatas = $this->loadTomlFile($this->path);
        
        $tomlFileDatas = $this->translateToArray($tomlFileDatas);

        if (empty($datas) && isset($this->section)) {

            unset($tomlFileDatas[$this->section]);

            $result = $this->translateToToml($tomlFileDatas);

            $this->saveToml($this->path, $result);

            return true;
        }

        if (!empty($datas) && !isset($this->section)) {

            $datasFound = $this->filterArrayDatas($tomlFileDatas, $datas);

            if (!empty($datasFound)) {

                foreach ($datasFound as $key => $value) {
                    if (isset($tomlFileDatas[$key]) && $tomlFileDatas[$key] === $value) {
                        unset($tomlFileDatas[$key]);
                    }
                }

                $result = $this->translateToToml($tomlFileDatas);

                $this->saveToml($this->path, $result);

                return true;
            }
        }

        return false;
    }
}