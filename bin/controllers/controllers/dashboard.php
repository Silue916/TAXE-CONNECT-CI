<?php

namespace Epaphrodites\controllers\controllers;

use Epaphrodites\controllers\switchers\MainSwitchers;

final class dashboard extends MainSwitchers
{

    private object $count;
    private object $select;
    private object $contributorsSelect;
    private object $insert;
    private object $update;

    /**
     * Build TaxeConnect dashboard data.
     */
    private function taxeConnectDashboardData(): array
    {
        $default = [
            'total_contributors' => 0,
            'active_contributors' => 0,
            'pending_contributors' => 0,
            'total_notices' => 0,
            'unpaid_notices' => 0,
            'overdue_notices' => 0,
            'assessed_amount' => 0,
            'paid_amount' => 0,
            'outstanding_amount' => 0,
            'collection_rate' => 0,
            'recent_payments' => [],
            'warning' => '',
            'generated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $this->select->ensureTaxeConnectMvpData();
            $summary = $this->select->taxeConnectSummary();
            return array_merge($default, $summary);
        } catch (\Throwable $exception) {
            $default['warning'] = "Migration requise: execute `php heredia migrate -u` puis recharge le dashboard.";
            return $default;
        }
    }

    /**
     * Initialize object properties when an instance is created
     * 
     * @return void
     */
    public final function __construct()
    {
        $this->initializeObjects();
    }    

   /**
     * Initialize each property using values retrieved from static configurations
     * 
     * @return void
     */
    private function initializeObjects(): void
    {
        $this->count = $this->getObject( static::$initQueryConfig , "count");
        $this->select = $this->getObject( static::$initQueryConfig , 'general');
        $this->contributorsSelect = $this->getObject( static::$initQueryConfig , 'select');
        $this->insert = $this->getObject(static::$initQueryConfig, 'insert');
        $this->update = $this->getObject(static::$initQueryConfig, 'update');
    }

    /**
     * Shared renderer for role dashboards.
     */
    private function renderRoleDashboard(
        string $html,
        array $extra = []
    ): void
    {
        $dashboard = $this->taxeConnectDashboardData();

        $this->views(
            $html,
            array_merge([
                'select' => $this->select,
                'count' => $this->count,
                'dashboard' => $dashboard
            ], $extra),
            true
        );
    }

    /**
     * Dashboard for super admin
     * 
     * @param string $html
     * @return void
     */
    public final function superAdmin(
        string $html
    ):void
    {
        if (!$this->allowRoles([1])) {
            return;
        }

        $board = [];
        $boardMessage = '';
        $actorsStats = [
            'mairies_total' => 0,
            'agents_total' => 0,
            'merchants_total' => 0,
        ];

        try {
            $board = $this->contributorsSelect->listCommuneMairieAgentBoard();
            $actorsStats = $this->contributorsSelect->superAdminActorsStats();
        } catch (\Throwable $exception) {
            $boardMessage = "Le tableau communes/mairies/agents est indisponible. Verifie les migrations SQL.";
        }

        $this->renderRoleDashboard(
            $html,
            [
                'commune_board' => $board,
                'commune_board_message' => $boardMessage,
                'actors_stats' => $actorsStats,
            ]
        );
    }

    /**
     * Dashboard for mairie account.
     * 
     * @param string $html
     * @return void
     */
    public final function compteMairie(
        string $html
    ): void
    {
        if (!$this->allowRoles([2])) {
            return;
        }

        $actorsStats = [
            'agents_total' => 0,
            'merchants_total' => 0,
            'users_total' => 0,
        ];
        $communeActivities = [];
        $communeActivitiesMessage = '';
        $login = (string) static::class('session')->login();

        try {
            $actorsStats = $this->contributorsSelect->mairieActorsStatsByLogin(
                $login
            );
            $communeActivities = $this->contributorsSelect->listCommuneActivities($login, 25);
        } catch (\Throwable $exception) {
            $actorsStats = [
                'agents_total' => 0,
                'merchants_total' => 0,
                'users_total' => 0,
            ];
            $communeActivitiesMessage = "Impossible de charger les activites de la commune.";
        }

        $this->renderRoleDashboard(
            $html,
            [
                'actors_stats' => $actorsStats,
                'commune_activities' => $communeActivities,
                'commune_activities_message' => $communeActivitiesMessage,
            ]
        );
    }

    /**
     * Backward compatibility for old route dashboard/administrator/.
     *
     * @param string $html
     * @return void
     */
    public final function administrator(
        string $html
    ): void
    {
        if (!$this->allowRoles([2])) {
            return;
        }

        $this->renderRoleDashboard('dashboardFolder/compte_mairie_ep');
    }
    
