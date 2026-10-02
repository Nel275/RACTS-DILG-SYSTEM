<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Follow-up round: User Logs - a barangay personnel/officials roster,
 * separate from real login accounts (see UserLog's doc comment).
 */
class UserLogsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin User',
            'username' => 'admin_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function staff(): User
    {
        return User::create([
            'name' => 'Staff User',
            'username' => 'staff_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
    }

    public function test_admin_can_add_a_personnel_record(): void
    {
        $barangay = Barangay::create(['barangay_name' => 'Test Barangay', 'status' => Barangay::STATUS_ACTIVE]);

        $response = $this->actingAs($this->admin())->post('/user-logs', [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'birthdate' => '1990-05-12',
            'gender' => 'Male',
            'designation' => 'Barangay Captain',
            'roles' => 'Chairperson',
            'status' => UserLog::STATUS_ACTIVE,
            'barangay_id' => $barangay->id,
        ]);

        $response->assertRedirect(route('user-logs.index'));
        $this->assertDatabaseHas('user_logs', ['first_name' => 'Juan', 'last_name' => 'Dela Cruz']);
    }

    public function test_creating_a_user_log_without_the_account_checkbox_creates_no_login_account(): void
    {
        // Login accounts are opt-in (the "create_account" checkbox) -
        // a plain personnel record, the common case, should never touch
        // the users table. The admin account needed to be logged in has
        // to be created first - counting User rows before that (as this
        // test originally did) baked in an off-by-one, since admin()
        // itself is the thing that creates a User row here, not anything
        // UserLogController::store() does.
        $admin = $this->admin();
        $countBefore = User::count();

        $this->actingAs($admin)->post('/user-logs', [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'birthdate' => '1985-03-20',
            'gender' => 'Female',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
        ]);

        $this->assertSame($countBefore, User::count());
    }

    public function test_staff_cannot_add_a_personnel_record(): void
    {
        $this->actingAs($this->staff())->post('/user-logs', [
            'first_name' => 'Should',
            'last_name' => 'BeBlocked',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Test',
            'roles' => 'Test',
        ])->assertForbidden();

        $this->assertDatabaseMissing('user_logs', ['first_name' => 'Should']);
    }

    public function test_both_roles_can_view_the_user_logs_list(): void
    {
        $this->actingAs($this->admin())->get('/user-logs')->assertOk();
        $this->actingAs($this->staff())->get('/user-logs')->assertOk();
    }

    public function test_admin_can_add_a_personnel_record_with_a_login_account(): void
    {
        $response = $this->actingAs($this->admin())->post('/user-logs', [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'birthdate' => '1990-05-12',
            'gender' => 'Male',
            'designation' => 'Barangay Captain',
            'roles' => 'Chairperson',
            'status' => UserLog::STATUS_ACTIVE,
            'create_account' => '1',
            'username' => 'juan_delacruz',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'account_role' => User::ROLE_STAFF,
        ]);

        $response->assertRedirect(route('user-logs.index'));

        $account = User::where('username', 'juan_delacruz')->first();
        $this->assertNotNull($account);
        $this->assertSame(User::ROLE_STAFF, $account->role);

        $log = UserLog::where('first_name', 'Juan')->first();
        $this->assertSame($account->id, $log->user_id);
        $this->assertTrue($log->hasLoginAccount());
    }

    public function test_login_account_role_is_restricted_to_admin_or_staff(): void
    {
        $response = $this->actingAs($this->admin())->post('/user-logs', [
            'first_name' => 'Pedro',
            'last_name' => 'Reyes',
            'birthdate' => '1990-05-12',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'create_account' => '1',
            'username' => 'pedro_reyes',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'account_role' => 'barangay-personnel',
        ]);

        $response->assertSessionHasErrors('account_role');
        $this->assertDatabaseMissing('users', ['username' => 'pedro_reyes']);
        $this->assertDatabaseMissing('user_logs', ['first_name' => 'Pedro']);
    }

    public function test_duplicate_username_is_rejected(): void
    {
        $this->admin(); // seed a username namespace, not asserted on directly
        User::create([
            'name' => 'Existing Account',
            'username' => 'taken_name',
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);

        $response = $this->actingAs($this->admin())->post('/user-logs', [
            'first_name' => 'Ana',
            'last_name' => 'Cruz',
            'birthdate' => '1990-05-12',
            'gender' => 'Female',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'create_account' => '1',
            'username' => 'taken_name',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'account_role' => User::ROLE_STAFF,
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertDatabaseMissing('user_logs', ['first_name' => 'Ana']);
    }

    public function test_admin_can_add_a_login_account_later_via_update(): void
    {
        $admin = $this->admin();
        $log = UserLog::create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'status' => UserLog::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)->put("/user-logs/{$log->id}", [
            'first_name' => 'Old',
            'last_name' => 'Name',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'status' => UserLog::STATUS_ACTIVE,
            'create_account' => '1',
            'username' => 'old_name',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'account_role' => User::ROLE_ADMIN,
        ])->assertRedirect(route('user-logs.index'));

        $log->refresh();
        $this->assertTrue($log->hasLoginAccount());
        $this->assertSame('old_name', $log->account->username);
        $this->assertSame(User::ROLE_ADMIN, $log->account->role);
    }

    public function test_admin_can_update_an_existing_linked_accounts_password(): void
    {
        $admin = $this->admin();
        $account = User::create([
            'name' => 'Old Name',
            'username' => 'old_name',
            'password' => 'originalpass',
            'role' => User::ROLE_STAFF,
        ]);
        $log = UserLog::create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'status' => UserLog::STATUS_ACTIVE,
            'user_id' => $account->id,
        ]);

        // Blank password: keep the current one, username/role still editable.
        $this->actingAs($admin)->put("/user-logs/{$log->id}", [
            'first_name' => 'Old',
            'last_name' => 'Name',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'status' => UserLog::STATUS_ACTIVE,
            'username' => 'old_name',
            'password' => '',
            'password_confirmation' => '',
            'account_role' => User::ROLE_ADMIN,
        ])->assertRedirect(route('user-logs.index'));

        $account->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('originalpass', $account->password));
        $this->assertSame(User::ROLE_ADMIN, $account->role);

        // Now actually change the password.
        $this->actingAs($admin)->put("/user-logs/{$log->id}", [
            'first_name' => 'Old',
            'last_name' => 'Name',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'status' => UserLog::STATUS_ACTIVE,
            'username' => 'old_name',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'account_role' => User::ROLE_ADMIN,
        ])->assertRedirect(route('user-logs.index'));

        $account->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $account->password));
    }

    public function test_cannot_delete_a_personnel_record_that_has_a_login_account(): void
    {
        $admin = $this->admin();
        $account = User::create([
            'name' => 'Linked Name',
            'username' => 'linked_name',
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
        $log = UserLog::create([
            'first_name' => 'Linked',
            'last_name' => 'Name',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'status' => UserLog::STATUS_ACTIVE,
            'user_id' => $account->id,
        ]);

        $this->actingAs($admin)->delete("/user-logs/{$log->id}")->assertRedirect(route('user-logs.index'));

        $this->assertDatabaseHas('user_logs', ['id' => $log->id]);
    }

    public function test_admin_can_update_and_delete_a_personnel_record(): void
    {
        $admin = $this->admin();
        $log = UserLog::create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'status' => UserLog::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)->put("/user-logs/{$log->id}", [
            'first_name' => 'New',
            'last_name' => 'Name',
            'birthdate' => '1990-01-01',
            'gender' => 'Male',
            'designation' => 'Kagawad',
            'roles' => 'Council Member',
            'status' => UserLog::STATUS_ACTIVE,
        ])->assertRedirect(route('user-logs.index'));

        $this->assertSame('New', $log->fresh()->first_name);

        $this->actingAs($admin)->delete("/user-logs/{$log->id}")->assertRedirect(route('user-logs.index'));
        $this->assertDatabaseMissing('user_logs', ['id' => $log->id]);
    }
}
