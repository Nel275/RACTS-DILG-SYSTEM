<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\Notification;
use App\Models\ReportRequirement;
use App\Models\User;
use App\Services\ReportEntryGenerator;
use FPDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Automatic monthly/annual My Reports entries from DILG-managed
 * requirements, DILG review (return/accept), and role limits.
 */
class ReportAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-23 09:00:00');
        Storage::fake('local');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function barangay(string $name = 'Centro East', string $status = Barangay::STATUS_ACTIVE): Barangay
    {
        return Barangay::create(['barangay_name' => $name, 'status' => $status]);
    }

    private function user(string $role, ?Barangay $barangay = null): User
    {
        return User::create([
            'name' => ucfirst($role).' User',
            'username' => $role.'_'.substr(uniqid(), -6),
            'password' => 'password123',
            'role' => $role,
            'barangay_id' => $barangay?->id,
        ]);
    }

    private function requirement(array $attributes = []): BarangayLog
    {
        return BarangayLog::create(array_merge([
            'item_description' => 'Monthly Accomplishment Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => BarangayLog::FREQUENCY_MONTHLY,
            'deadline_day' => 5,
            'effective_from' => '2026-10-01',
            'is_mandatory' => true,
            'status' => BarangayLog::STATUS_ACTIVE,
        ], $attributes));
    }

    private function generate(): int
    {
        return app(ReportEntryGenerator::class)->generate();
    }

    public function test_monthly_and_annual_entries_are_generated_for_both_councils(): void
    {
        $barangay = $this->barangay();
        $this->barangay('Inactive One', Barangay::STATUS_INACTIVE);

        $brgyMonthly = $this->requirement();
        $brgyAnnual = $this->requirement(['item_description' => 'Annual Accomplishment Report', 'frequency' => 'annual', 'deadline_month' => 1, 'deadline_day' => 15]);
        $skMonthly = $this->requirement(['item_description' => 'SK Monthly Report', 'council_type' => ReportRequirement::COUNCIL_SK]);
        $skAnnual = $this->requirement(['item_description' => 'SK Annual Report', 'council_type' => ReportRequirement::COUNCIL_SK, 'frequency' => 'annual', 'deadline_month' => 2, 'deadline_day' => 10]);

        $this->assertSame(4, $this->generate());

        $expect = [
            [$brgyMonthly, '2026-10', '2026-11-05'],
            [$brgyAnnual, '2026', '2027-01-15'],
            [$skMonthly, '2026-10', '2026-11-05'],
            [$skAnnual, '2026', '2027-02-10'],
        ];
        foreach ($expect as [$item, $period, $deadline]) {
            $entry = BarangayLogDetail::where('barangay_log_id', $item->id)->sole();
            $this->assertSame($barangay->id, $entry->barangay_id);
            $this->assertSame($period, $entry->period);
            $this->assertSame($deadline, $entry->deadline_date->toDateString());
            $this->assertSame(BarangayLogDetail::STATUS_PENDING, $entry->status);
        }
    }

    public function test_generated_reports_appear_on_my_reports_without_management_controls(): void
    {
        $barangay = $this->barangay();
        $this->requirement();
        $this->requirement(['item_description' => 'SK Monthly Report', 'council_type' => ReportRequirement::COUNCIL_SK]);
        $user = $this->user(User::ROLE_BARANGAY, $barangay);

        // Opening My Reports generates the entries itself (no scheduler needed).
        $this->actingAs($user)->get('/my-reports')->assertOk()
            ->assertSee('Monthly Accomplishment Report')->assertSee('October 2026')
            ->assertDontSee('SK Monthly Report')
            ->assertDontSee('Add Requirement')->assertDontSee('Manage Report Requirements');

        $this->actingAs($user)->get('/my-reports?council=sk')->assertOk()->assertSee('SK Monthly Report');
        $this->actingAs($user)->get('/my-reports?council=sk&frequency=annual')->assertDontSee('SK Monthly Report');
    }

    public function test_only_admin_can_create_and_edit_requirements(): void
    {
        $this->barangay();
        $this->barangay('Centro West');
        $admin = $this->user(User::ROLE_ADMIN);

        $this->actingAs($admin)->post('/barangay-logs', [
            'item_description' => 'Peace and Order Report',
            'council_type' => 'both',
            'frequency' => 'monthly',
            'deadline_day' => 10,
            'effective_from' => '2026-10-01',
            'is_mandatory' => 1,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertSame(2, BarangayLog::where('item_description', 'Peace and Order Report')->count());
        $this->assertSame(4, BarangayLogDetail::where('period', '2026-10')->count());

        $item = BarangayLog::where('council_type', 'barangay')->where('item_description', 'Peace and Order Report')->sole();
        $this->actingAs($admin)->put("/barangay-logs/{$item->id}", [
            'item_description' => 'Peace and Order Report',
            'frequency' => 'monthly',
            'deadline_day' => 12,
            'effective_from' => '2026-10-01',
            'is_mandatory' => 1,
            'status' => 'active',
        ])->assertSessionHasNoErrors();
        $this->assertSame(12, $item->fresh()->deadline_day);
    }

    public function test_barangay_and_staff_cannot_manage_requirements_or_review(): void
    {
        $barangay = $this->barangay();
        $item = $this->requirement();
        $this->generate();
        $entry = BarangayLogDetail::sole();

        foreach ([$this->user(User::ROLE_BARANGAY, $barangay), $this->user(User::ROLE_STAFF)] as $user) {
            $this->actingAs($user)->post('/barangay-logs', ['item_description' => 'Hack', 'frequency' => 'monthly', 'deadline_day' => 1, 'council_type' => 'barangay'])->assertForbidden();
            $this->actingAs($user)->put("/barangay-logs/{$item->id}", ['item_description' => 'Renamed', 'frequency' => 'monthly', 'deadline_day' => 1, 'status' => 'active'])->assertForbidden();
            $this->actingAs($user)->patch("/barangay-logs/{$item->id}/toggle")->assertForbidden();
            $this->actingAs($user)->patch("/barangay-log-details/{$entry->id}/return", ['review_remarks' => 'x'])->assertForbidden();
            $this->actingAs($user)->patch("/barangay-log-details/{$entry->id}/accept")->assertForbidden();
        }

        $this->assertSame('Monthly Accomplishment Report', $item->fresh()->item_description);
        $this->assertSame(1, BarangayLog::count());
    }

    public function test_repeated_runs_create_no_duplicate_entries_or_notifications(): void
    {
        $barangay = $this->barangay();
        $this->user(User::ROLE_BARANGAY, $barangay);
        $this->requirement(['effective_from' => '2026-08-01']);

        $this->artisan('racts:sync-report-entries')->assertSuccessful();
        $entries = BarangayLogDetail::count();
        $notifications = Notification::count();
        $this->artisan('racts:sync-report-entries')->assertSuccessful();
        $this->generate();

        $this->assertSame(3, $entries); // Aug, Sep, Oct 2026
        $this->assertSame($entries, BarangayLogDetail::count());
        $this->assertSame($notifications, Notification::count());

        // Back-filled periods already past their deadline are overdue.
        $this->assertSame(BarangayLogDetail::STATUS_MISSED, BarangayLogDetail::where('period', '2026-08')->value('status'));
        $this->assertSame(1, Notification::where('type', Notification::TYPE_REPORT_OVERDUE)->where('barangay_log_detail_id', BarangayLogDetail::where('period', '2026-08')->value('id'))->count());
    }

    public function test_effective_date_and_requirement_changes_keep_history(): void
    {
        $barangay = $this->barangay();
        $future = $this->requirement(['item_description' => 'Future Report', 'effective_from' => '2026-12-01']);
        $item = $this->requirement();
        $this->generate();

        $this->assertSame(0, BarangayLogDetail::where('barangay_log_id', $future->id)->count());

        $october = BarangayLogDetail::where('barangay_log_id', $item->id)->sole();
        $october->markSubmitted(now(), 'Barangay User');

        // DILG renames the requirement and moves its deadline.
        $item->update(['item_description' => 'Revised Monthly Report', 'deadline_day' => 20]);
        Carbon::setTestNow('2026-11-02 09:00:00');
        $this->generate();

        $october->refresh();
        $this->assertSame('Monthly Accomplishment Report', $october->item_description);
        $this->assertSame('2026-11-05', $october->deadline_date->toDateString());
        $this->assertSame(BarangayLogDetail::STATUS_SUBMITTED, $october->status);

        $november = BarangayLogDetail::where('barangay_log_id', $item->id)->where('period', '2026-11')->sole();
        $this->assertSame('Revised Monthly Report', $november->item_description);
        $this->assertSame('2026-12-20', $november->deadline_date->toDateString());
    }

    public function test_compliance_counts_only_mandatory_reports(): void
    {
        $barangay = $this->barangay();
        $user = $this->user(User::ROLE_BARANGAY, $barangay);
        $this->requirement();
        $this->requirement(['item_description' => 'Optional Newsletter', 'is_mandatory' => false]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->assertSame(2, BarangayLogDetail::count());

        $counts = $this->actingAs($user)->get('/dashboard')->viewData('counts');
        $this->assertSame(1, $counts['pending']);
        $this->assertSame(1, $counts['total']);
    }

    public function test_barangay_submits_admin_returns_and_barangay_resubmits(): void
    {
        $barangay = $this->barangay();
        $user = $this->user(User::ROLE_BARANGAY, $barangay);
        $admin = $this->user(User::ROLE_ADMIN);
        $this->requirement();
        $this->generate();
        $entry = BarangayLogDetail::sole();

        $pdf = function () {
            $p = new FPDF;
            $p->AddPage();

            return UploadedFile::fake()->createWithContent('report.pdf', $p->Output('S'));
        };

        $this->actingAs($user)->patch("/my-reports/{$entry->id}/submit", ['report_file' => $pdf()])->assertSessionHasNoErrors();
        $this->assertSame(BarangayLogDetail::STATUS_SUBMITTED, $entry->fresh()->status);
        $this->assertTrue(Notification::where('type', Notification::TYPE_REPORT_RECEIVED)->where('user_id', $user->id)->exists());

        // Can't submit twice while it's with DILG.
        $this->actingAs($user)->patch("/my-reports/{$entry->id}/submit", ['report_file' => $pdf()])->assertSessionHas('error');

        $this->actingAs($admin)->patch("/barangay-log-details/{$entry->id}/return", ['review_remarks' => 'Please add the signatures page.'])
            ->assertSessionHasNoErrors();
        $this->assertSame(BarangayLogDetail::STATUS_RETURNED, $entry->fresh()->status);
        $this->assertTrue(Notification::where('type', Notification::TYPE_REPORT_RETURNED)->where('user_id', $user->id)->exists());
        $this->actingAs($user)->get('/my-reports')->assertSee('Please add the signatures page.')->assertSee('Resubmit');

        $this->actingAs($user)->patch("/my-reports/{$entry->id}/submit", ['report_file' => $pdf()])->assertSessionHasNoErrors();
        $entry->refresh();
        $this->assertSame(BarangayLogDetail::STATUS_SUBMITTED, $entry->status);
        $this->assertSame(2, $entry->submission_count);
        $this->actingAs($user)->get('/my-reports?status=resubmitted')->assertSee('Monthly Accomplishment Report');

        $this->actingAs($admin)->patch("/barangay-log-details/{$entry->id}/accept")->assertSessionHasNoErrors();
        $this->assertSame(BarangayLogDetail::STATUS_ACCEPTED, $entry->fresh()->status);
    }

    public function test_barangay_cannot_submit_another_barangays_report(): void
    {
        $this->requirement();
        $this->barangay('Other');
        $mine = $this->barangay();
        $this->generate();
        $foreign = BarangayLogDetail::where('barangay_id', '!=', $mine->id)->firstOrFail();

        $this->actingAs($this->user(User::ROLE_BARANGAY, $mine))
            ->patch("/my-reports/{$foreign->id}/submit", ['report_file' => UploadedFile::fake()->create('r.pdf', 10, 'application/pdf')])
            ->assertNotFound();
    }
}
