<?php

namespace Epaphrodites\epaphrodites\env\toml\requests;

trait getToml
{
 
    /**
     * @param array $datas
     * @return array
     */
    public function get(array $datas = []):array
    {
        $tomlFileDatas = $this->loadTomlFile($this->path);

        $tomlFileDatas = $this->translateToArray($tomlFileDatas);

        $tomlFileDatas = $this->section ? $tomlFileDatas[$this->section] : $tomlFileDatas;

        if (empty($datas)) {
            return $tomlFileDatas;
        }
    
        return $this->filterArrayDatas( $tomlFileDatas , $datas );
    }
}