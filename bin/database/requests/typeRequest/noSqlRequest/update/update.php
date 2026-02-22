<?php

namespace Epaphrodites\database\requests\typeRequest\noSqlRequest\update;

use Epaphrodites\database\query\Builders;

class update extends Builders
{
    protected $desconnect;

    /**
     * Update users informations
     *
     * @param string $usersname
     * @param string $email
     * @param string $number
     * @return bool
     */
    public function noSqlUpdateUserDatas(
        string $usersname, 
        string $email, 
        string $number,
        int $otpStatut
    ): bool
    {

        $filter = [
            'login' => static::initNamespace()['session']->login(),
        ];

        $update = [
            '$set' => 
            [
                'contact' => $number,
                'email' => $email,
                'namesurname' => $usersname,
                'state' => 1,
                'otp' => $otpStatut
            ],
        ];   

        $this->db(1)->selectCollection('usersaccount')->updateMany($filter, $update);

        if (static::initNamespace()['verify']->onlyNumber($number, 11) === false) {


            $_SESSION["usersname"] = $usersname;

            $_SESSION["contact"] = $number;

            $_SESSION["email"] = $email;
            
            $actions = "Edit Personal Information : " . static::initNamespace()['session']->login();

            static::initQuery()['setting']->noSqlActionsRecente($actions);            

            $this->desconnect = static::initNamespace()['paths']->dashboard();

            header("Location: $this->desconnect ");
            exit;
        } else {
            return false;
        }                    
    }  

   /**
     * Update users informations
     *
     * @param string $usersname
     * @param string $email
     * @param string $number
     * @return bool
     */
    public function noSqlRedisUpdateUserDatas(
        string $usersname, 
        string $email, 
        string $number,
        int $otpStatut
    ): bool
    {

        $login = static::initNamespace()['session']->login();

        $datas = [
            'contact' => $number,
            'email' => $email,
            'namesurname' => $usersname,
            'state' => 1,
            'otp' => $otpStatut
        ];   

        $this->key('usersaccount')->index($login)->rset($datas)->updRedis();

        if (static::initNamespace()['verify']->onlyNumber($number, 11) === false) {

            $_SESSION["usersname"] = $usersname;

            $_SESSION["contact"] = $number;

            $_SESSION["email"] = $email;
            
            $actions = "Edit Personal Information : " . static::initNamespace()['session']->login();

            static::initQuery()['setting']->noSqlRedisActionsRecente($actions);            

            $this->desconnect = static::initNamespace()['paths']->dashboard();

            header("Location: $this->desconnect ");
            exit;
        } else {
            return false;
        }                    
    }      
    
    /**
     * Update users state
     *
     * @param string $login
     * @return bool
     */
    public function noSqlUpdateEtatsUsers(
        string $login
    ): bool
    {

        $GetUsersDatas = static::initQuery()['getid']->noSqlGetUsersDatas($login);

        if (!empty($GetUsersDatas)) {

            $state = !empty($GetUsersDatas[0]['state']) ? 0 : 1;

            $etatExact = "Close";

            if ($state == 1) {
                $etatExact = "Open";
            }

            $filter = [ 'login' => $GetUsersDatas[0]['login'] ];
    
            $update = [
                '$set' => [ 'state' => $state ]
            ];   
    
            $this->db(1)->selectCollection('usersaccount')->updateMany($filter, $update);

            $actions = $etatExact . " of the user's account : " . $GetUsersDatas[0]['login'];

            static::initQuery()['setting']->noSqlActionsRecente($actions);

            return true;
        } else {
            return false;
        }
    }   

   /**
     * Update users state
     *
     * @param string $usersname
     * @param string $email
     * @param string $number
     * @return bool
     */
    public function noSqlRedisUpdateEtatsUsers(
         $login
    ): bool
    {

        $GetUsersDatas = static::initQuery()['getid']->noSqlRedisGetUsersDatas($login); 

        if (!empty($GetUsersDatas)) {

            $state = !empty($GetUsersDatas[0]['state']) ? 0 : 1;

            $etatExact = "Close";

            if ($state == 1) {
                $etatExact = "Open";
            }

            $datas = [
                'state' => $state,
            ];  
    
            $this->key('usersaccount')->index($login)->rset($datas)->updRedis();

            $actions = $etatExact . " of the user's account : " . $GetUsersDatas[0]['login'];

            static::initQuery()['setting']->noSqlRedisActionsRecente($actions);

            return true;
        } else {
            return false;
        }
    }  

    /**
     * Reinitialize user password
     *
     * @param string $UsersLogin
     * @return bool
     */
    public function noSqlInitUsersPassword(
        string $UsersLogin
    ): bool
    {

        $filter = [ 'login' => $UsersLogin ];
    
        $update = [
            '$set' => [ 'password' => static::initConfig()['guard']->CryptPassword($UsersLogin) ]
        ];   

        $this->db(1)->selectCollection('usersaccount')->updateMany($filter, $update);

        $actions = "Reset user password : " . $UsersLogin;

        static::initQuery()['setting']->noSqlActionsRecente($actions);

        return true;
    } 

