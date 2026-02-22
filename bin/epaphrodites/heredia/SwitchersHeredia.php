<?php

namespace Epaphrodites\epaphrodites\heredia;

use Epaphrodites\epaphrodites\yedidiah\Authorize;

class SwitchersHeredia extends Authorize{
    
    /**
     * @param string|null $target
     * @param string|null $url
     * @param null|string $autorize
     * @param bool $runner
     * @return bool|null
     */
    public static function SwitcherPages( 
        string|null $target = null,
        string|null $url = null, 
        bool|null $autorize = null, 
        null $Run = null 
    ){

        if( static::target( $target , $url )===true ){

            $Run = $autorize === true ? static::Authorize($target) : true;
        }

        return $Run;
    }

    /**
     * @param mixed $target
     * @param mixed $url
     * @return bool
    */
    private static function target( 
        $target, 
        $url 
    ){

        return $target . _MAIN_EXTENSION_ === $url ? true : false;
    }

    /**
     * @param string|null $target
     * @param null|string $autorize
     * @param bool $runner
     * @return bool|null
     */
    public function swicthPagesAutorisation( 
        string|null $target = null, 
        bool|null $autorize = null, 
        null $Run = null 
    ){

        $isDashboard = strpos($target, _DASHBOARD_FOLDERS_ ) !== false;
        $selfServicePages = [
            'usersFolder/edit_users_infos_ep',
            'usersFolder/change_password_ep',
        ];
        $roleBasedDirectPages = [
            'usersFolder/create_mairie_account_ep' => [1],
            'usersFolder/create_agent_account_ep' => [2],
            'usersFolder/create_commune_tax_ep' => [2],
            'usersFolder/create_merchant_account_ep' => [3],
            'usersFolder/assign_tax_to_merchant_ep' => [3],
            'usersFolder/delete_agent_account_ep' => [2],
            'usersFolder/all_users_list_ep' => [1, 2],
            'usersFolder/delete_commune_account_ep' => [1],
            'settingFolder/management_of_user_colors_ep' => [1, 2, 3, 4],
            'usersFolder/suspend_mairie_account_ep' => [1],
            'settingFolder/list_of_recent_actions_ep' => [1, 2],
        ];

        if (in_array((string) $target, $selfServicePages, true)) {
            return true;
        }

        if (array_key_exists((string) $target, $roleBasedDirectPages)) {
            $allowedRoles = $roleBasedDirectPages[(string) $target];
            return in_array((int) static::class('session')->type(), $allowedRoles, true);
        }

        $Run = $autorize === true && $isDashboard === false ? static::Authorize($target) : true;

        return $Run;
    }    
}
