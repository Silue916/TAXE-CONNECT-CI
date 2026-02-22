<?php

namespace Epaphrodites\epaphrodites\yedidiah\treatement;

trait getAndUpdateRights
{

    /**
     * @param array $JsonDatas
     * @param string $usersGroup
     * @param int $state
     * @return bool
     */
    private function updateUsersRightsDatas(
        array $JsonDatas,
        string $usersGroup, 
        int $state
    ):bool{

        $hasChanges = false;

        foreach ($JsonDatas as $key => $value) {

            if (is_array($value) && $value['indexRight'] == $usersGroup) {
                $JsonDatas[$key]['Autorisations'] = $state;
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            static::saveJson($JsonDatas);
        }

        return $hasChanges;
    }

    /**
     * Request to select user right if exist
     * @param string $usersGroup
     * @param string $pages
     * @param array $getJsonArray
     * @return bool
     */
    private function hasAccessRight(
        string $usersGroup, 
        string $pages, 
        array $getJsonArray
    ): bool{
        
        $hasAccess = false;
    
        if (!empty($getJsonArray)) {

            $index = md5($usersGroup . ',' . $pages);

            foreach ($getJsonArray as $value) {

                if ($value['indexRight'] == $index) {
                    $hasAccess = true;
                    break;
                }
            }
        }
    
        return $hasAccess;
    }   
    
    /**
     * @param string $pages
     * @return bool
    */
    private static function checkAuthorization(
    string $pages
    ):bool{
        $actions = false;
        $pages = str_replace( _MAIN_EXTENSION_ , '', $pages);

        $index = md5(static::class('session')->type() . ',' . $pages);
        $jsonFileDatas = static::loadJsonFile();
       
        foreach ($jsonFileDatas as $key => $value) {

            if ($value['indexRight'] == $index) {
                $actions = $value['Autorisations'] == 1 ? true : false;
            }
        }

        return $actions;        
    }  
    
    /**
     * Request to select user rights by user type.
     * @param int $idusersGroup
     * @return array
     */
    private function showYediadiahRights(
        int $idusersGroup
    ): array{

        $result = [];
        $jsonFileDatas = static::loadJsonFile();

       foreach ($jsonFileDatas as $key => $value) {
           
            if (is_array($value) && $value['usersRightsGroup'] == $idusersGroup) {
                $result[] = $value;
            }
        }

        return $result;
    }   
}