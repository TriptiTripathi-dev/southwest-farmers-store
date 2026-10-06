<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemAvailabilityLog;
use App\Models\StoreSetting;
use App\Services\MenuAvailabilityService;
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

        $options = StoreSetting::availabilityOptions($storeId);
        $allItems = MenuItem::where('store_id', $storeId)->get();
        $current = $allItems->map(fn ($i) => $i->currentStatus($options['sold_out_resets_daily']));

        $stats = [
            'total' => $allItems->count(),
            'available_today' => $current->filter(fn ($st) => $st === MenuItem::AVAILABLE)->count(),
            'sold_out' => $current->filter(fn ($st) => $st === MenuItem::SOLD_OUT)->count(),
            'catering_only' => $allItems->where('is_catering_only', true)->count(),
        ];

        // Last few changes at this store (who / when), for the audit panel.
        $recentChanges = MenuItemAvailabilityLog::where('store_id', $storeId)->with('menuItem')
            ->latest('created_at')->latest('id')->limit(10)->get();

        return view('store.kitchen.availability.index', compact('menuItems', 'categories', 'daysOfWeek', 'stats', 'options', 'recentChanges'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        if ($menuItem->store_id != Auth::user()->store_id) {
            abort(403);
        }

        $request->validate([
            'availability_status' => 'nullable|in:available,unavailable,sold_out',
            'is_catering_only' => 'nullable|boolean',
            'advance_notice_days' => 'nullable|integer|min:0|max:90',
            'rush_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'available_days' => 'nullable|array',
            'available_days.*' => 'string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        if ($request->filled('availability_status')) {
            app(MenuAvailabilityService::class)->set($menuItem, $request->availability_status);
        }

        $menuItem->update([
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

    /** Available / Unavailable / Sold Out, one click, logged. */
    public function setStatus(Request $request, MenuItem $menuItem, MenuAvailabilityService $service)
    {
        if ($menuItem->store_id != Auth::user()->store_id) {
            abort(403);
        }
        $request->validate(['status' => 'required|in:available,unavailable,sold_out']);

        $service->set($menuItem, $request->status);
        $label = MenuItem::STATUS_LABELS[$request->status];

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $request->status, 'message' => "{$menuItem->name} is now {$label}."]);
        }

        return redirect()->back()->with('success', "{$menuItem->name} is now {$label}.");
    }

    /** Old one-click toggle: Available <-> Unavailable (kept for existing links). */
    public function toggleToday(Request $request, MenuItem $menuItem, MenuAvailabilityService $service)
    {
        if ($menuItem->store_id != Auth::user()->store_id) {
            abort(403);
        }

        $options = StoreSetting::availabilityOptions($menuItem->store_id);
        $next = $menuItem->currentStatus($options['sold_out_resets_daily']) === MenuItem::AVAILABLE ? MenuItem::UNAVAILABLE : MenuItem::AVAILABLE;
        $service->set($menuItem, $next);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_available_today' => $next === MenuItem::AVAILABLE,
                'message' => "{$menuItem->name} is now " . MenuItem::STATUS_LABELS[$next] . '.',
            ]);
        }

        return redirect()->back()->with('success', "{$menuItem->name} is now " . MenuItem::STATUS_LABELS[$next] . '.');
    }

    /** How the website shows Sold Out items, and whether Sold Out clears itself the next day. */
    public function updateOptions(Request $request)
    {
        $data = $request->validate([
            'sold_out_display' => 'required|in:show,hide',
            'sold_out_resets_daily' => 'nullable|boolean',
        ]);

        StoreSetting::updateOrCreate(
            ['store_id' => Auth::user()->store_id],
            ['sold_out_display' => $data['sold_out_display'], 'sold_out_resets_daily' => $request->boolean('sold_out_resets_daily')]
        );

        return redirect()->back()->with('success', 'Sold Out options saved.');
    }
}
