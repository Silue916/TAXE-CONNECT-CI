<?php

namespace Epaphrodites\epaphrodites\heredia\herediaSettingTraits;

trait adminLayouts{

    /**
     * Set admin layouts params
     * 
     * @return array
     */
    public function AdminLayout(): array
    {

        return [
                /*
            |--------------------------------------------------------------------------
            | Set admin layout to front in default
            |--------------------------------------------------------------------------
            */
            'layouts' => $this->layouts->admin($this->session->type()),
        ];
    }

}