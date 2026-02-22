<?php

namespace Epaphrodites\epaphrodites\heredia\herediaSettingTraits;

trait adminUsersOptions{

 /**
     * Set admin_init layouts params
     * 
     * @return array
     */
    public function AdminInitMainLayouts(): array
    {
        $mairieUnreadCount = 0;
        $superAdminMairieUnreadCount = 0;
        $agentUnreadCount = 0;
        if ((int) $this->session->type() === 1 && isset($this->select)) {
            try {
                $superAdminMairieUnreadCount = (int) $this->select->countUnreadMairieMessages(
                    (string) $this->session->login()
                );
            } catch (\Throwable $exception) {
                $superAdminMairieUnreadCount = 0;
            }
        }
        if ((int) $this->session->type() === 2 && isset($this->select)) {
            try {
                $mairieUnreadCount = (int) $this->select->countUnreadAgentMessages(
                    (string) $this->session->login()
                ) + (int) $this->select->countUnreadMairieMessages(
                    (string) $this->session->login()
                );
            } catch (\Throwable $exception) {
                $mairieUnreadCount = 0;
            }
        }
        if (in_array((int) $this->session->type(), [3, 4], true) && isset($this->select)) {
            try {
                $agentUnreadCount = (int) $this->select->countUnreadAgentMessages(
                    (string) $this->session->login()
                );
            } catch (\Throwable $exception) {
                $agentUnreadCount = 0;
            }
        }

        return
            [

            /*
            |--------------------------------------------------------------------------
            | Set datas to front in default
            |--------------------------------------------------------------------------
            */
                'data' => $this->datas,

            /*
            |--------------------------------------------------------------------------
            | Set path to front in default
            |--------------------------------------------------------------------------
            */
                'path' => $this->paths,

            /*
            |--------------------------------------------------------------------------
            | Set messages text to front in default
            |--------------------------------------------------------------------------
            */
                'messages' => $this->msg,

            /*
            |--------------------------------------------------------------------------
            | Set message layout to front in default
            |--------------------------------------------------------------------------
            */
                'message' => $this->layouts->msg(),

            /*
            |--------------------------------------------------------------------------
            | Set form to front in default
            |--------------------------------------------------------------------------
            */
                'forms' => $this->layouts->forms(),

            /*
            |--------------------------------------------------------------------------
            | Set login (Choose Name and surname) to front in default
            |--------------------------------------------------------------------------
            */
                'login' => $this->session->nameSurname(),

            /*
            |--------------------------------------------------------------------------
            | Set pagination layout to front in default
            |--------------------------------------------------------------------------
            */
                'pagination' => $this->layouts->pagination(),

            /*
            |--------------------------------------------------------------------------
            | Set ajax layout to front in default
            |--------------------------------------------------------------------------
            */
                'ajax' => $this->layouts->ajax(),

            /*
            |--------------------------------------------------------------------------
            | Set charts layout to front in default
            |--------------------------------------------------------------------------
            */
            'charts' => $this->layouts->charts(),

            /*
            |--------------------------------------------------------------------------
            | Set tools layout to front in default
            |--------------------------------------------------------------------------
            */
            'tools' => $this->layouts->tools(),            

            /*
            |--------------------------------------------------------------------------
            | Set breadcrumb layout to front in default
            |--------------------------------------------------------------------------
            */
                'breadcrumb' => $this->layouts->breadcrumbs(),

            /*
            |--------------------------------------------------------------------------
            | Set dashbord model to front in default
            |--------------------------------------------------------------------------
            */
                '_DASHMODEL_' => $this->getUsersDashboardColor(),

            /*
            |--------------------------------------------------------------------------
            | Set current connected user group id for role-based UI rendering
            |--------------------------------------------------------------------------
            */
                'current_user_type' => $this->session->type(),

            /*
            |--------------------------------------------------------------------------
            | Mairie messaging unread count (header icons)
            |--------------------------------------------------------------------------
            */
                'mairie_unread_count' => $mairieUnreadCount,
                'super_admin_mairie_unread_count' => $superAdminMairieUnreadCount,
                'agent_unread_count' => $agentUnreadCount,

            ];
    }

}
