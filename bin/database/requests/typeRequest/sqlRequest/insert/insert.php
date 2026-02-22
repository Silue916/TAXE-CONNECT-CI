<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\insert;

use Epaphrodites\database\requests\typeRequest\noSqlRequest\insert\insert as InsertInsert;

class insert extends InsertInsert
{
    /**
     * Ensure commune taxes catalog table exists.
     */
    private function ensureCommuneTaxesTable(): void
    {
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

        // Best effort index creation for performance.
        $this->chaine("CREATE INDEX IF NOT EXISTS idx_commune_taxes_commune_id ON commune_taxes (commune_id)")->setQuery();
        $this->chaine("CREATE UNIQUE INDEX IF NOT EXISTS uq_commune_taxes_commune_code ON commune_taxes (commune_id, code)")->setQuery();
    }

    /**
     * Ensure merchant tax assignment table exists.
     */
    private function ensureMerchantTaxAssignmentsTable(): void
    {
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

        $this->chaine("CREATE INDEX IF NOT EXISTS idx_mta_commune_id ON merchant_tax_assignments (commune_id)")->setQuery();
        $this->chaine("CREATE INDEX IF NOT EXISTS idx_mta_merchant_ref ON merchant_tax_assignments (merchant_ref)")->setQuery();
        $this->chaine("CREATE INDEX IF NOT EXISTS idx_mta_tax_id ON merchant_tax_assignments (tax_id)")->setQuery();
        $this->chaine("CREATE UNIQUE INDEX IF NOT EXISTS uq_mta_merchant_tax ON merchant_tax_assignments (merchant_ref, tax_id)")->setQuery();
    }

    /**
     * Fetch one TaxeConnect user by login.
     */
    private function taxeConnectUserByLogin(
        string $login
    ): array {
        $rows = $this->chaine(
            "SELECT id, login, role, commune_id
             FROM users
             WHERE login = ?"
        )->param([$login])->getQuery();

        $rows = static::initNamespace()['env']->dictKeyToLowers(is_array($rows) ? $rows : []);
        return $rows[0] ?? [];
    }

    /**
     * Ensure creator exists in TaxeConnect users table.
     */
    private function ensureTaxeConnectCreator(
        string $login,
        string $role
    ): array {
        $creator = $this->taxeConnectUserByLogin($login);
        if (!empty($creator)) {
            return $creator;
        }

        $this->table('users')
            ->insert('login, password_hash, full_name, role, status')
            ->values('?, ?, ?, ?, ?')
            ->param([
                $login,
                static::initConfig()['guard']->CryptPassword($login),
                $login,
                $role,
                'ACTIVE',
            ])
            ->IQuery();

        return $this->taxeConnectUserByLogin($login);
    }

    /**
     * Fetch commune id by code.
     */
    private function communeIdByCode(
        string $code
    ): ?int {
        $rows = $this->chaine(
            "SELECT id
             FROM communes
             WHERE code = ?"
        )->param([$code])->getQuery();

        $rows = static::initNamespace()['env']->dictKeyToLowers(is_array($rows) ? $rows : []);
        return isset($rows[0]['id']) ? (int) $rows[0]['id'] : null;
    }

