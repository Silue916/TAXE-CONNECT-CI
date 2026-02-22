<?php

namespace Epaphrodites\epaphrodites\env\csv\requests;

trait updCsv{
    
    
    public function update($conditions, $updateData) {

        foreach ($this->data as &$row) {

            $match = true;

            foreach ($conditions as $key => $value) {

                if (!isset($row[$key]) || $row[$key] != $value) {
                    $match = false;
                    break;
                }
            }

            if ($match) {

                $row = array_merge($row, $updateData);
            }
        }

        $this->saveCSV();
        
        return true;
    }
}