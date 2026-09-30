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

    public function test_creating_a_user_log_never_creates_a_real_login_account(): void
    {
        // The admin account needed to be logged in has to be created
        // first - counting User rows before that (as this test
        // originally did) baked in an off-by-one, since admin() itself
        // is the thing that creates a User row here, not anything
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
