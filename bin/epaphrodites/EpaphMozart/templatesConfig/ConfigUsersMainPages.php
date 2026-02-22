<?php

namespace Epaphrodites\epaphrodites\EpaphMozart\templatesConfig;


class ConfigUsersMainPages
{

    /** 
     * Login interface manager
     */
    public function login():string
    {
        return _LOGIN_;
    }

    /** 
     * Login interface manager
     */
    public function main():string
    {
        return _HOME_;
    }
}