    /**
     * Create commune and mairie admin account (only by super admin).
     */
    public function sqlCreateMairieAccount(
        array $payload,
        string $creatorLogin
    ): bool {
        $creator = $this->taxeConnectUserByLogin($creatorLogin);
        if (($creator['role'] ?? '') !== 'SUPER_ADMIN') {
            $creator = $this->ensureTaxeConnectCreator($creatorLogin, 'SUPER_ADMIN');
        }

        if (($creator['role'] ?? '') !== 'SUPER_ADMIN') {
            return false;
        }

        $communeCode = strtoupper(trim((string) ($payload['commune_code'] ?? '')));
        $communeName = trim((string) ($payload['commune_name'] ?? ''));
        $login = trim((string) ($payload['login'] ?? ''));
        $password = (string) ($payload['password'] ?? '');
        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $email = trim((string) ($payload['email'] ?? ''));
        $phone = trim((string) ($payload['phone'] ?? ''));

        if ($communeCode === '' || $communeName === '' || $login === '' || $password === '') {
            return false;
        }

        if (!empty($this->taxeConnectUserByLogin($login))) {
            return false;
        }

        $communeId = $this->communeIdByCode($communeCode);
        if ($communeId === null) {
            $this->table('communes')
                ->insert('code, name')
                ->values('?, ?')
                ->param([$communeCode, $communeName])
                ->IQuery();

            $communeId = $this->communeIdByCode($communeCode);
        }

        if ($communeId === null) {
            return false;
        }

        $existingMairie = $this->chaine(
            "SELECT id
             FROM mairies
             WHERE commune_id = ?"
        )->param([$communeId])->getQuery();

        if (!empty($existingMairie)) {
            return false;
        }

        $this->table('users')
            ->insert('login, password_hash, full_name, email, phone, role, status, commune_id, created_by')
            ->values('?, ?, ?, ?, ?, ?, ?, ?, ?')
            ->param([
                $login,
                static::initConfig()['guard']->CryptPassword($password),
                $fullName !== '' ? $fullName : null,
                $email !== '' ? $email : null,
                $phone !== '' ? $phone : null,
                'MAIRIE_ADMIN',
                'ACTIVE',
                $communeId,
                (int) $creator['id'],
            ])
            ->IQuery();

        $mairieUser = $this->taxeConnectUserByLogin($login);
        if (empty($mairieUser['id'])) {
            return false;
        }

        $this->table('mairies')
            ->insert('commune_id, mairie_admin_user_id, created_by_super_admin')
            ->values('?, ?, ?')
            ->param([$communeId, (int) $mairieUser['id'], (int) $creator['id']])
            ->IQuery();

        // Keep compatibility with current Epaphrodites auth table.
        if (count(static::initQuery()['getid']->sqlGetUsersDatas($login)) < 1) {
            $this->table('usersaccount')
                ->insert('login, password, usersgroup')
                ->values('?, ?, ?')
                ->param([$login, static::initConfig()['guard']->CryptPassword($password), 2])
                ->IQuery();
        }

        return true;
    }

    /**
     * Create agent account (only by mairie admin and in own commune).
     */
    public function sqlCreateAgentAccount(
        array $payload,
        string $creatorLogin
    ): bool {
        $creator = $this->taxeConnectUserByLogin($creatorLogin);
        if (($creator['role'] ?? '') !== 'MAIRIE_ADMIN') {
            return false;
        }

        $creatorCommuneId = isset($creator['commune_id']) ? (int) $creator['commune_id'] : 0;
        if ($creatorCommuneId <= 0) {
            return false;
        }

        $login = trim((string) ($payload['login'] ?? ''));
        $password = (string) ($payload['password'] ?? '');
        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $email = trim((string) ($payload['email'] ?? ''));
        $phone = trim((string) ($payload['phone'] ?? ''));

        if ($login === '' || $password === '') {
            return false;
        }

        if (!empty($this->taxeConnectUserByLogin($login))) {
            return false;
        }

        $this->table('users')
            ->insert('login, password_hash, full_name, email, phone, role, status, commune_id, created_by')
            ->values('?, ?, ?, ?, ?, ?, ?, ?, ?')
            ->param([
                $login,
                static::initConfig()['guard']->CryptPassword($password),
                $fullName !== '' ? $fullName : null,
                $email !== '' ? $email : null,
                $phone !== '' ? $phone : null,
                'AGENT',
                'ACTIVE',
                $creatorCommuneId,
                (int) $creator['id'],
            ])
            ->IQuery();

        $agentUser = $this->taxeConnectUserByLogin($login);
        if (empty($agentUser['id'])) {
            return false;
        }

        $this->table('agents')
            ->insert('commune_id, agent_user_id, created_by_mairie_admin')
            ->values('?, ?, ?')
            ->param([$creatorCommuneId, (int) $agentUser['id'], (int) $creator['id']])
            ->IQuery();

        // Keep compatibility with current Epaphrodites auth table.
        if (count(static::initQuery()['getid']->sqlGetUsersDatas($login)) < 1) {
            $this->table('usersaccount')
                ->insert('login, password, usersgroup')
                ->values('?, ?, ?')
                ->param([$login, static::initConfig()['guard']->CryptPassword($password), 3])
                ->IQuery();
        }

        return true;
    }

