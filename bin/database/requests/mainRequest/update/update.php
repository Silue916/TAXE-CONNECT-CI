<?php

namespace Epaphrodites\database\requests\mainRequest\update;

use Epaphrodites\database\requests\typeRequest\sqlRequest\update\update as UpdateUpdate;

final class update extends UpdateUpdate
{

  /**
   * Request to update users rights
   * 
   * @param int|null $usersGroup
   * @param int|null $etat
   * @return bool
   */
  public function updateUserRights(
    string|int|null $usersRightSelected = null, 
    int|null $state = null,
    int|null $usersGroup = null
  ): bool{

      $stateLabel = $state == 1 ? 'Granted' : 'Restricted';

      if(static::initConfig()['updright']->UpdateUsersRights($usersRightSelected, $state) == true ){

        $config = static::initQuery()['setting'];
        $actions = "This users group is {$stateLabel} : " . static::initNamespace()['datas']->usersGroup($usersGroup, 'label');

        match (_FIRST_DRIVER_) {

          'mongodb' => $config->noSqlActionsRecente($actions),
          'redis' => $config->noSqlRedisActionsRecente($actions),
    
          default => $config->ActionsRecente($actions),
        };

        return true;
      }
    
    return false;

  }

  /**
   * Request to update users informations
   * 
   * @param string $userName
   * @param string $email
   * @param string $number
   * @return bool
   */
  public function updateUserDatas(
    string $userName,
    string $email,
    string $number,
    int $otpStatut
  ): bool{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlUpdateUserDatas($userName, $email, $number, $otpStatut),
      'redis' => $this->noSqlRedisUpdateUserDatas($userName, $email, $number, $otpStatut),

      default => $this->sqlUpdateUserDatas($userName, $email, $number, $otpStatut),
    };
  }

  /**
   * Request to update users state
   * 
   * @param string $login
   * @return bool
   */
  public function updateEtatsUsers(
    string $login
  ): bool{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlUpdateEtatsUsers($login),
      'redis' => $this->noSqlRedisUpdateEtatsUsers($login),
      'oracle' => $this->sqlUpdateOracleUsersState($login),

      default => $this->sqlUpdateUsersState($login),
    };
  }

  /**
   * Request to init users password
   * 
   * @param string $login
   * @return bool
   */
  public function initUsersPassword(
    string $login
  ): bool{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlInitUsersPassword($login),
      'redis' => $this->noSqlRedisInitUsersPassword($login),

      default => $this->sqlInitUsersPassword($login),
    };
  }

  /**
   * update users group
   * 
   * @param string $login
   * @param int $usersGroupSelected
   * @return bool
   */
  public function updateUsersGroup(
    string $login,
    int $usersGroupSelected
  ): bool{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlToUpdateUsersGroup($login, $usersGroupSelected),
      'redis' => $this->noSqlRedisToUpdateUsersGroup($login, $usersGroupSelected),

      default => $this->sqlToUpdateUsersGroup($login, $usersGroupSelected),
    };
  }  

  /**
   * Change users password
   *
   * @param string $oldPassword
   * @param string $newPassword
   * @param string $confirmePassword
   * @return bool
   */
  public function changeUsersPassword(
    string $oldPassword,
    string $newPassword,
    string $confirmePassword
  ): bool{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlChangeUsersPassword($oldPassword, $newPassword, $confirmePassword),
      'redis' => $this->noSqlRedisChangeUsersPassword($oldPassword, $newPassword, $confirmePassword),

      default => $this->sqlChangeUsersPassword($oldPassword, $newPassword, $confirmePassword)
    };
  }

  /**
   * Request to update users datas from console
   *
   * @param string $login
   * @param string $password
   * @param int $usersGroup
   * @return bool
   */
  public function ConsoleUpdateUsers(
    string $login,
    string $password,
    int $usersGroup
  ): bool{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlConsoleUpdateUsers($login, $password, $usersGroup),
      'redis' => $this->noSqlConsoleUpdateUsers($login, $password, $usersGroup),

      default => $this->sqlConsoleUpdateUsers($login, $password, $usersGroup)
    };
  }

  /**
   * Suspend one mairie account by commune name.
   *
   * @param string $communeName
   * @return bool
   */
  public function suspendMairieAccountByCommune(
    string $communeName
  ): bool {
    return match (_FIRST_DRIVER_) {
      'sqlite', 'mysql' => $this->sqlSuspendMairieAccountByCommune($communeName),
      default => false,
    };
  }

  /**
   * Reactivate one mairie account by commune name.
   *
   * @param string $communeName
   * @return bool
   */
  public function reactivateMairieAccountByCommune(
    string $communeName
  ): bool {
    return match (_FIRST_DRIVER_) {
      'sqlite', 'mysql' => $this->sqlReactivateMairieAccountByCommune($communeName),
      default => false,
    };
  }

  /**
   * Mark one mairie message as read for receiver.
   *
   * @param string $receiverLogin
   * @param int $messageId
   * @return bool
   */
  public function markMairieMessageAsRead(
    string $receiverLogin,
    int $messageId
  ): bool {
    $receiverLogin = trim($receiverLogin);
    if ($receiverLogin === '' || $messageId <= 0) {
      return false;
    }

    try {
      if (_FIRST_DRIVER_ === 'mysql') {
        $this->chaine(
          "UPDATE mairie_messages
           SET is_read = 1, read_at = NOW()
           WHERE id = ?
           AND receiver_login = ?"
        )->param([$messageId, $receiverLogin])->setQuery();
      } else {
        $this->chaine(
          "UPDATE mairie_messages
           SET is_read = 1, read_at = datetime('now')
           WHERE id = ?
           AND receiver_login = ?"
        )->param([$messageId, $receiverLogin])->setQuery();
      }

      return true;
    } catch (\Throwable $exception) {
      return false;
    }
  }

  /**
   * Delete one mairie message for sender/receiver only.
   *
   * @param string $login
   * @param int $messageId
   * @return bool
   */
  public function deleteMairieMessageForUser(
    string $login,
    int $messageId
  ): bool {
    $login = trim($login);
    if ($login === '' || $messageId <= 0) {
      return false;
    }

    try {
      $this->chaine(
        "DELETE FROM mairie_messages
         WHERE id = ?
         AND (sender_login = ? OR receiver_login = ?)"
      )->param([$messageId, $login, $login])->setQuery();
      return true;
    } catch (\Throwable $exception) {
      return false;
    }
  }

  /**
   * Mark one agent message as read for receiver.
   */
  public function markAgentMessageAsRead(
    string $receiverLogin,
    int $messageId
  ): bool {
    $receiverLogin = trim($receiverLogin);
    if ($receiverLogin === '' || $messageId <= 0) {
      return false;
    }

    try {
      if (_FIRST_DRIVER_ === 'mysql') {
        $this->chaine(
          "UPDATE agent_messages
           SET is_read = 1, read_at = NOW()
           WHERE id = ?
           AND receiver_login = ?"
        )->param([$messageId, $receiverLogin])->setQuery();
      } else {
        $this->chaine(
          "UPDATE agent_messages
           SET is_read = 1, read_at = datetime('now')
           WHERE id = ?
           AND receiver_login = ?"
        )->param([$messageId, $receiverLogin])->setQuery();
      }

      return true;
    } catch (\Throwable $exception) {
      return false;
    }
  }

  /**
   * Delete one agent message for sender/receiver only.
   */
  public function deleteAgentMessageForUser(
    string $login,
    int $messageId
  ): bool {
    $login = trim($login);
    if ($login === '' || $messageId <= 0) {
      return false;
    }

    try {
      $this->chaine(
        "DELETE FROM agent_messages
         WHERE id = ?
         AND (sender_login = ? OR receiver_login = ?)"
      )->param([$messageId, $login, $login])->setQuery();
      return true;
    } catch (\Throwable $exception) {
      return false;
    }
  }
}
