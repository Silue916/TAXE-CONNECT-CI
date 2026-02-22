<?php

namespace Epaphrodites\epaphrodites\Console\Setting;

class OutputDirectory
{

    /**
     * @param null|string $Key
     * @return string
     */
    public static function Files(?string $Key = null)
    {
        $result = false;

        $list = [
            'views' => 'public/views',
            'main' => 'bin/views/main',
            'admin' => 'bin/views/admin',
            'json' => 'bin/database/datas/json',
            'schema' => 'bin/database/gearShift',
            'request' => 'bin/database/requests',
            'python' => 'bin/epaphrodites/python',
            'console' => 'bin/epaphrodites/Console',
            'startsession' => 'bin/epaphrodites/auth',
            'crsfsecure' => 'bin/epaphrodites/CsrfToken',
            'controlleur' => 'bin/controllers/controllers',
            'controllermaps' => 'bin/controllers/controllerMap',
            'count' => 'bin/database/requests/mainRequest/count',
            'insert' => 'bin/database/requests/mainRequest/insert',
            'update' => 'bin/database/requests/mainRequest/update',
            'delete' => 'bin/database/requests/mainRequest/delete',
            'select' => 'bin/database/requests/mainRequest/select',
            'rightlist' => 'bin/epaphrodites/EpaphMozart/ModulesConfig/Lists/GetRightList',
            'modulelist' => 'bin/epaphrodites/EpaphMozart/ModulesConfig/Lists/GetModulesList',
        ];

        foreach ($list as $ListKey => $value) {
            if ($ListKey == $Key) {
                $result = $value;
            }
        }
        return $result;
    }
}