    /**
     * Create one tax rule for commune (only by mairie admin in own commune).
     */
    public function sqlCreateCommuneTax(
        array $payload,
        string $creatorLogin
    ): bool {
        $creator = $this->taxeConnectUserByLogin($creatorLogin);
        if (($creator['role'] ?? '') !== 'MAIRIE_ADMIN') {
            return false;
        }

        $communeId = isset($creator['commune_id']) ? (int) $creator['commune_id'] : 0;
        if ($communeId <= 0) {
            return false;
        }

        $code = strtoupper(trim((string) ($payload['code'] ?? '')));
        $label = trim((string) ($payload['label'] ?? ''));
        $amount = (float) ($payload['amount'] ?? 0);
        $frequency = strtoupper(trim((string) ($payload['frequency'] ?? 'ANNUELLE')));
        $status = strtoupper(trim((string) ($payload['status'] ?? 'ACTIVE')));

        if ($code === '' || $label === '' || $amount <= 0) {
            return false;
        }

        if (!in_array($frequency, ['ANNUELLE', 'MENSUELLE', 'TRIMESTRIELLE', 'PONCTUELLE'], true)) {
            $frequency = 'ANNUELLE';
        }

        if (!in_array($status, ['ACTIVE', 'INACTIVE'], true)) {
            $status = 'ACTIVE';
        }

        $this->ensureCommuneTaxesTable();

        $existing = $this->chaine(
            "SELECT id
             FROM commune_taxes
             WHERE commune_id = ?
             AND code = ?"
        )->param([$communeId, $code])->getQuery();

        if (!empty($existing)) {
            return false;
        }

        $this->table('commune_taxes')
            ->insert('commune_id, code, label, amount, frequency, status, created_by_user_id, created_at')
            ->values('?, ?, ?, ?, ?, ?, ?, ?')
            ->param([
                $communeId,
                $code,
                $label,
                $amount,
                $frequency,
                $status,
                (int) ($creator['id'] ?? 0),
                date('Y-m-d H:i:s'),
            ])
            ->IQuery();

        return true;
    }

    /**
     * Create merchant account (only by agent and in own commune).
     */
    public function sqlCreateMerchantAccount(
        array $payload,
        string $creatorLogin
    ): bool {
        $creator = $this->taxeConnectUserByLogin($creatorLogin);
        if (($creator['role'] ?? '') !== 'AGENT') {
            return false;
        }

        $creatorCommuneId = isset($creator['commune_id']) ? (int) $creator['commune_id'] : 0;
        if ($creatorCommuneId <= 0) {
            return false;
        }

        $login = trim((string) ($payload['login'] ?? ''));
        $password = (string) ($payload['password'] ?? '');
        $businessName = trim((string) ($payload['business_name'] ?? ''));
        $ownerName = trim((string) ($payload['owner_name'] ?? ''));
        $phone = trim((string) ($payload['phone'] ?? ''));
        $email = trim((string) ($payload['email'] ?? ''));
        $activity = trim((string) ($payload['activity'] ?? ''));
        $address = trim((string) ($payload['address'] ?? ''));

        if ($login === '' || $password === '' || $businessName === '' || $ownerName === '') {
            return false;
        }

        if (!empty($this->taxeConnectUserByLogin($login))) {
            return false;
        }

        $communeCodeRows = $this->chaine(
            "SELECT code
             FROM communes
             WHERE id = ?"
        )->param([$creatorCommuneId])->getQuery();
        $communeCodeRows = static::initNamespace()['env']->dictKeyToLowers(is_array($communeCodeRows) ? $communeCodeRows : []);
        $communeCode = strtoupper((string) ($communeCodeRows[0]['code'] ?? 'COM'));

        $merchantRef = sprintf(
            '%s-%s-%04d',
            $communeCode,
            date('ymdHis'),
            random_int(1, 9999)
        );

        $this->table('users')
            ->insert('login, password_hash, full_name, email, phone, role, status, commune_id, created_by')
            ->values('?, ?, ?, ?, ?, ?, ?, ?, ?')
            ->param([
                $login,
                static::initConfig()['guard']->CryptPassword($password),
                $ownerName,
                $email !== '' ? $email : null,
                $phone !== '' ? $phone : null,
                'MERCHANT',
                'ACTIVE',
                $creatorCommuneId,
                (int) $creator['id'],
            ])
            ->IQuery();

        $merchantUser = $this->taxeConnectUserByLogin($login);
        if (empty($merchantUser['id'])) {
            return false;
        }

        $this->table('merchants')
            ->insert('merchant_ref, user_id, commune_id, created_by_agent_user_id, business_name, owner_name, phone, email, activity, address, status, created_at')
            ->values('?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?')
            ->param([
                $merchantRef,
                (int) $merchantUser['id'],
                $creatorCommuneId,
                (int) $creator['id'],
                $businessName,
                $ownerName,
                $phone !== '' ? $phone : null,
                $email !== '' ? $email : null,
                $activity !== '' ? $activity : null,
                $address !== '' ? $address : null,
                'ACTIVE',
                date('Y-m-d H:i:s'),
            ])
            ->IQuery();

        if (count(static::initQuery()['getid']->sqlGetUsersDatas($login)) < 1) {
            $this->table('usersaccount')
                ->insert('login, password, usersgroup')
                ->values('?, ?, ?')
                ->param([$login, static::initConfig()['guard']->CryptPassword($password), 4])
                ->IQuery();
        }

        return true;
    }

