<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\BarangayLogController;
use App\Http\Controllers\BarangayLogDetailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\ReportGridController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| RACTS - Report Automation and Compliance Tracking System
| Original build: Phases 1-12. This redesign phase (client-requested,
| see RACTS_Client_Revision_Implementation_Prompt.md) replaces the old
| Submissions, Submission History, Report Categories, Compliance, Tally,
| and Report Generation pages with the Barangay Reports / SK Reports
| grid pages and their admin-managed report slots.
|
*/

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

// Guest-only routes (already logged in users are redirected away from these)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Routes that require being logged in
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard - real summary view, available to both roles.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clears the dashboard's recent-activity feed (audit_logs) only -
    // never touches an actual recorded report. Admin-only, matching
    // every other destructive action in RACTS.
    Route::middleware('role:admin')
        ->post('/dashboard/clear-activity', [DashboardController::class, 'clearActivity'])
        ->name('dashboard.clear-activity');

    // Activity Logs - the full, paginated version of the dashboard's
    // "Recent Activity" preview (follow-up round). Read-only for both
    // roles, same as Records and Analytics; linked from the dashboard's
    // "View All" button rather than the main nav, to match what was
    // actually asked for.
    Route::middleware('role:admin,staff')
        ->get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');

    // User account management - DILG Administrator only
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Barangay Management - both roles can view/search, only the
    // administrator can add, edit, or delete records.
    Route::prefix('barangays')->name('barangays.')->group(function () {
        Route::get('/', [BarangayController::class, 'index'])
            ->middleware('role:admin,staff')->name('index');

        Route::get('/create', [BarangayController::class, 'create'])
            ->middleware('role:admin')->name('create');
        Route::post('/', [BarangayController::class, 'store'])
            ->middleware('role:admin')->name('store');
        Route::get('/{barangay}/edit', [BarangayController::class, 'edit'])
            ->middleware('role:admin')->name('edit');
        Route::put('/{barangay}', [BarangayController::class, 'update'])
            ->middleware('role:admin')->name('update');
        Route::delete('/{barangay}', [BarangayController::class, 'destroy'])
            ->middleware('role:admin')->name('destroy');

        // Registered after /create so "create" isn't swallowed by {barangay}.
        Route::get('/{barangay}', [BarangayController::class, 'show'])
            ->middleware('role:admin,staff')->name('show');
    });

    // Barangay Reports and SK Reports - the grid pages (rows = active
    // barangays, columns = that council's active report slots) that
    // replace the old Submissions, Submission History, and Report
    // Categories pages. Both roles can view and record entries; only
    // the administrator can add, rename, or deactivate report slots
    // (see the report-slots.* routes below).
    Route::middleware('role:admin,staff')->group(function () {
        Route::prefix('barangay-reports')->name('barangay-reports.')->group(function () {
            Route::get('/', [ReportGridController::class, 'barangay'])->name('index');
            Route::post('/save', [ReportGridController::class, 'saveBarangay'])->name('save');
        });

        Route::prefix('sk-reports')->name('sk-reports.')->group(function () {
            Route::get('/', [ReportGridController::class, 'sk'])->name('index');
            Route::post('/save', [ReportGridController::class, 'saveSk'])->name('save');
        });
    });

    // Follow-up round: Barangay Logs (the "Report" catalog behind the
    // dropdown below). No standalone index page any more - catalog
    // management now lives inline on the Barangay Log Details tab (see
    // the "Manage Reports" panel there), so only the mutating actions
    // are routed here. Admin-only, same as report slot management.
    Route::prefix('barangay-logs')->name('barangay-logs.')->group(function () {
        Route::post('/', [BarangayLogController::class, 'store'])
            ->middleware('role:admin')->name('store');
        Route::put('/{barangayLog}', [BarangayLogController::class, 'update'])
            ->middleware('role:admin')->name('update');
        Route::patch('/{barangayLog}/toggle', [BarangayLogController::class, 'toggle'])
            ->middleware('role:admin')->name('toggle');

        // Follow-up round: clicking a report in the Report Filing list
        // now lands here instead of a single barangay's entry - this
        // page lists every barangay logged against this one report.
        Route::get('/{barangayLog}', [BarangayLogController::class, 'show'])
            ->middleware('role:admin,staff')->name('show');
    });

    Route::prefix('barangay-log-details')->name('barangay-log-details.')->group(function () {
        Route::get('/', [BarangayLogDetailController::class, 'index'])
            ->middleware('role:admin,staff')->name('index');
        Route::post('/', [BarangayLogDetailController::class, 'store'])
            ->middleware('role:admin')->name('store');
        Route::patch('/{barangayLogDetail}/submit', [BarangayLogDetailController::class, 'markSubmitted'])
            ->middleware('role:admin,staff')->name('submit');

        // Follow-up round: opens/downloads the attached file itself
        // (whatever report_file_path currently points to - staged or
        // already filed, either way), separate from Download below,
        // which moves the file into the barangay's folder rather than
        // showing it.
        Route::get('/{barangayLogDetail}/view-file', [BarangayLogDetailController::class, 'viewFile'])
            ->middleware('role:admin,staff')->name('view-file');

        // Follow-up round: replaces the old View/Delete row actions.
        // Download files the uploaded report into the barangay's own
        // folder (see Barangay::ensureFolder()); Close marks the entry
        // done/archived. Both roles can do either, same as submit above
        // - destroy() below is kept for now but no longer linked from
        // the list's action menu.
        Route::patch('/{barangayLogDetail}/download', [BarangayLogDetailController::class, 'download'])
            ->middleware('role:admin,staff')->name('download');
        Route::patch('/{barangayLogDetail}/close', [BarangayLogDetailController::class, 'close'])
            ->middleware('role:admin,staff')->name('close');

        Route::delete('/{barangayLogDetail}', [BarangayLogDetailController::class, 'destroy'])
            ->middleware('role:admin')->name('destroy');

        // Registered last so it doesn't swallow the routes above.
        Route::get('/{barangayLogDetail}', [BarangayLogDetailController::class, 'show'])
            ->middleware('role:admin,staff')->name('show');
    });

    // Follow-up round: Report Filing for the SK council - same feature
    // as barangay-log-details above, just scoped to SK via
    // BarangayLog::council_type (see that model's doc comment). Only
    // the list and "Add Log Entry" need their own routes here, since
    // they're the only two actions that have to be told which council
    // they're working with; every other action on an individual entry
    // (view/submit/download/close/destroy/show) stays under
    // barangay-log-details above - an entry's own id already carries
    // that information via its barangayLog relation, so a second set
    // of routes for those would have nothing to add.
    Route::prefix('sk-log-details')->name('sk-log-details.')->group(function () {
        Route::get('/', [BarangayLogDetailController::class, 'indexSk'])
            ->middleware('role:admin,staff')->name('index');
        Route::post('/', [BarangayLogDetailController::class, 'storeSk'])
            ->middleware('role:admin')->name('store');
    });

    // Follow-up round: User Logs - a barangay personnel/officials
    // roster, unrelated to real login accounts (see UserLog's doc
    // comment). Both roles can view; only the administrator manages
    // entries, matching User account management and Barangay
    // Management above.
    Route::prefix('user-logs')->name('user-logs.')->group(function () {
        Route::get('/', [UserLogController::class, 'index'])
            ->middleware('role:admin,staff')->name('index');

        Route::get('/create', [UserLogController::class, 'create'])
            ->middleware('role:admin')->name('create');
        Route::post('/', [UserLogController::class, 'store'])
            ->middleware('role:admin')->name('store');
        Route::get('/{userLog}/edit', [UserLogController::class, 'edit'])
            ->middleware('role:admin')->name('edit');
        Route::put('/{userLog}', [UserLogController::class, 'update'])
            ->middleware('role:admin')->name('update');
        Route::delete('/{userLog}', [UserLogController::class, 'destroy'])
            ->middleware('role:admin')->name('destroy');

        // Registered after /create so "create" isn't swallowed by {userLog}.
        Route::get('/{userLog}', [UserLogController::class, 'show'])
            ->middleware('role:admin,staff')->name('show');
    });

    // Records - read-only, filterable list of everything recorded on
    // the grids (added post-redesign, follow-up request). No editing
    // capability of its own; both roles can view, same as the grids
    // and Analytics.
    Route::middleware('role:admin,staff')
        ->get('/records', [RecordsController::class, 'index'])
        ->name('records.index');

    // Analytics & Ranking - replaces the old Tally page. Read-only for
    // both roles; built entirely on RankingCalculator (Phase 1), the
    // same engine the Barangay/SK Reports grids use for their %
    // column, so every page agrees on the numbers. Export (PDF/Excel)
    // lives here too, since it exports whatever this page is showing.
    Route::middleware('role:admin,staff')->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/export/pdf', [AnalyticsController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export/csv', [AnalyticsController::class, 'exportCsv'])->name('export-csv');
    });

    // Report slot management - DILG Administrator only. Slots are the
    // named monthly/yearly report requirements that become the grid's
    // columns; there's no separate admin page for these any more - this
    // is called from the "Manage Report Slots" panel on each grid page.
    Route::middleware('role:admin')->prefix('report-slots')->name('report-slots.')->group(function () {
        Route::post('/', [ReportGridController::class, 'storeSlot'])->name('store');
        Route::put('/{reportRequirement}', [ReportGridController::class, 'updateSlot'])->name('update');
        Route::patch('/{reportRequirement}/toggle', [ReportGridController::class, 'toggleSlot'])->name('toggle');
    });

    // Notification Management - every logged-in user sees only their own
    // notifications, regardless of role.
    Route::middleware('role:admin,staff')->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::post('/clear', [NotificationController::class, 'clearAll'])->name('clear');
    });
});
