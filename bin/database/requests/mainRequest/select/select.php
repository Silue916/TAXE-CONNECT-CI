<?php

namespace Epaphrodites\database\requests\mainRequest\select;

use Epaphrodites\database\requests\typeRequest\sqlRequest\select\select as SelectSelect;

final class select extends SelectSelect
{

  /**
   * Normalize row keys for Oracle driver.
   */
  private function normalizeRows(
    array $rows
  ): array {
    if (_FIRST_DRIVER_ === 'oracle') {
      return static::initNamespace()['env']->dictKeyToLowers($rows);
    }

    return $rows;
  }

  /**
   * Build SQL filter clause for tax payments.
   */
  private function taxPaymentsFilters(
    ?string $status = null,
    ?string $channel = null,
    ?string $search = null
  ): array {
    $where = [];
    $params = [];

    if (!empty($status)) {
      $where[] = "p.status = ?";
      $params[] = $status;
    }

    if (!empty($channel)) {
      $where[] = "p.channel = ?";
      $params[] = $channel;
    }

    if (!empty($search)) {
      $where[] = "(p.receipt_ref LIKE ? OR p.notice_ref LIKE ? OR p.merchant_id LIKE ?)";
      $needle = "%$search%";
      $params[] = $needle;
      $params[] = $needle;
      $params[] = $needle;
    }

    $sqlWhere = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));

    return [$sqlWhere, $params];
  }

  /**
   * Build SQL filter clause for tax payments with commune restriction.
   */
  private function taxPaymentsFiltersScoped(
    ?string $status = null,
    ?string $channel = null,
    ?string $search = null,
    ?string $commune = null
  ): array {
    [$sqlWhere, $params] = $this->taxPaymentsFilters($status, $channel, $search);
    $where = [];

    if (!empty($sqlWhere)) {
      $where[] = ltrim(substr($sqlWhere, 6));
    }

    if (!empty($commune)) {
      $where[] = "c.commune = ?";
      $params[] = $commune;
    }

    $finalWhere = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));

    return [$finalWhere, $params];
  }

  /**
   * Resolve current user scope from TaxeConnect MySQL tables.
   */
  public function getTaxeConnectAccessScope(
    ?string $login,
    ?int $userType
  ): array {
    $scope = [
      'is_scoped' => false,
      'commune' => null,
    ];

    if (!is_string($login) || trim($login) === '') {
      return $scope;
    }

    if (!in_array((int) $userType, [2, 3], true)) {
      return $scope;
    }

    try {
      $rows = $this->chaine(
        "SELECT c.name AS commune
         FROM users u
         LEFT JOIN communes c ON c.id = u.commune_id
         WHERE u.login = ?"
      )->param([$login])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      $commune = isset($rows[0]['commune']) ? (string) $rows[0]['commune'] : '';

      if ($commune !== '') {
        return [
          'is_scoped' => true,
          'commune' => $commune,
        ];
      }
    } catch (\Throwable $exception) {
      return $scope;
    }

    // If role should be scoped but commune mapping is missing, block data.
    return [
      'is_scoped' => true,
      'commune' => '__NO_COMMUNE_SCOPE__',
    ];
  }

  /**
   * List tax payments with filters and pagination.
   */
  public function listTaxPayments(
    int $currentPage,
    int $numLine,
    ?string $status = null,
    ?string $channel = null,
    ?string $search = null
  ): array {
    [$sqlWhere, $params] = $this->taxPaymentsFilters($status, $channel, $search);

    $offset = max(0, ($currentPage - 1) * $numLine);

    $baseSql = "SELECT p.*, n.status AS notice_status, n.amount_due
                FROM tax_payments p
                LEFT JOIN tax_notices n ON p.notice_ref = n.notice_ref
                $sqlWhere
                ORDER BY p.paid_at DESC";

    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET $offset ROWS FETCH NEXT $numLine ROWS ONLY",
      default => " LIMIT $numLine OFFSET $offset",
    };

    $rows = $this->chaine($baseSql . $paginationSql)
      ->param($params)
      ->getQuery();

    return $this->normalizeRows(is_array($rows) ? $rows : []);
  }

  /**
   * List tax payments with optional commune scope.
   */
  public function listTaxPaymentsScoped(
    int $currentPage,
    int $numLine,
    ?string $status = null,
    ?string $channel = null,
    ?string $search = null,
    ?string $commune = null
  ): array {
    [$sqlWhere, $params] = $this->taxPaymentsFiltersScoped($status, $channel, $search, $commune);
    $offset = max(0, ($currentPage - 1) * $numLine);

    $baseSql = "SELECT p.*, n.status AS notice_status, n.amount_due, c.commune
                FROM tax_payments p
                LEFT JOIN tax_notices n ON p.notice_ref = n.notice_ref
                LEFT JOIN contributors c ON c.merchant_id = n.merchant_id
                $sqlWhere
                ORDER BY p.paid_at DESC";

    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET $offset ROWS FETCH NEXT $numLine ROWS ONLY",
      default => " LIMIT $numLine OFFSET $offset",
    };

    $rows = $this->chaine($baseSql . $paginationSql)
      ->param($params)
      ->getQuery();

    return $this->normalizeRows(is_array($rows) ? $rows : []);
  }

  /**
   * Count tax payments with optional filters.
   */
  public function countTaxPayments(
    ?string $status = null,
    ?string $channel = null,
    ?string $search = null
  ): int {
    [$sqlWhere, $params] = $this->taxPaymentsFilters($status, $channel, $search);

    $rows = $this->chaine("SELECT COUNT(*) AS nbre FROM tax_payments p $sqlWhere")
      ->param($params)
      ->getQuery();

    $rows = $this->normalizeRows(is_array($rows) ? $rows : []);

    return (int)($rows[0]['nbre'] ?? 0);
  }

  /**
   * Count tax payments with optional commune scope.
   */
  public function countTaxPaymentsScoped(
    ?string $status = null,
    ?string $channel = null,
    ?string $search = null,
    ?string $commune = null
  ): int {
    [$sqlWhere, $params] = $this->taxPaymentsFiltersScoped($status, $channel, $search, $commune);

    $rows = $this->chaine(
      "SELECT COUNT(*) AS nbre
       FROM tax_payments p
       LEFT JOIN tax_notices n ON p.notice_ref = n.notice_ref
       LEFT JOIN contributors c ON c.merchant_id = n.merchant_id
       $sqlWhere"
    )->param($params)->getQuery();

    $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
    return (int)($rows[0]['nbre'] ?? 0);
  }

  /**
   * Get one payment by receipt reference.
   */
  public function getTaxPaymentByReceipt(
    string $receiptRef
  ): array {
    $rows = $this->chaine(
      "SELECT p.*, n.status AS notice_status, n.amount_due, n.fiscal_period, n.due_date
       FROM tax_payments p
       LEFT JOIN tax_notices n ON p.notice_ref = n.notice_ref
       WHERE p.receipt_ref = ?"
    )->param([$receiptRef])->getQuery();

    $rows = $this->normalizeRows(is_array($rows) ? $rows : []);

    return $rows[0] ?? [];
  }

  /**
   * Get one payment by receipt with optional commune restriction.
   */
  public function getTaxPaymentByReceiptScoped(
    string $receiptRef,
    ?string $commune = null
  ): array {
    $sql = "SELECT p.*, n.status AS notice_status, n.amount_due, n.fiscal_period, n.due_date, c.commune
            FROM tax_payments p
            LEFT JOIN tax_notices n ON p.notice_ref = n.notice_ref
            LEFT JOIN contributors c ON c.merchant_id = n.merchant_id
            WHERE p.receipt_ref = ?";
    $params = [$receiptRef];

    if (!empty($commune)) {
      $sql .= " AND c.commune = ?";
      $params[] = $commune;
    }

    $rows = $this->chaine($sql)->param($params)->getQuery();
    $rows = $this->normalizeRows(is_array($rows) ? $rows : []);

    return $rows[0] ?? [];
  }

  /**
   * Request to get contributors list.
   *
   * @param int $currentPage
   * @param int $numLine
   * @return array
   */
  public function listOfAllContributors(
    int $currentPage,
    int $numLine
  ): array {

    return match (_FIRST_DRIVER_) {

      'mongodb' => [],
      'redis' => [],
      'sqlserver' => $this->sqlServerListOfContributors($currentPage, $numLine),
      'oracle' => $this->oracleListOfContributors($currentPage, $numLine),

      default => $this->defaultSqlListOfContributors($currentPage, $numLine),
    };
  }

  public function selectUsersColors(): array
  {

    $namespace = static::initNamespace();
    $json = $namespace['json'];
    $datas = $namespace['datas'];

    $groupColors = array_column(
        $json->path(_DIR_COLORS_PATH_)->get(),
        'color',
        'usersGroup'
    );

    return array_map(
        fn(array $group): array => [
            '_id' => $group['_id'],
            'label' => $group['label'],
            'color' => $groupColors[$group['_id']] ?? 'No Color'
        ],
        $datas->usersGroup()
    );
  }


  /**
   * Request to get users list
   * 
   * @param int $currentPage
   * @param int $numLine
   * @return array
   */
  public function listeOfAllUsers(
    int $currentPage, 
    int $numLine
  ):array{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlListeOfAllUsers($currentPage, $numLine),
      'redis' => $this->noSqlRedisListeOfAllUsers($currentPage, $numLine),
      'sqlserver' => $this->sqlServerListeOfAllUsers( $currentPage, $numLine),
      'oracle' => $this->oracleListeOfAllUsers( $currentPage, $numLine),

      default => $this->defaultSqlListeOfAllUsers($currentPage, $numLine),
    };       
  }  

  /**
   * Count users visible for a mairie account (agents and derived users only).
   */
  public function countMairieScopedUsers(
    string $mairieLogin,
    int|string|null $usersGroup = null,
    ?string $search = null
  ): int {
    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return 0;
    }

    $where = ["u.role IN ('AGENT', 'MERCHANT')"];
    $params = [$mairieLogin];

    if (!empty($usersGroup)) {
      $where[] = "ua.usersgroup = ?";
      $params[] = (string) $usersGroup;
    }

    if (!empty($search)) {
      $where[] = "ua.login LIKE ?";
      $params[] = '%' . trim($search) . '%';
    }

    $sqlWhere = ' WHERE ' . implode(' AND ', $where);

    $rows = $this->chaine(
      "SELECT COUNT(*) AS nbre
       FROM usersaccount ua
       INNER JOIN users u ON u.login = ua.login
       INNER JOIN users mairie ON mairie.login = ? AND mairie.role = 'MAIRIE_ADMIN'
       $sqlWhere
       AND u.commune_id = mairie.commune_id"
    )->param($params)->getQuery();

    $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
    return (int) ($rows[0]['nbre'] ?? 0);
  }

  /**
   * List users visible for a mairie account (agents and derived users only).
   */
  public function listMairieScopedUsers(
    string $mairieLogin,
    int $currentPage,
    int $numLine,
    int|string|null $usersGroup = null,
    ?string $search = null
  ): array {
    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return [];
    }

    $where = ["u.role IN ('AGENT', 'MERCHANT')"];
    $params = [$mairieLogin];

    if (!empty($usersGroup)) {
      $where[] = "ua.usersgroup = ?";
      $params[] = (string) $usersGroup;
    }

    if (!empty($search)) {
      $where[] = "ua.login LIKE ?";
      $params[] = '%' . trim($search) . '%';
    }

    $sqlWhere = ' WHERE ' . implode(' AND ', $where);
    $offset = max(0, ($currentPage - 1) * $numLine);
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET $offset ROWS FETCH NEXT $numLine ROWS ONLY",
      default => " LIMIT $numLine OFFSET $offset",
    };

    $rows = $this->chaine(
      "SELECT ua.*
       FROM usersaccount ua
       INNER JOIN users u ON u.login = ua.login
       INNER JOIN users mairie ON mairie.login = ? AND mairie.role = 'MAIRIE_ADMIN'
       $sqlWhere
       AND u.commune_id = mairie.commune_id
       ORDER BY ua.login ASC
       $paginationSql"
    )->param($params)->getQuery();

    return $this->normalizeRows(is_array($rows) ? $rows : []);
  }

  /**
   * Return all login values visible for a mairie account.
   */
  public function mairieScopedLogins(
    string $mairieLogin
  ): array {
    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return [];
    }

    $rows = $this->chaine(
      "SELECT ua.login
       FROM usersaccount ua
       INNER JOIN users u ON u.login = ua.login
       INNER JOIN users mairie ON mairie.login = ? AND mairie.role = 'MAIRIE_ADMIN'
       WHERE u.role IN ('AGENT', 'MERCHANT')
       AND u.commune_id = mairie.commune_id"
    )->param([$mairieLogin])->getQuery();

    $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
    return array_values(array_filter(array_map(
      static fn(array $row): string => (string) ($row['login'] ?? ''),
      $rows
    )));
  }

  /**
   * Request contributors list with optional commune restriction.
   */
  public function listOfAllContributorsScoped(
    int $currentPage,
    int $numLine,
    ?string $commune = null
  ): array {
    if (empty($commune)) {
      return $this->listOfAllContributors($currentPage, $numLine);
    }

    $offset = (($currentPage - 1) * $numLine);

    $sql = "SELECT *
            FROM contributors
            WHERE commune = ?
            ORDER BY name ASC";

    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET $offset ROWS FETCH NEXT $numLine ROWS ONLY",
      default => " LIMIT $offset, $numLine",
    };

    $rows = $this->chaine($sql . $paginationSql)->param([$commune])->getQuery();

    return $this->normalizeRows(is_array($rows) ? $rows : []);
  }

  /**
   * Count contributors with optional commune restriction.
   */
  public function countAllContributorsScoped(
    ?string $commune = null
  ): int {
    if (empty($commune)) {
      return (int) static::initQuery()['count']->CountAllContributors();
    }

    $rows = $this->chaine(
      "SELECT COUNT(*) AS nbre
       FROM contributors
       WHERE commune = ?"
    )->param([$commune])->getQuery();

    $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
    return (int)($rows[0]['nbre'] ?? 0);
  }

  /**
   * Super admin board: communes, mairie account and number of agents.
   */
  public function listCommuneMairieAgentBoard(): array
  {
    $rows = $this->chaine(
      "SELECT
          c.id AS commune_id,
          c.code AS commune_code,
          c.name AS commune_name,
          c.department,
          c.region,
          um.login AS mairie_login,
          um.full_name AS mairie_full_name,
          um.status AS mairie_status,
          COUNT(a.id) AS agents_total
       FROM communes c
       LEFT JOIN mairies m ON m.commune_id = c.id
       LEFT JOIN users um ON um.id = m.mairie_admin_user_id
       LEFT JOIN agents a ON a.commune_id = c.id
       GROUP BY
          c.id, c.code, c.name, c.department, c.region,
          um.login, um.full_name, um.status
       ORDER BY c.name ASC"
    )->getQuery();

    return $this->normalizeRows(is_array($rows) ? $rows : []);
  }

  /**
   * Super admin dashboard counters for mairie, agents and merchants.
   */
  public function superAdminActorsStats(): array
  {
    $default = [
      'mairies_total' => 0,
      'agents_total' => 0,
      'merchants_total' => 0,
      'users_total' => 0,
    ];

    try {
      $rows = $this->chaine(
        "SELECT
          (SELECT COUNT(*) FROM mairies) AS mairies_total,
          (SELECT COUNT(*) FROM agents) AS agents_total,
          (SELECT COUNT(*) FROM merchants) AS merchants_total,
          (SELECT COUNT(*) FROM users) AS users_total"
      )->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      $stats = $rows[0] ?? [];

      return [
        'mairies_total' => (int) ($stats['mairies_total'] ?? 0),
        'agents_total' => (int) ($stats['agents_total'] ?? 0),
        'merchants_total' => (int) ($stats['merchants_total'] ?? 0),
        'users_total' => (int) ($stats['users_total'] ?? 0),
      ];
    } catch (\Throwable $exception) {
      return $default;
    }
  }

  /**
   * Mairie dashboard counters scoped to connected mairie commune.
   */
  public function mairieActorsStatsByLogin(
    string $login
  ): array {
    $default = [
      'agents_total' => 0,
      'merchants_total' => 0,
      'users_total' => 0,
    ];

    $login = trim($login);
    if ($login === '') {
      return $default;
    }

    try {
      $userRows = $this->chaine(
        "SELECT commune_id FROM users WHERE login = ? LIMIT 1"
      )->param([$login])->getQuery();

      $userRows = $this->normalizeRows(is_array($userRows) ? $userRows : []);
      $communeId = (int) ($userRows[0]['commune_id'] ?? 0);

      if ($communeId <= 0) {
        return $default;
      }

      $rows = $this->chaine(
        "SELECT
          (SELECT COUNT(*) FROM agents WHERE commune_id = ?) AS agents_total,
          (SELECT COUNT(*) FROM merchants WHERE commune_id = ?) AS merchants_total,
          (SELECT COUNT(*) FROM users WHERE commune_id = ?) AS users_total"
      )->param([$communeId, $communeId, $communeId])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      $stats = $rows[0] ?? [];

      return [
        'agents_total' => (int) ($stats['agents_total'] ?? 0),
        'merchants_total' => (int) ($stats['merchants_total'] ?? 0),
        'users_total' => (int) ($stats['users_total'] ?? 0),
      ];
    } catch (\Throwable $exception) {
      return $default;
    }
  }

  /**
   * Recent activities for one mairie scope (contributors + payments).
   */
  public function listCommuneActivities(
    string $mairieLogin,
    int $limit = 25
  ): array {
    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return [];
    }

    try {
      $userRows = $this->chaine(
        "SELECT c.name AS commune_name
         FROM users u
         LEFT JOIN communes c ON c.id = u.commune_id
         WHERE u.login = ?"
      )->param([$mairieLogin])->getQuery();

      $userRows = $this->normalizeRows(is_array($userRows) ? $userRows : []);
      $communeName = trim((string) ($userRows[0]['commune_name'] ?? ''));

      if ($communeName === '') {
        return [];
      }

      $safeLimit = max(1, min(100, $limit));
      $paginationSql = match (_FIRST_DRIVER_) {
        'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
        default => " LIMIT $safeLimit",
      };

      $rows = $this->chaine(
        "SELECT *
         FROM (
           SELECT
             'ENREGISTREMENT' AS activity_type,
             c.created_at AS activity_date,
             c.merchant_id AS reference_id,
             c.name AS actor_name,
             c.business AS details,
             NULL AS amount,
             c.status AS activity_status
           FROM contributors c
           WHERE c.commune = ?

           UNION ALL

           SELECT
             'PAIEMENT' AS activity_type,
             p.paid_at AS activity_date,
             p.receipt_ref AS reference_id,
             p.merchant_id AS actor_name,
             p.channel AS details,
             p.amount AS amount,
             p.status AS activity_status
           FROM tax_payments p
           INNER JOIN contributors c ON c.merchant_id = p.merchant_id
           WHERE c.commune = ?
         ) x
         ORDER BY activity_date DESC
         $paginationSql"
      )->param([$communeName, $communeName])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Graph data for mairie statistics dashboard.
   */
  public function mairieStatisticsGraphsData(
    string $mairieLogin
  ): array {
    $default = [
      'actors' => [
        'agents_total' => 0,
        'merchants_total' => 0,
        'users_total' => 0,
      ],
      'contributors_total' => 0,
      'payment_status' => [
        'success' => 0,
        'pending' => 0,
        'failed' => 0,
      ],
      'payments_by_month' => [],
    ];

    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return $default;
    }

    try {
      $actors = $this->mairieActorsStatsByLogin($mairieLogin);
      $scope = $this->getTaxeConnectAccessScope($mairieLogin, 2);
      $communeName = (string) ($scope['commune'] ?? '');
      $contributorsTotal = $communeName !== ''
        ? $this->countAllContributorsScoped($communeName)
        : 0;

      $rows = $this->chaine(
        "SELECT p.status, p.amount, p.paid_at
         FROM tax_payments p
         INNER JOIN contributors c ON c.merchant_id = p.merchant_id
         WHERE c.commune = ?"
      )->param([$communeName])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);

      $paymentStatus = [
        'success' => 0,
        'pending' => 0,
        'failed' => 0,
      ];
      $monthly = [];

      foreach ($rows as $row) {
        $status = strtolower(trim((string) ($row['status'] ?? '')));
        if (array_key_exists($status, $paymentStatus)) {
          $paymentStatus[$status]++;
        }

        $paidAt = (string) ($row['paid_at'] ?? '');
        $amount = (float) ($row['amount'] ?? 0);
        $monthKey = '';

        $timestamp = strtotime($paidAt);
        if ($timestamp !== false) {
          $monthKey = date('Y-m', $timestamp);
        }

        if ($monthKey !== '') {
          if (!isset($monthly[$monthKey])) {
            $monthly[$monthKey] = ['count' => 0, 'amount' => 0];
          }
          $monthly[$monthKey]['count']++;
          $monthly[$monthKey]['amount'] += $amount;
        }
      }

      ksort($monthly);
      $paymentsByMonth = [];
      foreach ($monthly as $month => $data) {
        $paymentsByMonth[] = [
          'month' => $month,
          'count' => (int) ($data['count'] ?? 0),
          'amount' => (float) ($data['amount'] ?? 0),
        ];
      }

      return [
        'actors' => $actors,
        'contributors_total' => (int) $contributorsTotal,
        'payment_status' => $paymentStatus,
        'payments_by_month' => $paymentsByMonth,
      ];
    } catch (\Throwable $exception) {
      return $default;
    }
  }

  /**
   * Agent dashboard counters scoped to connected agent account.
   */
  public function agentActorsStatsByLogin(
    string $agentLogin
  ): array {
    $default = [
      'merchants_total' => 0,
      'contributors_total' => 0,
      'payments_total' => 0,
    ];

    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return $default;
    }

    try {
      $agentRows = $this->chaine(
        "SELECT id FROM users WHERE login = ? AND role = 'AGENT' LIMIT 1"
      )->param([$agentLogin])->getQuery();

      $agentRows = $this->normalizeRows(is_array($agentRows) ? $agentRows : []);
      $agentUserId = (int) ($agentRows[0]['id'] ?? 0);
      if ($agentUserId <= 0) {
        return $default;
      }

      $rows = $this->chaine(
        "SELECT
          (SELECT COUNT(*) FROM merchants WHERE created_by_agent_user_id = ?) AS merchants_total,
          (SELECT COUNT(*)
           FROM contributors c
           INNER JOIN merchants m ON m.merchant_ref = c.merchant_id
           WHERE m.created_by_agent_user_id = ?) AS contributors_total,
          (SELECT COUNT(*)
           FROM tax_payments p
           INNER JOIN merchants m ON m.merchant_ref = p.merchant_id
           WHERE m.created_by_agent_user_id = ?) AS payments_total"
      )->param([$agentUserId, $agentUserId, $agentUserId])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      $stats = $rows[0] ?? [];

      return [
        'merchants_total' => (int) ($stats['merchants_total'] ?? 0),
        'contributors_total' => (int) ($stats['contributors_total'] ?? 0),
        'payments_total' => (int) ($stats['payments_total'] ?? 0),
      ];
    } catch (\Throwable $exception) {
      return $default;
    }
  }

  /**
   * Recent activities for one agent scope (merchant creation + payments).
   */
  public function listAgentActivities(
    string $agentLogin,
    int $limit = 25
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    try {
      $agentRows = $this->chaine(
        "SELECT id FROM users WHERE login = ? AND role = 'AGENT' LIMIT 1"
      )->param([$agentLogin])->getQuery();

      $agentRows = $this->normalizeRows(is_array($agentRows) ? $agentRows : []);
      $agentUserId = (int) ($agentRows[0]['id'] ?? 0);
      if ($agentUserId <= 0) {
        return [];
      }

      $safeLimit = max(1, min(100, $limit));
      $paginationSql = match (_FIRST_DRIVER_) {
        'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
        default => " LIMIT $safeLimit",
      };

      $rows = $this->chaine(
        "SELECT *
         FROM (
           SELECT
             'CREATION_COMMERÇANT' AS activity_type,
             m.created_at AS activity_date,
             m.merchant_ref AS reference_id,
             m.owner_name AS actor_name,
             m.business_name AS details,
             NULL AS amount,
             m.status AS activity_status
           FROM merchants m
           WHERE m.created_by_agent_user_id = ?

           UNION ALL

           SELECT
             'PAIEMENT' AS activity_type,
             p.paid_at AS activity_date,
             p.receipt_ref AS reference_id,
             p.merchant_id AS actor_name,
             p.channel AS details,
             p.amount AS amount,
             p.status AS activity_status
           FROM tax_payments p
           INNER JOIN merchants m ON m.merchant_ref = p.merchant_id
           WHERE m.created_by_agent_user_id = ?
         ) x
         ORDER BY activity_date DESC
         $paginationSql"
      )->param([$agentUserId, $agentUserId])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List merchant payment states for agent commune.
   */
  public function listAgentMerchantsPaymentStatus(
    string $agentLogin
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    try {
      $rows = $this->chaine(
        "SELECT
            m.merchant_ref,
            m.business_name,
            m.owner_name,
            COUNT(DISTINCT n.notice_ref) AS notices_total,
            COUNT(DISTINCT CASE WHEN p.notice_ref IS NOT NULL THEN n.notice_ref END) AS paid_notices,
            SUM(CASE WHEN p.amount IS NOT NULL THEN p.amount ELSE 0 END) AS total_paid,
            MAX(p.paid_at) AS last_payment
         FROM merchants m
         INNER JOIN users a ON a.login = ? AND a.role = 'AGENT'
         LEFT JOIN tax_notices n ON n.merchant_id = m.merchant_ref
         LEFT JOIN tax_payments p ON p.notice_ref = n.notice_ref AND p.status = 'success'
         WHERE m.commune_id = a.commune_id
         GROUP BY m.merchant_ref, m.business_name, m.owner_name
         ORDER BY m.business_name ASC"
      )->param([$agentLogin])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);

      return array_map(static function (array $row): array {
        $noticesTotal = (int) ($row['notices_total'] ?? 0);
        $paidNotices = (int) ($row['paid_notices'] ?? 0);
        $unpaidNotices = max(0, $noticesTotal - $paidNotices);

        $state = 'AUCUN_AVIS';
        if ($noticesTotal > 0) {
          $state = $unpaidNotices === 0 ? 'PAYE' : 'IMPAYE';
        }

        $row['notices_total'] = $noticesTotal;
        $row['paid_notices'] = $paidNotices;
        $row['unpaid_notices'] = $unpaidNotices;
        $row['total_paid'] = (float) ($row['total_paid'] ?? 0);
        $row['payment_state'] = $state;

        return $row;
      }, $rows);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Detailed statistics by commune/mairie account.
   */
  public function listMairieStatistics(): array
  {
    try {
      $rows = $this->chaine(
        "SELECT
          c.id AS commune_id,
          c.code AS commune_code,
          c.name AS commune_name,
          c.created_at AS commune_created_at,
          um.login AS mairie_login,
          um.full_name AS mairie_full_name,
          um.status AS mairie_status,
          (SELECT COUNT(*) FROM agents a WHERE a.commune_id = c.id) AS agents_total,
          (SELECT COUNT(*) FROM merchants m WHERE m.commune_id = c.id) AS merchants_total,
          (SELECT COUNT(*) FROM contributors co WHERE LOWER(co.commune) = LOWER(c.name)) AS contributors_total
        FROM communes c
        LEFT JOIN mairies ma ON ma.commune_id = c.id
        LEFT JOIN users um ON um.id = ma.mairie_admin_user_id
        WHERE ma.mairie_admin_user_id IS NOT NULL
        ORDER BY c.name ASC"
      )->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List taxes created by current mairie commune.
   */
  public function listCommuneTaxesByLogin(
    string $mairieLogin
  ): array {
    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return [];
    }

    try {
      $this->chaine(
        "CREATE TABLE IF NOT EXISTS commune_taxes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            commune_id INTEGER NOT NULL,
            code TEXT NOT NULL,
            label TEXT NOT NULL,
            amount REAL NOT NULL DEFAULT 0,
            frequency TEXT DEFAULT 'ANNUELLE',
            status TEXT NOT NULL DEFAULT 'ACTIVE',
            created_by_user_id INTEGER NOT NULL,
            created_at TEXT DEFAULT NULL
        )"
      )->setQuery();

      $rows = $this->chaine(
        "SELECT
            t.id,
            t.code,
            t.label,
            t.amount,
            t.frequency,
            t.status,
            t.created_at
         FROM commune_taxes t
         INNER JOIN users mairie ON mairie.login = ? AND mairie.role = 'MAIRIE_ADMIN'
         WHERE t.commune_id = mairie.commune_id
         ORDER BY t.created_at DESC, t.id DESC"
      )->param([$mairieLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List active commune taxes visible by agent scope.
   */
  public function listCommuneTaxesForAgent(
    string $agentLogin
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    try {
      $this->chaine(
        "CREATE TABLE IF NOT EXISTS commune_taxes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            commune_id INTEGER NOT NULL,
            code TEXT NOT NULL,
            label TEXT NOT NULL,
            amount REAL NOT NULL DEFAULT 0,
            frequency TEXT DEFAULT 'ANNUELLE',
            status TEXT NOT NULL DEFAULT 'ACTIVE',
            created_by_user_id INTEGER NOT NULL,
            created_at TEXT DEFAULT NULL
        )"
      )->setQuery();

      $rows = $this->chaine(
        "SELECT t.id, t.code, t.label, t.amount, t.frequency, t.status
         FROM commune_taxes t
         INNER JOIN users a ON a.login = ? AND a.role = 'AGENT'
         WHERE t.commune_id = a.commune_id
         ORDER BY t.label ASC"
      )->param([$agentLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List merchants visible by agent scope (same commune).
   */
  public function listCommuneMerchantsForAgent(
    string $agentLogin
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    try {
      $rows = $this->chaine(
        "SELECT m.merchant_ref, m.business_name, m.owner_name, m.status
         FROM merchants m
         INNER JOIN users a ON a.login = ? AND a.role = 'AGENT'
         WHERE m.commune_id = a.commune_id
         ORDER BY m.business_name ASC"
      )->param([$agentLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List tax assignments made in agent commune.
   */
  public function listTaxAssignmentsForAgent(
    string $agentLogin,
    int $limit = 50
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    $safeLimit = max(1, min(200, $limit));
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
      default => " LIMIT $safeLimit",
    };

    try {
      $this->chaine(
        "CREATE TABLE IF NOT EXISTS merchant_tax_assignments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            commune_id INTEGER NOT NULL,
            merchant_ref TEXT NOT NULL,
            tax_id INTEGER NOT NULL,
            assigned_by_agent_user_id INTEGER NOT NULL,
            assigned_at TEXT DEFAULT NULL,
            status TEXT NOT NULL DEFAULT 'ACTIVE'
        )"
      )->setQuery();

      $rows = $this->chaine(
        "SELECT
            a.id,
            a.assigned_at,
            a.status,
            a.merchant_ref,
            t.code AS tax_code,
            t.label AS tax_label,
            t.amount AS tax_amount,
            m.business_name
         FROM merchant_tax_assignments a
         INNER JOIN users agent ON agent.login = ? AND agent.role = 'AGENT'
         INNER JOIN commune_taxes t ON t.id = a.tax_id
         LEFT JOIN merchants m ON m.merchant_ref = a.merchant_ref
         WHERE a.commune_id = agent.commune_id
         ORDER BY a.assigned_at DESC, a.id DESC
         $paginationSql"
      )->param([$agentLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Ensure mairie messaging table exists.
   */
  private function ensureMairieMessagesTable(): void
  {
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
      return;
    }

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

  /**
   * Count unread mairie messages for connected mairie login.
   */
  public function countUnreadMairieMessages(
    string $mairieLogin
  ): int {
    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return 0;
    }

    try {
      $this->ensureMairieMessagesTable();
      $rows = $this->chaine(
        "SELECT COUNT(*) AS nbre
         FROM mairie_messages
         WHERE receiver_login = ?
         AND is_read = 0"
      )->param([$mairieLogin])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      return (int) ($rows[0]['nbre'] ?? 0);
    } catch (\Throwable $exception) {
      return 0;
    }
  }

  /**
   * List all mairie admin logins except current one.
   */
  public function listOtherMairieLogins(
    string $currentLogin
  ): array {
    $currentLogin = trim($currentLogin);
    if ($currentLogin === '') {
      return [];
    }

    try {
      $rows = $this->chaine(
        "SELECT u.login, u.full_name, c.name AS commune_name
         FROM users u
         LEFT JOIN communes c ON c.id = u.commune_id
         WHERE u.role = 'MAIRIE_ADMIN'
         AND u.login <> ?
         AND u.status = 'ACTIVE'
         ORDER BY c.name ASC, u.login ASC"
      )->param([$currentLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List active super admin logins.
   */
  public function listSuperAdminLogins(): array
  {
    try {
      $rows = $this->chaine(
        "SELECT u.login, u.full_name
         FROM users u
         WHERE u.role = 'SUPER_ADMIN'
         AND u.status = 'ACTIVE'
         ORDER BY u.login ASC"
      )->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List active mairie admin logins (for super admin messaging).
   */
  public function listAllMairieLogins(): array
  {
    try {
      $rows = $this->chaine(
        "SELECT u.login, u.full_name, c.name AS commune_name
         FROM users u
         LEFT JOIN communes c ON c.id = u.commune_id
         WHERE u.role = 'MAIRIE_ADMIN'
         AND u.status = 'ACTIVE'
         ORDER BY c.name ASC, u.login ASC"
      )->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Inbox for one mairie account.
   */
  public function listMairieInbox(
    string $mairieLogin,
    int $limit = 100
  ): array {
    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return [];
    }

    $safeLimit = max(1, min(500, $limit));
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
      default => " LIMIT $safeLimit",
    };

    try {
      $this->ensureMairieMessagesTable();
      $rows = $this->chaine(
        "SELECT
            m.id, m.sender_login, m.receiver_login, m.subject, m.message_text, m.parent_id, m.thread_ref,
            m.attachment_path, m.attachment_name, m.attachment_mime, m.is_read, m.created_at, m.read_at,
            COALESCE(ua.namesurname, us.full_name, m.sender_login) AS sender_name,
            COALESCE(ub.namesurname, ur.full_name, m.receiver_login) AS receiver_name
         FROM mairie_messages m
         LEFT JOIN users us ON us.login = m.sender_login
         LEFT JOIN users ur ON ur.login = m.receiver_login
         LEFT JOIN usersaccount ua ON ua.login = m.sender_login
         LEFT JOIN usersaccount ub ON ub.login = m.receiver_login
         WHERE (m.receiver_login = ? OR m.sender_login = ?)
         ORDER BY m.created_at DESC, m.id DESC
         $paginationSql"
      )->param([$mairieLogin, $mairieLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Get one mairie message scoped to connected user.
   */
  public function getMairieMessageByIdForUser(
    string $login,
    int $messageId
  ): array {
    $login = trim($login);
    if ($login === '' || $messageId <= 0) {
      return [];
    }

    try {
      $this->ensureMairieMessagesTable();
      $rows = $this->chaine(
        "SELECT
            m.id, m.sender_login, m.receiver_login, m.subject, m.message_text, m.parent_id, m.thread_ref,
            m.attachment_path, m.attachment_name, m.attachment_mime, m.is_read, m.created_at, m.read_at,
            COALESCE(ua.namesurname, us.full_name, m.sender_login) AS sender_name,
            COALESCE(ub.namesurname, ur.full_name, m.receiver_login) AS receiver_name
         FROM mairie_messages m
         LEFT JOIN users us ON us.login = m.sender_login
         LEFT JOIN users ur ON ur.login = m.receiver_login
         LEFT JOIN usersaccount ua ON ua.login = m.sender_login
         LEFT JOIN usersaccount ub ON ub.login = m.receiver_login
         WHERE m.id = ?
         AND (m.sender_login = ? OR m.receiver_login = ?)
         LIMIT 1"
      )->param([$messageId, $login, $login])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      return $rows[0] ?? [];
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List one conversation thread scoped to connected mairie login.
   */
  public function listMairieThreadForUser(
    string $login,
    string $threadRef,
    int $limit = 300
  ): array {
    $login = trim($login);
    $threadRef = trim($threadRef);
    if ($login === '' || $threadRef === '') {
      return [];
    }

    $safeLimit = max(1, min(500, $limit));
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
      default => " LIMIT $safeLimit",
    };

    try {
      $this->ensureMairieMessagesTable();
      $rows = $this->chaine(
        "SELECT
            m.id, m.sender_login, m.receiver_login, m.subject, m.message_text, m.parent_id, m.thread_ref,
            m.attachment_path, m.attachment_name, m.attachment_mime, m.is_read, m.created_at, m.read_at,
            COALESCE(ua.namesurname, us.full_name, m.sender_login) AS sender_name,
            COALESCE(ub.namesurname, ur.full_name, m.receiver_login) AS receiver_name
         FROM mairie_messages m
         LEFT JOIN users us ON us.login = m.sender_login
         LEFT JOIN users ur ON ur.login = m.receiver_login
         LEFT JOIN usersaccount ua ON ua.login = m.sender_login
         LEFT JOIN usersaccount ub ON ub.login = m.receiver_login
         WHERE m.thread_ref = ?
         AND (m.sender_login = ? OR m.receiver_login = ?)
         ORDER BY m.created_at ASC, m.id ASC
         $paginationSql"
      )->param([$threadRef, $login, $login])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Resolve commune id for one agent login.
   */
  private function agentCommuneIdByLogin(
    string $agentLogin
  ): int {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return 0;
    }

    try {
      $rows = $this->chaine(
        "SELECT commune_id
         FROM users
         WHERE login = ?
         AND role = 'AGENT'
         LIMIT 1"
      )->param([$agentLogin])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      return (int) ($rows[0]['commune_id'] ?? 0);
    } catch (\Throwable $exception) {
      return 0;
    }
  }

  /**
   * Ensure agent messaging table exists.
   */
  private function ensureAgentMessagesTable(): void
  {
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
      return;
    }

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

  /**
   * Count unread agent messages for connected agent.
   */
  public function countUnreadAgentMessages(
    string $agentLogin
  ): int {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return 0;
    }

    try {
      $this->ensureAgentMessagesTable();
      $rows = $this->chaine(
        "SELECT COUNT(*) AS nbre
         FROM agent_messages
         WHERE receiver_login = ?
         AND is_read = 0"
      )->param([$agentLogin])->getQuery();
      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      return (int) ($rows[0]['nbre'] ?? 0);
    } catch (\Throwable $exception) {
      return 0;
    }
  }

  /**
   * List other agents in same commune.
   */
  public function listOtherAgentsSameCommune(
    string $agentLogin
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    try {
      $rows = $this->chaine(
        "SELECT a.login, a.full_name
         FROM users a
         INNER JOIN users me ON me.login = ? AND me.role = 'AGENT'
         WHERE a.role = 'AGENT'
         AND a.login <> me.login
         AND a.commune_id = me.commune_id
         AND a.status = 'ACTIVE'
         ORDER BY a.login ASC"
      )->param([$agentLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List active agents for one mairie (same commune).
   */
  public function listAgentsForMairie(
    string $mairieLogin
  ): array {
    $mairieLogin = trim($mairieLogin);
    if ($mairieLogin === '') {
      return [];
    }

    try {
      $rows = $this->chaine(
        "SELECT a.login, a.full_name
         FROM users a
         INNER JOIN users m ON m.login = ? AND m.role = 'MAIRIE_ADMIN'
         WHERE a.role = 'AGENT'
         AND a.commune_id = m.commune_id
         AND a.status = 'ACTIVE'
         ORDER BY a.login ASC"
      )->param([$mairieLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List mairie admin contact for one agent (same commune).
   */
  public function listMairieForAgent(
    string $agentLogin
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    try {
      $rows = $this->chaine(
        "SELECT m.login, m.full_name
         FROM users m
         INNER JOIN users a ON a.login = ? AND a.role = 'AGENT'
         WHERE m.role = 'MAIRIE_ADMIN'
         AND m.commune_id = a.commune_id
         AND m.status = 'ACTIVE'
         ORDER BY m.login ASC"
      )->param([$agentLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List active merchants for one agent (same commune).
   */
  public function listMerchantsForAgent(
    string $agentLogin
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    try {
      $rows = $this->chaine(
        "SELECT u.login, u.full_name
         FROM users u
         INNER JOIN users a ON a.login = ? AND a.role = 'AGENT'
         WHERE u.role = 'MERCHANT'
         AND u.commune_id = a.commune_id
         AND u.status = 'ACTIVE'
         ORDER BY u.login ASC"
      )->param([$agentLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List active agents for one merchant (same commune).
   */
  public function listAgentsForMerchant(
    string $merchantLogin
  ): array {
    $merchantLogin = trim($merchantLogin);
    if ($merchantLogin === '') {
      return [];
    }

    try {
      $rows = $this->chaine(
        "SELECT u.login, u.full_name
         FROM users u
         INNER JOIN users m ON m.login = ? AND m.role = 'MERCHANT'
         WHERE u.role = 'AGENT'
         AND u.commune_id = m.commune_id
         AND u.status = 'ACTIVE'
         ORDER BY u.login ASC"
      )->param([$merchantLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List communes where a mairie account is already active.
   */
  public function listOpenCommunesForMerchantSignup(): array
  {
    try {
      $rows = $this->chaine(
        "SELECT c.id, c.code, c.name
         FROM communes c
         INNER JOIN mairies m ON m.commune_id = c.id
         INNER JOIN users u ON u.id = m.mairie_admin_user_id
         WHERE u.status = 'ACTIVE'
         ORDER BY c.name ASC"
      )->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Agent inbox.
   */
  public function listAgentInbox(
    string $agentLogin,
    int $limit = 100
  ): array {
    $agentLogin = trim($agentLogin);
    if ($agentLogin === '') {
      return [];
    }

    $safeLimit = max(1, min(500, $limit));
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
      default => " LIMIT $safeLimit",
    };

    try {
      $this->ensureAgentMessagesTable();
      $rows = $this->chaine(
        "SELECT
            m.id, m.commune_id, m.sender_login, m.receiver_login, m.subject, m.message_text, m.parent_id, m.thread_ref,
            m.attachment_path, m.attachment_name, m.attachment_mime, m.is_read, m.created_at, m.read_at,
            COALESCE(ua.namesurname, us.full_name, m.sender_login) AS sender_name,
            COALESCE(ub.namesurname, ur.full_name, m.receiver_login) AS receiver_name
         FROM agent_messages m
         LEFT JOIN users us ON us.login = m.sender_login
         LEFT JOIN users ur ON ur.login = m.receiver_login
         LEFT JOIN usersaccount ua ON ua.login = m.sender_login
         LEFT JOIN usersaccount ub ON ub.login = m.receiver_login
         WHERE (m.receiver_login = ? OR m.sender_login = ?)
         ORDER BY m.created_at DESC, m.id DESC
         $paginationSql"
      )->param([$agentLogin, $agentLogin])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Get one agent message for sender/receiver scope.
   */
  public function getAgentMessageByIdForUser(
    string $login,
    int $messageId
  ): array {
    $login = trim($login);
    if ($login === '' || $messageId <= 0) {
      return [];
    }

    try {
      $this->ensureAgentMessagesTable();
      $rows = $this->chaine(
        "SELECT
            m.id, m.commune_id, m.sender_login, m.receiver_login, m.subject, m.message_text, m.parent_id, m.thread_ref,
            m.attachment_path, m.attachment_name, m.attachment_mime, m.is_read, m.created_at, m.read_at,
            COALESCE(ua.namesurname, us.full_name, m.sender_login) AS sender_name,
            COALESCE(ub.namesurname, ur.full_name, m.receiver_login) AS receiver_name
         FROM agent_messages m
         LEFT JOIN users us ON us.login = m.sender_login
         LEFT JOIN users ur ON ur.login = m.receiver_login
         LEFT JOIN usersaccount ua ON ua.login = m.sender_login
         LEFT JOIN usersaccount ub ON ub.login = m.receiver_login
         WHERE m.id = ?
         AND (m.sender_login = ? OR m.receiver_login = ?)
         LIMIT 1"
      )->param([$messageId, $login, $login])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      return $rows[0] ?? [];
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Thread messages for one agent scope.
   */
  public function listAgentThreadForUser(
    string $login,
    string $threadRef,
    int $limit = 300
  ): array {
    $login = trim($login);
    $threadRef = trim($threadRef);
    if ($login === '' || $threadRef === '') {
      return [];
    }

    $safeLimit = max(1, min(500, $limit));
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
      default => " LIMIT $safeLimit",
    };

    try {
      $this->ensureAgentMessagesTable();
      $rows = $this->chaine(
        "SELECT
            m.id, m.commune_id, m.sender_login, m.receiver_login, m.subject, m.message_text, m.parent_id, m.thread_ref,
            m.attachment_path, m.attachment_name, m.attachment_mime, m.is_read, m.created_at, m.read_at,
            COALESCE(ua.namesurname, us.full_name, m.sender_login) AS sender_name,
            COALESCE(ub.namesurname, ur.full_name, m.receiver_login) AS receiver_name
         FROM agent_messages m
         LEFT JOIN users us ON us.login = m.sender_login
         LEFT JOIN users ur ON ur.login = m.receiver_login
         LEFT JOIN usersaccount ua ON ua.login = m.sender_login
         LEFT JOIN usersaccount ub ON ub.login = m.receiver_login
         WHERE m.thread_ref = ?
         AND (m.sender_login = ? OR m.receiver_login = ?)
         ORDER BY m.created_at ASC, m.id ASC
         $paginationSql"
      )->param([$threadRef, $login, $login])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Resolve merchant reference from connected merchant login.
   */
  private function merchantRefByLogin(
    string $merchantLogin
  ): string {
    $merchantLogin = trim($merchantLogin);
    if ($merchantLogin === '') {
      return '';
    }

    try {
      $rows = $this->chaine(
        "SELECT m.merchant_ref
         FROM merchants m
         INNER JOIN users u ON u.id = m.user_id
         WHERE u.login = ?
         LIMIT 1"
      )->param([$merchantLogin])->getQuery();

      $rows = $this->normalizeRows(is_array($rows) ? $rows : []);
      return trim((string) ($rows[0]['merchant_ref'] ?? ''));
    } catch (\Throwable $exception) {
      return '';
    }
  }

  /**
   * Merchant dashboard KPIs (strictly scoped to connected merchant).
   */
  public function merchantDashboardStatsByLogin(
    string $merchantLogin
  ): array {
    $default = [
      'merchant_ref' => '',
      'business_name' => '',
      'owner_name' => '',
      'commune_name' => '',
      'total_notices' => 0,
      'paid_notices' => 0,
      'unpaid_notices' => 0,
      'total_due' => 0,
      'total_paid' => 0,
      'outstanding' => 0,
    ];

    $merchantLogin = trim($merchantLogin);
    if ($merchantLogin === '') {
      return $default;
    }

    try {
      $merchantRows = $this->chaine(
        "SELECT
            m.merchant_ref,
            m.business_name,
            m.owner_name,
            c.name AS commune_name
         FROM merchants m
         INNER JOIN users u ON u.id = m.user_id
         LEFT JOIN communes c ON c.id = m.commune_id
         WHERE u.login = ?
         LIMIT 1"
      )->param([$merchantLogin])->getQuery();

      $merchantRows = $this->normalizeRows(is_array($merchantRows) ? $merchantRows : []);
      $merchant = $merchantRows[0] ?? [];
      $merchantRef = trim((string) ($merchant['merchant_ref'] ?? ''));

      if ($merchantRef === '') {
        return $default;
      }

      $noticesRows = $this->chaine(
        "SELECT
            COUNT(*) AS total_notices,
            SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) AS paid_notices,
            SUM(CASE WHEN status <> 'paid' THEN 1 ELSE 0 END) AS unpaid_notices,
            COALESCE(SUM(amount_due), 0) AS total_due
         FROM tax_notices
         WHERE merchant_id = ?"
      )->param([$merchantRef])->getQuery();

      $noticesRows = $this->normalizeRows(is_array($noticesRows) ? $noticesRows : []);
      $notices = $noticesRows[0] ?? [];

      $paymentsRows = $this->chaine(
        "SELECT COALESCE(SUM(amount), 0) AS total_paid
         FROM tax_payments
         WHERE merchant_id = ?
         AND status = 'success'"
      )->param([$merchantRef])->getQuery();

      $paymentsRows = $this->normalizeRows(is_array($paymentsRows) ? $paymentsRows : []);
      $totalPaid = (float) ($paymentsRows[0]['total_paid'] ?? 0);
      $totalDue = (float) ($notices['total_due'] ?? 0);

      return [
        'merchant_ref' => $merchantRef,
        'business_name' => (string) ($merchant['business_name'] ?? ''),
        'owner_name' => (string) ($merchant['owner_name'] ?? ''),
        'commune_name' => (string) ($merchant['commune_name'] ?? ''),
        'total_notices' => (int) ($notices['total_notices'] ?? 0),
        'paid_notices' => (int) ($notices['paid_notices'] ?? 0),
        'unpaid_notices' => (int) ($notices['unpaid_notices'] ?? 0),
        'total_due' => $totalDue,
        'total_paid' => $totalPaid,
        'outstanding' => max(0, $totalDue - $totalPaid),
      ];
    } catch (\Throwable $exception) {
      return $default;
    }
  }

  /**
   * List tax notices visible by connected merchant only.
   */
  public function listMerchantNoticesByLogin(
    string $merchantLogin,
    int $limit = 20
  ): array {
    $merchantRef = $this->merchantRefByLogin($merchantLogin);
    if ($merchantRef === '') {
      return [];
    }

    $safeLimit = max(1, min(200, $limit));
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
      default => " LIMIT $safeLimit",
    };

    try {
      $rows = $this->chaine(
        "SELECT notice_ref, fiscal_period, amount_due, status, due_date, created_at
         FROM tax_notices
         WHERE merchant_id = ?
         ORDER BY created_at DESC, _id DESC
         $paginationSql"
      )->param([$merchantRef])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * List payments visible by connected merchant only.
   */
  public function listMerchantPaymentsByLogin(
    string $merchantLogin,
    int $limit = 20
  ): array {
    $merchantRef = $this->merchantRefByLogin($merchantLogin);
    if ($merchantRef === '') {
      return [];
    }

    $safeLimit = max(1, min(200, $limit));
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
      default => " LIMIT $safeLimit",
    };

    try {
      $rows = $this->chaine(
        "SELECT receipt_ref, notice_ref, amount, channel, status, paid_at
         FROM tax_payments
         WHERE merchant_id = ?
         ORDER BY paid_at DESC, _id DESC
         $paginationSql"
      )->param([$merchantRef])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }

  /**
   * Recent merchant activities (notices + payments).
   */
  public function listMerchantActivitiesByLogin(
    string $merchantLogin,
    int $limit = 20
  ): array {
    $merchantRef = $this->merchantRefByLogin($merchantLogin);
    if ($merchantRef === '') {
      return [];
    }

    $safeLimit = max(1, min(200, $limit));
    $paginationSql = match (_FIRST_DRIVER_) {
      'oracle', 'sqlserver' => " OFFSET 0 ROWS FETCH NEXT $safeLimit ROWS ONLY",
      default => " LIMIT $safeLimit",
    };

    try {
      $rows = $this->chaine(
        "SELECT *
         FROM (
           SELECT
             'AVIS' AS activity_type,
             n.created_at AS activity_date,
             n.notice_ref AS reference_id,
             n.fiscal_period AS details,
             n.amount_due AS amount,
             n.status AS activity_status
           FROM tax_notices n
           WHERE n.merchant_id = ?
           UNION ALL
           SELECT
             'PAIEMENT' AS activity_type,
             p.paid_at AS activity_date,
             p.receipt_ref AS reference_id,
             p.channel AS details,
             p.amount AS amount,
             p.status AS activity_status
           FROM tax_payments p
           WHERE p.merchant_id = ?
         ) x
         ORDER BY activity_date DESC
         $paginationSql"
      )->param([$merchantRef, $merchantRef])->getQuery();

      return $this->normalizeRows(is_array($rows) ? $rows : []);
    } catch (\Throwable $exception) {
      return [];
    }
  }
  
  /**
   * Request to get list of users recents actions
   * @param int $currentPage
   * @param int $numLine
   * @return array
   */
  public function listOfRecentActions(
    int $currentPage, 
    int $numLine
  ):array{

    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->noSqlListOfRecentActions($currentPage, $numLine),
      'redis' => $this->noSqlRedisListOfRecentActions($currentPage, $numLine),
      'oracle' => $this->oracleListOfRecentActions($currentPage, $numLine),
      'sqlserver' => $this->sqlServerListOfRecentActions($currentPage, $numLine),

      default => $this->defaultSqlListOfRecentActions($currentPage, $numLine),
    };           
  }   
}
