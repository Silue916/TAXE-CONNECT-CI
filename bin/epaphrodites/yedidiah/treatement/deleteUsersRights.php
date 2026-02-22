<?php

namespace Epaphrodites\epaphrodites\yedidiah\treatement;

trait deleteUsersRights
{

    /**
     * @param array $JsonDatas
     * @param string|int $searchType
     * @param string $arrayKey
     * @return bool
    */
    private function deletedSelectRights(
        array $JsonDatas,
        string|int $searchType,
        string $arrayKey
    ):bool{

        $hasChanges = false;

        foreach ($JsonDatas as $key => $value) {

            if (is_array($value) && $value[$arrayKey] == $searchType) {
                unset($JsonDatas[$key]);
                $hasChanges = true;
            }
        }
    
        if ($hasChanges) {
            static::saveJson($JsonDatas);
        }

        return $hasChanges;
    }
}