    /**
     * Assign one commune tax type to a merchant (by agent scope).
     */
    public function sqlAssignTaxToMerchant(
        array $payload,
        string $agentLogin
    ): bool {
        $agent = $this->taxeConnectUserByLogin($agentLogin);
        if (($agent['role'] ?? '') !== 'AGENT') {
            return false;
        }

        $communeId = (int) ($agent['commune_id'] ?? 0);
        $agentUserId = (int) ($agent['id'] ?? 0);
        if ($communeId <= 0 || $agentUserId <= 0) {
            return false;
        }

        $merchantRef = trim((string) ($payload['merchant_ref'] ?? ''));
        $taxId = (int) ($payload['tax_id'] ?? 0);
        if ($merchantRef === '' || $taxId <= 0) {
            return false;
        }

        $this->ensureCommuneTaxesTable();
        $this->ensureMerchantTaxAssignmentsTable();

        $merchant = $this->chaine(
            "SELECT id
             FROM merchants
             WHERE merchant_ref = ?
             AND commune_id = ?"
        )->param([$merchantRef, $communeId])->getQuery();

        if (empty($merchant)) {
            return false;
        }

        $tax = $this->chaine(
            "SELECT id, status
             FROM commune_taxes
             WHERE id = ?
             AND commune_id = ?"
        )->param([$taxId, $communeId])->getQuery();
        $tax = static::initNamespace()['env']->dictKeyToLowers(is_array($tax) ? $tax : []);

        if (empty($tax[0]['id'])) {
            return false;
        }

        // Block assignment of inactive tax types.
        if (strtoupper((string) ($tax[0]['status'] ?? 'ACTIVE')) !== 'ACTIVE') {
            return false;
        }

        $existing = $this->chaine(
            "SELECT id
             FROM merchant_tax_assignments
             WHERE merchant_ref = ?
             AND tax_id = ?"
        )->param([$merchantRef, $taxId])->getQuery();

        if (!empty($existing)) {
            return false;
        }

        $this->table('merchant_tax_assignments')
            ->insert('commune_id, merchant_ref, tax_id, assigned_by_agent_user_id, assigned_at, status')
            ->values('?, ?, ?, ?, ?, ?')
            ->param([
                $communeId,
                $merchantRef,
                $taxId,
                $agentUserId,
                date('Y-m-d H:i:s'),
                'ACTIVE',
            ])
            ->IQuery();

        return true;
    }

    /**
     * Add users to the system from the console
     *
     * @param string|null $login
     * @param string|null $password
     * @param int|null $usersGroup
     * @return bool
     */
    public function sqlConsoleAddUsers(
        string|null $login = null, 
        string|null $password = null, 
        int|null $usersGroup = null
    ):bool
    {

        $usersGroup = $usersGroup !== NULL ? $usersGroup : 1;

        if (!empty($login) && count(static::initQuery()['getid']->sqlGetUsersDatas($login)) < 1) {

            $this->table('usersaccount')
                ->insert(' login , password , usersgroup ')
                ->values(' ? , ? , ? ')
                ->param([static::initNamespace()['env']->no_space($login), static::initConfig()['guard']->CryptPassword($password), $usersGroup])
                ->IQuery();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Add users to the system
     *
     * @param string|null $login
     * @param int|null $usersgroup
     * @return bool
     */
    public function sqlAddUsers(
        string|null $login = null, 
        int|null $usersgroup = null
    ):bool
    {

        if (!empty($login) && !empty($usersgroup) && count(static::initQuery()['getid']->sqlGetUsersDatas($login)) < 1) {

            $this->table('usersaccount')
                ->insert(' login , password , usersgroup ')
                ->values(' ? , ? , ? ')
                ->param([static::initNamespace()['env']->no_space($login), static::initConfig()['guard']->CryptPassword($login), $usersgroup])
                ->IQuery();

            $actions = "Add a User : " . $login;
            static::initQuery()['setting']->ActionsRecente($actions);

            return true;
        } else {
            return false;
        }
    }   
}
