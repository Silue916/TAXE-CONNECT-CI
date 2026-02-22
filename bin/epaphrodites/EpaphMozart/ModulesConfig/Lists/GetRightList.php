<?php

namespace Epaphrodites\epaphrodites\EpaphMozart\ModulesConfig\Lists;

use Epaphrodites\epaphrodites\constant\epaphroditeClass;

class GetRightList extends epaphroditeClass
{

   /** users right list
    * 
    * @return array
    */
   public static function RightList():array
   {

      return [
         [
            'apps' => 'profil',
            'libelle' => "Change password",
            'path' => 'usersFolder/change_password'
         ],
         [
            'apps' => 'profil',
            'libelle' => "Change my informations",
            'path' => 'usersFolder/edit_users_infos'
         ],
         [
            'apps' => 'chatbot',
            'libelle' => 'Start chatbot model one',
            'path' => 'chatsFolder/start_chatbot_model_one',
         ],   
         [
            'apps' => 'chatbot',
            'libelle' => 'Start chatbot model two',
            'path' => 'chatsFolder/start_chatbot_model_two',
         ], 
         [
            'apps' => 'chatbot',
            'libelle' => 'Start chatbot model three',
            'path' => 'chatsFolder/start_chatbot_model_three',
         ], 
         [
            'apps' => 'chatbot',
            'libelle' => 'Start bot writing',
            'path' => 'chatsFolder/start_bot_writing',
         ],                                                       
         [
            'apps' => 'users',
            'libelle' => 'Import system users',
            'path' => 'usersFolder/import_users',
         ], 
         [
            'apps' => 'users',
            'libelle' => 'List of system all Users',
            'path' => 'usersFolder/all_users_list',
         ], 
         [
            'apps' => 'actions',
            'libelle' => 'List of recent actions',
            'path' => 'settingFolder/list_of_recent_actions',
         ],           

        ];
    }
 }