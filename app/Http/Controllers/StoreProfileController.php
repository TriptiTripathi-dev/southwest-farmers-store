<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreDetail;
use App\Models\StoreUser;
use Illuminate\Support\Facades\Auth;

class StoreProfileController extends Controller
{
    /**
     * Show the Store Profile Page
     */
    public function index(Request $request)
    {
        // Get the logged-in user
        $user = StoreUser::where('id',auth()->user()->id)->first();
$query = StoreDetail::query();

        if ($request->search) {
            $query->where('store_name', 'like', '%' . $request->search . '%')
                  ->orWhere('store_code', 'like', '%' . $request->search . '%');
        }

       

        $stores = $query->latest()->paginate(10);

        return view('store.index', compact('stores'));
    }
public function edit(StoreDetail $store)
    {
        return view('store.edit', compact('store'));
    }
    public function update(Request $request, StoreDetail $store)
    {
        if (!$store) {
            return back()->with('error', 'Store not found.');
        }
        $request->validate([
            'store_name' => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'city'       => 'nullable|string',
            'pincode'    => 'nullable|string',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
        ]);
        $store->update([
            'store_name' => $request->store_name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'city'       => $request->city,
            'state'      => $request->state,
            'country'    => $request->country,
            'pincode'    => $request->pincode,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
        ]);
        return back()->with('success', 'Store profile updated successfully!');
    }
    public function updateStatus(Request $request)
    {
        $warehouse = Warehouse::findOrFail($request->id);
        $warehouse->update(['is_active' => $request->status]);

        return response()->json([
            'status'  => true,
            'message' => 'Warehouse status updated successfully.',
        ]);
    }
}