<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StorePermission; // Using your custom model
use Illuminate\Http\Request;

class StorePermissionController extends Controller
{
    /**
     * Display a listing of permissions grouped by 'group_name'.
     */
    public function index()
    {
        // Fetch only store permissions and group them
        $permissions = StorePermission::
            orderBy('group_name')
            ->get()
            ->groupBy('group_name');

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:store_permissions,name', // Check unique in store_permissions table
            'group_name' => 'nullable|string|max:255',
        ]);

        StorePermission::create([
            'name' => $request->name,
            'group_name' => $request->group_name ?? 'General',
            'guard_name' => 'store_user', // Force store guard
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit($id)
    {
        $permission = StorePermission::findOrFail($id);
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, $id)
    {
        $permission = StorePermission::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:store_permissions,name,' . $id,
            'group_name' => 'nullable|string|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy($id)
    {
        $permission = StorePermission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}