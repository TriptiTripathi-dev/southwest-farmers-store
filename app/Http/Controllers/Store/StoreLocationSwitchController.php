<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Item 1: "When I log in I see Bissonnet at the top. How do I choose
 * another location to view?" -- lets a Super Admin (any store) or a staff
 * member assigned a Store Group, e.g. a Regional Manager (client PDF 9/22,
 * item 6; only that group's stores) switch which store's
 * data they're viewing/acting in without logging into a separate account.
 * StoreUser::getStoreIdAttribute() reads the session key this sets, so
 * every existing store_id-scoped controller respects the switch automatically.
 */
class StoreLocationSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:store_details,id',
        ]);

        if (!Auth::user()->canSwitchToStore((int) $request->store_id)) {
            abort(403);
        }

        $store = StoreDetail::where('id', $request->store_id)->where('is_active', true)->firstOrFail();

        session(['active_store_id' => $store->id]);

        return redirect()->back()->with('success', "Now viewing: {$store->store_name}");
    }

    public function reset()
    {
        session()->forget('active_store_id');

        return redirect()->back()->with('success', 'Switched back to your home location.');
    }
}
