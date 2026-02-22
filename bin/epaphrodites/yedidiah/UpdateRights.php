<?php

namespace Epaphrodites\epaphrodites\yedidiah;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;
use Epaphrodites\epaphrodites\yedidiah\saveLoad\loadJson;
use Epaphrodites\epaphrodites\yedidiah\saveLoad\saveJsonDatas;
use Epaphrodites\epaphrodites\yedidiah\treatement\getAndUpdateRights;

class UpdateRights extends epaphroditeClass
{

    use loadJson, saveJsonDatas, getAndUpdateRights;

    /**
     * Request to update users rights
     * @param int|null $usersGroup
     * @param int|null $state
     * @return bool
     */
    public function UpdateUsersRights( 
        string|null $usersGroup = null, 
        int|null $state = null 
    ):bool{

       $JsonDatas = static::loadJsonFile();

       if (!is_array($JsonDatas)) {
        return false;
        }

        $result = $this->updateUsersRightsDatas($JsonDatas, $usersGroup, $state);

        return $result;
    }
}