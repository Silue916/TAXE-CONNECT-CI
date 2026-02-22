<?php

namespace Epaphrodites\epaphrodites\EpaphMozart\ModulesConfig\Lists;

use Epaphrodites\epaphrodites\EpaphMozart\ModulesConfig\RighstList;

class GetModulesList extends RighstList
{

    /**
     * @return array
     */
    public static function GetModuleList():array
    {

        return 
        [
            'profil' => 'MY PROFILE',
            'search' => 'SEARCH',
            'print' => 'PRINT MANAGEMENT',  
            'import' => 'IMPORT MANAGEMENT',
            'export' => 'EXPORT MANAGEMENT',
            'statistic' => 'STATICS MANAGEMENT',
            'annuaire' => 'DIRECTORY MANAGEMENT',
            'habilit' => 'AUTHORIZATIONS',
            'chatbot' => 'CHATBOT',
            'faq' => 'FAQ (Frequently Asked Questions)',
            'chats' => 'CHAT MESSAGES',
            'users' => 'USERS MANAGEMENT',
            'actions' => 'ACTIONS MANAGEMENT',
            'setting' => 'SYSTEM SETTING',
        ];
     } 
 }