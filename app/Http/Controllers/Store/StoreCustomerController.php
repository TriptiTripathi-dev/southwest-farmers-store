<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreCustomerController extends Controller
{
    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $store = \App\Models\StoreDetail::find($storeId);
        
        $query = StoreCustomer::query();

        // Location based filtering
        $query->where(function($q) use ($storeId, $store) {
            // Priority 1: Customers explicitly belonging to this store
            $q->where('store_id', $storeId);
            
            // Priority 2: Website customers within a 50km radius
            if ($store && $store->latitude && $store->longitude) {
                // Use the new scope for consistency with product radius
                $q->orWhere(function($sq) use ($store) {
                    $sq->where('source', 'website')
                       ->whereNotNull('latitude')
                       ->whereNotNull('longitude')
                       ->withinDistance($store->latitude, $store->longitude, 50);
                });
            }

            // Fallback: If they were assigned to this store on signup but have no coords
            $q->orWhere(function($sq) use ($storeId) {
                $sq->where('source', 'website')
                   ->where('store_id', $storeId);
            });
        });

        // 1. Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
                $q->where('name', $operator, "%{$search}%")
                  ->orWhere('phone', $operator, "%{$search}%")
                  ->orWhere('email', $operator, "%{$search}%");
            });
        }

        // 2. Sorting Logic
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'name': $query->orderBy('name', 'asc'); break;
            case 'due': $query->orderBy('due', 'desc'); break;
            case 'recent':
            default: $query->latest(); break;
        }

        $customers = $query->paginate(10)->withQueryString();

        return view('customers.index', compact('customers'));
    }
    public function create()
    {
        return view('customers.create');
    }

    public function show($id)
    {
        $user = Auth::user()->store_id;
        // Ensure we only show customers belonging to the current store
        $customer = StoreCustomer::where('id', $id)->where('store_id', $user)->firstOrFail();
        
        return view('customers.show', compact('customer'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'party_type' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'due' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        // FIXED: Changed $user->id to $user->store_id
        $data['store_id'] = $user->store_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('customers', 'public');
        }

        StoreCustomer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit($id)
    {
        $user = Auth::user()->store_id;
        // This was already correct ($user->store_id)
        $customer = StoreCustomer::where('id', $id)->where('store_id', $user)->firstOrFail();
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user()->store_id;
        // This was already correct ($user->store_id)
        $customer = StoreCustomer::where('id', $id)->where('store_id', $user)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'party_type' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'due' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($customer->image) {
                Storage::disk('public')->delete($customer->image);
            }
            $data['image'] = $request->file('image')->store('customers', 'public');
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user()->store_id;
        // This was already correct ($user->store_id)
        $customer = StoreCustomer::where('id', $id)->where('store_id', $user)->firstOrFail();

        if ($customer->image) {
            Storage::disk('public')->delete($customer->image);
        }
        
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}