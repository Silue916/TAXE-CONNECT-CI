<?php

namespace Epaphrodites\epaphrodites\heredia\herediaSettingTraits;

trait mainLayouts{

    /**
     * Set Main layouts params
     * 
     * @return array
     */
    public function MainLayout(): array
    {

        return [
                /*
            |--------------------------------------------------------------------------
            | Set main layout to front in default
            |--------------------------------------------------------------------------
            */
            'layouts' => $this->layouts->main(),
        ];
    }

}