<?php

namespace Epaphrodites\epaphrodites\env\csv\requests;

trait getCsv{
    
    public function select(
        array $conditions = []
    ) {

        $result = array_filter($this->data, function ($row) use ($conditions) {

            foreach ($conditions as $key => $value) {

                if (!isset($row[$key]) || $row[$key] != $value) {
                    return false;
                }
            }

            return true;

        });

        return $result;
    }
}