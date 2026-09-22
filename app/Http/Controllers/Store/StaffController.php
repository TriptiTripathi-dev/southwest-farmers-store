<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreDetail;
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
            // Filters against every role the staff member holds, not just their
            // primary (store_role_id) one, now that a staff member can have more
            // than one role.
            $query->whereHas('roles', fn ($q) => $q->where('store_roles.id', $request->role));
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        $staffMembers = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $rolesQuery = StoreRole::where('guard_name', 'store_user');
        if (!$currentUser->hasRole('Super Admin')) {
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        $roles = $rolesQuery->get();

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
                ->where('store_id', $currentUser->store_id)
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
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->hasRole('Super Admin');

        $rolesQuery = StoreRole::query();
        if (!$isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        $roles = $rolesQuery->get();

        // Item 3: only a Super Admin gets to pick which location a new hire
        // belongs to; everyone else keeps the existing behavior of the new
        // staff member silently inheriting the creating admin's own store.
        $locations = $isSuperAdmin ? StoreDetail::where('is_active', true)->orderBy('store_name')->get() : collect();

        return view('staff.create', compact('roles', 'locations', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:store_users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:store_roles,id',
            'store_id' => 'nullable|exists:store_details,id',
        ]);

        $currentUser = Auth::user();
        $roleIds = array_map('intval', $request->role_ids);

        // Guard against a raw POST picking the "Super Admin" role option the
        // checkboxes never rendered for a non-Super-Admin actor.
        if ($this->requestsSuperAdminRole($roleIds) && !$currentUser->hasRole('Super Admin')) {
            return back()->withInput()->with('error', 'Only a Super Admin can assign the Super Admin role.');
        }

        try {
            DB::beginTransaction();

            $targetStoreId = $currentUser->store_id;
            if ($currentUser->hasRole('Super Admin') && $request->filled('store_id')) {
                $targetStoreId = $request->store_id;
            }

            $staff = StoreUser::create([
                'parent_id' => $currentUser->id,
                // Was never set — new staff had store_id = null, so they never
                // matched index()'s `where('store_id', $currentUser->store_id)`
                // filter and silently disappeared from the Store Staff list.
                'store_id' => $targetStoreId,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                // Legacy single-role column; kept in sync with the first
                // selected role so anything still reading it (e.g. the
                // pre-multi-role fallback in edit()) sees a sane value.
                'store_role_id' => $roleIds[0],
                'is_active' => $request->has('is_active') ? 1 : 0,
                // Item 4: every employee needs a store ID to clock in/out with.
                'staff_code' => 'EMP-' . str_pad((\App\Models\StoreUser::withTrashed()->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT),
            ]);

            $this->syncRoles($staff, $roleIds);

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
        $isSuperAdmin = $currentUser->hasRole('Super Admin');
        $staff = StoreUser::where('id', $id)->where('store_id', $currentUser->store_id)->firstOrFail();

        $rolesQuery = StoreRole::query();
        if (!$isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        $roles = $rolesQuery->get();
        $currentRoleIds = $staff->roles->pluck('id')->all();
        if (empty($currentRoleIds) && $staff->store_role_id) {
            $currentRoleIds = [$staff->store_role_id];
        }

        $locations = $isSuperAdmin ? StoreDetail::where('is_active', true)->orderBy('store_name')->get() : collect();

        return view('staff.edit', compact('staff', 'roles', 'currentRoleIds', 'locations', 'isSuperAdmin'));
    }

    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        $staff = StoreUser::where('id', $id)->where('store_id', $currentUser->store_id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:store_users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:store_roles,id',
            'store_id' => 'nullable|exists:store_details,id',
            'is_active' => 'sometimes'
        ]);

        $roleIds = array_map('intval', $request->role_ids);

        if ($this->requestsSuperAdminRole($roleIds) && !$currentUser->hasRole('Super Admin')) {
            return back()->withInput()->with('error', 'Only a Super Admin can assign the Super Admin role.');
        }

        try {
            DB::beginTransaction();

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'store_role_id' => $roleIds[0],
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            if ($currentUser->hasRole('Super Admin') && $request->filled('store_id')) {
                $data['store_id'] = $request->store_id;
            }

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $staff->update($data);

            $this->syncRoles($staff, $roleIds);

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
        $staff = StoreUser::where('id', $id)->where('store_id', $currentUser->store_id)->firstOrFail();

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

    /** @param int[] $roleIds */
    private function requestsSuperAdminRole(array $roleIds): bool
    {
        return StoreRole::whereIn('id', $roleIds)->where('name', 'Super Admin')->exists();
    }

    /**
     * Replaces every role currently attached to $staff with exactly $roleIds
     * (add newly-checked roles, remove unchecked ones). sync() itself never
     * creates a duplicate row for a role already attached, and
     * store_model_has_roles now has a unique constraint as a second line of
     * defense (see migration 2026_09_22_090000).
     *
     * @param int[] $roleIds
     */
    private function syncRoles(StoreUser $staff, array $roleIds): void
    {
        $staff->roles()->sync(
            collect($roleIds)->mapWithKeys(fn ($roleId) => [$roleId => ['model_type' => get_class($staff)]])->all()
        );
    }
}