    /**
     * Dashboard for users
     * 
     * @param string $html
     * @return void
     */
    public final function users(
        string $html
    ): void
    {
        if (!$this->allowRoles([4])) {
            return;
        }

        $merchantStats = [
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
        $merchantNotices = [];
        $merchantPayments = [];
        $merchantActivities = [];
        $merchantMessage = '';
        $login = (string) static::class('session')->login();

        try {
            $merchantStats = $this->contributorsSelect->merchantDashboardStatsByLogin($login);
            $merchantNotices = $this->contributorsSelect->listMerchantNoticesByLogin($login, 15);
            $merchantPayments = $this->contributorsSelect->listMerchantPaymentsByLogin($login, 15);
            $merchantActivities = $this->contributorsSelect->listMerchantActivitiesByLogin($login, 20);
        } catch (\Throwable $exception) {
            $merchantMessage = "Impossible de charger les donnees du compte commercant.";
        }

        $this->renderRoleDashboard(
            $html,
            [
                'merchant_stats' => $merchantStats,
                'merchant_notices' => $merchantNotices,
                'merchant_payments' => $merchantPayments,
                'merchant_activities' => $merchantActivities,
                'merchant_message' => $merchantMessage,
            ]
        );
    }

    /**
     * Merchant page: tax notices of connected merchant only.
     *
     * @param string $html
     * @return void
     */
    public final function mesAvis(
        string $html
    ): void
    {
        if (!$this->allowRoles([4])) {
            return;
        }

        $login = (string) static::class('session')->login();
        $notices = [];
        $stats = [];

        try {
            $notices = $this->contributorsSelect->listMerchantNoticesByLogin($login, 100);
            $stats = $this->contributorsSelect->merchantDashboardStatsByLogin($login);
        } catch (\Throwable $exception) {
            $notices = [];
            $stats = [];
        }

        $this->views(
            $html,
            [
                'notices' => $notices,
                'stats' => $stats,
            ],
            true
        );
    }

    /**
     * Merchant page: payments of connected merchant only.
     *
     * @param string $html
     * @return void
     */
    public final function mesPaiements(
        string $html
    ): void
    {
        if (!$this->allowRoles([4])) {
            return;
        }

        $login = (string) static::class('session')->login();
        $payments = [];
        $stats = [];

        try {
            $payments = $this->contributorsSelect->listMerchantPaymentsByLogin($login, 100);
            $stats = $this->contributorsSelect->merchantDashboardStatsByLogin($login);
        } catch (\Throwable $exception) {
            $payments = [];
            $stats = [];
        }

        $this->views(
            $html,
            [
                'payments' => $payments,
                'stats' => $stats,
            ],
            true
        );
    }

    /**
     * Merchant page: activity timeline of connected merchant only.
     *
     * @param string $html
     * @return void
     */
    public final function historiqueCommercant(
        string $html
    ): void
    {
        if (!$this->allowRoles([4])) {
            return;
        }

        $login = (string) static::class('session')->login();
        $activities = [];
        $stats = [];

        try {
            $activities = $this->contributorsSelect->listMerchantActivitiesByLogin($login, 200);
            $stats = $this->contributorsSelect->merchantDashboardStatsByLogin($login);
        } catch (\Throwable $exception) {
            $activities = [];
            $stats = [];
        }

        $this->views(
            $html,
            [
                'activities' => $activities,
                'stats' => $stats,
            ],
            true
        );
    }

    /**
     * Dashboard for agents.
     *
     * @param string $html
     * @return void
     */
    public final function agent(
        string $html
    ): void
    {
        if (!$this->allowRoles([3])) {
            return;
        }

        $actorsStats = [
            'merchants_total' => 0,
            'contributors_total' => 0,
            'payments_total' => 0,
        ];
        $agentActivities = [];
        $agentActivitiesMessage = '';
        $login = (string) static::class('session')->login();

        try {
            $actorsStats = $this->contributorsSelect->agentActorsStatsByLogin($login);
            $agentActivities = $this->contributorsSelect->listAgentActivities($login, 25);
        } catch (\Throwable $exception) {
            $agentActivitiesMessage = "Impossible de charger les activites de l'agent.";
        }

        $this->renderRoleDashboard(
            $html,
            [
                'actors_stats' => $actorsStats,
                'agent_activities' => $agentActivities,
                'agent_activities_message' => $agentActivitiesMessage,
            ]
        );
    }

    /**
     * Statistics dashboard for mairie account.
     *
     * @param string $html
     * @return void
     */
    public final function statistiqueMairie(
        string $html
    ): void
    {
        if (!$this->allowRoles([2])) {
            return;
        }

        $graphs = [];
        try {
            $graphs = $this->contributorsSelect->mairieStatisticsGraphsData(
                (string) static::class('session')->login()
            );
        } catch (\Throwable $exception) {
            $graphs = [];
        }

        $this->views(
            $html,
            [
                'graphs' => $graphs,
            ],
            true
        );
    }

    /**
     * Statistics dashboard for created mairie accounts.
     *
     * @param string $html
     * @return void
     */
    public final function statistique(
        string $html
    ): void
    {
        if (!$this->allowRoles([1])) {
            return;
        }

        $stats = [];
        $message = '';

        try {
            $stats = $this->contributorsSelect->listMairieStatistics();
        } catch (\Throwable $exception) {
            $message = "Impossible de charger les statistiques. Verifie les migrations SQL.";
        }

        $this->views(
            $html,
            [
                'stats' => $stats,
                'message' => $message,
            ],
            true
        );
    }

    /**
     * First MVP view for taxpayer registry.
     * Read-only list before wiring DB CRUD.
     *
     * @param string $html
     * @return void
     */
    public final function contribuables(
        string $html
    ): void
    {
        if (!$this->allowRoles([1, 2, 3])) {
            return;
        }

        $contributors = [];
        $total = 0;
        $message = '';
        $scope = ['is_scoped' => false, 'commune' => null];
        $currentPage = static::isGet('_p', 'int') ? static::getGet('_p') : 1;
        $numLine = 100;

        try {
            $scope = $this->contributorsSelect->getTaxeConnectAccessScope(
                (string) static::class('session')->login(),
                (int) static::class('session')->type()
            );

            $communeScope = $scope['is_scoped'] ? (string) ($scope['commune'] ?? '') : null;

            $contributors = $this->contributorsSelect->listOfAllContributorsScoped($currentPage, $numLine, $communeScope);
            $total = $this->contributorsSelect->countAllContributorsScoped($communeScope);
        } catch (\Throwable $exception) {
            $message = "La table 'contributors' n'est pas encore migree. Execute: php heredia migrate -u";
        }

        $this->views(
            $html,
            [
                'contributors' => $contributors,
                'total' => $total,
                'message' => $message,
                'snapshot_date' => date('Y-m-d H:i:s'),
                'scope' => $scope,
            ],
            true
        );
    }

    /**
     * Transactions monitor (payments and receipt verification context).
     *
     * @param string $html
     * @return void
     */
    public final function transactions(
        string $html
    ): void
    {
        if (!$this->allowRoles([1, 2, 3])) {
            return;
        }

        $payments = [];
        $total = 0;
        $message = '';
        $numLine = 20;
        $currentPage = static::isGet('_p', 'int') ? (int) static::getGet('_p') : 1;
        $status = static::isGet('status') ? (string) static::getGet('status') : '';
        $channel = static::isGet('channel') ? (string) static::getGet('channel') : '';
        $search = static::isGet('search') ? (string) static::getGet('search') : '';
        $receipt = static::isGet('receipt') ? (string) static::getGet('receipt') : '';
        $scope = ['is_scoped' => false, 'commune' => null];
        $detail = [];

        try {
            $this->select->ensureTaxeConnectMvpData();

            $scope = $this->contributorsSelect->getTaxeConnectAccessScope(
                (string) static::class('session')->login(),
                (int) static::class('session')->type()
            );
            $communeScope = $scope['is_scoped'] ? (string) ($scope['commune'] ?? '') : null;

            $payments = $this->contributorsSelect->listTaxPaymentsScoped($currentPage, $numLine, $status, $channel, $search, $communeScope);
            $total = $this->contributorsSelect->countTaxPaymentsScoped($status, $channel, $search, $communeScope);

            if (!empty($receipt)) {
                $detail = $this->contributorsSelect->getTaxPaymentByReceiptScoped($receipt, $communeScope);
            }
        } catch (\Throwable $exception) {
            $message = "Impossible de charger les transactions. Verifie les migrations: php heredia migrate -u";
        }

        $this->views(
            $html,
            [
                'payments' => $payments,
                'total' => $total,
                'message' => $message,
                'detail' => $detail,
                'status' => $status,
                'channel' => $channel,
                'search' => $search,
                'current' => $currentPage,
                'nbrePage' => max(1, (int) ceil(($total) / $numLine)),
                'scope' => $scope,
            ],
            true
        );
    }

    /**
     * Agent payment follow-up: paid vs unpaid merchants.
     *
     * @param string $html
     * @return void
     */
    public final function suiviPaiements(
        string $html
    ): void
    {
        if (!$this->allowRoles([3])) {
            return;
        }

        $state = static::isGet('etat') ? strtoupper((string) static::getGet('etat')) : '';
        $rows = [];
        $message = '';

        try {
            $rows = $this->contributorsSelect->listAgentMerchantsPaymentStatus(
                (string) static::class('session')->login()
            );
        } catch (\Throwable $exception) {
            $message = "Impossible de charger le suivi des paiements.";
        }

        if (in_array($state, ['PAYE', 'IMPAYE', 'AUCUN_AVIS'], true)) {
            $rows = array_values(array_filter(
                $rows,
                static fn(array $row): bool => (string) ($row['payment_state'] ?? '') === $state
            ));
        }

        $paidCount = 0;
        $unpaidCount = 0;
        foreach ($rows as $row) {
            if (($row['payment_state'] ?? '') === 'PAYE') {
                $paidCount++;
            }
            if (($row['payment_state'] ?? '') === 'IMPAYE') {
                $unpaidCount++;
            }
        }

        $this->views(
            $html,
            [
                'rows' => $rows,
                'message' => $message,
                'state' => $state,
                'paid_count' => $paidCount,
                'unpaid_count' => $unpaidCount,
            ],
            true
        );
    }

    /**
     * Verify one merchant receipt inside commune scope.
     *
     * @param string $html
     * @return void
     */
    public final function verificationRecu(
        string $html
    ): void
    {
        if (!$this->allowRoles([2, 3])) {
            return;
        }

        $receipt = static::isGet('receipt') ? trim((string) static::getGet('receipt')) : '';
        $detail = [];
        $scope = ['is_scoped' => false, 'commune' => null];
        $searched = ($receipt !== '');

        try {
            $this->select->ensureTaxeConnectMvpData();
            $scope = $this->contributorsSelect->getTaxeConnectAccessScope(
                (string) static::class('session')->login(),
                (int) static::class('session')->type()
            );
            $communeScope = $scope['is_scoped'] ? (string) ($scope['commune'] ?? '') : null;

            if ($searched) {
                $detail = $this->contributorsSelect->getTaxPaymentByReceiptScoped($receipt, $communeScope);
            }
        } catch (\Throwable $exception) {
            $detail = [];
        }

        $this->views(
            $html,
            [
                'receipt' => $receipt,
                'detail' => $detail,
                'scope' => $scope,
                'searched' => $searched,
            ],
            true
        );
    }

    /**
     * Messaging center:
     * - Super admin: mairie channel.
     * - Mairie admin: commune channel with own agents.
     *
     * @param string $html
     * @return void
     */
    public final function messagerieMairie(
        string $html
    ): void
    {
        if (!$this->allowRoles([1, 2])) {
            return;
        }

        $login = (string) static::class('session')->login();
        $role = (int) static::class('session')->type();
        $message = '';
        $alert = '';
        $replyTo = static::isGet('reply', 'int') ? (int) static::getGet('reply') : 0;
        $threadRef = static::isGet('thread') ? trim((string) static::getGet('thread')) : '';
        $newConversation = static::isGet('new') && (string) static::getGet('new') === '1';
        $startTo = static::isGet('to') ? trim((string) static::getGet('to')) : '';
        $threadSource = static::isGet('src') ? trim((string) static::getGet('src')) : '';
        $replyTarget = [];
        $threadMessages = [];
        $conversationTo = '';
        $conversationName = '';
        $contacts = [];
        $superAdmins = [];
        if ($role === 2) {
            $contacts = $this->contributorsSelect->listAgentsForMairie($login);
            $superAdmins = $this->contributorsSelect->listSuperAdminLogins();
            foreach ($superAdmins as $admin) {
                $contacts[] = [
                    'login' => (string) ($admin['login'] ?? ''),
                    'full_name' => (string) ($admin['full_name'] ?? ''),
                    'commune_name' => 'Administrateur systeme',
                ];
            }
        }

        if (static::isGet('delete', 'int')) {
            if ($role === 1) {
                $this->update->deleteMairieMessageForUser($login, (int) static::getGet('delete'));
            } else {
                if ($threadSource === 'mairie') {
                    $this->update->deleteMairieMessageForUser($login, (int) static::getGet('delete'));
                } else {
                    $this->update->deleteAgentMessageForUser($login, (int) static::getGet('delete'));
                }
            }
        }

        if (static::isValidMethod(true) && static::isPost('__send__')) {
            $to = trim((string) static::getPost('__to__'));
            $subject = trim((string) static::getPost('__subject__'));
            $text = trim((string) static::getPost('__message__'));
            $replyId = static::isPost('__reply_to__', 'int') ? (int) static::getPost('__reply_to__') : null;
            $threadInput = static::isPost('__thread_ref__') ? trim((string) static::getPost('__thread_ref__')) : '';
            $attachmentPath = null;
            $attachmentName = null;
            $attachmentMime = null;

            if (static::isFileName('__attachment__')) {
                $rawName = (string) static::getFileName('__attachment__');
                $ext = strtolower((string) pathinfo($rawName, PATHINFO_EXTENSION));
                if ($ext !== 'pdf') {
                    $message = "Seuls les fichiers PDF sont autorises.";
                    $alert = 'alert-danger';
                } else {
                    $targetDir = _ROOT_ . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'mairie_messages';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0775, true);
                    }

                    $safeFile = 'msg_' . date('Ymd_His') . '_' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8) . '.pdf';
                    $tmpName = (string) ($_FILES['__attachment__']['tmp_name'] ?? '');
                    $fullPath = $targetDir . DIRECTORY_SEPARATOR . $safeFile;

                    if ($tmpName !== '' && @move_uploaded_file($tmpName, $fullPath)) {
                        $attachmentPath = 'static/docs/mairie_messages/' . $safeFile;
                        $attachmentName = $rawName;
                        $attachmentMime = 'application/pdf';
                    } else {
                        $message = "Impossible d'enregistrer la piece jointe PDF.";
                        $alert = 'alert-danger';
                    }
                }
            }

