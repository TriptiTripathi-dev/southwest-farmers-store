<?php

namespace App\Http\Controllers\Store;

use App\Exceptions\KitchenStockException;
use App\Http\Controllers\Controller;
use App\Models\KitchenLocation;
use App\Models\KitchenStock;
use App\Models\KitchenStockTransaction;
use App\Models\StoreStock;
use App\Services\KitchenInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Store Kitchen inventory (kitchen spec section 6, contract B.4): kept apart
 * from the store shelf, every movement written to the kitchen ledger with
 * user and time. Each store has its own KitchenLocation.
 *
 * Access: the existing 'adjust_stock' permission (the menu already used it);
 * who may adjust kitchen stock is a client decision, made through roles.
 */
class KitchenInventoryController extends Controller
{
    public function __construct(private KitchenInventoryService $inventory)
    {
    }

    protected function authorizeKitchen(): void
    {
        abort_unless(Auth::user()?->hasPermission('adjust_stock'), 403, 'You do not have access to Kitchen Inventory.');
    }

    protected function locationForCurrentStore(): KitchenLocation
    {
        $storeId = Auth::user()->store_id;

        return KitchenLocation::firstOrCreate(
            ['store_id' => $storeId, 'type' => 'store'],
            ['name' => 'Store Kitchen']
        );
    }

    /** A kitchen stock row of this store's kitchen, or 404. */
    protected function ownStock(int $id): KitchenStock
    {
        return KitchenStock::where('kitchen_location_id', $this->locationForCurrentStore()->id)->findOrFail($id);
    }

    public function index(Request $request)
    {
        $this->authorizeKitchen();
        $storeId = Auth::user()->store_id;
        $location = $this->locationForCurrentStore();

        $kitchenStocks = KitchenStock::where('kitchen_location_id', $location->id)
            ->where('item_type', 'product')
            ->with('product')
            ->get()
            ->sortBy(fn ($s) => $s->product->product_name ?? '')
            ->values();

        // Only products with stock on hand at this store are eligible to transfer.
        $storeStocks = StoreStock::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->with('product')
            ->get();

        $stats = [
            'items' => $kitchenStocks->count(),
            'below_min' => $kitchenStocks->where('stock_status', 'Below Minimum')->count(),
            'out' => $kitchenStocks->where('stock_status', 'Out of Stock')->count(),
        ];
        $wasteReasons = KitchenInventoryService::WASTE_REASONS;
        $adjustReasons = KitchenInventoryService::ADJUST_REASONS;

        return view('store.kitchen-inventory.index', compact('kitchenStocks', 'storeStocks', 'stats', 'wasteReasons', 'adjustReasons'));
    }

    /**
     * Old one-step "Transfer to Kitchen" form. Kitchen spec 7.1: shelf ->
     * kitchen stock may only move after Area Manager approval, so this now
     * files a pending request instead of moving stock.
     */
    public function transfer(Request $request, \App\Services\KitchenTransferService $transfers)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $req = $transfers->create(Auth::user(), [['product_id' => $request->product_id, 'quantity' => $request->quantity]], $request->notes);
        } catch (KitchenStockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('kitchen-transfers.show', $req->id)
            ->with('success', "Request {$req->number()} sent for Area Manager approval. Kitchen stock changes only after approval.");
    }

    /**
     * Receive / Use / Adjust / Waste / Count on one kitchen item.
     * action: receive | use | adjust | waste | count
     */
    public function movement(Request $request, int $stock)
    {
        $this->authorizeKitchen();
        $kitchenStock = $this->ownStock($stock);

        $data = $request->validate([
            'action' => 'required|in:receive,use,adjust,waste,count',
            'quantity' => 'required|numeric|min:0' . ($request->input('action') === 'count' ? '' : '|gt:0'),
            'direction' => 'required_if:action,adjust|nullable|in:in,out',
            'reason' => 'required_if:action,adjust,waste|nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ], [
            'reason.required_if' => 'Choose a reason.',
            'direction.required_if' => 'Choose whether the adjustment adds or removes stock.',
            'quantity.gt' => 'Enter a quantity greater than 0.',
        ]);

        $qty = round((float) $data['quantity'], 2);
        $name = $kitchenStock->product->product_name ?? 'Item';
        $reason = $data['reason'] ?? null;
        $notes = $data['notes'] ?? null;

        try {
            $entry = match ($data['action']) {
                'receive' => $this->inventory->move($kitchenStock, 'receive', $qty, $reason, $notes),
                'use' => $this->inventory->move($kitchenStock, 'use', -$qty, $reason, $notes),
                'waste' => $this->inventory->move($kitchenStock, 'waste', -$qty, $reason, $notes),
                'adjust' => $data['direction'] === 'in'
                    ? $this->inventory->move($kitchenStock, 'adjust_in', $qty, $reason, $notes)
                    : $this->inventory->move($kitchenStock, 'adjust_out', -$qty, $reason, $notes),
                'count' => $this->inventory->count($kitchenStock, $qty, $notes),
            };
        } catch (KitchenStockException $e) {
            return back()->with('error', $e->getMessage());
        }

        $kitchenStock->refresh();
        $now = rtrim(rtrim(number_format((float) $kitchenStock->quantity, 2), '0'), '.');

        return back()->with('success', $entry
            ? "{$name}: {$entry->typeLabel()} recorded. Kitchen now has {$now}."
            : "{$name}: count matches the system ({$now}). Nothing to change.");
    }

    /** Minimum level (low-stock status) and reserved quantity. */
    public function updateLevels(Request $request, int $stock)
    {
        $this->authorizeKitchen();
        $kitchenStock = $this->ownStock($stock);

        $data = $request->validate([
            'min_quantity' => 'required|numeric|min:0',
            'reserved_quantity' => 'nullable|numeric|min:0',
        ]);

        $kitchenStock->update([
            'min_quantity' => round((float) $data['min_quantity'], 2),
            'reserved_quantity' => round((float) ($data['reserved_quantity'] ?? $kitchenStock->reserved_quantity), 2),
        ]);

        return back()->with('success', ($kitchenStock->product->product_name ?? 'Item') . ': levels updated.');
    }

    /** Kitchen ledger: every movement, newest first, with filters. */
    public function history(Request $request)
    {
        $this->authorizeKitchen();
        $location = $this->locationForCurrentStore();
        $tz = config('app.display_timezone', 'America/Chicago');

        $query = KitchenStockTransaction::where('kitchen_location_id', $location->id)
            ->with('stock.product')
            ->latest('created_at')->latest('id');

        if ($request->filled('stock')) {
            $query->where('kitchen_stock_id', $request->integer('stock'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        // Dates are the store's local (Central) days.
        if ($request->filled('from')) {
            $query->where('created_at', '>=', Carbon::parse($request->from, $tz)->startOfDay()->utc());
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', Carbon::parse($request->to, $tz)->endOfDay()->utc());
        }

        $transactions = $query->paginate(25)->withQueryString();
        $items = KitchenStock::where('kitchen_location_id', $location->id)->with('product')->get()
            ->sortBy(fn ($s) => $s->product->product_name ?? '')->values();
        $types = KitchenStockTransaction::TYPES;

        return view('store.kitchen-inventory.history', compact('transactions', 'items', 'types', 'tz'));
    }
}
