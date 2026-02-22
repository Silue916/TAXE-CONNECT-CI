<?php

namespace Epaphrodites\epaphrodites\env\csv\requests;

trait delCsv{
    
    
    public function delete($conditions) {

        $this->data = array_filter($this->data, function ($row) use ($conditions) {

            foreach ($conditions as $key => $value) {
                if (isset($row[$key]) && $row[$key] == $value) {
                    return false;
                }
            }

            return true;
        });

        $this->saveCSV();

        return true;
    }
}