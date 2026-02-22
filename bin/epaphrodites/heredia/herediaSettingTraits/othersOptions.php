<?php

namespace Epaphrodites\epaphrodites\heredia\herediaSettingTraits;

trait othersOptions{

    /**
     * Set others options
     * 
     * @return array
     */
    public function others_options(): array
    {
        return
            [

                /*
            |--------------------------------------------------------------------------
            | Secure session
            |--------------------------------------------------------------------------
            |
            | Supported: "true", "false"
            |
            */
                'secure' => true
            ];
    }   
    
    /**
     * Users dashboard color
     * 
     * @return string
     */
    public function getUsersDashboardColor(): string
    {
        $colorsPath = $this->json->path(_DIR_COLORS_PATH_);
        $login = (string) $this->session->login();

        $userRows = $login !== '' ? array_values($colorsPath->get(['login' => $login])) : [];
        $userColor = $userRows[0]['color'] ?? null;
        if (!empty($userColor)) {
            return (string) $userColor;
        }

        $groupRows = array_values($colorsPath->get(['usersGroup' => $this->session->type()]));
        return $groupRows[0]['color'] ?? 'main';
    }    
}
