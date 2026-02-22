<?php

namespace Epaphrodites\epaphrodites\yedidiah\treatement;

trait addUsersRights
{

    /**
     * @param int $usersGroup
     * @param string $actions
     * @param string $modules
     * @param string $indexModule
     * @param string $indexRight
     * @return bool
     */
    private function saveUsersRights(
        int $usersGroup, 
        string $actions ,
        string $modules , 
        string $indexModule , 
        string $indexRight
    ):bool{

        $JsonDatas = !empty(file_get_contents(static::JsonDatas())) ? file_get_contents(static::JsonDatas()) : "[]";

        if ($JsonDatas !== false) {
            $JsonDatas = json_decode($JsonDatas, true);            
        }

        $JsonDatas[] = 
            [
                'usersRightsGroup' => $usersGroup,
                'Autorisations' => $actions,
                'Modules' => $modules,
                'indexModule' => $indexModule,
                'indexRight' => md5($indexRight),
            ];            

        static::saveJson($JsonDatas);       
        
        return true;
    }
}