            $resolvedThread = $threadInput;
            if (($replyId ?? 0) > 0) {
                if ($role === 1) {
                    $target = $this->contributorsSelect->getMairieMessageByIdForUser($login, (int) $replyId);
                    $threadSource = 'mairie';
                } else {
                    $target = $this->contributorsSelect->getAgentMessageByIdForUser($login, (int) $replyId);
                    $threadSource = 'agent';
                    if (empty($target)) {
                        $target = $this->contributorsSelect->getMairieMessageByIdForUser($login, (int) $replyId);
                        $threadSource = 'mairie';
                    }
                }
                if (!empty($target)) {
                    $resolvedThread = trim((string) ($target['thread_ref'] ?? ''));
                    if ($resolvedThread === '') {
                        $resolvedThread = 'THR_' . (string) ($target['id'] ?? $replyId);
                    }
                }
            }

            if ($alert === '') {
                if ($role === 1) {
                    $ok = $this->insert->sendMairieMessage(
                        $login,
                        $to,
                        $subject,
                        $text,
                        $replyId,
                        $resolvedThread,
                        $attachmentPath,
                        $attachmentName,
                        $attachmentMime
                    );
                } else {
                    $isAdminTarget = false;
                    foreach ($superAdmins as $admin) {
                        if (((string) ($admin['login'] ?? '')) === $to) {
                            $isAdminTarget = true;
                            break;
                        }
                    }
                    if ($isAdminTarget) {
                        $ok = $this->insert->sendMairieMessage(
                            $login,
                            $to,
                            $subject,
                            $text,
                            $replyId,
                            $resolvedThread,
                            $attachmentPath,
                            $attachmentName,
                            $attachmentMime
                        );
                    } else {
                        $ok = $this->insert->sendAgentMessage(
                            $login,
                            $to,
                            $subject,
                            $text,
                            $replyId,
                            $resolvedThread,
                            $attachmentPath,
                            $attachmentName,
                            $attachmentMime
                        );
                    }
                }
            } else {
                $ok = false;
            }

