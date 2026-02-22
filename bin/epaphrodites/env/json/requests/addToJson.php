<?php

namespace Epaphrodites\epaphrodites\env\json\requests;

trait addToJson
{

    private $file;

    /**
     * @param string $file
     * @return self
     */
    public function path(
        string $file
    ):self{
        $this->file = $file;

        return $this;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function add(
        array $data = []
    ):bool{
        
        $JsonDatas = !empty(file_get_contents($this->file)) ? file_get_contents($this->file) : "[]";

        if ($JsonDatas !== false) {
            $JsonDatas = json_decode($JsonDatas, true);
        } else {
            $JsonDatas = [];
        }

        $JsonDatas[] = $data;

        $result = $this->saveJson( $this->file, $JsonDatas );

        return $result ? true : false;
    }
}