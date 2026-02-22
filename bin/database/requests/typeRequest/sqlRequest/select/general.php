<?php

namespace Epaphrodites\database\requests\typeRequest\sqlRequest\select;

use Epaphrodites\database\requests\typeRequest\noSqlRequest\select\general as SelectGeneral;

class general extends SelectGeneral
{

    /**
     * Normalize a date string to SQL datetime format.
     */
    private function toSqlDateTime(
        ?string $value,
        string $fallbackTime = '00:00:00'
    ): ?string {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value . ' ' . $fallbackTime;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}$/', $value) === 1) {
            return $value;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Normalize legacy date formats in existing TaxeConnect rows.
     */
    private function normalizeTaxeConnectDates(): void
    {
        $notices = $this->normalizeRows(
            $this->table('tax_notices')->SQuery('notice_ref, due_date')
        );

        foreach ($notices as $row) {
            $dueDate = $this->toSqlDateTime((string)($row['due_date'] ?? ''), '00:00:00');
            if ($dueDate === null) {
                continue;
            }

            if (($row['due_date'] ?? '') !== $dueDate) {
                $this->table('tax_notices')
                    ->set(['due_date'])
                    ->where('notice_ref')
                    ->param([$dueDate, (string)$row['notice_ref']])
                    ->UQuery();
            }
        }

        $payments = $this->normalizeRows(
            $this->table('tax_payments')->SQuery('receipt_ref, paid_at')
        );

        foreach ($payments as $row) {
            $paidAt = $this->toSqlDateTime((string)($row['paid_at'] ?? ''), '00:00:00');
            if ($paidAt === null) {
                continue;
            }

            if (($row['paid_at'] ?? '') !== $paidAt) {
                $this->table('tax_payments')
                    ->set(['paid_at'])
                    ->where('receipt_ref')
                    ->param([$paidAt, (string)$row['receipt_ref']])
                    ->UQuery();
            }
        }
    }

    /**
     * Normalize keys for drivers returning upper-case names.
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
     * Return scalar value from a single-row query.
     */
    private function scalar(
        array $rows,
        string $key,
        mixed $default = 0
    ): mixed {
        $rows = $this->normalizeRows($rows);
        return $rows[0][$key] ?? $default;
    }

