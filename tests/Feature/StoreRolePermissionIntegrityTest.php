<?php

namespace Tests\Feature;

use App\Http\Controllers\Store\StaffController;
use App\Models\StoreRole;
use App\Models\StoreUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Locks in the fix for "Store Admin role is showing twice" / "no way to
 * remove an individual role" / "will we have multiple unique roles per
 * staff?". Root cause (found live against production, read-only first):
 * store_roles, store_permissions and store_role_has_permissions had NO
 * primary key at all and every row was duplicated exactly once, so any join
 * against store_roles doubled regardless of whether a given staff member's
 * own role assignment was clean. store_model_has_roles also had no unique
 * key, letting a race or repeat sync() literally duplicate an assignment.
 *
 * Uses config(['session.driver' => 'array']) to avoid this app's unrelated,
 * pre-existing test-suite gap (its migrations don't create store_sessions
 * from a clean schema even though SESSION_DRIVER=database), and calls the
 * controller directly (no HTTP kernel) for the same reason.
 */
class StoreRolePermissionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['session.driver' => 'array']);
    }

    private function role(string $name): StoreRole
    {
        return StoreRole::create(['name' => $name, 'guard_name' => 'store_user']);
    }

    public function test_store_roles_and_permissions_have_a_unique_name_per_guard(): void
    {
        $this->role('Cashier');

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->role('Cashier');
    }

    public function test_a_staff_member_can_be_created_with_multiple_roles(): void
    {
        $admin = StoreUser::create(['name' => 'Admin', 'email' => 'admin@t.local', 'password' => bcrypt('x'), 'store_id' => 1, 'is_active' => true]);
        $superAdminRole = $this->role('Super Admin');
        $admin->roles()->attach($superAdminRole->id, ['model_type' => StoreUser::class]);

        Auth::guard('store')->setUser($admin);
        Auth::shouldUse('store');

        $cashier = $this->role('Cashier');
        $manager = $this->role('Store Manager');

        $request = Request::create('/staff', 'POST', [
            'name' => 'Multi Role Person', 'email' => 'multirole@t.local',
            'phone' => '5551234', 'password' => 'password123', 'password_confirmation' => 'password123',
            'role_ids' => [$cashier->id, $manager->id], 'is_active' => 1,
        ]);
        $request->setUserResolver(fn () => $admin);

        (new StaffController())->store($request);

        $staff = StoreUser::where('email', 'multirole@t.local')->firstOrFail();
        $this->assertEqualsCanonicalizing([$cashier->id, $manager->id], $staff->roles->pluck('id')->all());
        $this->assertSame($cashier->id, $staff->store_role_id, 'legacy column keeps the first selected role');
    }

    public function test_editing_a_staff_member_can_remove_an_individual_role_while_keeping_another(): void
    {
        $admin = StoreUser::create(['name' => 'Admin', 'email' => 'admin2@t.local', 'password' => bcrypt('x'), 'store_id' => 1, 'is_active' => true]);
        $superAdminRole = $this->role('Super Admin');
        $admin->roles()->attach($superAdminRole->id, ['model_type' => StoreUser::class]);

        Auth::guard('store')->setUser($admin);
        Auth::shouldUse('store');

        $cashier = $this->role('Cashier');
        $manager = $this->role('Store Manager');

        $staff = StoreUser::create(['name' => 'Two Roles', 'email' => 'tworoles@t.local', 'password' => bcrypt('x'), 'store_id' => 1, 'is_active' => true, 'store_role_id' => $cashier->id]);
        $staff->roles()->sync([
            $cashier->id => ['model_type' => StoreUser::class],
            $manager->id => ['model_type' => StoreUser::class],
        ]);
        $this->assertCount(2, $staff->roles);

        $request = Request::create("/staff/{$staff->id}", 'PUT', [
            'name' => $staff->name, 'email' => $staff->email, 'role_ids' => [$manager->id], 'is_active' => 1,
        ]);
        $request->setUserResolver(fn () => $admin);

        (new StaffController())->update($request, $staff->id);

        $staff->refresh();
        $this->assertSame([$manager->id], $staff->roles->pluck('id')->all());
    }

    public function test_repeatedly_assigning_the_same_role_never_creates_a_duplicate_pivot_row(): void
    {
        $staff = StoreUser::create(['name' => 'X', 'email' => 'x@t.local', 'password' => bcrypt('x'), 'store_id' => 1, 'is_active' => true]);
        $role = $this->role('Cashier');

        for ($i = 0; $i < 3; $i++) {
            $staff->roles()->sync([$role->id => ['model_type' => StoreUser::class]]);
        }

        $this->assertSame(1, DB::table('store_model_has_roles')->where('model_id', $staff->id)->count());

        $this->expectException(\Illuminate\Database\QueryException::class);
        DB::table('store_model_has_roles')->insert(['model_id' => $staff->id, 'model_type' => StoreUser::class, 'role_id' => $role->id]);
    }

    public function test_a_non_super_admin_cannot_assign_the_super_admin_role_via_multi_select(): void
    {
        $viewer = StoreUser::create(['name' => 'Viewer', 'email' => 'viewer@t.local', 'password' => bcrypt('x'), 'store_id' => 1, 'is_active' => true]);
        $cashier = $this->role('Cashier');
        $viewer->roles()->attach($cashier->id, ['model_type' => StoreUser::class]);

        Auth::guard('store')->setUser($viewer);
        Auth::shouldUse('store');

        $superAdminRole = $this->role('Super Admin');

        $request = Request::create('/staff', 'POST', [
            'name' => 'Sneaky', 'email' => 'sneaky@t.local',
            'phone' => '555', 'password' => 'password123', 'password_confirmation' => 'password123',
            'role_ids' => [$cashier->id, $superAdminRole->id],
        ]);
        $request->setUserResolver(fn () => $viewer);

        $response = (new StaffController())->store($request);

        $this->assertSame('Only a Super Admin can assign the Super Admin role.', $response->getSession()->get('error'));
        $this->assertDatabaseMissing('store_users', ['email' => 'sneaky@t.local']);
    }
}
