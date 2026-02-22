<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\update;

use Epaphrodites\database\requests\typeRequest\noSqlRequest\update\update as UpdateUpdate;

class update extends UpdateUpdate
{

    /**
     * Request to update users password
     *
     * @param string $OldPassword
     * @param string $NewPassword
     * @param string $confirmdp
     * @return int|bool
     */
    public function sqlChangeUsersPassword(
        string $OldPassword, 
        string $NewPassword, 
        string $confirmdp
    ):int|bool
    {

        if (static::initConfig()['guard']->GostCrypt($NewPassword) === static::initConfig()['guard']->GostCrypt($confirmdp)) {

            $result = static::initQuery()['auth']->findSqlUsers(static::initNamespace()['session']->login());

            if (!empty($result)) {

                $result = static::initNamespace()['env']->dictKeyToLowers($result);

                if (static::initConfig()['guard']->AuthenticatedPassword($result[0]["password"], $OldPassword) === true) {

                    $this->table('usersaccount')
                        ->set(['password'])
                        ->where('login')
                        ->param([static::initConfig()['guard']->CryptPassword($NewPassword), static::initNamespace()['session']->login()])
                        ->UQuery();

                    $actions = "Change password : " . static::initNamespace()['session']->login();
                    static::initQuery()['setting']->ActionsRecente($actions);

                    $this->desconnect = static::initNamespace()['paths']->logout();

                    header("Location: $this->desconnect ");
                    exit;
                } else {
                    return 3;
                }
            } else {

                return 2;
            }
        } else {
            return 1;
        }
    }

