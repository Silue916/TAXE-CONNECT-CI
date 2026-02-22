<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\delete;

use Epaphrodites\database\requests\typeRequest\noSqlRequest\delete\delete as DeleteDelete;

class delete extends DeleteDelete
{
    /**
     * Find one commune account scope for deletion review.
     */
    public function sqlFindCommuneAccountForDeletion(
        string $communeName
    ): array {
        $communeName = trim($communeName);
        if ($communeName === '') {
            return [];
        }

        $rows = $this->chaine(
            "SELECT
                c.id AS commune_id,
                c.code AS commune_code,
                c.name AS commune_name,
                u.id AS mairie_user_id,
                u.login AS mairie_login,
                u.full_name AS mairie_full_name,
                u.email AS mairie_email,
                u.phone AS mairie_phone,
                u.status AS mairie_status
             FROM communes c
             LEFT JOIN mairies m ON m.commune_id = c.id
             LEFT JOIN users u ON u.id = m.mairie_admin_user_id
             WHERE LOWER(c.name) = LOWER(?)"
        )->param([$communeName])->getQuery();

        $rows = static::initNamespace()['env']->dictKeyToLowers(is_array($rows) ? $rows : []);
        $scope = $rows[0] ?? [];

        if (empty($scope)) {
            return [];
        }

        $communeId = (int) ($scope['commune_id'] ?? 0);
        $name = (string) ($scope['commune_name'] ?? '');

        $agents = $this->chaine(
            "SELECT COUNT(*) AS nbre FROM agents WHERE commune_id = ?"
        )->param([$communeId])->getQuery();
        $agents = static::initNamespace()['env']->dictKeyToLowers(is_array($agents) ? $agents : []);

        $merchants = $this->chaine(
            "SELECT COUNT(*) AS nbre FROM merchants WHERE commune_id = ?"
        )->param([$communeId])->getQuery();
        $merchants = static::initNamespace()['env']->dictKeyToLowers(is_array($merchants) ? $merchants : []);

        $contributors = $this->chaine(
            "SELECT COUNT(*) AS nbre FROM contributors WHERE LOWER(commune) = LOWER(?)"
        )->param([$name])->getQuery();
        $contributors = static::initNamespace()['env']->dictKeyToLowers(is_array($contributors) ? $contributors : []);

        return array_merge($scope, [
            'agents_total' => (int) ($agents[0]['nbre'] ?? 0),
            'merchants_total' => (int) ($merchants[0]['nbre'] ?? 0),
            'contributors_total' => (int) ($contributors[0]['nbre'] ?? 0),
        ]);
    }

    /**
     * Delete one commune account and all linked data.
     */
    public function sqlDeleteCommuneAccountCascade(
        string $communeName
    ): bool {
        $scope = $this->sqlFindCommuneAccountForDeletion($communeName);
        if (empty($scope)) {
            return false;
        }

        $communeId = (int) ($scope['commune_id'] ?? 0);
        $communeRealName = (string) ($scope['commune_name'] ?? '');
        if ($communeId <= 0 || $communeRealName === '') {
            return false;
        }

        $pdo = $this->db();

        try {
            $pdo->beginTransaction();

            // Remove payments/notices bound to contributors of the commune.
            $stmt = $pdo->prepare(
                "DELETE FROM tax_payments
                 WHERE notice_ref IN (
                   SELECT n.notice_ref
                   FROM tax_notices n
                   INNER JOIN contributors c ON c.merchant_id = n.merchant_id
                   WHERE LOWER(c.commune) = LOWER(?)
                 )"
            );
            $stmt->execute([$communeRealName]);

            $stmt = $pdo->prepare(
                "DELETE FROM tax_notices
                 WHERE merchant_id IN (
                   SELECT merchant_id FROM contributors WHERE LOWER(commune) = LOWER(?)
                 )"
            );
            $stmt->execute([$communeRealName]);

            $stmt = $pdo->prepare("DELETE FROM contributors WHERE LOWER(commune) = LOWER(?)");
            $stmt->execute([$communeRealName]);

            // Remove login compatibility rows first for commune SQL users.
            $stmt = $pdo->prepare(
                "DELETE FROM usersaccount
                 WHERE login IN (
                   SELECT login FROM users WHERE commune_id = ?
                 )"
            );
            $stmt->execute([$communeId]);

            // Remove commune business layer.
            $stmt = $pdo->prepare("DELETE FROM merchants WHERE commune_id = ?");
            $stmt->execute([$communeId]);

            $stmt = $pdo->prepare("DELETE FROM agents WHERE commune_id = ?");
            $stmt->execute([$communeId]);

            $stmt = $pdo->prepare("DELETE FROM mairies WHERE commune_id = ?");
            $stmt->execute([$communeId]);

            // Remove all SQL users of this commune (mairie, agents, merchants).
            $stmt = $pdo->prepare("DELETE FROM users WHERE commune_id = ?");
            $stmt->execute([$communeId]);

            // Remove commune.
            $stmt = $pdo->prepare("DELETE FROM communes WHERE id = ?");
            $stmt->execute([$communeId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        }
    }

    /**
     * List agent accounts scoped to one mairie login.
     */
    public function sqlListMairieAgents(
        string $mairieLogin
    ): array {
        $mairieLogin = trim($mairieLogin);
        if ($mairieLogin === '') {
            return [];
        }

        $rows = $this->chaine(
            "SELECT
                u.id AS user_id,
                u.login,
                u.full_name,
                u.email,
                u.phone,
                u.status,
                u.created_at
             FROM users u
             INNER JOIN users mairie ON mairie.login = ? AND mairie.role = 'MAIRIE_ADMIN'
             WHERE u.role = 'AGENT'
             AND u.commune_id = mairie.commune_id
             ORDER BY u.login ASC"
        )->param([$mairieLogin])->getQuery();

        return static::initNamespace()['env']->dictKeyToLowers(is_array($rows) ? $rows : []);
    }

    /**
     * Delete one agent account only (without deleting merchants data).
     */
    public function sqlDeleteAgentAccountOnly(
        string $mairieLogin,
        string $agentLogin
    ): bool {
        $mairieLogin = trim($mairieLogin);
        $agentLogin = trim($agentLogin);

        if ($mairieLogin === '' || $agentLogin === '') {
            return false;
        }

        $agentRows = $this->chaine(
            "SELECT
                u.id AS user_id,
                u.login
             FROM users u
             INNER JOIN users mairie ON mairie.login = ? AND mairie.role = 'MAIRIE_ADMIN'
             WHERE u.login = ?
             AND u.role = 'AGENT'
             AND u.commune_id = mairie.commune_id"
        )->param([$mairieLogin, $agentLogin])->getQuery();

        $agentRows = static::initNamespace()['env']->dictKeyToLowers(is_array($agentRows) ? $agentRows : []);
        $agent = $agentRows[0] ?? [];

        if (empty($agent['user_id'])) {
            return false;
        }

        $userId = (int) $agent['user_id'];
        $login = (string) $agent['login'];

        $pdo = $this->db();

        try {
            $pdo->beginTransaction();

            // Keep merchants/contributors/payments untouched by design.
            $stmt = $pdo->prepare("DELETE FROM agents WHERE agent_user_id = ?");
            $stmt->execute([$userId]);

            $stmt = $pdo->prepare("DELETE FROM usersaccount WHERE login = ?");
            $stmt->execute([$login]);

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        }
    }
}
