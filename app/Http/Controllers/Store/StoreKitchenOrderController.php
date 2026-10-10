<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Kitchen Display (kitchen spec 5.2, contract B.3):
 * New -> Accepted -> Preparing -> Ready -> Handoff (pickup / delivery) -> Completed,
 * Cancel with a reason from any open step. Only orders that contain kitchen
 * menu items are shown (grocery POS sales used to fill the board).
 */
class StoreKitchenOrderController extends Controller
{
    /** status => the one step it may move to */
    public const NEXT = [
        'New' => 'Accepted',
        'Accepted' => 'Preparing',
        'Preparing' => 'Ready',
        'Ready' => 'Handoff',
        'Handoff' => 'Completed',
    ];

    public const CANCEL_REASONS = ['Customer cancelled', 'Item unavailable / sold out', 'Kitchen closed', 'Duplicate order', 'Other'];

    public function index()
    {
        $storeId = Auth::user()->store_id;

        $orders = Sale::with('items.product', 'items.menuItem', 'customer')
            ->where('store_id', $storeId)
            ->whereHas('items', fn ($q) => $q->whereNotNull('menu_item_id'))
            ->whereNotIn('kitchen_status', ['Completed', 'Cancelled'])
            // A website card order isn't real until it is paid.
            ->where(fn ($q) => $q->whereRaw("LOWER(COALESCE(payment_method, '')) <> 'card'")
                ->orWhere('source', '!=', 'website')->orWhereNull('source')
                ->orWhere('payment_status', 'completed')->orWhere('status', 'paid'))
            // Needed-by time: the event time for catering, otherwise when it was placed.
            ->orderByRaw('COALESCE(due_at, created_at) ASC')
            ->get();

        $kanbanData = collect(array_keys(self::NEXT))
            ->mapWithKeys(fn ($status) => [$status => $orders->where('kitchen_status', $status)->values()])
            ->all();
        $cancelReasons = self::CANCEL_REASONS;

        return view('store.kitchen.kds', compact('kanbanData', 'cancelReasons'));
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        if ($sale->store_id != Auth::user()->store_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:Accepted,Preparing,Ready,Handoff,Completed,Cancelled',
            'reason' => 'required_if:status,Cancelled|nullable|string|max:255',
        ], ['reason.required_if' => 'Choose a reason for cancelling.']);

        $current = $sale->kitchen_status ?: 'New';
        $target = $request->status;

        $allowed = $target === 'Cancelled'
            ? array_key_exists($current, self::NEXT)          // any open step
            : (self::NEXT[$current] ?? null) === $target;      // only the next step
        if (!$allowed) {
            return response()->json([
                'success' => false,
                'message' => "Order #{$sale->invoice_number} is {$current}; it can't move to {$target}. Refresh the board.",
            ], 422);
        }

        $sale->update([
            'kitchen_status' => $target,
            'kitchen_cancel_reason' => $target === 'Cancelled' ? $request->reason : $sale->kitchen_cancel_reason,
            'kitchen_status_changed_at' => now(),
            'kitchen_status_changed_by' => Auth::user()->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order #{$sale->invoice_number} is now {$target}.",
        ]);
    }
}
