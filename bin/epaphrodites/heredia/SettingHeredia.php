<?php

namespace Epaphrodites\epaphrodites\heredia;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;
use Epaphrodites\epaphrodites\heredia\herediaSettingTraits\cookies;
use Epaphrodites\epaphrodites\heredia\herediaSettingTraits\session;
use Epaphrodites\epaphrodites\heredia\herediaSettingTraits\mainLayouts;
use Epaphrodites\epaphrodites\heredia\herediaSettingTraits\adminLayouts;
use Epaphrodites\epaphrodites\heredia\herediaSettingTraits\othersOptions;
use Epaphrodites\epaphrodites\heredia\herediaSettingTraits\mainUsersOptions;
use Epaphrodites\epaphrodites\heredia\herediaSettingTraits\adminUsersOptions;
use Epaphrodites\epaphrodites\Contracts\settingHeredia as ContractsSettingHeredia;

class SettingHeredia extends epaphroditeClass implements ContractsSettingHeredia
{

    use session, cookies, othersOptions, mainLayouts, adminLayouts, mainUsersOptions, adminUsersOptions;

    /**
     * @return void
     */
    private object $msg;
    private object $json;
    private object $datas;
    private object $paths;
    private object $count;
    private object $select;
    private object $layouts;
    private object $session;

    /**
     * Initialize object properties when an instance is created
     * @return void
     */
    public function __construct()
    {
        $this->initializeObjects();
    }

    /**
     * Initialize each property using values retrieved from static configurations
     * @return void
     */
    private function initializeObjects(): void
    {
        $this->msg = $this->getObject(static::$initNamespace, 'msg');
        $this->json = $this->getObject(static::$initNamespace, 'json');
        $this->datas = $this->getObject(static::$initNamespace, 'datas');
        $this->paths = $this->getObject(static::$initNamespace, 'paths');
        $this->count = $this->getObject(static::$initQueryConfig, 'count');
        $this->select = $this->getObject(static::$initQueryConfig, 'select');
        $this->layouts = $this->getObject(static::$initNamespace, 'layout');
        $this->session = $this->getObject(static::$initNamespace, 'session');
    }
}
