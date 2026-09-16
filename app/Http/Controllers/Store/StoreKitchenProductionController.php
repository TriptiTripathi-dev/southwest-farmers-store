<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\KitchenLocation;
use App\Models\KitchenProduction;
use App\Models\LeftoverLog;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreKitchenProductionController extends Controller
{
    protected function locationForCurrentStore(): KitchenLocation
    {
        $storeId = Auth::user()->store_id;

        return KitchenLocation::firstOrCreate(
            ['store_id' => $storeId, 'type' => 'store'],
            ['name' => 'Store Kitchen']
        );
    }

    public function index()
    {
        $location = $this->locationForCurrentStore();

        $productions = KitchenProduction::with('menuItem')
            ->where('kitchen_location_id', $location->id)
            ->orderByDesc('produced_at')
            ->get();

        return view('store.kitchen.production.index', compact('productions'));
    }

    public function create()
    {
        $storeId = Auth::user()->store_id;
        $menuItems = MenuItem::where('store_id', $storeId)->orderBy('name')->get();

        return view('store.kitchen.production.create', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $location = $this->locationForCurrentStore();

        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity_made' => 'required|numeric|min:0.01',
            'quantity_unit' => 'required|string',
            'yield_plates' => 'nullable|numeric|min:0',
            'daily_target' => 'nullable|integer|min:0',
            'produced_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        MenuItem::where('id', $validated['menu_item_id'])->where('store_id', $storeId)->firstOrFail();

        KitchenProduction::create($validated + [
            'kitchen_location_id' => $location->id,
            'ware_user_id' => Auth::id(),
        ]);

        return redirect()->route('store.kitchen.production.index')->with('success', 'Production log saved successfully!');
    }

    public function leftovers()
    {
        $location = $this->locationForCurrentStore();

        $logs = LeftoverLog::with('menuItem')
            ->where('kitchen_location_id', $location->id)
            ->orderByDesc('log_date')
            ->get();

        return view('store.kitchen.production.leftovers', compact('logs'));
    }

    public function createLeftover()
    {
        $storeId = Auth::user()->store_id;
        $menuItems = MenuItem::where('store_id', $storeId)->orderBy('name')->get();

        return view('store.kitchen.production.leftovers-create', compact('menuItems'));
    }

    public function storeLeftover(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $location = $this->locationForCurrentStore();

        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity_produced' => 'required|numeric|min:0',
            'quantity_sold' => 'required|numeric|min:0',
            'log_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        MenuItem::where('id', $validated['menu_item_id'])->where('store_id', $storeId)->firstOrFail();

        LeftoverLog::create($validated + [
            'kitchen_location_id' => $location->id,
            'quantity_leftover' => max(0, $validated['quantity_produced'] - $validated['quantity_sold']),
        ]);

        return redirect()->route('store.kitchen.production.leftovers')->with('success', 'Leftover log saved successfully!');
    }
}
