<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreRole;
use App\Models\StorePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreRoleController extends Controller
{
    public function index()
    {
        $roles = StoreRole::where('guard_name', 'store_user')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = StorePermission::where('guard_name', 'store_user')->get()->groupBy('group_name');
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:store_roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:store_permissions,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create Role
            $role = StoreRole::create([
                'name' => $request->name,
                'guard_name' => 'store_user'
            ]);

            // 2. Permissions Sync (FIXED HERE)
            if ($request->has('permissions')) {
                // IDs directly pass karne se Spatie default table dhoondhta hai.
                // Isliye hum pehle Custom Model se permissions fetch karenge.
                $permissions = StorePermission::whereIn('id', $request->permissions)->get();
                
                // Ab Objects pass karein, IDs nahi
                $role->syncPermissions($permissions);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $role = StoreRole::findOrFail($id);
        $permissions = StorePermission::where('guard_name', 'store_user')->get()->groupBy('group_name');
        $assignedPermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'assignedPermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = StoreRole::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:store_roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:store_permissions,id',
        ]);

        try {
            $role->update(['name' => $request->name]);

            if ($request->has('permissions')) {
                // FIXED HERE ALSO
                $permissions = StorePermission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            return redirect()->route('roles.index')->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $role = StoreRole::findOrFail($id);
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}