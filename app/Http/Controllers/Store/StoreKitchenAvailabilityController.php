<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreKitchenAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;

        $categories = MenuCategory::where('store_id', $storeId)->with('menuItems')->get();

        $query = MenuItem::where('store_id', $storeId)->with('category');

        if ($request->filled('category_id')) {
            $query->where('menu_category_id', $request->category_id);
        }

        if ($request->filled('catering_filter')) {
            if ($request->catering_filter === 'catering_only') {
                $query->where('is_catering_only', true);
            } elseif ($request->catering_filter === 'regular') {
                $query->where('is_catering_only', false);
            }
        }

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        $menuItems = $query->orderBy('name')->paginate(25)->withQueryString();
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        $stats = [
            'total' => MenuItem::where('store_id', $storeId)->count(),
            'available_today' => MenuItem::where('store_id', $storeId)->where('is_available_today', true)->count(),
            'catering_only' => MenuItem::where('store_id', $storeId)->where('is_catering_only', true)->count(),
            'pre_cooked' => MenuItem::where('store_id', $storeId)->where('is_pre_cooked', true)->count(),
        ];

        return view('store.kitchen.availability.index', compact('menuItems', 'categories', 'daysOfWeek', 'stats'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        if ($menuItem->store_id != Auth::user()->store_id) {
            abort(403);
        }

        $request->validate([
            'is_available_today' => 'nullable|boolean',
            'is_catering_only' => 'nullable|boolean',
            'advance_notice_days' => 'nullable|integer|min:0|max:90',
            'rush_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'available_days' => 'nullable|array',
            'available_days.*' => 'string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        $menuItem->update([
            'is_available_today' => $request->has('is_available_today') ? (bool) $request->is_available_today : false,
            'is_catering_only' => $request->has('is_catering_only') ? (bool) $request->is_catering_only : false,
            'advance_notice_days' => $request->input('advance_notice_days', 7),
            'rush_fee_percentage' => $request->input('rush_fee_percentage', 0),
            'available_days' => $request->input('available_days', ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Updated availability for {$menuItem->name}.",
            ]);
        }

        return redirect()->back()->with('success', "Updated availability parameters for {$menuItem->name}.");
    }

    public function toggleToday(Request $request, MenuItem $menuItem)
    {
        if ($menuItem->store_id != Auth::user()->store_id) {
            abort(403);
        }

        $menuItem->is_available_today = !$menuItem->is_available_today;
        $menuItem->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_available_today' => $menuItem->is_available_today,
                'message' => "{$menuItem->name} is now " . ($menuItem->is_available_today ? 'Available Today' : 'Unavailable Today') . '.',
            ]);
        }

        return redirect()->back()->with('success', "{$menuItem->name} availability status updated.");
    }
}
