<?php

namespace Epaphrodites\database\requests\mainRequest\insert;

use Epaphrodites\database\requests\typeRequest\sqlRequest\insert\insert as InsertInsert;

final class insert extends InsertInsert
{

    /**
     * Add users rights
     * 
     * @param int|null $usersGroup
     * @param string|null $pages
     * @param string|null $actions
     * @return bool
     */
    public function AddUsersRights(
      int|null $usersGroup = null, 
      string|null $pages = null, 
      string|null  $actions = null
    ):bool{

      if (static::initConfig()['addright']->AddUsersRights($usersGroup, $pages, $actions) === true) {
        
            $config = static::initQuery()['setting'];
            $actions = "Assign a right to the user group : " . static::initNamespace()['datas']->usersGroup($usersGroup, 'label');

            match (_FIRST_DRIVER_) {

              'mongodb' => $config->noSqlActionsRecente($actions),
              'redis' => $config->noSqlRedisActionsRecente($actions),
        
              default => $config->ActionsRecente($actions),
            };

            return true;
        } else {
            return false;
        }
    }  

    /**
     * Set user dashboard color
     * 
     * @param int $usersGroup
     * @param string $color
     * @return bool
     */
    public function setDashboardColors(
      int $usersGroup, 
      string $color
    ): bool{
        if (empty($usersGroup) || empty($color)) {
            return false;
        }

        $json = static::initNamespace()['json'];
        $path = $json->path(_DIR_COLORS_PATH_);

        $existingEntry = $path->get(['usersGroup' => $usersGroup]);

        if (!$existingEntry) {
            $path->add(['usersGroup' => $usersGroup, 'color' => $color]);
        } else {
            $path->where(['usersGroup' => $usersGroup])->update(['color' => $color]);
        }

        return true;
    }  

    /**
     * Set dashboard color for one connected user only.
     *
     * @param string $login
     * @param string $color
     * @return bool
     */
  public function setUserDashboardColor(
      string $login,
      string $color
    ): bool {
        $login = trim($login);
        $color = trim($color);

        if ($login === '' || $color === '') {
            return false;
        }

        $json = static::initNamespace()['json'];
        $path = $json->path(_DIR_COLORS_PATH_);
        $existingEntry = $path->get(['login' => $login]);

        if (!$existingEntry) {
            $path->add(['login' => $login, 'color' => $color]);
        } else {
            $path->where(['login' => $login])->update(['color' => $color]);
        }

        return true;
    }

