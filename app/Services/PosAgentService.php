<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PosAgentService
{
    protected $baseUrl;
    protected $agentSecret;

    public function __construct()
    {
        $this->baseUrl = config('app.pos_agent_url', env('POS_AGENT_URL', 'https://pos-agent-33ky.onrender.com'));

        // Try getting dynamic secret from DB, fallback to env
        $dynamicSecret = null;
        if (\Illuminate\Support\Facades\Auth::check()) {
            $store = \App\Models\StoreDetail::where('id', \Illuminate\Support\Facades\Auth::user()->store_id)->first();
            if ($store && $store->pos_agent_secret) {
                $dynamicSecret = $store->pos_agent_secret;
            }
        }

        $this->agentSecret = $dynamicSecret ?: config('app.pos_agent_secret', env('POS_AGENT_SECRET', ''));
    }
    /**
     * Public check to see who the terminal is.
     */
    public function whoAmI()
    {
        try {
            $response = Http::timeout(100)->get($this->baseUrl . '/api/terminal/whoami');
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Agent whoami error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Approve a terminal for a specific store.
     */
    public function approveTerminal($terminalId, $storeId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/api/terminal/approve', [
                'store_id' => $storeId
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('POS Agent terminal approval error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check the status of the terminal.
     */
    public function getTerminalStatus($terminalId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
                'Content-Type' => 'application/json',
            ])->timeout(100)->get($this->baseUrl . '/api/terminal/status');

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
     */
    public function registerTerminal($terminalId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/api/terminal/register', [
                'terminal_id' => $terminalId
            ]);

            if ($response->successful()) {
                return array_merge(['success' => true], $response->json());
            }

            Log::error('POS Agent terminal registration failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['success' => false, 'message' => 'Registration failed.'];
        } catch (\Exception $e) {
            Log::error('POS Agent terminal registration error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception occurred during registration.'];
        }
    }

    /**
     * Print receipt for a sale.
     */
    public function printReceipt($terminalId, $sale)
    {
        try {
            // Prepare items data
            $items = $sale->items->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown Item',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                ];
            })->toArray();

            $payload = [
                'title' => 'Southwest Farmers Store',
                'invoice_number' => $sale->invoice_number,
                'items' => $items,
                'subtotal' => $sale->subtotal,
                'tax' => $sale->tax_amount,
                'discount' => $sale->discount_amount,
                'total' => $sale->total_amount,
                'payment_method' => $sale->payment_method,
                'date' => $sale->created_at->format('Y-m-d H:i:s'),
            ];

            // HMAC Signature generation using the agent secret
            $signature = hash_hmac('sha256', json_encode($payload), $this->agentSecret);

            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
                'x-agent-signature' => $signature,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/api/printer/print', $payload);

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
     * Check if cash drawer configuration is active.
     */
    public function getCashDrawerStatus($terminalId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
            ])->timeout(100)->get($this->baseUrl . '/api/cash-drawer/status');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Cash Drawer Status Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Open the cash drawer.
     */
    public function openCashDrawer($terminalId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
                'Content-Type' => 'application/json',
            ])->timeout(100)->post($this->baseUrl . '/api/cash-drawer/open', []);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('POS Cash Drawer Open Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get Scale Status
     */
    public function getScaleStatus($terminalId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
            ])->timeout(100)->get($this->baseUrl . '/api/scale/status');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Scale Status Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get Weight from Scale
     */
    public function getWeight($terminalId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
            ])->timeout(100)->get($this->baseUrl . '/api/scale/weight');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Scale Weight Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get Scanner Status
     */
    public function getScannerStatus($terminalId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
            ])->timeout(100)->get($this->baseUrl . '/api/scanner/status');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Scanner Status Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get Last Scanned Barcode
     */
    public function getLastScan($terminalId)
    {
        try {
            $response = Http::withHeaders([
                'x-terminal-id' => $terminalId,
                'x-agent-secret' => $this->agentSecret,
            ])->timeout(100)->get($this->baseUrl . '/api/scanner/last');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('POS Scanner Last Scan Error', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
