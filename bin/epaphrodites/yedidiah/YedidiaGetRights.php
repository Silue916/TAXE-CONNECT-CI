<?php

namespace Epaphrodites\epaphrodites\yedidiah;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;
use Epaphrodites\epaphrodites\yedidiah\saveLoad\loadJson;
use Epaphrodites\epaphrodites\yedidiah\treatement\getAndUpdateRights;

class YedidiaGetRights extends epaphroditeClass{

    use loadJson, getAndUpdateRights;

    /**
     * Request to select user right by module and user group.
     * 
     * @param string|null $module
     * @return bool
     */
    public function modules(
       string|null $module = null
    ): bool{
        $result = false;
        $index = $module . ',' . static::class('session')->type();

        $jsonFileDatas = static::loadJsonFile();
        
        foreach ($jsonFileDatas as $key => $value) {
           
            if (is_array($value) && $value['indexModule'] == $index) {
                $result = true;
            }
        }        

        return $result;
    }

   /**
     * Request to select user rights by user type and key.
     * 
     * @param string|null $key
     * @return array
     */
    public function liste_menu(
        string|null $key = null
    ): array{

        $result = [];
        $index = $key . ',' . static::class('session')->type();

        $jsonFileDatas = static::loadJsonFile();

        foreach ($jsonFileDatas as $key => $value) {
            if ($value['indexModule'] === $index) {
                $result[] = $jsonFileDatas[$key];
            }
        }

        return $result;
    }

    /**
     * @param int $usersGroup
     * @return array
     */
    public function getUsersRights(
    int $usersGroup
    ):array{

        return $this->showYediadiahRights($usersGroup);
    }
}