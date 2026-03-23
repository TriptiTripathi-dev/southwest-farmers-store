<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\StoreDetail;

class PosAgentService
{
    protected $baseUrl;
    protected $agentSecret;
    protected $posStoreId;

    public function __construct()
    {
        $this->baseUrl = config('app.pos_agent_url', env('POS_AGENT_URL'));
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

    protected function getHeaders($terminalId, $requireStoreId = false, $isPost = false)
    {
        $headers = [
            'x-terminal-id' => $terminalId,
            'x-agent-secret' => $this->agentSecret,
        ];

        if ($isPost) {
            $headers['Content-Type'] = 'application/json';
        }

        if ($requireStoreId && $this->posStoreId) {
            $headers['x-store-id'] = (string) $this->posStoreId;
        }

        return $headers;
    }

    /**
     * Public check to see who the terminal is.
     * URI: /api/terminal/whoami
     */
    public function whoAmI()
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/api/terminal/whoami');
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Agent whoami error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check the status of the terminal.
     * URI: /api/terminal/status
     */
    public function getTerminalStatus($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, false, false))
                ->timeout(10)
                ->get($this->baseUrl . '/api/terminal/status');

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('POS Agent terminal status failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['status' => 'offline', 'connected' => false];
        } catch (\Exception $e) {
            Log::error('POS Agent terminal status error', ['error' => $e->getMessage()]);
            return ['status' => 'offline', 'connected' => false];
        }
    }

    /**
     * Register the terminal with the POS Agent.
     * URI: /api/terminal/register
     */
    public function registerTerminal($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, false, true))
                ->timeout(10)
                ->post($this->baseUrl . '/api/terminal/register', [
                    'terminal_id' => $terminalId
                ]);

            if ($response->successful()) {
                return array_merge(['success' => true], $response->json());
            }

            return ['success' => false, 'message' => 'Registration failed: ' . $response->body()];
        } catch (\Exception $e) {
            Log::error('POS Agent terminal register error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception occurred during registration.'];
        }
    }

    /**
     * Approve a terminal for a specific store.
     * URI: /api/terminal/approve
     */
    public function approveTerminal($terminalId, $storeIdParam)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, false, true))
                ->timeout(10)
                ->post($this->baseUrl . '/api/terminal/approve', [
                    'store_id' => $storeIdParam
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('POS Agent terminal approve error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check if cash drawer configuration is active.
     * URI: /api/cash-drawer/status
     */
    public function getCashDrawerStatus($terminalId)
    {
        try {
            $headers = $this->getHeaders($terminalId, true, false);
            $url = $this->baseUrl . '/api/cash-drawer/status';

            Log::info('POS Agent: Checking Cash Drawer Status', [
                'url' => $url,
                'headers' => $headers
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            Log::info('POS Agent: Cash Drawer Status response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            return $response->json() ?? ['success' => false, 'message' => 'API Error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('POS Cash Drawer Status Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Agent connection failed.'];
        }
    }

    /**
     * Open the cash drawer.
     * URI: /api/cash-drawer/open
     */
    public function openCashDrawer($terminalId)
    {
        try {
            $headers = $this->getHeaders($terminalId, true, true);
            $url = $this->baseUrl . '/api/cash-drawer/open';
            
            Log::info('POS Agent: Opening Cash Drawer', [
                'url' => $url,
                'headers' => $headers,
                'terminalId' => $terminalId
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, []);

            Log::info('POS Agent: Open Cash Drawer response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            return $response->json() ?? ['success' => false, 'message' => 'API Error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('POS Cash Drawer Open Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Agent connection failed.'];
        }
    }

    /**
     * Get list of available printers from the agent.
     * URI: /api/cloud/printer/list
     */
    public function getPrinterList($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, false))
                ->timeout(10)
                ->get($this->baseUrl . '/api/cloud/printer/list');

            if ($response->successful()) {
                return $response->json();
            }
            return ['success' => false, 'message' => 'Status: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('POS Printer List Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Print receipt for a sale.
     * URI: /api/cloud/printer/print
     */
    public function printReceipt($terminalId, $sale, $printerName = null)
    {
        try {
            $items = $sale->items->map(function ($item) {
                return [
                    'name'  => $item->product->product_name ?? $item->product->name ?? 'Unknown Item',
                    'qty'   => (float) $item->quantity,
                    'price' => (float) $item->price,
                ];
            })->toArray();

            $cashier = Auth::user()->name ?? 'Cashier';

            $payload = [
                'store_name'     => 'Southwest Farmers',
                'cashier'        => $cashier,
                'order_id'       => $sale->invoice_number,
                'items'          => $items,
                'total'          => (float) $sale->total_amount,
                'payment_method' => strtoupper($sale->payment_method),
                'auth_code'      => $sale->card_auth_code ?? '',
                'printer_name'   => $printerName,
            ];

            $response = Http::withHeaders($this->getHeaders($terminalId, true, true))
                ->timeout(15)
                ->post($this->baseUrl . '/api/cloud/printer/print', $payload);

            if ($response->successful()) {
                Log::info('POS Receipt printed successfully', ['invoice' => $sale->invoice_number]);
                return ['success' => true, 'message' => 'Receipt printed.'];
            }

            Log::error('POS Printing failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'Printer error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('POS Print Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Printing exception: ' . $e->getMessage()];
        }
    }

    /**
     * Get Scale Status
     * URI: /api/scale/status
     */
    public function getScaleStatus($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, false))
                ->timeout(10)
                ->get($this->baseUrl . '/api/scale/status');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Scale Status Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get Weight from Scale
     * URI: /api/scale/weight
     */
    public function getWeight($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, false))
                ->timeout(10)
                ->get($this->baseUrl . '/api/scale/weight');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Scale Weight Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get Scanner Status
     * URI: /api/scanner/status
     */
    public function getScannerStatus($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, false))
                ->timeout(10)
                ->get($this->baseUrl . '/api/scanner/status');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Scanner Status Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get Last Scanned Barcode
     * URI: /api/scanner/last
     */
    public function getLastScan($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, false))
                ->timeout(10)
                ->get($this->baseUrl . '/api/scanner/last');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Scanner Last Scan Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Simulate a Barcode Scan (Testing)
     * URI: /api/scanner/simulate
     */
    public function simulateScan($terminalId, $barcodeVal)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, true))
                ->timeout(10)
                ->post($this->baseUrl . '/api/scanner/simulate', [
                    'value' => $barcodeVal
                ]);

            if ($response->successful()) {
                return $response->json();
            }
            return ['success' => false, 'message' => 'Status: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('POS Scanner Simulate Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get Elavon configuration for PAX terminal.
     * URI: /api/payment/elavon-config
     */
    public function getElavonConfig($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, false, false))
                ->timeout(10)
                ->get($this->baseUrl . '/api/payment/elavon-config');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS PAX Elavon Config Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get PAX payment terminal status.
     * URI: /api/payment/status
     */
    public function getPaymentStatus($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, false))
                ->timeout(10)
                ->get($this->baseUrl . '/api/payment/status');

            return $response->successful() ? $response->json() : ['success' => false, 'online' => false];
        } catch (\Exception $e) {
            Log::error('POS PAX Payment Status Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'online' => false];
        }
    }

    /**
     * Initiate a PAX payment transaction.
     * URI: /api/payment/initiate
     */
    public function initiatePayment($terminalId, $amount, $orderId, $currency = 'USD')
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, true))
                ->timeout(60) // Payment initiation can take time
                ->post($this->baseUrl . '/api/payment/initiate', [
                    'amount' => (float)$amount,
                    'currency' => $currency,
                    'order_id' => $orderId,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'message' => 'Payment initiation failed: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('POS PAX Payment Initiate Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception during payment initiation.'];
        }
    }

    /**
     * Cancel an active PAX payment transaction.
     * URI: /api/payment/cancel
     */
    public function cancelPayment($terminalId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, true))
                ->timeout(10)
                ->post($this->baseUrl . '/api/payment/cancel', []);

            return $response->successful() ? $response->json() : ['success' => false, 'message' => 'Cancel failed'];
        } catch (\Exception $e) {
            Log::error('POS PAX Payment Cancel Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception during payment cancellation.'];
        }
    }

    /**
     * Void a PAX transaction.
     * URI: /api/payment/void
     */
    public function voidTransaction($terminalId, $refNum)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, true))
                ->timeout(20)
                ->post($this->baseUrl . '/api/payment/void', [
                    'ref_num' => $refNum
                ]);

            return $response->successful() ? $response->json() : ['success' => false, 'message' => 'Void failed'];
        } catch (\Exception $e) {
            Log::error('POS PAX Void Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception during transaction void.'];
        }
    }

    /**
     * Refund a PAX transaction.
     * URI: /api/payment/refund
     */
    public function refundTransaction($terminalId, $amount, $refNum)
    {
        try {
            $response = Http::withHeaders($this->getHeaders($terminalId, true, true))
                ->timeout(20)
                ->post($this->baseUrl . '/api/payment/refund', [
                    'amount' => (float)$amount,
                    'ref_num' => $refNum
                ]);

            return $response->successful() ? $response->json() : ['success' => false, 'message' => 'Refund failed'];
        } catch (\Exception $e) {
            Log::error('POS PAX Refund Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception during transaction refund.'];
        }
    }
}
