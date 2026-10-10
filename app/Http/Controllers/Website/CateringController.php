<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StoreDetail;
use App\Models\StoreNotification;
use App\Models\StoreSetting;
use App\Services\CateringService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Website catering section (kitchen spec 8, contract B.7): separate from the
 * daily menu, ordered for a future date/time, urgency surcharge (or block)
 * shown before checkout, accepted orders go to the kitchen (KDS) with the
 * requested date/time.
 */
class CateringController extends Controller
{
    public function __construct(private CateringService $catering)
    {
    }

    /** Same store choice as the website menu: session, customer's store, first active. */
    protected function storeId(): ?int
    {
        if (session()->has('store_id')) {
            return (int) session('store_id');
        }
        if (auth('customer')->check() && auth('customer')->user()->store_id) {
            return (int) auth('customer')->user()->store_id;
        }

        return StoreDetail::where('is_active', true)->value('id');
    }

    public function index()
    {
        $storeId = $this->storeId();
        $store = $storeId ? StoreDetail::find($storeId) : null;
        $options = StoreSetting::cateringOptions($storeId);
        $tiers = $storeId ? $this->catering->tiers($storeId) : collect();

        // Every active menu item can be ordered for catering (catering-only items
        // included); daily Sold Out does not apply to a future date.
        $categories = $storeId ? MenuCategory::where('store_id', $storeId)->where('is_active', true)->orderBy('name')->get() : collect();
        $items = $storeId ? MenuItem::where('store_id', $storeId)->where('is_active', true)->orderBy('name')->get()->groupBy('menu_category_id') : collect();

        $tz = CateringService::tz();
        $earliest = now($tz)->addDays($options['notice_days'])->format('Y-m-d');

        return view('website.catering.index', compact('store', 'options', 'tiers', 'categories', 'items', 'earliest'));
    }

    protected function dueAt(Request $request): ?Carbon
    {
        try {
            return Carbon::parse($request->input('due_date') . ' ' . $request->input('due_time', '12:00'), CateringService::tz());
        } catch (\Throwable) {
            return null;
        }
    }

    /** Live price check for the page (same rules the order uses). */
    public function quote(Request $request)
    {
        $request->validate(['due_date' => 'required|date', 'due_time' => 'nullable|date_format:H:i', 'items' => 'array']);
        $storeId = $this->storeId();
        $dueAt = $this->dueAt($request);
        if (!$storeId || !$dueAt) {
            return response()->json(['ok' => false, 'message' => 'Choose a valid date and time.'], 422);
        }

        return response()->json($this->catering->quote($storeId, $request->input('items', []), $dueAt));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'due_date' => 'required|date',
            'due_time' => 'required|date_format:H:i',
            'items' => 'required|array',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:30',
            'guest_count' => 'nullable|integer|min:1|max:10000',
            'order_type' => 'required|in:Pickup,Delivery',
            'notes' => 'nullable|string|max:2000',
            'payment_method' => 'required|in:card,cod',
        ]);

        $user = auth('customer')->user();
        $storeId = $this->storeId();
        $dueAt = $this->dueAt($request);
        $quote = $this->catering->quote($storeId, $data['items'], $dueAt);
        if (!$quote['ok']) {
            return response()->json(['success' => false, 'message' => $quote['message']], 422);
        }

        DB::beginTransaction();
        try {
            $prefix = 'CAT-' . date('Ymd');
            $seq = (int) substr(Sale::where('invoice_number', 'like', $prefix . '-%')->orderByDesc('id')->value('invoice_number') ?? '0000', -4) + 1;
            $invoice = $prefix . '-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'store_id' => $storeId,
                'customer_id' => $user->id,
                'invoice_number' => $invoice,
                'subtotal' => $quote['subtotal'],
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $quote['total'],
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
                'source' => 'website',
                'created_by' => null,
                'order_type' => 'Catering',
                'due_at' => $dueAt->copy()->utc(),
                'special_instructions' => trim(($data['order_type'] === 'Delivery' ? 'DELIVERY. ' : 'PICKUP. ') . ($data['notes'] ?? '')),
                'catering_notice_days' => $quote['notice_days'],
                'catering_surcharge_percent' => $quote['percent'],
                'catering_surcharge_amount' => $quote['surcharge'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'guest_count' => $data['guest_count'] ?? null,
            ]);

            foreach ($quote['lines'] as $line) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'menu_item_id' => $line['menu_item_id'],
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                    'total' => $line['total'],
                    'total_cogs' => 0,
                ]);
            }

            if ($data['payment_method'] === 'card') {
                $converge = new \App\Services\ConvergeService();
                $token = $converge->generateTransactionToken($quote['total'], $invoice, $user);
                if (!$token) {
                    throw new \RuntimeException('Unable to start the card payment. Please try again or choose pay on pickup.');
                }
                DB::commit();

                return response()->json(['success' => true, 'payment_required' => true, 'redirect' => $converge->getHppUrl($token, $invoice)]);
            }

            StoreNotification::create([
                'store_id' => $storeId,
                'title' => 'New Catering Order',
                'message' => "Catering order #{$invoice} for " . $dueAt->format('m/d/Y h:i A') . " from {$data['contact_name']}"
                    . ($quote['surcharge'] > 0 ? " (urgency surcharge \${$quote['surcharge']})" : ''),
                'type' => 'info',
                'url' => route('store.kitchen.catering.index'),
            ]);

            DB::commit();

            return response()->json(['success' => true, 'redirect' => route('website.checkout.success', $invoice)]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Catering order failed', ['exception' => $e]);

            return response()->json(['success' => false, 'message' => $e instanceof \RuntimeException && !$e instanceof \Illuminate\Database\QueryException
                ? $e->getMessage() : 'The catering order could not be placed. Please try again.'], 500);
        }
    }
}
