<?php

namespace Epaphrodites\controllers\controllers;

use Epaphrodites\epaphrodites\heredia\HerediaApiSwitcher;
use Epaphrodites\epaphrodites\env\config\ResponseSequence;

final class api extends HerediaApiSwitcher
{

    protected object $Response;
    private object $select;
    private object $insert;
    private object $update;

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
     * @return void
     */
    private function initializeObjects():void
    {
        $this->Response = new ResponseSequence;
        $this->select = $this->getFunctionObject(static::initQuery(), 'select');
        $this->insert = $this->getFunctionObject(static::initQuery(), 'insert');
        $this->update = $this->getFunctionObject(static::initQuery(), 'update');
    }      

    /**
     * Decode incoming JSON payload.
     */
    private function getJsonPayload(): array
    {
        $raw = file_get_contents('php://input');
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Normalize potential uppercase keys from some drivers.
     */
    private function normalizeRows(array $rows): array
    {
        if (_FIRST_DRIVER_ === 'oracle') {
            return static::initNamespace()['env']->dictKeyToLowers($rows);
        }

        return $rows;
    }

    /**
     * Generate a receipt reference.
     */
    private function makeReceiptRef(): string
    {
        $stamp = date('YmdHis');
        $rand = substr(str_shuffle('0123456789'), 0, 6);
        return "RCP-$stamp-$rand";
    }

    /**
     * Normalize inbound datetime to SQL format.
     */
    private function toSqlDateTime(
        ?string $value,
        string $fallbackTime = '00:00:00'
    ): string {
        if (!is_string($value) || trim($value) === '') {
            return date('Y-m-d H:i:s');
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
            return date('Y-m-d H:i:s');
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Create a signed token for QR verification.
     */
    private function makeQrToken(
        string $receiptRef,
        string $noticeRef,
        float $amount
    ): string {
        $secret = defined('_TAXECONNECT_CALLBACK_SECRET_')
            ? _TAXECONNECT_CALLBACK_SECRET_
            : 'taxeconnect-dev-secret';

        return hash('sha256', $receiptRef . '|' . $noticeRef . '|' . $amount . '|' . $secret);
    }

    /**
     * Build a QR SVG payload.
     */
    private function makeQrSvg(string $token): string
    {
        $options = new \chillerlan\QRCode\QROptions(
            [
                'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
                'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
                'version' => 5,
            ]
        );

        $verifyUrl = (defined('_TAXECONNECT_SITE_URL_') ? _TAXECONNECT_SITE_URL_ : 'https://taxeconnect.ci') . "/verify?token=$token";

        return (new \chillerlan\QRCode\QRCode($options))->render($verifyUrl);
    }

    /**
     * Check callback signature when provided.
     */
    private function isValidCallbackSignature(string $raw): bool
    {
        $signature = $_SERVER['HTTP_X_TAXECONNECT_SIGNATURE'] ?? '';

        if ($signature === '') {
            return true; // dev mode fallback
        }

        $secret = defined('_TAXECONNECT_CALLBACK_SECRET_')
            ? _TAXECONNECT_CALLBACK_SECRET_
            : 'taxeconnect-dev-secret';

        $expected = hash_hmac('sha256', $raw, $secret);
        return hash_equals($expected, $signature);
    }

    /**
     * make api test
     */
    public final function makeApiTest()
    {

        $Result = [];
        $code = 400;
        if (static::isValidApiMethod(true)) {

            $code = 200;
            $Result = ['this' , 'is' , 'api' , 'result' , 'test']; 
        }

        return $this->Response->JsonResponse($code, $Result);
    }

    /**
     * Mobile Money callback endpoint (POST /api/mobile_money_callback/).
     *
     * Expected JSON payload:
     * {
     *   "notice_ref": "NTC-2026-0001",
     *   "merchant_id": "TCI-1001",
     *   "amount": 180000,
     *   "channel": "orange_money",
     *   "status": "success",
     *   "transaction_id": "OM-123456"
     * }
     */
    public final function mobileMoneyCallback()
    {
        if (!static::isValidApiMethod(false, 'POST')) {
            return $this->Response->JsonResponse(405, ['error' => 'Method not allowed']);
        }

        $raw = file_get_contents('php://input');
        if (!is_string($raw) || trim($raw) === '') {
            return $this->Response->JsonResponse(400, ['error' => 'Empty payload']);
        }

        if (!$this->isValidCallbackSignature($raw)) {
            return $this->Response->JsonResponse(401, ['error' => 'Invalid callback signature']);
        }

        $payload = $this->getJsonPayload();
        $noticeRef = (string)($payload['notice_ref'] ?? '');
        $merchantId = (string)($payload['merchant_id'] ?? '');
        $channel = (string)($payload['channel'] ?? 'mobile_money');
        $status = (string)($payload['status'] ?? 'success');
        $transactionId = (string)($payload['transaction_id'] ?? '');
        $amount = (float)($payload['amount'] ?? 0);
        $paidAt = $this->toSqlDateTime((string)($payload['paid_at'] ?? date('Y-m-d H:i:s')));

        if ($noticeRef === '' || $merchantId === '' || $amount <= 0) {
            return $this->Response->JsonResponse(422, ['error' => 'notice_ref, merchant_id and amount are required']);
        }

        try {
            // Ensure MVP demo data exists in development.
            static::initQuery()['general']->ensureTaxeConnectMvpData();

            $notice = $this->normalizeRows(
                $this->select->table('tax_notices')
                    ->where('notice_ref')
                    ->param([$noticeRef])
                    ->SQuery()
            );

            if (empty($notice)) {
                return $this->Response->JsonResponse(404, ['error' => 'Notice not found']);
            }

            $noticeRow = $notice[0];
            if (($noticeRow['merchant_id'] ?? '') !== $merchantId) {
                return $this->Response->JsonResponse(409, ['error' => 'merchant_id does not match notice']);
            }

            $receiptRef = $this->makeReceiptRef();
            $qrToken = $this->makeQrToken($receiptRef, $noticeRef, $amount);
            $qrSvg = $this->makeQrSvg($qrToken);

            $this->insert->table('tax_payments')
                ->insert('receipt_ref, notice_ref, merchant_id, amount, channel, status, paid_at, qr_token')
                ->values('?, ?, ?, ?, ?, ?, ?, ?')
                ->param([$receiptRef, $noticeRef, $merchantId, $amount, $channel, $status, $paidAt, $qrToken])
                ->IQuery();

            $sumPaidRows = $this->normalizeRows(
                $this->select->table('tax_payments')
                    ->where('notice_ref')
                    ->and(['status'])
                    ->param([$noticeRef, 'success'])
                    ->SQuery('COALESCE(SUM(amount),0) AS total_paid')
            );

            $totalPaid = (float)($sumPaidRows[0]['total_paid'] ?? 0);
            $amountDue = (float)($noticeRow['amount_due'] ?? 0);

            $nextStatus = 'unpaid';
            if ($totalPaid >= $amountDue && $amountDue > 0) {
                $nextStatus = 'paid';
            } elseif ($totalPaid > 0 && $totalPaid < $amountDue) {
                $nextStatus = 'partial';
            }

            $this->update->table('tax_notices')
                ->set(['status'])
                ->where('notice_ref')
                ->param([$nextStatus, $noticeRef])
                ->UQuery();

            // Webhook may arrive without authenticated session; write history directly.
            $this->insert->table('history')
                ->insert('actions , dates , label')
                ->values(' ? , ? , ? ')
                ->param(['mobile_money_webhook', date('Y-m-d H:i:s'), "Tax payment callback: $receiptRef / notice $noticeRef / tx $transactionId"])
                ->IQuery();

            return $this->Response->JsonResponse(
                200,
                [
                    'receipt_ref' => $receiptRef,
                    'notice_ref' => $noticeRef,
                    'merchant_id' => $merchantId,
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'channel' => $channel,
                    'status' => $status,
                    'notice_status' => $nextStatus,
                    'paid_total_for_notice' => $totalPaid,
                    'qr_token' => $qrToken,
                    'qr_svg' => $qrSvg,
                ]
            );
        } catch (\Throwable $exception) {
            return $this->Response->JsonResponse(500, ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Verify a receipt token (GET /api/verify_receipt/?token=...).
     */
    public final function verifyReceipt()
    {
        if (!static::isValidApiMethod(false, 'GET')) {
            return $this->Response->JsonResponse(405, ['error' => 'Method not allowed']);
        }

        $token = isset($_GET['token']) ? (string)$_GET['token'] : '';
        if ($token === '') {
            return $this->Response->JsonResponse(422, ['error' => 'token is required']);
        }

        try {
            $rows = $this->normalizeRows(
                $this->select->table('tax_payments')
                    ->where('qr_token')
                    ->param([$token])
                    ->SQuery()
            );

            if (empty($rows)) {
                return $this->Response->JsonResponse(404, ['verified' => false, 'error' => 'Receipt not found']);
            }

            $payment = $rows[0];

            $notice = $this->normalizeRows(
                $this->select->table('tax_notices')
                    ->where('notice_ref')
                    ->param([$payment['notice_ref'] ?? ''])
                    ->SQuery()
            );

            return $this->Response->JsonResponse(
                200,
                [
                    'verified' => true,
                    'payment' => $payment,
                    'notice' => $notice[0] ?? [],
                ]
            );
        } catch (\Throwable $exception) {
            return $this->Response->JsonResponse(500, ['error' => $exception->getMessage()]);
        }
    }
}
