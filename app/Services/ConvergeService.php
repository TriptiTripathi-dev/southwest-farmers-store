<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConvergeService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('services.converge');
    }

    /**
     * Request a transaction token from Converge.
     */
    public function generateTransactionToken($amount, $invoiceNumber, $customer = null)
    {
        $endpoint = $this->config['api_url'] . '/transaction_token';

        // Generate a secure hash to prevent callback spoofing
        $secureHash = hash_hmac('sha256', $invoiceNumber, config('app.key'));
        $callbackUrl = route('website.payment.callback', ['secure_hash' => $secureHash]);

        $params = [
            'ssl_merchant_id' => $this->config['merchant_id'],
            'ssl_user_id' => $this->config['user_id'],
            'ssl_pin' => $this->config['pin'],
            'ssl_transaction_type' => 'ccsale',
            'ssl_amount' => number_format($amount, 2, '.', ''),
            'ssl_invoice_number' => $invoiceNumber,
            'ssl_show_receipt' => 'false',
            'ssl_receipt_link_method' => 'GET',
            'ssl_receipt_link_url' => $callbackUrl,
            'ssl_error_url' => $callbackUrl,
            'ssl_cancel_url' => $callbackUrl,
        ];

        // Pre-fill customer data if provided
        if ($customer) {
            $names = explode(' ', $customer->name, 2);
            $params['ssl_first_name'] = $names[0] ?? '';
            if (isset($names[1])) {
                $params['ssl_last_name'] = $names[1];
            }
            $params['ssl_email'] = $customer->email ?? '';
            $params['ssl_phone'] = $customer->phone ?? '';
        }

        try {
            $response = Http::asForm()->post($endpoint, $params);

            if ($response->successful()) {
                $token = $response->body();
                if (str_contains($token, 'errorCode')) {
                    Log::error('Converge Token Error: ' . $token);
                    return null;
                }
                return $token;
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Converge Service Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the full Hosted Payment Page URL.
     */
    public function getHppUrl($token, $invoiceNumber = null)
    {
        return $this->config['api_url'] . '?ssl_txn_auth_token=' . urlencode($token);
    }
}
