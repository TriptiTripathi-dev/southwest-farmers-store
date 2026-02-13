<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreNotification;
use App\Models\StoreUser;
use App\Models\StoreRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $query = StoreUser::where('store_id', $currentUser->store_id)
            ->where('id', '!=', $currentUser->id)
            ->with('roles');

        // FIXED: Case-Insensitive Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Use 'ilike' for PostgreSQL case-insensitive search
                // If using MySQL, 'like' is usually fine, but 'ilike' ensures it works for Postgres users
                $operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';

                $q->where('name', $operator, "%{$search}%")
                    ->orWhere('email', $operator, "%{$search}%")
                    ->orWhere('phone', $operator, "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('store_role_id', $request->role);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        $staffMembers = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = StoreRole::where('guard_name', 'store_user')
            ->where('name', '!=', 'Super Admin')
            ->get();

        return view('staff.index', compact('staffMembers', 'roles'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:store_users,id',
            'status' => 'required|boolean'
        ]);

        try {
            $currentUser = Auth::user();

            $staff = StoreUser::where('id', $request->id)
                ->where('parent_id', $currentUser->id)
                ->firstOrFail();

            $staff->is_active = $request->status;
            $staff->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status'], 500);
        }
    }

    public function create()
    {
        $roles = StoreRole::where('name', '!=', 'Super Admin')->get();
        return view('staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:store_users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:store_roles,id',
        ]);

        try {
            DB::beginTransaction();
            $currentUser = Auth::user();

            $staff = StoreUser::create([
                'parent_id' => $currentUser->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'store_role_id' => $request->role_id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // --- FIX START ---
            // Explicitly pass 'model_type' in the array
            $staff->roles()->sync([
                $request->role_id => ['model_type' => get_class($staff)]
            ]);
            // --- FIX END ---

            StoreNotification::create([
                'user_id' => Auth::id(),
                'store_id' => Auth::user()->store_id,
                'title' => 'Staff Added',
                'message' => "New staff member '{$staff->name}' added successfully.",
                'type' => 'info',
                'url' => route('staff.index'),
            ]);

            DB::commit();
            return redirect()->route('staff.index')->with('success', 'Staff member created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating staff: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $currentUser = Auth::user();
        $staff = StoreUser::where('id', $id)->where('parent_id', $currentUser->id)->firstOrFail();
        $roles = StoreRole::where('name', '!=', 'Super Admin')->get();
        $currentRoleId = $staff->store_role_id ?? $staff->roles->first()?->id;
        dd($currentRoleId);

        return view('staff.edit', compact('staff', 'roles', 'currentRoleId'));
    }

    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        $staff = StoreUser::where('id', $id)->where('parent_id', $currentUser->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:store_users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:store_roles,id',
            'is_active' => 'sometimes'
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'store_role_id' => $request->role_id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $staff->update($data);

            $role = StoreRole::find($request->role_id);
            if ($role) {
                $staff->roles()->sync([$role]);
            }

            DB::commit();
            return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating staff: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $currentUser = Auth::user();
        $staff = StoreUser::where('id', $id)->where('parent_id', $currentUser->id)->firstOrFail();

        if ($staff->id === $currentUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $name = $staff->name;

        $staff->delete();
        StoreNotification::create([
            'user_id' => Auth::id(),
            'store_id' => Auth::user()->store_id,
            'title' => 'Staff Deleted',
            'message' => "Staff member '{$name}' was removed.",
            'type' => 'danger',
            'url' => route('staff.index'),
        ]);
        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully.');
    }
}
