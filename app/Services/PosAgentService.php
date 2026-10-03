<?php

namespace App\Services;

use App\Models\StoreDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Talks to the POS hardware cloud service (POS_AGENT_URL, e.g.
 * https://pos-7mvx.onrender.com), which forwards each call to the hardware
 * agent on the register. Spec: "Register hardware for the POS" (live test
 * 25 Sep 2026, register CYGNUS-POS).
 *
 * The cloud service finds the register by x-store-id and checks
 * x-agent-secret; both go on every call. A device reply with demo=true means
 * the agent is not talking to the real device and is treated as a failure.
 *
 * Methods keep their old $terminalId parameter so callers don't change; it is
 * still sent as x-terminal-id, but the service does not need it.
 */
class PosAgentService
{
    protected $baseUrl;
    protected $agentSecret;
    protected $posStoreId;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('app.pos_agent_url', env('POS_AGENT_URL')), '/');
        $this->agentSecret = config('app.pos_agent_secret', env('POS_AGENT_SECRET'));
        $this->posStoreId  = config('app.pos_store_id', env('POS_STORE_ID'));

        if (Auth::check()) {
            $store = StoreDetail::where('id', Auth::user()->store_id)->first();
            if ($store) {
                if ($store->pos_store_id) {
                    $this->posStoreId = $store->pos_store_id;
                }
                if ($store->pos_agent_secret) {
                    $this->agentSecret = $store->pos_agent_secret;
                }
            }
        }
    }

    /** True when this store can call the hardware service at all. */
    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && !empty($this->posStoreId) && !empty($this->agentSecret);
    }

    public function notConfiguredMessage(): string
    {
        return $this->baseUrl === ''
            ? 'POS hardware service URL (POS_AGENT_URL) is not set.'
            : 'POS Store ID and Agent Secret are not set in Settings > Quick POS.';
    }

    protected function getHeaders($terminalId = null): array
    {
        $headers = [
            'x-store-id' => (string) $this->posStoreId,
            'x-agent-secret' => (string) $this->agentSecret,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        if ($terminalId) {
            $headers['x-terminal-id'] = (string) $terminalId;
        }

        return $headers;
    }

    /**
     * One call to the hardware service. Always returns an array; a failure
     * carries success=false, http_status and a message the cashier can act on.
     * Never logs the headers (they hold the secret).
     */
    protected function call(string $method, string $path, $terminalId = null, array $body = [], int $timeout = 10): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => $this->notConfiguredMessage()];
        }

        try {
            $request = Http::withHeaders($this->getHeaders($terminalId))->timeout($timeout);
            $response = $method === 'GET'
                ? $request->get($this->baseUrl . $path)
                : $request->post($this->baseUrl . $path, (object) $body);

            $json = $response->json();
            $json = is_array($json) ? $json : [];

            if ($response->successful()) {
                return $json + ['success' => true];
            }

            $raw = $json['message'] ?? $json['error'] ?? null;
            $message = match ($response->status()) {
                400 => $raw ?: 'Request rejected by the hardware service (check the POS Store ID).',
                401 => 'Hardware service refused the Agent Secret. Check Settings > Quick POS.',
                404 => $raw && stripos($raw, 'terminal') !== false
                    ? 'Register hardware agent is not connected (No active terminal). Check the agent and ngrok on the register.'
                    : ($raw ?: 'Not found: ' . $path),
                502 => 'Register hardware agent is unreachable. Check the agent and ngrok on the register.',
                default => $raw ?: 'Hardware service error ' . $response->status() . '.',
            };
            Log::warning('POS hardware call failed', ['path' => $path, 'status' => $response->status(), 'body' => $json]);

            return ['success' => false, 'message' => $message, 'http_status' => $response->status(), 'raw_message' => $raw] + $json;
        } catch (\Throwable $e) {
            Log::error('POS hardware call error', ['path' => $path, 'error' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Could not reach the POS hardware service.'];
        }
    }

    /** A device reply flagged demo=true is not the real device. */
    protected static function isDemo(array $r): bool
    {
        return !empty($r['demo']);
    }

    /* ---------------- Status ---------------- */

    /**
     * Hardware status for the POS badge and Settings. The spec has no
     * terminal-status endpoint: the agent is online when the service answers
     * a device status call. Scanner/scale flags follow their own status
     * (connected=true, scale unit lb, not demo).
     */
    public function getHardwareStatus($terminalId = null): array
    {
        if (!$this->isConfigured()) {
            return ['online' => false, 'scanner' => false, 'scale' => false, 'message' => $this->notConfiguredMessage()];
        }

        $scanner = $this->getScannerStatus($terminalId);
        $scale = $this->getScaleStatus($terminalId);
        $online = !empty($scanner['success']) || !empty($scale['success']);

        return [
            'online' => $online,
            'scanner' => !empty($scanner['success']) && ($scanner['connected'] ?? false) === true && !self::isDemo($scanner),
            'scale' => !empty($scale['success']) && ($scale['connected'] ?? false) === true
                && strtolower((string) ($scale['unit'] ?? 'lb')) === 'lb' && !self::isDemo($scale),
            'message' => $online ? null : ($scanner['message'] ?? $scale['message'] ?? 'Hardware agent offline.'),
        ];
    }

    /** The shape returned by getHardwareStatus() is online. */
    public static function isTerminalOnline($status): bool
    {
        return is_array($status) && !empty($status['online']);
    }

    /* ---------------- Scanner ---------------- */

    public function getScannerStatus($terminalId = null)
    {
        return $this->call('GET', '/api/scanner/status', $terminalId);
    }

    /** { success, scan: { value, at } | null }. The POS adds an item when scan.at changes. */
    public function getLastScan($terminalId = null)
    {
        return $this->call('GET', '/api/scanner/last', $terminalId);
    }

    /* ---------------- Scale ---------------- */

    public function getScaleStatus($terminalId = null)
    {
        return $this->call('GET', '/api/scale/status', $terminalId);
    }

    /**
     * Weight in pounds. usable=true only when success, connected, not demo,
     * status=stable, unit=lb and weight > 0; otherwise a reason, and retry=true
     * when waiting may help (motion / not_ready / no reading yet).
     */
    public function getWeight($terminalId = null)
    {
        $r = $this->call('GET', '/api/scale/weight', $terminalId);
        if (empty($r['success'])) {
            return $r + ['usable' => false, 'retry' => false, 'reason' => $r['message'] ?? 'Scale error.'];
        }

        $status = strtolower((string) ($r['status'] ?? ''));
        $reason = match (true) {
            self::isDemo($r) => 'Scale is in demo mode, not reading the real scale.',
            ($r['connected'] ?? false) !== true => 'Scale is not connected.',
            strtolower((string) ($r['unit'] ?? '')) !== 'lb' => 'Scale is not set to pounds (lb).',
            $status === 'motion' => 'Scale is still moving. Wait for it to settle.',
            $status === 'not_ready' => 'Scale is not ready yet.',
            $status === 'under_zero' => 'Scale reads below zero. Re-zero the scale.',
            $status === 'over_capacity' => 'Item is too heavy for the scale.',
            $status !== 'stable' => 'Scale reading is not stable yet.',
            !is_numeric($r['weight'] ?? null) || (float) $r['weight'] <= 0 => 'No weight on the scale.',
            default => null,
        };

        return $r + [
            'usable' => $reason === null,
            'retry' => in_array($status, ['motion', 'not_ready'], true) || ($r['weight'] ?? null) === null,
            'reason' => $reason,
        ];
    }

    /* ---------------- Receipt printer ---------------- */

    /** { success, configured_printer, printers[] } */
    public function getPrinterList($terminalId = null)
    {
        $r = $this->call('GET', '/api/cloudprinter/list', $terminalId);
        if (($r['http_status'] ?? null) === 404 && stripos((string) ($r['raw_message'] ?? ''), 'terminal') === false) {
            // Path not found (not "No active terminal"): older agents use this one.
            $r = $this->call('GET', '/api/cloud/printer/list', $terminalId);
        }

        return $r;
    }

    /**
     * Print the sale receipt. Without $printerName the agent prints to its
     * configured_printer (the proven queue). Printed only when success=true
     * and demo=false; otherwise the cashier sees the error and can reprint.
     */
    public function printReceipt($terminalId, $sale, $printerName = null)
    {
        $store = StoreDetail::find($sale->store_id);
        $tz = config('app.display_timezone', 'America/Chicago');

        $payment = strtoupper((string) $sale->payment_method);
        if ($payment === 'CARD' && !empty($sale->card_auth_code)) {
            $payment .= ' (Auth ' . $sale->card_auth_code . ')';
        }

        $payload = array_filter([
            'company_name' => 'Southwest Farmers',
            'address_lines' => array_values(array_filter([
                $store->store_name ?? null,
                $store->address ?? null,
                'Invoice: ' . $sale->invoice_number,
                'Cashier: ' . (Auth::user()->name ?? 'Cashier'),
            ])),
            'phone' => $store->phone ?? null,
            'datetime' => $sale->created_at ? $sale->created_at->copy()->timezone($tz)->format('m/d/Y h:i A') : null,
            'items' => $sale->items->map(fn ($item) => [
                'name' => $item->product->product_name ?? $item->product->name ?? 'Item',
                'qty' => (float) $item->quantity,
                'price' => (float) $item->price, // unit price
            ])->values()->all(),
            'subtotal' => (float) $sale->subtotal,
            'tax' => (float) $sale->tax_amount,
            'total' => (float) $sale->total_amount,
            'payment' => $payment,
            'printer_name' => $printerName ?: null,
        ], fn ($v) => $v !== null);

        $r = $this->call('POST', '/api/cloud/printer/print', $terminalId, $payload, 20);

        if (!empty($r['success']) && !self::isDemo($r)) {
            Log::info('POS receipt printed', ['invoice' => $sale->invoice_number, 'printer' => $r['printer_name'] ?? null]);

            return ['success' => true, 'message' => 'Receipt printed.', 'printer_name' => $r['printer_name'] ?? null];
        }

        return ['success' => false, 'message' => self::isDemo($r)
            ? 'Printer is in demo mode; nothing was printed.'
            : 'Receipt not printed: ' . ($r['message'] ?? 'printer error') . ' You can reprint.'];
    }

    /* ---------------- Cash drawer (physical open not proven yet) ---------------- */

    public function getCashDrawerStatus($terminalId = null)
    {
        return $this->call('GET', '/api/cash-drawer/status', $terminalId);
    }

    /**
     * Send the open command. 503 = drawer not configured, 500 = command not
     * sent. success=true + demo=false only means the command was sent.
     */
    public function openCashDrawer($terminalId = null)
    {
        $r = $this->call('POST', '/api/cash-drawer/open', $terminalId);
        if (!empty($r['success']) && self::isDemo($r)) {
            return ['success' => false, 'message' => 'Cash drawer is in demo mode; the drawer was not opened.'];
        }
        if (($r['http_status'] ?? null) === 503) {
            $r['message'] = 'Cash drawer is not configured on the register.';
        }

        return $r;
    }

    /* ---------------- Card reader: Ingenico Lane 3600 (not tested yet) ---------------- */

    /** Ready only when success, ready, cws_reachable and reader_detected are all true. */
    public function getPaymentStatus($terminalId = null)
    {
        $r = $this->call('GET', '/api/payment/status', $terminalId);
        $ready = !empty($r['success']) && ($r['ready'] ?? false) === true
            && ($r['cws_reachable'] ?? false) === true && ($r['reader_detected'] ?? false) === true;

        return ['online' => $ready, 'message' => $ready ? null : ($r['message'] ?? 'Card reader is not ready.')] + $r;
    }

    /**
     * The customer is at the reader for this whole call: wait up to 120 s.
     * Paid only when HTTP 200, success=true and approved=true. Returns the
     * approved amount, transactionId and authCode.
     */
    public function initiatePayment($terminalId, $amount, $orderId, $currency = 'USD')
    {
        $r = $this->call('POST', '/api/payment/initiate', $terminalId, [
            'amount' => round((float) $amount, 2),
            'currency' => $currency,
            'order_id' => $orderId,
        ], 125);

        $approved = !empty($r['success']) && !isset($r['http_status']) && ($r['approved'] ?? false) === true;
        if (!$approved) {
            return ['success' => false, 'approved' => false, 'message' => $r['message'] ?? 'Card not approved.'];
        }

        return [
            'success' => true,
            'approved' => true,
            'amount' => (float) ($r['amount'] ?? $amount),
            'transactionId' => $r['transactionId'] ?? null,
            'authCode' => $r['authCode'] ?? null,
            'message' => 'Card approved.',
        ];
    }

    /** Cancel the sale currently waiting on the reader. */
    public function cancelPayment($terminalId = null)
    {
        return $this->call('POST', '/api/payment/cancel', $terminalId);
    }

    /** Void a finished sale: ref_num = the transactionId from initiate. */
    public function voidTransaction($terminalId, $refNum, $amount = null)
    {
        return $this->call('POST', '/api/payment/void', $terminalId, array_filter([
            'ref_num' => $refNum,
            'amount' => $amount !== null ? (float) $amount : null,
        ], fn ($v) => $v !== null), 60);
    }

    /** Refund an amount; ref_num = the original transactionId when known. */
    public function refundTransaction($terminalId, $amount, $refNum = null)
    {
        return $this->call('POST', '/api/payment/refund', $terminalId, array_filter([
            'amount' => (float) $amount,
            'ref_num' => $refNum,
        ], fn ($v) => $v !== null), 60);
    }
}
