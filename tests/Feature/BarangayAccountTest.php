<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\ReportRequirement;
use App\Models\User;
use App\Rules\NoInappropriateWords;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Panelist revision: barangay accounts, portal login, barangay-only
 * dashboard/reports, role limits, profile, and the input filters.
 */
class BarangayAccountTest extends TestCase
{
    use RefreshDatabase;

    private function barangay(string $name = 'Centro East'): Barangay
    {
        return Barangay::create(['barangay_name' => $name, 'status' => Barangay::STATUS_ACTIVE]);
    }

    private function user(string $role, ?Barangay $barangay = null, array $extra = []): User
    {
        return User::create(array_merge([
            'name' => ucfirst($role).' User',
            'username' => $role.'_'.substr(uniqid(), -6),
            'password' => 'password123',
            'role' => $role,
            'barangay_id' => $barangay?->id,
        ], $extra));
    }

    private function entry(Barangay $barangay, string $deadline = '+10 days'): BarangayLogDetail
    {
        $log = BarangayLog::create([
            'item_description' => 'Monthly Report '.uniqid(),
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'deadline_date' => now()->modify($deadline)->toDateString(),
            'status' => BarangayLog::STATUS_ACTIVE,
        ]);

        return BarangayLogDetail::create([
            'barangay_log_id' => $log->id,
            'barangay_id' => $barangay->id,
            'item_description' => $log->item_description,
            'deadline_date' => $log->deadline_date,
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
    }

    // --- Login & classification -------------------------------------------

    public function test_barangay_user_logs_in_through_barangay_tab_and_sees_own_dashboard(): void
    {
        $barangay = $this->barangay();
        $user = $this->user(User::ROLE_BARANGAY, $barangay, ['username' => 'brgy_centro']);

        $this->post('/login', ['username' => 'brgy_centro', 'password' => 'password123', 'portal' => 'barangay'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Centro East')
            ->assertSee('Reports to Submit')
            ->assertDontSee('Active Report Slots');
    }

    public function test_wrong_tab_gets_a_clear_message(): void
    {
        $this->user(User::ROLE_BARANGAY, $this->barangay(), ['username' => 'brgy_one']);
        $this->user(User::ROLE_STAFF, null, ['username' => 'dilg_one']);

        $this->from('/login')->post('/login', ['username' => 'brgy_one', 'password' => 'password123', 'portal' => 'dilg'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();

        $this->from('/login')->post('/login', ['username' => 'dilg_one', 'password' => 'password123', 'portal' => 'barangay'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_deactivated_account_cannot_log_in(): void
    {
        $this->user(User::ROLE_STAFF, null, ['username' => 'off_user', 'is_active' => false]);

        $this->post('/login', ['username' => 'off_user', 'password' => 'password123', 'portal' => 'dilg'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_rejects_invalid_username_characters(): void
    {
        $this->post('/login', ['username' => "admin'--<script>", 'password' => 'x', 'portal' => 'dilg'])
            ->assertSessionHasErrors('username');
    }

    // --- Limits ------------------------------------------------------------

    public function test_barangay_user_is_blocked_from_dilg_pages(): void
    {
        $user = $this->user(User::ROLE_BARANGAY, $this->barangay());

        foreach (['/barangays', '/barangays/create', '/users', '/analytics', '/records', '/activity-logs', '/user-logs', '/barangay-reports', '/sk-reports'] as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }
    }

    public function test_dilg_users_cannot_open_the_barangay_portal(): void
    {
        $this->actingAs($this->user(User::ROLE_STAFF))->get('/my-reports')->assertForbidden();
        $this->actingAs($this->user(User::ROLE_ADMIN))->get('/my-reports')->assertForbidden();
    }

    // --- Barangay portal ---------------------------------------------------

    public function test_barangay_only_sees_its_own_reports(): void
    {
        $mine = $this->barangay('Mine');
        $other = $this->barangay('Other');
        $own = $this->entry($mine);
        $foreign = $this->entry($other);

        $this->actingAs($this->user(User::ROLE_BARANGAY, $mine))
            ->get('/my-reports')
            ->assertOk()
            ->assertSee($own->item_description)
            ->assertDontSee($foreign->item_description);
    }

    public function test_barangay_can_submit_its_own_report_and_not_someone_elses(): void
    {
        Storage::fake('local');
        $mine = $this->barangay('Mine');
        $other = $this->barangay('Other');
        $own = $this->entry($mine);
        $foreign = $this->entry($other);
        $user = $this->user(User::ROLE_BARANGAY, $mine);

        $this->actingAs($user)->patch("/my-reports/{$own->id}/submit", [
            'report_file' => UploadedFile::fake()->create('r.pdf', 50, 'application/pdf'),
        ])->assertSessionHas('success');

        $fresh = $own->fresh();
        $this->assertSame(BarangayLogDetail::STATUS_SUBMITTED, $fresh->status);
        $this->assertTrue($fresh->hasReportFile());
        $this->assertSame($user->name, $fresh->processed_by);

        $this->actingAs($user)->patch("/my-reports/{$foreign->id}/submit", [
            'report_file' => UploadedFile::fake()->create('r.pdf', 50, 'application/pdf'),
        ])->assertNotFound();
        $this->assertSame(BarangayLogDetail::STATUS_PENDING, $foreign->fresh()->status);

        $this->actingAs($user)->get("/my-reports/{$foreign->id}/file")->assertNotFound();
    }

    public function test_submission_requires_a_valid_file(): void
    {
        Storage::fake('local');
        $mine = $this->barangay();
        $entry = $this->entry($mine);
        $user = $this->user(User::ROLE_BARANGAY, $mine);

        $this->actingAs($user)->patch("/my-reports/{$entry->id}/submit", [])->assertSessionHasErrors('report_file');
        $this->actingAs($user)->patch("/my-reports/{$entry->id}/submit", [
            'report_file' => UploadedFile::fake()->create('virus.exe', 10),
        ])->assertSessionHasErrors('report_file');
    }

    // --- Admin account management -----------------------------------------

    public function test_admin_creates_a_barangay_account_linked_to_a_barangay(): void
    {
        $barangay = $this->barangay();

        $this->actingAs($this->user(User::ROLE_ADMIN))->post('/users', [
            'name' => 'Maria Santos',
            'username' => 'maria.santos',
            'role' => User::ROLE_BARANGAY,
            'barangay_id' => $barangay->id,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['username' => 'maria.santos', 'barangay_id' => $barangay->id, 'role' => 'barangay']);
    }

    public function test_barangay_account_requires_a_barangay_and_dilg_accounts_ignore_it(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $barangay = $this->barangay();

        $this->actingAs($admin)->post('/users', [
            'name' => 'No Barangay', 'username' => 'nobrgy', 'role' => User::ROLE_BARANGAY,
            'password' => 'secret1234', 'password_confirmation' => 'secret1234',
        ])->assertSessionHasErrors('barangay_id');

        $this->actingAs($admin)->post('/users', [
            'name' => 'Staff Person', 'username' => 'staffer', 'role' => User::ROLE_STAFF, 'barangay_id' => $barangay->id,
            'password' => 'secret1234', 'password_confirmation' => 'secret1234',
        ]);
        $this->assertDatabaseHas('users', ['username' => 'staffer', 'barangay_id' => null]);
    }

    public function test_cannot_demote_deactivate_or_delete_the_last_admin(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $second = $this->user(User::ROLE_ADMIN);

        // Self-protection.
        $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name, 'username' => $admin->username, 'role' => User::ROLE_STAFF, 'is_active' => 1,
        ])->assertSessionHas('error');
        $this->assertSame(User::ROLE_ADMIN, $admin->fresh()->role);

        // Another admin can demote the second admin while two exist...
        $this->actingAs($admin)->put("/users/{$second->id}", [
            'name' => $second->name, 'username' => $second->username, 'role' => User::ROLE_STAFF, 'is_active' => 1,
        ])->assertRedirect(route('users.index'));

        // ...but now $admin is the last active one, so deleting/demoting it is refused.
        $this->assertSame(1, User::activeAdmins()->count());
        $other = $this->user(User::ROLE_STAFF);
        $this->actingAs($admin)->delete("/users/{$admin->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    // --- Filters ------------------------------------------------------------

    public function test_inappropriate_words_are_blocked_but_normal_names_pass(): void
    {
        foreach (['fuck', 'f.u.c.k', 'sh!t', 'PUTANGINA', 'tarantado', 'b1tch', 'gago', 'fuuuck'] as $bad) {
            $this->assertTrue(NoInappropriateWords::contains($bad), "'{$bad}' should be blocked");
        }
        foreach (['Juan Dela Cruz', 'Santos', 'Class Assessment', 'Bassett', 'Pedro Gagalac', 'Tae-yang Kim', 'Maria Cristina'] as $ok) {
            $this->assertFalse(NoInappropriateWords::contains($ok), "'{$ok}' should pass");
        }
    }

    public function test_user_form_rejects_bad_words_and_bad_characters(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $base = ['role' => User::ROLE_STAFF, 'password' => 'secret1234', 'password_confirmation' => 'secret1234'];

        $this->actingAs($admin)->post('/users', $base + ['name' => 'Juan Gago', 'username' => 'juan_ok'])
            ->assertSessionHasErrors('name');
        $this->actingAs($admin)->post('/users', $base + ['name' => 'Juan 123', 'username' => 'juan_ok'])
            ->assertSessionHasErrors('name');
        $this->actingAs($admin)->post('/users', $base + ['name' => 'Juan Cruz', 'username' => 'bad name!'])
            ->assertSessionHasErrors('username');
        $this->actingAs($admin)->post('/users', $base + ['name' => 'Juan Cruz', 'username' => 'shit_user'])
            ->assertSessionHasErrors('username');
        $this->actingAs($admin)->post('/users', ['name' => 'Juan Cruz', 'username' => 'juan_ok', 'role' => 'staff', 'password' => 'abcdefgh', 'password_confirmation' => 'abcdefgh'])
            ->assertSessionHasErrors('password');
    }

    public function test_barangay_name_and_report_names_are_filtered(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);

        $this->actingAs($admin)->post('/barangays', ['barangay_name' => 'Putangina Hills', 'status' => 'active'])
            ->assertSessionHasErrors('barangay_name');
        $this->actingAs($admin)->post('/barangays', ['barangay_name' => 'Valid Hills', 'status' => 'active'])
            ->assertSessionHasNoErrors();

        $this->actingAs($admin)->post('/report-slots', [
            'report_name' => 'Report <script>', 'council_type' => 'barangay', 'frequency' => 'monthly',
        ])->assertSessionHasErrors('report_name');
    }

    // --- Profile -----------------------------------------------------------

    public function test_user_can_update_profile_and_change_password(): void
    {
        Storage::fake('local');
        $user = $this->user(User::ROLE_BARANGAY, $this->barangay(), ['username' => 'prof_user']);

        $this->actingAs($user)->put('/profile', [
            'name' => 'Ana Reyes',
            'position' => 'Barangay Secretary',
            'contact_number' => '09171234567',
            'avatar' => UploadedFile::fake()->createWithContent('me.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')),
        ])->assertRedirect(route('profile.show'));

        $fresh = $user->fresh();
        $this->assertSame('Ana Reyes', $fresh->name);
        $this->assertSame('Barangay Secretary', $fresh->position);
        $this->assertTrue($fresh->hasAvatar());
        $this->assertSame(User::ROLE_BARANGAY, $fresh->role); // role/username cannot be self-edited

        $this->actingAs($fresh)->get('/profile')->assertOk()->assertSee('Ana Reyes')->assertSee('You cannot');

        $this->actingAs($fresh)->put('/profile', ['name' => 'Ana Gago'])->assertSessionHasErrors('name');
        $this->actingAs($fresh)->put('/profile', ['name' => 'Ana Reyes', 'contact_number' => 'abc'])->assertSessionHasErrors('contact_number');

        $this->actingAs($fresh)->put('/profile/password', [
            'current_password' => 'wrong', 'password' => 'newpass123', 'password_confirmation' => 'newpass123',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($fresh)->put('/profile/password', [
            'current_password' => 'password123', 'password' => 'newpass123', 'password_confirmation' => 'newpass123',
        ])->assertSessionHas('success');
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpass123', $fresh->fresh()->password));
    }
}