    /**
     * Update user password and user group
     *
     * @param string|null $login
     * @param string|null $password
     * @param int|null $usersGroup
     * @return bool
     */
    public function sqlConsoleUpdateUsers(
        string|null $login = null, 
        string|null $password = null, 
        int|null $usersGroup = null
    ): bool
    {
        $GetDatas = static::initQuery()['getid']->sqlGetUsersDatas($login);

        if (!empty($GetDatas)) {

            $password = $password !== null ? $password : $login;
            $usersGroup = $usersGroup !== null ? $usersGroup : $GetDatas[0]['usersgroup'];

            $this->table('usersaccount')
                ->set(['password', 'usersgroup'])
                ->where('login')
                ->param([static::initConfig()['guard']->CryptPassword($password), $usersGroup, "$login"])
                ->UQuery();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Request to initialize user password
     *
     * @param string $UsersLogin
     * @return bool
     */
    public function sqlInitUsersPassword(
        string $UsersLogin
    ): bool
    {

        $this->table('usersaccount')
            ->set(['password'])
            ->where('login')
            ->param([static::initConfig()['guard']->CryptPassword($UsersLogin), $UsersLogin])
            ->UQuery();

        $actions = "Reset user password : " . $UsersLogin;
        static::initQuery()['setting']->ActionsRecente($actions);

        return true;
    }

    /**
     * Request to update users group
     *
     * @param string $UsersLogin
     * @param integer $UsersLogin
     * @return bool
     */
    public function sqlToUpdateUsersGroup(
        string $UsersLogin,
        int $usersGroup
    ): bool
    {

        $this->table('usersaccount')
            ->set(['usersgroup'])
            ->where('login')
            ->param([$usersGroup, $UsersLogin])
            ->UQuery();

        $actions = "Update user group : " . $UsersLogin;
        static::initQuery()['setting']->ActionsRecente($actions);

        return true;
    }    

    /**
     * Request to switch user connexion state (For: Oracle)
     *
     * @param integer $login
     * @return bool
     */
    public function sqlUpdateOracleUsersState(
        string $login
    ): bool
    {
        
        $GetUsersDatas = static::initQuery()['getid']->sqlGetUsersDatas($login);

        if (!empty($GetUsersDatas)) {

            $GetUsersDatas = static::initNamespace()['env']->dictKeyToLowers($GetUsersDatas);

            $state = !empty($GetUsersDatas[0]['state']) ? 0 : 1;

            $etatExact = "Close";

            if ($state == 1) {
                $etatExact = "Open";
            }

            $this->table('usersaccount')
                ->set(['state'])
                ->like('login')
                ->param([$state, $GetUsersDatas[0]['login']])
                ->UQuery();
              
            $actions = $etatExact . " of the user's account : " . $GetUsersDatas[0]['login'];
            static::initQuery()['setting']->ActionsRecente($actions);
           
            return true;
        } else {
            return false;
        }
    }

    /**
     * Request to switch user connexion state (For: mysql/postgres/sqlServer/sqLite)
     *
     * @param integer $login
     * @return bool
     */
    public function sqlUpdateUsersState(
        string $login
    ): bool
    {
        
        $GetUsersDatas = static::initQuery()['getid']->sqlGetUsersDatas($login);

        if (!empty($GetUsersDatas)) {

            $state = !empty($GetUsersDatas[0]['state']) ? 0 : 1;

            $etatExact = "Close";

            if ($state == 1) {
                $etatExact = "Open";
            }

            $this->table('usersaccount')
                ->set(['state'])
                ->like('login')
                ->param([$state, $GetUsersDatas[0]['login']])
                ->UQuery();
              
            $actions = $etatExact . " of the user's account : " . $GetUsersDatas[0]['login'];
            static::initQuery()['setting']->ActionsRecente($actions);
           
            return true;
        } else {
            return false;
        }
    }

    /**
     * Request to update user datas
     *
     * @param string $usersname
     * @param string $email
     * @param string $number
     * @return mixed
     */
    public function sqlUpdateUserDatas(
        string $usersname, 
        string $email, 
        string $number,
        int $otpStatut
    ):mixed
    {
        
        if (static::initNamespace()['verify']->onlyNumber($number, 11) === false) {

            $this->table('usersaccount')
                ->set(['contact', 'email', 'namesurname', 'state', 'otp'])
                ->where('login')
                ->param([$number, $email, $usersname, 1, $otpStatut, static::initNamespace()['session']->login()])
                ->UQuery();

            $_SESSION["usersname"] = $usersname;

            $_SESSION["contact"] = $number;

            $_SESSION["email"] = $email;

            $actions = "Edit Personal Information : " . static::initNamespace()['session']->login();
            static::initQuery()['setting']->ActionsRecente($actions);

            $this->desconnect = static::initNamespace()['paths']->dashboard();

            header("Location: $this->desconnect ");
            exit;
        } else {
            return false;
        }
    }

    /**
     * Suspend mairie account login by commune name.
     *
     * @param string $communeName
     * @return bool
     */
    public function sqlSuspendMairieAccountByCommune(
        string $communeName
    ): bool
    {
        $communeName = trim($communeName);
        if ($communeName === '') {
            return false;
        }

        $rows = $this->chaine(
            "SELECT u.login
             FROM users u
             INNER JOIN communes c ON c.id = u.commune_id
             WHERE u.role = 'MAIRIE_ADMIN'
             AND LOWER(c.name) = LOWER(?)
             LIMIT 1"
        )->param([$communeName])->getQuery();

        $rows = static::initNamespace()['env']->dictKeyToLowers(is_array($rows) ? $rows : []);
        $login = trim((string) ($rows[0]['login'] ?? ''));
        if ($login === '') {
            return false;
        }

        $this->table('users')
            ->set(['status'])
            ->where('login')
            ->param(['SUSPENDED', $login])
            ->UQuery();

        $this->table('usersaccount')
            ->set(['state'])
            ->where('login')
            ->param([0, $login])
            ->UQuery();

        $actions = "Suspend mairie account : " . $login . " (" . $communeName . ")";
        static::initQuery()['setting']->ActionsRecente($actions);

        return true;
    }

    /**
     * Reactivate mairie account login by commune name.
     *
     * @param string $communeName
     * @return bool
     */
    public function sqlReactivateMairieAccountByCommune(
        string $communeName
    ): bool
    {
        $communeName = trim($communeName);
        if ($communeName === '') {
            return false;
        }

        $rows = $this->chaine(
            "SELECT u.login
             FROM users u
             INNER JOIN communes c ON c.id = u.commune_id
             WHERE u.role = 'MAIRIE_ADMIN'
             AND LOWER(c.name) = LOWER(?)
             LIMIT 1"
        )->param([$communeName])->getQuery();

        $rows = static::initNamespace()['env']->dictKeyToLowers(is_array($rows) ? $rows : []);
        $login = trim((string) ($rows[0]['login'] ?? ''));
        if ($login === '') {
            return false;
        }

        $this->table('users')
            ->set(['status'])
            ->where('login')
            ->param(['ACTIVE', $login])
            ->UQuery();

        $this->table('usersaccount')
            ->set(['state'])
            ->where('login')
            ->param([1, $login])
            ->UQuery();

        $actions = "Reactivate mairie account : " . $login . " (" . $communeName . ")";
        static::initQuery()['setting']->ActionsRecente($actions);

        return true;
    }
}