    /**
     * Seed MVP data once if contributors table is empty.
     */
    public function ensureTaxeConnectMvpData(): void
    {
        // Keep existing rows consistent before any read.
        try {
            $this->normalizeTaxeConnectDates();
        } catch (\Throwable $exception) {
            // Table may not exist yet before migrations.
        }

        $existing = $this->table('contributors')->SQuery("COUNT(*) AS nbre");
        $existingCount = (int) $this->scalar($existing, 'nbre', 0);

        if ($existingCount > 0) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $contributors = [
            ['TCI-1001', 'Kouassi Koffi', 'Boutique alimentaire', 'Yopougon', '+225 07 01 02 03 04', 'Actif'],
            ['TCI-1002', 'Awa Traore', 'Salon de coiffure', 'Cocody', '+225 05 11 22 33 44', 'Actif'],
            ['TCI-1003', 'Yao Nguessan', 'Atelier de couture', 'Abobo', '+225 01 22 33 44 55', 'En attente'],
            ['TCI-1004', 'Nadia Diabate', 'Pharmacie de quartier', 'Treichville', '+225 07 55 66 77 88', 'Actif'],
            ['TCI-1005', 'Seydou Coulibaly', 'Librairie', 'Plateau', '+225 05 99 88 77 66', 'Suspendu'],
        ];

        foreach ($contributors as $row) {
            $this->table('contributors')
                ->insert('merchant_id, name, business, commune, contact, status, created_at')
                ->values('?, ?, ?, ?, ?, ?, ?')
                ->param([$row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $now])
                ->IQuery();
        }

        $notices = [
            ['NTC-2026-0001', 'TCI-1001', '2026-Q1', 180000, 'paid', '2026-01-30 00:00:00'],
            ['NTC-2026-0002', 'TCI-1002', '2026-Q1', 145000, 'paid', '2026-01-28 00:00:00'],
            ['NTC-2026-0003', 'TCI-1003', '2026-Q1', 95000, 'unpaid', '2026-02-15 00:00:00'],
            ['NTC-2026-0004', 'TCI-1004', '2026-Q1', 210000, 'partial', '2026-02-10 00:00:00'],
            ['NTC-2026-0005', 'TCI-1005', '2026-Q1', 125000, 'overdue', '2026-01-20 00:00:00'],
            ['NTC-2026-0006', 'TCI-1001', '2026-Q2', 185000, 'unpaid', '2026-04-20 00:00:00'],
        ];

        foreach ($notices as $row) {
            $this->table('tax_notices')
                ->insert('notice_ref, merchant_id, fiscal_period, amount_due, status, due_date, created_at')
                ->values('?, ?, ?, ?, ?, ?, ?')
                ->param([$row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $now])
                ->IQuery();
        }

        $payments = [
            ['RCP-2026-0001', 'NTC-2026-0001', 'TCI-1001', 180000, 'orange_money', 'success', '2026-01-22 10:12:20', 'QR-TCI-0001'],
            ['RCP-2026-0002', 'NTC-2026-0002', 'TCI-1002', 145000, 'mtn_momo', 'success', '2026-01-25 16:02:10', 'QR-TCI-0002'],
            ['RCP-2026-0003', 'NTC-2026-0004', 'TCI-1004', 120000, 'moov_money', 'success', '2026-02-04 09:44:55', 'QR-TCI-0003'],
        ];

        foreach ($payments as $row) {
            $this->table('tax_payments')
                ->insert('receipt_ref, notice_ref, merchant_id, amount, channel, status, paid_at, qr_token')
                ->values('?, ?, ?, ?, ?, ?, ?, ?')
                ->param([$row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]])
                ->IQuery();
        }
    }

    /**
     * TaxeConnect KPI summary for dashboard.
     */
    public function taxeConnectSummary(): array
    {
        $totalContributors = (int) $this->scalar(
            $this->table('contributors')->SQuery("COUNT(*) AS nbre"),
            'nbre',
            0
        );
        $activeContributors = (int) $this->scalar(
            $this->table('contributors')->where('status')->param(['Actif'])->SQuery("COUNT(*) AS nbre"),
            'nbre',
            0
        );
        $pendingContributors = (int) $this->scalar(
            $this->table('contributors')->where('status')->param(['En attente'])->SQuery("COUNT(*) AS nbre"),
            'nbre',
            0
        );

        $totalNotices = (int) $this->scalar(
            $this->table('tax_notices')->SQuery("COUNT(*) AS nbre"),
            'nbre',
            0
        );
        $unpaidNotices = (int) $this->scalar(
            $this->table('tax_notices')->where('status')->param(['unpaid'])->SQuery("COUNT(*) AS nbre"),
            'nbre',
            0
        );
        $overdueNotices = (int) $this->scalar(
            $this->table('tax_notices')->where('status')->param(['overdue'])->SQuery("COUNT(*) AS nbre"),
            'nbre',
            0
        );

        $assessedAmount = (float) $this->scalar(
            $this->table('tax_notices')->SQuery("COALESCE(SUM(amount_due), 0) AS amount"),
            'amount',
            0
        );
        $paidAmount = (float) $this->scalar(
            $this->table('tax_payments')->SQuery("COALESCE(SUM(amount), 0) AS amount"),
            'amount',
            0
        );

        $collectionRate = $assessedAmount > 0 ? round(($paidAmount / $assessedAmount) * 100, 2) : 0;
        $outstandingAmount = max(0, $assessedAmount - $paidAmount);

        $recentPayments = $this->normalizeRows(
            $this->table('tax_payments')
                ->orderBy('paid_at', 'DESC')
                ->limit(0, 6)
                ->SQuery()
        );

        return [
            'total_contributors' => $totalContributors,
            'active_contributors' => $activeContributors,
            'pending_contributors' => $pendingContributors,
            'total_notices' => $totalNotices,
            'unpaid_notices' => $unpaidNotices,
            'overdue_notices' => $overdueNotices,
            'assessed_amount' => $assessedAmount,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $outstandingAmount,
            'collection_rate' => $collectionRate,
            'recent_payments' => $recentPayments,
        ];
    }

    /**
     * Request select last users history actions (For: sqlServer)
     * 
     * @return array
     */
    public function sqlSeverHistoryRequest():array
    {
       
        $UserConnected = static::initNamespace()['session']->login();

        $result = $this->table('history')
            ->like('actions')
            ->orderBy('_id', 'DESC')
            ->offset(0,6)
            ->param([$UserConnected])
            ->SQuery();
       
        return $result;
    }

    /**
     * Request select last users history actions (For: oracle )
     * 
     * @return array
     */
    public function oracleHistoryRequest():array
    {
       
        $UserConnected = static::initNamespace()['session']->login();
       
        $result = $this->table('history')
            ->like('actions')
            ->orderBy('"_id"', 'DESC')
            ->offset(0,6)
            ->param([$UserConnected])
            ->SQuery();
            
        return static::initNamespace()['env']->dictKeyToLowers($result);
    }    

    /**
     * Request select last users history actions (For: Mysql, postgres, sqLite)
     * 
     * @return array
     */
    public function defaultSqlHistoryRequest():array
    {
       
        $UserConnected = static::initNamespace()['session']->login();

        $result = $this->table('history')
            ->like('actions')
            ->orderBy('_id', 'DESC')
            ->limit(0,6)
            ->param([$UserConnected])
            ->SQuery();
       
        return $result;
    }    
}