    /**
     * Send one mairie-to-mairie message.
     *
     * @param string $senderLogin
     * @param string $receiverLogin
     * @param string $subject
     * @param string $messageText
     * @return bool
     */
    public function sendMairieMessage(
      string $senderLogin,
      string $receiverLogin,
      string $subject,
      string $messageText,
      ?int $parentId = null,
      ?string $threadRef = null,
      ?string $attachmentPath = null,
      ?string $attachmentName = null,
      ?string $attachmentMime = null
    ): bool {
        $senderLogin = trim($senderLogin);
        $receiverLogin = trim($receiverLogin);
        $subject = trim($subject);
        $messageText = trim($messageText);

        if ($senderLogin === '' || $receiverLogin === '' || $messageText === '' || $senderLogin === $receiverLogin) {
            return false;
        }

        try {
            if (_FIRST_DRIVER_ === 'mysql') {
                $this->chaine(
                    "CREATE TABLE IF NOT EXISTS mairie_messages (
                      id INT AUTO_INCREMENT PRIMARY KEY,
                      sender_login VARCHAR(80) NOT NULL,
                      receiver_login VARCHAR(80) NOT NULL,
                      subject VARCHAR(180) DEFAULT '',
                      message_text TEXT NOT NULL,
                      parent_id INT NULL,
                      thread_ref VARCHAR(80) NOT NULL DEFAULT '',
                      attachment_path VARCHAR(255) NULL,
                      attachment_name VARCHAR(180) NULL,
                      attachment_mime VARCHAR(100) NULL,
                      is_read TINYINT(1) NOT NULL DEFAULT 0,
                      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                      read_at DATETIME NULL,
                      INDEX idx_mm_receiver_read (receiver_login, is_read),
                      INDEX idx_mm_created (created_at),
                      INDEX idx_mm_thread (thread_ref)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
                )->setQuery();
            } else {
                $this->chaine(
                    "CREATE TABLE IF NOT EXISTS mairie_messages (
                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                      sender_login TEXT NOT NULL,
                      receiver_login TEXT NOT NULL,
                      subject TEXT DEFAULT '',
                      message_text TEXT NOT NULL,
                      parent_id INTEGER DEFAULT NULL,
                      thread_ref TEXT NOT NULL DEFAULT '',
                      attachment_path TEXT DEFAULT NULL,
                      attachment_name TEXT DEFAULT NULL,
                      attachment_mime TEXT DEFAULT NULL,
                      is_read INTEGER NOT NULL DEFAULT 0,
                      created_at TEXT DEFAULT NULL,
                      read_at TEXT DEFAULT NULL
                    )"
                )->setQuery();
                $this->chaine("CREATE INDEX IF NOT EXISTS idx_mm_receiver_read ON mairie_messages (receiver_login, is_read)")->setQuery();
                $this->chaine("CREATE INDEX IF NOT EXISTS idx_mm_created ON mairie_messages (created_at)")->setQuery();
                $this->chaine("CREATE INDEX IF NOT EXISTS idx_mm_thread ON mairie_messages (thread_ref)")->setQuery();
            }

            $senderRows = $this->chaine(
                "SELECT login FROM users
                 WHERE login = ?
                 AND role IN ('MAIRIE_ADMIN', 'SUPER_ADMIN')
                 AND status = 'ACTIVE'
                 LIMIT 1"
            )->param([$senderLogin])->getQuery();
            $receiverRows = $this->chaine(
                "SELECT login FROM users
                 WHERE login = ?
                 AND role IN ('MAIRIE_ADMIN', 'SUPER_ADMIN')
                 AND status = 'ACTIVE'
                 LIMIT 1"
            )->param([$receiverLogin])->getQuery();

            $senderRows = static::initNamespace()['env']->dictKeyToLowers(is_array($senderRows) ? $senderRows : []);
            $receiverRows = static::initNamespace()['env']->dictKeyToLowers(is_array($receiverRows) ? $receiverRows : []);
            if (empty($senderRows) || empty($receiverRows)) {
                return false;
            }

            $threadRef = trim((string) $threadRef);
            if ($threadRef === '') {
                $threadRef = 'THR_' . date('YmdHis') . '_' . substr(md5(uniqid((string) mt_rand(), true)), 0, 10);
            }

            if (_FIRST_DRIVER_ === 'mysql') {
                $this->chaine(
                    "INSERT INTO mairie_messages (sender_login, receiver_login, subject, message_text, parent_id, thread_ref, attachment_path, attachment_name, attachment_mime, is_read, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())"
                )->param([
                    $senderLogin,
                    $receiverLogin,
                    $subject,
                    $messageText,
                    $parentId,
                    $threadRef,
                    $attachmentPath,
                    $attachmentName,
                    $attachmentMime
                ])->setQuery();
            } else {
                $this->chaine(
                    "INSERT INTO mairie_messages (sender_login, receiver_login, subject, message_text, parent_id, thread_ref, attachment_path, attachment_name, attachment_mime, is_read, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, datetime('now'))"
                )->param([
                    $senderLogin,
                    $receiverLogin,
                    $subject,
                    $messageText,
                    $parentId,
                    $threadRef,
                    $attachmentPath,
                    $attachmentName,
                    $attachmentMime
                ])->setQuery();
            }

            static::initQuery()['setting']->ActionsRecente(
                "Message mairie envoye: {$senderLogin} -> {$receiverLogin}"
            );
            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    /**
     * Send one commune-scoped message (agent/mairie/merchant) in the same commune.
     */
    public function sendAgentMessage(
      string $senderLogin,
      string $receiverLogin,
      string $subject,
      string $messageText,
      ?int $parentId = null,
      ?string $threadRef = null,
      ?string $attachmentPath = null,
      ?string $attachmentName = null,
      ?string $attachmentMime = null
    ): bool {
        $senderLogin = trim($senderLogin);
        $receiverLogin = trim($receiverLogin);
        $subject = trim($subject);
        $messageText = trim($messageText);

        if ($senderLogin === '' || $receiverLogin === '' || $messageText === '' || $senderLogin === $receiverLogin) {
            return false;
        }

        try {
            if (_FIRST_DRIVER_ === 'mysql') {
                $this->chaine(
                    "CREATE TABLE IF NOT EXISTS agent_messages (
                      id INT AUTO_INCREMENT PRIMARY KEY,
                      commune_id INT NOT NULL,
                      sender_login VARCHAR(80) NOT NULL,
                      receiver_login VARCHAR(80) NOT NULL,
                      subject VARCHAR(180) DEFAULT '',
                      message_text TEXT NOT NULL,
                      parent_id INT NULL,
                      thread_ref VARCHAR(80) NOT NULL DEFAULT '',
                      attachment_path VARCHAR(255) NULL,
                      attachment_name VARCHAR(180) NULL,
                      attachment_mime VARCHAR(100) NULL,
                      is_read TINYINT(1) NOT NULL DEFAULT 0,
                      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                      read_at DATETIME NULL,
                      INDEX idx_am_receiver_read (receiver_login, is_read),
                      INDEX idx_am_created (created_at),
                      INDEX idx_am_thread (thread_ref),
                      INDEX idx_am_commune (commune_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
                )->setQuery();
            } else {
                $this->chaine(
                    "CREATE TABLE IF NOT EXISTS agent_messages (
                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                      commune_id INTEGER NOT NULL,
                      sender_login TEXT NOT NULL,
                      receiver_login TEXT NOT NULL,
                      subject TEXT DEFAULT '',
                      message_text TEXT NOT NULL,
                      parent_id INTEGER DEFAULT NULL,
                      thread_ref TEXT NOT NULL DEFAULT '',
                      attachment_path TEXT DEFAULT NULL,
                      attachment_name TEXT DEFAULT NULL,
                      attachment_mime TEXT DEFAULT NULL,
                      is_read INTEGER NOT NULL DEFAULT 0,
                      created_at TEXT DEFAULT NULL,
                      read_at TEXT DEFAULT NULL
                    )"
                )->setQuery();
                $this->chaine("CREATE INDEX IF NOT EXISTS idx_am_receiver_read ON agent_messages (receiver_login, is_read)")->setQuery();
                $this->chaine("CREATE INDEX IF NOT EXISTS idx_am_created ON agent_messages (created_at)")->setQuery();
                $this->chaine("CREATE INDEX IF NOT EXISTS idx_am_thread ON agent_messages (thread_ref)")->setQuery();
                $this->chaine("CREATE INDEX IF NOT EXISTS idx_am_commune ON agent_messages (commune_id)")->setQuery();
            }

            $senderRows = $this->chaine(
                "SELECT login, commune_id, role, status
                 FROM users
                 WHERE login = ?
                 AND role IN ('AGENT', 'MAIRIE_ADMIN', 'MERCHANT')
                 LIMIT 1"
            )->param([$senderLogin])->getQuery();
            $receiverRows = $this->chaine(
                "SELECT login, commune_id, role, status
                 FROM users
                 WHERE login = ?
                 AND role IN ('AGENT', 'MAIRIE_ADMIN', 'MERCHANT')
                 LIMIT 1"
            )->param([$receiverLogin])->getQuery();

            $senderRows = static::initNamespace()['env']->dictKeyToLowers(is_array($senderRows) ? $senderRows : []);
            $receiverRows = static::initNamespace()['env']->dictKeyToLowers(is_array($receiverRows) ? $receiverRows : []);
            $sender = $senderRows[0] ?? [];
            $receiver = $receiverRows[0] ?? [];
            $senderCommune = (int) ($sender['commune_id'] ?? 0);
            $receiverCommune = (int) ($receiver['commune_id'] ?? 0);
            $senderRole = (string) ($sender['role'] ?? '');
            $receiverRole = (string) ($receiver['role'] ?? '');
            $senderStatus = (string) ($sender['status'] ?? '');
            $receiverStatus = (string) ($receiver['status'] ?? '');

            if (
                $senderCommune <= 0 ||
                $senderCommune !== $receiverCommune ||
                $senderStatus !== 'ACTIVE' ||
                $receiverStatus !== 'ACTIVE' ||
                !in_array($senderRole, ['AGENT', 'MAIRIE_ADMIN', 'MERCHANT'], true) ||
                !in_array($receiverRole, ['AGENT', 'MAIRIE_ADMIN', 'MERCHANT'], true)
            ) {
                return false;
            }

            $threadRef = trim((string) $threadRef);
            if ($threadRef === '') {
                $threadRef = 'THR_' . date('YmdHis') . '_' . substr(md5(uniqid((string) mt_rand(), true)), 0, 10);
            }

            if (_FIRST_DRIVER_ === 'mysql') {
                $this->chaine(
                    "INSERT INTO agent_messages (commune_id, sender_login, receiver_login, subject, message_text, parent_id, thread_ref, attachment_path, attachment_name, attachment_mime, is_read, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())"
                )->param([
                    $senderCommune,
                    $senderLogin,
                    $receiverLogin,
                    $subject,
                    $messageText,
                    $parentId,
                    $threadRef,
                    $attachmentPath,
                    $attachmentName,
                    $attachmentMime
                ])->setQuery();
            } else {
                $this->chaine(
                    "INSERT INTO agent_messages (commune_id, sender_login, receiver_login, subject, message_text, parent_id, thread_ref, attachment_path, attachment_name, attachment_mime, is_read, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, datetime('now'))"
                )->param([
                    $senderCommune,
                    $senderLogin,
                    $receiverLogin,
                    $subject,
                    $messageText,
                    $parentId,
                    $threadRef,
                    $attachmentPath,
                    $attachmentName,
                    $attachmentMime
                ])->setQuery();
            }

            static::initQuery()['setting']->ActionsRecente(
                "Message agent envoye: {$senderLogin} -> {$receiverLogin}"
            );
            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

  /**
   * Add a new users
   * @param string $login
   * @param int $usersGroup
   * @return bool
   */
  public function addUsers(
    string $login, 
    int $usersGroup
  ):bool{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqladdUsers($login , $usersGroup),
      'redis' => $this->noSqlRedisAddUsers($login , $usersGroup),

      default => $this->sqlAddUsers($login , $usersGroup),
    };    
  } 
  
   /**
   * Add a new users from console
   * @param string $login
   * @param string $password
   * @param int $usersGroup
   * @return array
   */
  public function ConsoleAddUsers(
    string $login,
    string $password,
    int $usersGroup
  ):bool{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlConsoleAddUsers($login , $password, $usersGroup),
      'redis' => $this->noSqlRedisConsoleAddUsers($login , $password, $usersGroup),

      default => $this->sqlConsoleAddUsers($login , $password, $usersGroup),
    };     
  }

  /**
   * Create mairie account from TaxeConnect SQL model.
   */
  public function createMairieAccount(
    array $payload,
    string $creatorLogin
  ): bool {
    if (!in_array(_FIRST_DRIVER_, ['mysql', 'sqlite'], true)) {
      return false;
    }

    $ok = $this->sqlCreateMairieAccount($payload, $creatorLogin);

    if ($ok) {
      static::initQuery()['setting']->ActionsRecente(
        "TaxeConnect: mairie account created ({$payload['login']})"
      );
    }

    return $ok;
  }

  /**
   * Create agent account from TaxeConnect SQL model.
   */
  public function createAgentAccount(
    array $payload,
    string $creatorLogin
  ): bool {
    if (!in_array(_FIRST_DRIVER_, ['mysql', 'sqlite'], true)) {
      return false;
    }

    $ok = $this->sqlCreateAgentAccount($payload, $creatorLogin);

    if ($ok) {
      static::initQuery()['setting']->ActionsRecente(
        "TaxeConnect: agent account created ({$payload['login']})"
      );
    }

    return $ok;
  }

  /**
   * Create commune tax from mairie account.
   */
  public function createCommuneTax(
    array $payload,
    string $creatorLogin
  ): bool {
    if (!in_array(_FIRST_DRIVER_, ['mysql', 'sqlite'], true)) {
      return false;
    }

    $ok = $this->sqlCreateCommuneTax($payload, $creatorLogin);

    if ($ok) {
      static::initQuery()['setting']->ActionsRecente(
        "TaxeConnect: commune tax created ({$payload['code']})"
      );
    }

    return $ok;
  }

  /**
   * Create merchant account from agent account.
   */
  public function createMerchantAccount(
    array $payload,
    string $creatorLogin
  ): bool {
    if (!in_array(_FIRST_DRIVER_, ['mysql', 'sqlite'], true)) {
      return false;
    }

    $ok = $this->sqlCreateMerchantAccount($payload, $creatorLogin);

    if ($ok) {
      static::initQuery()['setting']->ActionsRecente(
        "TaxeConnect: merchant account created ({$payload['login']})"
      );
    }

    return $ok;
  }

  /**
   * Public self-signup for merchant account.
   * Allowed only if commune has an active mairie account.
   */
  public function selfRegisterMerchantAccount(
    array $payload
  ): bool {
    if (!in_array(_FIRST_DRIVER_, ['mysql', 'sqlite'], true)) {
      return false;
    }

    $communeInput = trim((string) ($payload['commune'] ?? ''));
    $login = trim((string) ($payload['login'] ?? ''));
    $password = (string) ($payload['password'] ?? '');
    $businessName = trim((string) ($payload['business_name'] ?? ''));
    $ownerName = trim((string) ($payload['owner_name'] ?? ''));
    $phone = trim((string) ($payload['phone'] ?? ''));
    $email = trim((string) ($payload['email'] ?? ''));
    $activity = trim((string) ($payload['activity'] ?? ''));
    $address = trim((string) ($payload['address'] ?? ''));

    if (
      $communeInput === '' ||
      $login === '' ||
      $password === '' ||
      $businessName === '' ||
      $ownerName === ''
    ) {
      return false;
    }

    try {
      $exists = $this->chaine(
        "SELECT id FROM users WHERE login = ? LIMIT 1"
      )->param([$login])->getQuery();
      $exists = static::initNamespace()['env']->dictKeyToLowers(is_array($exists) ? $exists : []);
      if (!empty($exists)) {
        return false;
      }

      $communeRows = $this->chaine(
        "SELECT c.id, c.code, c.name, m.mairie_admin_user_id
         FROM communes c
         INNER JOIN mairies m ON m.commune_id = c.id
         INNER JOIN users u ON u.id = m.mairie_admin_user_id
         WHERE u.status = 'ACTIVE'
         AND (LOWER(c.name) = LOWER(?) OR UPPER(c.code) = UPPER(?))
         LIMIT 1"
      )->param([$communeInput, $communeInput])->getQuery();
      $communeRows = static::initNamespace()['env']->dictKeyToLowers(is_array($communeRows) ? $communeRows : []);
      $commune = $communeRows[0] ?? [];

      $communeId = (int) ($commune['id'] ?? 0);
      $communeCode = strtoupper((string) ($commune['code'] ?? 'COM'));
      $mairieAdminId = (int) ($commune['mairie_admin_user_id'] ?? 0);
      if ($communeId <= 0 || $mairieAdminId <= 0) {
        return false;
      }

      $merchantRef = sprintf(
        '%s-%s-%04d',
        $communeCode,
        date('ymdHis'),
        random_int(1, 9999)
      );

      $hashedPassword = static::initConfig()['guard']->CryptPassword($password);

      $this->table('users')
        ->insert('login, password_hash, full_name, email, phone, role, status, commune_id, created_by')
        ->values('?, ?, ?, ?, ?, ?, ?, ?, ?')
        ->param([
          $login,
          $hashedPassword,
          $ownerName,
          $email !== '' ? $email : null,
          $phone !== '' ? $phone : null,
          'MERCHANT',
          'ACTIVE',
          $communeId,
          $mairieAdminId
        ])
        ->IQuery();

      $merchantUser = $this->chaine(
        "SELECT id FROM users WHERE login = ? LIMIT 1"
      )->param([$login])->getQuery();
      $merchantUser = static::initNamespace()['env']->dictKeyToLowers(is_array($merchantUser) ? $merchantUser : []);
      $merchantUserId = (int) (($merchantUser[0]['id'] ?? 0));
      if ($merchantUserId <= 0) {
        return false;
      }

      $this->table('merchants')
        ->insert('merchant_ref, user_id, commune_id, created_by_agent_user_id, business_name, owner_name, phone, email, activity, address, status, created_at')
        ->values('?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?')
        ->param([
          $merchantRef,
          $merchantUserId,
          $communeId,
          $mairieAdminId,
          $businessName,
          $ownerName,
          $phone !== '' ? $phone : null,
          $email !== '' ? $email : null,
          $activity !== '' ? $activity : null,
          $address !== '' ? $address : null,
          'ACTIVE',
          date('Y-m-d H:i:s')
        ])
        ->IQuery();

      if (count(static::initQuery()['getid']->sqlGetUsersDatas($login)) < 1) {
        $this->table('usersaccount')
          ->insert('login, password, usersgroup')
          ->values('?, ?, ?')
          ->param([$login, $hashedPassword, 4])
          ->IQuery();
      }

      static::initQuery()['setting']->ActionsRecente(
        "TaxeConnect: public merchant signup ({$login})"
      );

      return true;
    } catch (\Throwable $exception) {
      return false;
    }
  }

  /**
   * Assign one tax type to one merchant (agent scope).
   */
  public function assignTaxToMerchant(
    array $payload,
    string $agentLogin
  ): bool {
    if (!in_array(_FIRST_DRIVER_, ['mysql', 'sqlite'], true)) {
      return false;
    }

    $ok = $this->sqlAssignTaxToMerchant($payload, $agentLogin);

    if ($ok) {
      static::initQuery()['setting']->ActionsRecente(
        "TaxeConnect: tax assigned to merchant ({$payload['merchant_ref']})"
      );
    }

    return $ok;
  }

 }
