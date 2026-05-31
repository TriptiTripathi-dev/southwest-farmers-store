<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Cart;
use App\Models\StoreNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    /**
     * Handle the return from Converge HPP.
     */
    public function handle(Request $request)
    {
        // Elavon sends data back in GET or POST depending on configuration
        // ssl_result: 0 means success
        $result = $request->input('ssl_result');
        $invoice = $request->input('ssl_invoice_number');
        $txnId = $request->input('ssl_txn_id');
        $approvalCode = $request->input('ssl_approval_code');
        $message = $request->input('ssl_result_message');

        Log::info('Converge Callback Received', $request->all());

        // Validate secure hash to prevent callback spoofing
        $expectedHash = hash_hmac('sha256', $invoice, config('app.key'));
        if ($request->input('secure_hash') !== $expectedHash) {
            Log::error("Invalid secure hash for invoice {$invoice}");
            return redirect()->route('website.home')->with('error', 'Invalid payment verification token.');
        }

        $sale = Sale::where('invoice_number', $invoice)->first();

        if (!$sale) {
            return redirect()->route('website.home')->with('error', 'Order not found.');
        }

        if ($result == '0') {
            // SUCCESS
            DB::beginTransaction();
            try {
                // Update Sale Status
                $sale->update([
                    'status' => 'paid',
                    'payment_status' => 'completed',
                    'transaction_id' => $txnId,
                    'notes' => "Payment Approved. Auth: {$approvalCode}"
                ]);

                // Notify Store
                StoreNotification::create([
                    'store_id' => $sale->store_id,
                    'title' => 'Payment Received: Order #' . $invoice,
                    'message' => "Order #{$invoice} has been paid via Card.",
                    'type' => 'success',
                    'url' => route('store.sales.orders.show', $sale->id),
                ]);

                // Clear the Cart for this user
                $cart = Cart::where('user_id', $sale->customer_id)->where('status', 'active')->first();
                if ($cart) {
                    $cart->items()->delete();
                    $cart->delete();
                }

                DB::commit();
                return redirect()->route('website.checkout.success', $invoice);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Callback Processing Error: ' . $e->getMessage());
                return redirect()->route('website.home')->with('error', 'Internal error processing payment.');
            }
        } else {
            // FAILURE or CANCEL
            $sale->update([
                'status' => 'payment_failed',
                'notes' => "Payment Failed: {$message}"
            ]);

            return redirect()->route('website.home') // Or back to checkout
                ->with('error', 'Payment failed or was cancelled: ' . $message);
        }
    }
}
