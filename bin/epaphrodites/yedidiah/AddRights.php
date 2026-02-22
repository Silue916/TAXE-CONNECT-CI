<?php

namespace Epaphrodites\epaphrodites\yedidiah;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;
use Epaphrodites\epaphrodites\yedidiah\saveLoad\loadJson;
use Epaphrodites\epaphrodites\yedidiah\saveLoad\saveJsonDatas;
use Epaphrodites\epaphrodites\yedidiah\treatement\addUsersRights;
use Epaphrodites\epaphrodites\yedidiah\treatement\getAndUpdateRights;

class AddRights extends epaphroditeClass{

    use loadJson, saveJsonDatas, addUsersRights, getAndUpdateRights;

    /**
     * Add users rights
     * index ( module , type_user , idpage , action)
     * @param int|null $usersGroup
     * @param string|null $pages
     * @param string|null $actions
     * @return bool
     */
    public function AddUsersRights(
        int|null $usersGroup = null, 
        string|null $modulePage = null,  
        string|null  $actions = null
    ):bool{

        $JsonDatas = static::loadJsonFile();
        $pages = explode( '@', $modulePage);

        if (!empty($usersGroup) && !empty($pages) && $this->hasAccessRight($usersGroup, $pages[1] , $JsonDatas) === false) {

            $this->saveUsersRights($usersGroup, $actions , $pages[0] , "{$pages[0]},{$usersGroup}" , "{$usersGroup},{$pages[1]}");
            return true;
        } else {
            return false;
        }
    }
}