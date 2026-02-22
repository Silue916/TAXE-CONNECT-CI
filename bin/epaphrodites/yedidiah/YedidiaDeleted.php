<?php

namespace Epaphrodites\epaphrodites\yedidiah;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;
use Epaphrodites\epaphrodites\yedidiah\saveLoad\loadJson;
use Epaphrodites\epaphrodites\yedidiah\saveLoad\saveJsonDatas;
use Epaphrodites\epaphrodites\yedidiah\treatement\deleteUsersRights;

class YedidiaDeleted extends epaphroditeClass{

    use loadJson, saveJsonDatas, deleteUsersRights;

    /**
     * Request to delete users right by @id
     * 
     * @param string $idUsersRights
     * @return bool
     */
    public function DeletedUsersRights(
        string $idUsersRights
    ):bool{

        $JsonDatas = $this->loadJsonFile();

        if (!is_array($JsonDatas)) {
            return false;
        }

        $result = $this->deletedSelectRights($JsonDatas, $idUsersRights , 'indexRight');
    
        return $result;
    }

    /**
     * Request to delete all users right by users group
     * 
     * @param int $usersGroup
     * @return bool
     */
    public function EmptyAllUsersRight(
        int $usersGroup
    ):bool{

        $JsonDatas = static::loadJsonFile();

        if (!is_array($JsonDatas)) {
            return false;
        }        

        $result = $this->deletedSelectRights($JsonDatas, $usersGroup , 'usersRightsGroup');

        return $result;
    }    
}