            if ($ok) {
                $message = "Message envoye avec succes.";
                $alert = 'alert-success';
                $replyTo = 0;
            } else {
                if ($message === '') {
                    $message = $role === 1
                        ? "Echec d'envoi. Verifie le destinataire (mairie / administrateur systeme) et le contenu."
                        : "Echec d'envoi. Verifie le destinataire (agent de ta commune) et le contenu.";
                    $alert = 'alert-danger';
                }
            }
        }

        if (static::isGet('read') && static::isGet('read', 'int')) {
            if ($role === 1) {
                $this->update->markMairieMessageAsRead($login, (int) static::getGet('read'));
            } else {
                if ($threadSource === 'mairie') {
                    $this->update->markMairieMessageAsRead($login, (int) static::getGet('read'));
                } else {
                    $this->update->markAgentMessageAsRead($login, (int) static::getGet('read'));
                }
            }
        }

        if ($role === 1) {
            $contacts = $this->contributorsSelect->listAllMairieLogins();
        } else {
            $contacts = $contacts;
        }
        if ($startTo !== '') {
            foreach ($contacts as $contact) {
                if ((string) ($contact['login'] ?? '') === $startTo) {
                    $conversationTo = $startTo;
                    $conversationName = trim((string) ($contact['full_name'] ?? $contact['commune_name'] ?? $startTo));
                    break;
                }
            }
        }
        if ($role === 1) {
            $inbox = $this->contributorsSelect->listMairieInbox($login, 100);
            foreach ($inbox as $k => $row) {
                $inbox[$k]['message_source'] = 'mairie';
            }
            $unreadCount = $this->contributorsSelect->countUnreadMairieMessages($login);
        } else {
            $agentInbox = $this->contributorsSelect->listAgentInbox($login, 100);
            foreach ($agentInbox as $k => $row) {
                $agentInbox[$k]['message_source'] = 'agent';
            }
            $adminInbox = $this->contributorsSelect->listMairieInbox($login, 100);
            foreach ($adminInbox as $k => $row) {
                $adminInbox[$k]['message_source'] = 'mairie';
            }
            $inbox = array_merge($agentInbox, $adminInbox);
            usort($inbox, static function (array $a, array $b): int {
                $aDate = (string) ($a['created_at'] ?? '');
                $bDate = (string) ($b['created_at'] ?? '');
                $cmp = strcmp($bDate, $aDate);
                if ($cmp !== 0) {
                    return $cmp;
                }
                return ((int) ($b['id'] ?? 0)) <=> ((int) ($a['id'] ?? 0));
            });
            $unreadCount = $this->contributorsSelect->countUnreadAgentMessages($login)
                + $this->contributorsSelect->countUnreadMairieMessages($login);
        }
        $conversationsMap = [];
        foreach ($inbox as $row) {
            $source = trim((string) ($row['message_source'] ?? ($role === 1 ? 'mairie' : 'agent')));
            $threadKey = trim((string) ($row['thread_ref'] ?? ''));
            if ($threadKey === '') {
                $threadKey = 'THR_' . (string) ($row['id'] ?? '');
            }
            $mapKey = $source . '::' . $threadKey;
            if (!array_key_exists($mapKey, $conversationsMap)) {
                $row['thread_ref'] = $threadKey;
                $row['message_source'] = $source;
                $row['thread_unread'] = 0;
                $conversationsMap[$mapKey] = $row;
            }
            if ((int) ($row['is_read'] ?? 0) === 0) {
                $conversationsMap[$mapKey]['thread_unread'] = (int) ($conversationsMap[$mapKey]['thread_unread'] ?? 0) + 1;
            }
        }
        $conversations = array_values($conversationsMap);
        if ($replyTo > 0) {
            if ($role === 1) {
                $replyTarget = $this->contributorsSelect->getMairieMessageByIdForUser($login, $replyTo);
                $threadSource = 'mairie';
            } else {
                $replyTarget = $this->contributorsSelect->getAgentMessageByIdForUser($login, $replyTo);
                $threadSource = 'agent';
                if (empty($replyTarget)) {
                    $replyTarget = $this->contributorsSelect->getMairieMessageByIdForUser($login, $replyTo);
                    $threadSource = 'mairie';
                }
            }
            if (!empty($replyTarget)) {
                $threadRef = trim((string) ($replyTarget['thread_ref'] ?? $threadRef));
                $sender = (string) ($replyTarget['sender_login'] ?? '');
                $receiver = (string) ($replyTarget['receiver_login'] ?? '');
                if ($sender !== '' && $sender !== $login) {
                    $conversationTo = $sender;
                    $conversationName = trim((string) ($replyTarget['sender_name'] ?? $sender));
                } elseif ($receiver !== '' && $receiver !== $login) {
                    $conversationTo = $receiver;
                    $conversationName = trim((string) ($replyTarget['receiver_name'] ?? $receiver));
                }
            }
        }
        if ($threadRef !== '') {
            if ($role === 1) {
                $threadMessages = $this->contributorsSelect->listMairieThreadForUser($login, $threadRef, 300);
                $threadSource = 'mairie';
            } else {
                if ($threadSource === 'mairie') {
                    $threadMessages = $this->contributorsSelect->listMairieThreadForUser($login, $threadRef, 300);
                } elseif ($threadSource === 'agent') {
                    $threadMessages = $this->contributorsSelect->listAgentThreadForUser($login, $threadRef, 300);
                } else {
                    $threadMessages = $this->contributorsSelect->listAgentThreadForUser($login, $threadRef, 300);
                    $threadSource = 'agent';
                    if (empty($threadMessages)) {
                        $threadMessages = $this->contributorsSelect->listMairieThreadForUser($login, $threadRef, 300);
                        $threadSource = 'mairie';
                    }
                }
            }
            foreach ($threadMessages as $row) {
                $sender = (string) ($row['sender_login'] ?? '');
                $receiver = (string) ($row['receiver_login'] ?? '');
                if ($sender !== '' && $sender !== $login) {
                    $conversationTo = $sender;
                    $conversationName = trim((string) ($row['sender_name'] ?? $sender));
                    break;
                }
                if ($receiver !== '' && $receiver !== $login) {
                    $conversationTo = $receiver;
                    $conversationName = trim((string) ($row['receiver_name'] ?? $receiver));
                    break;
                }
            }
        }

        $this->views(
            $html,
            [
                'reponse' => $message,
                'alert' => $alert,
                'contacts' => $contacts,
                'inbox' => $inbox,
                'conversations' => $conversations,
                'unread_count' => $unreadCount,
                'reply_to' => $replyTo,
                'reply_target' => $replyTarget,
                'thread_ref' => $threadRef,
                'thread_source' => $threadSource,
                'thread_messages' => $threadMessages,
                'conversation_to' => $conversationTo,
                'conversation_name' => $conversationName,
                'new_conversation' => $newConversation,
                'current_login' => $login,
            ],
            true
        );
    }

    /**
     * Notifications page.
     * - Super admin: mairie channel notifications.
     * - Mairie admin: commune channel notifications (agents).
     *
     * @param string $html
     * @return void
     */
    public final function notificationsMairie(
        string $html
    ): void
    {
        if (!$this->allowRoles([1, 2])) {
            return;
        }

        $login = (string) static::class('session')->login();
        $role = (int) static::class('session')->type();
        $source = static::isGet('src') ? trim((string) static::getGet('src')) : '';
        if (static::isGet('read') && static::isGet('read', 'int')) {
            if ($role === 1) {
                $this->update->markMairieMessageAsRead($login, (int) static::getGet('read'));
            } else {
                if ($source === 'mairie') {
                    $this->update->markMairieMessageAsRead($login, (int) static::getGet('read'));
                } else {
                    $this->update->markAgentMessageAsRead($login, (int) static::getGet('read'));
                }
            }
        }
        if (static::isGet('delete', 'int')) {
            if ($role === 1) {
                $this->update->deleteMairieMessageForUser($login, (int) static::getGet('delete'));
            } else {
                if ($source === 'mairie') {
                    $this->update->deleteMairieMessageForUser($login, (int) static::getGet('delete'));
                } else {
                    $this->update->deleteAgentMessageForUser($login, (int) static::getGet('delete'));
                }
            }
        }

        if ($role === 1) {
            $inbox = $this->contributorsSelect->listMairieInbox($login, 200);
            foreach ($inbox as $k => $row) {
                $inbox[$k]['message_source'] = 'mairie';
            }
            $unreadCount = $this->contributorsSelect->countUnreadMairieMessages($login);
        } else {
            $agentInbox = $this->contributorsSelect->listAgentInbox($login, 200);
            foreach ($agentInbox as $k => $row) {
                $agentInbox[$k]['message_source'] = 'agent';
            }
            $adminInbox = $this->contributorsSelect->listMairieInbox($login, 200);
            foreach ($adminInbox as $k => $row) {
                $adminInbox[$k]['message_source'] = 'mairie';
            }
            $inbox = array_merge($agentInbox, $adminInbox);
            usort($inbox, static function (array $a, array $b): int {
                $aDate = (string) ($a['created_at'] ?? '');
                $bDate = (string) ($b['created_at'] ?? '');
                $cmp = strcmp($bDate, $aDate);
                if ($cmp !== 0) {
                    return $cmp;
                }
                return ((int) ($b['id'] ?? 0)) <=> ((int) ($a['id'] ?? 0));
            });
            $unreadCount = $this->contributorsSelect->countUnreadAgentMessages($login)
                + $this->contributorsSelect->countUnreadMairieMessages($login);
        }
        $unread = array_values(array_filter(
            $inbox,
            static fn(array $row): bool => (int) ($row['is_read'] ?? 0) === 0
        ));

        $this->views(
            $html,
            [
                'notifications' => $unread,
                'unread_count' => $unreadCount,
            ],
            true
        );
    }

    /**
     * Agent messaging: communicate with agents of the same commune.
     */
    public final function messagerieAgent(
        string $html
    ): void
    {
        if (!$this->allowRoles([3, 4])) {
            return;
        }

        $login = (string) static::class('session')->login();
        $role = (int) static::class('session')->type();
        $message = '';
        $alert = '';
        $replyTo = static::isGet('reply', 'int') ? (int) static::getGet('reply') : 0;
        $threadRef = static::isGet('thread') ? trim((string) static::getGet('thread')) : '';
        $newConversation = static::isGet('new') && (string) static::getGet('new') === '1';
        $startTo = static::isGet('to') ? trim((string) static::getGet('to')) : '';
        $replyTarget = [];
        $threadMessages = [];
        $conversationTo = '';
        $conversationName = '';
        $contacts = [];
        if ($role === 3) {
            $contacts = $this->contributorsSelect->listOtherAgentsSameCommune($login);
            $merchantContacts = $this->contributorsSelect->listMerchantsForAgent($login);
            foreach ($merchantContacts as $merchantContact) {
                $contacts[] = $merchantContact;
            }
            $mairieContacts = $this->contributorsSelect->listMairieForAgent($login);
            foreach ($mairieContacts as $mairieContact) {
                $contacts[] = $mairieContact;
            }
        } else {
            $contacts = $this->contributorsSelect->listAgentsForMerchant($login);
        }
        $allowedContactLogins = [];
        foreach ($contacts as $c) {
            $v = trim((string) ($c['login'] ?? ''));
            if ($v !== '') {
                $allowedContactLogins[$v] = true;
            }
        }

        if (static::isGet('delete', 'int')) {
            $this->update->deleteAgentMessageForUser($login, (int) static::getGet('delete'));
        }

        if (static::isValidMethod(true) && static::isPost('__send__')) {
            $to = trim((string) static::getPost('__to__'));
            $subject = trim((string) static::getPost('__subject__'));
            $text = trim((string) static::getPost('__message__'));
            $replyId = static::isPost('__reply_to__', 'int') ? (int) static::getPost('__reply_to__') : null;
            $threadInput = static::isPost('__thread_ref__') ? trim((string) static::getPost('__thread_ref__')) : '';
            $attachmentPath = null;
            $attachmentName = null;
            $attachmentMime = null;

            if (static::isFileName('__attachment__')) {
                $rawName = (string) static::getFileName('__attachment__');
                $ext = strtolower((string) pathinfo($rawName, PATHINFO_EXTENSION));
                if ($ext !== 'pdf') {
                    $message = "Seuls les fichiers PDF sont autorises.";
                    $alert = 'alert-danger';
                } else {
                    $targetDir = _ROOT_ . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'agent_messages';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0775, true);
                    }

                    $safeFile = 'agent_msg_' . date('Ymd_His') . '_' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8) . '.pdf';
                    $tmpName = (string) ($_FILES['__attachment__']['tmp_name'] ?? '');
                    $fullPath = $targetDir . DIRECTORY_SEPARATOR . $safeFile;

                    if ($tmpName !== '' && @move_uploaded_file($tmpName, $fullPath)) {
                        $attachmentPath = 'static/docs/agent_messages/' . $safeFile;
                        $attachmentName = $rawName;
                        $attachmentMime = 'application/pdf';
                    } else {
                        $message = "Impossible d'enregistrer la piece jointe PDF.";
                        $alert = 'alert-danger';
                    }
                }
            }

            $resolvedThread = $threadInput;
            if (($replyId ?? 0) > 0) {
                $target = $this->contributorsSelect->getAgentMessageByIdForUser($login, (int) $replyId);
                if (!empty($target)) {
                    $resolvedThread = trim((string) ($target['thread_ref'] ?? ''));
                    if ($resolvedThread === '') {
                        $resolvedThread = 'THR_' . (string) ($target['id'] ?? $replyId);
                    }
                }
            }

            if ($alert === '') {
                if (!isset($allowedContactLogins[$to])) {
                    $ok = false;
                    $message = "Destinataire non autorise pour ton compte.";
                    $alert = 'alert-danger';
                } else {
                    $ok = $this->insert->sendAgentMessage(
                        $login,
                        $to,
                        $subject,
                        $text,
                        $replyId,
                        $resolvedThread,
                        $attachmentPath,
                        $attachmentName,
                        $attachmentMime
                    );
                }
            } else {
                $ok = false;
            }

            if ($ok) {
                $message = "Message envoye avec succes.";
                $alert = 'alert-success';
                $replyTo = 0;
            } else {
                if ($message === '') {
                    $message = $role === 3
                        ? "Echec d'envoi. Verifie le destinataire (agent/commercant) de ta commune et le contenu."
                        : "Echec d'envoi. Verifie le destinataire agent de ta commune et le contenu.";
                    $alert = 'alert-danger';
                }
            }
        }

        if (static::isGet('read') && static::isGet('read', 'int')) {
            $this->update->markAgentMessageAsRead($login, (int) static::getGet('read'));
        }

        if ($startTo !== '') {
            foreach ($contacts as $contact) {
                if ((string) ($contact['login'] ?? '') === $startTo) {
                    $conversationTo = $startTo;
                    $conversationName = trim((string) ($contact['full_name'] ?? $startTo));
                    break;
                }
            }
        }
        $inbox = $this->contributorsSelect->listAgentInbox($login, 100);
        $conversationsMap = [];
        foreach ($inbox as $row) {
            $threadKey = trim((string) ($row['thread_ref'] ?? ''));
            if ($threadKey === '') {
                $threadKey = 'THR_' . (string) ($row['id'] ?? '');
            }
            if (!array_key_exists($threadKey, $conversationsMap)) {
                $row['thread_ref'] = $threadKey;
                $row['thread_unread'] = 0;
                $conversationsMap[$threadKey] = $row;
            }
            if ((int) ($row['is_read'] ?? 0) === 0) {
                $conversationsMap[$threadKey]['thread_unread'] = (int) ($conversationsMap[$threadKey]['thread_unread'] ?? 0) + 1;
            }
        }
        $conversations = array_values($conversationsMap);
        $unreadCount = $this->contributorsSelect->countUnreadAgentMessages($login);

        if ($replyTo > 0) {
            $replyTarget = $this->contributorsSelect->getAgentMessageByIdForUser($login, $replyTo);
            if (!empty($replyTarget)) {
                $threadRef = trim((string) ($replyTarget['thread_ref'] ?? $threadRef));
                $sender = (string) ($replyTarget['sender_login'] ?? '');
                $receiver = (string) ($replyTarget['receiver_login'] ?? '');
                if ($sender !== '' && $sender !== $login) {
                    $conversationTo = $sender;
                    $conversationName = trim((string) ($replyTarget['sender_name'] ?? $sender));
                } elseif ($receiver !== '' && $receiver !== $login) {
                    $conversationTo = $receiver;
                    $conversationName = trim((string) ($replyTarget['receiver_name'] ?? $receiver));
                }
            }
        }
        if ($threadRef !== '') {
            $threadMessages = $this->contributorsSelect->listAgentThreadForUser($login, $threadRef, 300);
            foreach ($threadMessages as $row) {
                $sender = (string) ($row['sender_login'] ?? '');
                $receiver = (string) ($row['receiver_login'] ?? '');
                if ($sender !== '' && $sender !== $login) {
                    $conversationTo = $sender;
                    $conversationName = trim((string) ($row['sender_name'] ?? $sender));
                    break;
                }
                if ($receiver !== '' && $receiver !== $login) {
                    $conversationTo = $receiver;
                    $conversationName = trim((string) ($row['receiver_name'] ?? $receiver));
                    break;
                }
            }
        }

        $this->views(
            $html,
            [
                'reponse' => $message,
                'alert' => $alert,
                'contacts' => $contacts,
                'inbox' => $inbox,
                'conversations' => $conversations,
                'unread_count' => $unreadCount,
                'reply_to' => $replyTo,
                'reply_target' => $replyTarget,
                'thread_ref' => $threadRef,
                'thread_messages' => $threadMessages,
                'conversation_to' => $conversationTo,
                'conversation_name' => $conversationName,
                'new_conversation' => $newConversation,
                'current_login' => $login,
            ],
            true
        );
    }

    /**
     * Agent notifications page (unread messages).
     */
    public final function notificationsAgent(
        string $html
    ): void
    {
        if (!$this->allowRoles([3, 4])) {
            return;
        }

        $login = (string) static::class('session')->login();
        if (static::isGet('read') && static::isGet('read', 'int')) {
            $this->update->markAgentMessageAsRead($login, (int) static::getGet('read'));
        }
        if (static::isGet('delete', 'int')) {
            $this->update->deleteAgentMessageForUser($login, (int) static::getGet('delete'));
        }

        $inbox = $this->contributorsSelect->listAgentInbox($login, 200);
        $unread = array_values(array_filter(
            $inbox,
            static fn(array $row): bool => (int) ($row['is_read'] ?? 0) === 0
        ));

        $this->views(
            $html,
            [
                'notifications' => $unread,
                'unread_count' => $this->contributorsSelect->countUnreadAgentMessages($login),
            ],
            true
        );
    }
}
