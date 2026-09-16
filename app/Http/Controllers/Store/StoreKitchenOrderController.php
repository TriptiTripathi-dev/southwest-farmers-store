<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreKitchenOrderController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store_id;

        $orders = Sale::with('items.product', 'items.menuItem')
            ->where('store_id', $storeId)
            ->whereNotIn('kitchen_status', ['Completed', 'Cancelled'])
            ->orderBy('created_at', 'asc')
            ->get();

        $kanbanData = [
            'New' => $orders->where('kitchen_status', 'New')->values(),
            'Accepted' => $orders->where('kitchen_status', 'Accepted')->values(),
            'Preparing' => $orders->where('kitchen_status', 'Preparing')->values(),
            'Ready' => $orders->where('kitchen_status', 'Ready')->values(),
        ];

        return view('store.kitchen.kds', compact('kanbanData'));
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $storeId = Auth::user()->store_id;
        if ($sale->store_id != $storeId) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:Accepted,Preparing,Ready,Completed,Cancelled',
        ]);

        $sale->update(['kitchen_status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated to ' . $request->status,
        ]);
    }
}