    /**
     * Update users group
     *
     * @param string $UsersLogin
     * @param int $usersGroup
     * @return bool
     */
    public function noSqlToUpdateUsersGroup(
        string $UsersLogin,
        int $usersGroup
    ): bool
    {

        $filter = [ 'login' => $UsersLogin ];
    
        $update = [
            '$set' => [ 'usersgroup' => $usersGroup ]
        ];   

        $this->db(1)->selectCollection('usersaccount')->updateMany($filter, $update);

        $actions = "Update user group : " . $UsersLogin;
        static::initQuery()['setting']->noSqlActionsRecente($actions);

        return true;
    } 
    
    /**
     * Reinitialize user password
     *
     * @return bool
     */
    public function noSqlRedisInitUsersPassword(
         $login
    ): bool
    {

        $datas = 
        [
            'password' => static::initConfig()['guard']->CryptPassword($login) ,
        ];  
    
        $this->key('usersaccount')->index($login)->rset($datas)->updRedis();

        $actions = "Reset user password : " . $login;

        static::initQuery()['setting']->noSqlRedisActionsRecente($actions);

        return true;
    }  
    
    /**
     * Update users group
     *
     * @return bool
     */
    public function noSqlRedisToUpdateUsersGroup(
         $login,
         $usersGroup
    ): bool
    {

        $datas = 
        [
            'usersgroup' => $usersGroup ,
        ];  
    
        $this->key('usersaccount')->index($login)->rset($datas)->updRedis();

        $actions = "Update user group : " . $login;

        static::initQuery()['setting']->noSqlRedisActionsRecente($actions);

        return true;
    }     
    
    /**
     * Update user password
     *
     * @param string $OldPassword
     * @param string $NewPassword
     * @param string $confirmdp
     * @return bool
     */
    public function noSqlChangeUsersPassword( 
        string $OldPassword, 
        string $NewPassword, 
        string $confirmdp
    ): bool
    {

        if (static::initConfig()['guard']->GostCrypt($NewPassword) === static::initConfig()['guard']->GostCrypt($confirmdp)) {

            $result = static::initQuery()['auth']->findNosqlUsers( static::initNamespace()['session']->login() );

            if (!empty($result)) {

                if (static::initConfig()['guard']->AuthenticatedPassword($result[0]["password"], $OldPassword) === true) {

                    $filter = [ 'login' => static::initNamespace()['session']->login() ];
    
                    $update = [
                        '$set' => [ 'password' => static::initConfig()['guard']->CryptPassword($NewPassword) ]
                    ];   
            
                    $this->db(1)->selectCollection('usersaccount')->updateMany($filter, $update);
            
                    $actions = "Change password : " . static::initNamespace()['session']->login() ;

                    static::initQuery()['setting']->noSqlActionsRecente($actions);

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
     * Update user password
     *
     * @param string $OldPassword
     * @param string $NewPassword
     * @param string $confirmdp
     * @return bool
     */
    public function noSqlRedisChangeUsersPassword( 
        string $OldPassword, 
        string $NewPassword, 
        string $confirmdp
    ): bool
    {
        if (static::initConfig()['guard']->GostCrypt($NewPassword) === static::initConfig()['guard']->GostCrypt($confirmdp)) {

            $login = static::initNamespace()['session']->login();

            $result = static::initQuery()['auth']->findNosqlRedisUsers( $login );

            if (!empty($result)) {

                if (static::initConfig()['guard']->AuthenticatedPassword($result[0]["password"], $OldPassword) === true) {

                    $datas = 
                    [
                        'password' => static::initConfig()['guard']->CryptPassword($NewPassword) ,
                    ];  
                
                    $this->key('usersaccount')->index($login)->rset($datas)->updRedis();
            
                    $actions = "Change password : " . $login;
            
                    static::initQuery()['setting']->noSqlRedisActionsRecente($actions);

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
     * @param string $login
     * @param string $password
     * @param int $usersGroup
     * @return bool
     */
    public function noSqlConsoleUpdateUsers(
        string $login , 
        string $password , 
        int $usersGroup 
    ): bool
    {
        $GetDatas = static::initQuery()['getid']->noSqlGetUsersDatas($login);

        if (!empty($GetDatas)) {

            $password = $password !== NULL ? $password : $login;
            $usersGroup = $usersGroup !== NULL ? $usersGroup : $GetDatas[0]['usersgroup'];

            $filter = [ 'login' => $login ];
    
            $update = [
                '$set' => [ 'password' => static::initConfig()['guard']->CryptPassword($password), 'usersgroup' => $usersGroup ]
            ];   
    
            $this->db(1)->selectCollection('usersaccount')->updateMany($filter, $update);
    
            $actions = "Edit Personal Information : " . $login ;
            
            static::initQuery()['setting']->noSqlActionsRecente($actions);

            return true;
        } else {
            return false;
        }
    }
}