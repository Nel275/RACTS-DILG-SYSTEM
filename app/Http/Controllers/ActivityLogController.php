<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\View\View;

/**
 * Follow-up round: the full, unabridged version of the Dashboard's
 * "Recent Activity" preview. Same underlying data (AuditLog, now
 * written by every admin action - see ReportGridService::saveRow()
 * for report entries and UserController/BarangayController/
 * ReportGridController's slot methods for everything else added this
 * round), just not capped to 5 rows and paginated instead of a flat
 * list, since this page is meant to be scrolled/searched through
 * rather than glanced at.
 */
class ActivityLogController extends Controller
{
    public function index(): View
    {
        // log_date is a plain dateTime column (whole-second precision),
        // so two actions landing in the same second would otherwise tie
        // on ->latest('log_date') alone with no guaranteed order between
        // them. id (auto-increment) always matches insertion order, so
        // it's the tiebreaker - same fix as DashboardController below.
        $logs = AuditLog::with('user')
            ->orderByDesc('log_date')
            ->orderByDesc('id')
            ->paginate(25);

        return view('activity-logs.index', compact('logs'));
    }
}
