<?php

namespace App\Services;

use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\Notification;
use Illuminate\Support\Carbon;

/**
 * Turns the DILG's recurring report requirements (monthly/annual
 * BarangayLog items) into My Reports entries: one per active barangay
 * per reporting period, from the requirement's effective date up to the
 * current period. Idempotent - existing entries are skipped and the
 * unique (requirement, barangay, period) index plus createOrFirst() stop
 * two concurrent runs from inserting the same entry twice.
 *
 * Existing entries are never rewritten: a renamed requirement or a new
 * deadline rule only shapes entries generated after the change.
 */
class ReportEntryGenerator
{
    /** How far back an effective date may reach when back-filling. */
    private const MAX_MONTHS_BACK = 24;

    /** @return int number of entries created */
    public function generate(?Barangay $only = null, ?Carbon $today = null): int
    {
        $today ??= now();
        $barangays = $only ? collect([$only]) : Barangay::where('status', Barangay::STATUS_ACTIVE)->get();
        $created = 0;

        $items = BarangayLog::where('status', BarangayLog::STATUS_ACTIVE)
            ->whereIn('frequency', [BarangayLog::FREQUENCY_MONTHLY, BarangayLog::FREQUENCY_ANNUAL])
            ->get();

        foreach ($items as $item) {
            $periods = $this->periodsFor($item, $today);

            foreach ($barangays as $barangay) {
                $existing = BarangayLogDetail::where('barangay_log_id', $item->id)
                    ->where('barangay_id', $barangay->id)
                    ->whereIn('period', $periods)
                    ->pluck('period')
                    ->all();

                foreach (array_diff($periods, $existing) as $period) {
                    $deadline = $item->deadlineFor($period);

                    $entry = BarangayLogDetail::createOrFirst(
                        ['barangay_log_id' => $item->id, 'barangay_id' => $barangay->id, 'period' => $period],
                        [
                            'item_description' => $item->item_description,
                            'deadline_date' => $deadline->toDateString(),
                            'status' => $deadline->lt($today->copy()->startOfDay())
                                ? BarangayLogDetail::STATUS_MISSED
                                : BarangayLogDetail::STATUS_PENDING,
                        ]
                    );

                    if ($entry->wasRecentlyCreated) {
                        $created++;
                        Notification::notifyBarangay($barangay->id, Notification::TYPE_REPORT_ASSIGNED, sprintf(
                            'New report required: %s (%s), due %s.',
                            $entry->item_description, $entry->periodLabel(), $deadline->format('M j, Y')
                        ), $entry, once: true);
                    }
                }
            }
        }

        return $created;
    }

    /**
     * Reporting periods due for $item as of $today: "Y-m" for monthly,
     * "Y" for annual, starting at its effective date (or creation date).
     *
     * @return list<string>
     */
    public function periodsFor(BarangayLog $item, Carbon $today): array
    {
        $start = ($item->effective_from ?? $item->created_at ?? $today)->copy()->startOfMonth();
        $start = $start->max($today->copy()->startOfMonth()->subMonths(self::MAX_MONTHS_BACK - 1));

        if ($start->gt($today)) {
            return [];
        }

        if ($item->frequency === BarangayLog::FREQUENCY_ANNUAL) {
            return array_map('strval', range($start->year, $today->year));
        }

        $periods = [];
        for ($month = $start; $month->lte($today); $month = $month->copy()->addMonthNoOverflow()) {
            $periods[] = $month->format('Y-m');
        }

        return $periods;
    }

    /**
     * Daily reminders: pending entries due within $days, and entries
     * that went overdue. Each is sent once per entry (see notifyBarangay).
     *
     * @return int notifications created
     */
    public function sendReminders(?Carbon $today = null, int $days = 3): int
    {
        $today ??= now();
        $sent = 0;

        BarangayLogDetail::where('status', BarangayLogDetail::STATUS_PENDING)
            ->whereDate('deadline_date', '<', $today->toDateString())
            ->update(['status' => BarangayLogDetail::STATUS_MISSED]);

        $dueSoon = BarangayLogDetail::where('status', BarangayLogDetail::STATUS_PENDING)
            ->whereDate('deadline_date', '<=', $today->copy()->addDays($days)->toDateString())
            ->get();
        foreach ($dueSoon as $entry) {
            $sent += Notification::notifyBarangay($entry->barangay_id, Notification::TYPE_REPORT_DUE_SOON, sprintf(
                'Reminder: %s%s is due on %s.',
                $entry->item_description, $entry->period ? ' ('.$entry->periodLabel().')' : '', $entry->deadline_date->format('M j, Y')
            ), $entry, once: true);
        }

        foreach (BarangayLogDetail::where('status', BarangayLogDetail::STATUS_MISSED)->get() as $entry) {
            $sent += Notification::notifyBarangay($entry->barangay_id, Notification::TYPE_REPORT_OVERDUE, sprintf(
                'Overdue: %s%s was due on %s. Please submit it as soon as possible.',
                $entry->item_description, $entry->period ? ' ('.$entry->periodLabel().')' : '', $entry->deadline_date->format('M j, Y')
            ), $entry, once: true);
        }

        return $sent;
    }
}
