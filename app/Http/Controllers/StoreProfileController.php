<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreDetail;
use App\Models\StoreUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreProfileController extends Controller
{
    /**
     * Show the Store Profile Page
     */
    public function index(Request $request)
    {
        $user = StoreUser::where('id', auth()->user()->id)->first();
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
        // 1. Remove dd() to allow code to run. 
        // If $store is null here, ensure your Route is using {store} and not {id}
        
        // 2. Validation
        $request->validate([
            'store_name' => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'city'       => 'nullable|string',
            'pincode'    => 'nullable|string',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'profile'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // 3. Prepare data for StoreDetail
        $data = [
            'store_name' => $request->store_name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'city'       => $request->city,
            'state'      => $request->state,
            'country'    => $request->country,
            'pincode'    => $request->pincode,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
        ];

        // 4. Handle Profile Image Upload
        if ($request->hasFile('profile')) {
            
            // Delete old image if it exists
            if ($store->profile && Storage::disk('public')->exists($store->profile)) {
                Storage::disk('public')->delete($store->profile);
            }

            // Store new image
            $path = $request->file('profile')->store('store-profiles', 'public');
            
            // Add path to StoreDetail data array
            $data['profile'] = $path;

            // 5. Update the associated StoreUser (if exists)
            // Ensure 'store_user_id' is the correct Foreign Key in your store_details table
            if ($store->store_user_id) {
                $storeUser = StoreUser::find($store->store_user_id);
                
                if ($storeUser) {
                    // FIX: update() requires an array ['column_name' => 'value']
                    $storeUser->update([
                        'profile' => $path
                    ]);
                }
            }
        }

        // 6. Update StoreDetail
        $store->update($data);

        return back()->with('success', 'Store profile updated successfully!');
    }

    public function updateStatus(Request $request)
    {
        $warehouse = StoreUser::findOrFail($request->id);
        $warehouse->update(['is_active' => $request->status]);

        return response()->json([
            'status'  => true,
            'message' => 'Store status updated successfully.',
        ]);
    }
}