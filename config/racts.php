<?php

/**
 * RACTS-specific configuration (Follow-up Round 5). Currently just the
 * DILG submission-window rule, pulled out of CheckLateSubmissions and
 * ReportDeadlineService so it can be changed without touching code, per
 * the client's own late-submission rule (Section 4): a month's reports
 * are due the 1st-5th of the following month.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Submission window
    |--------------------------------------------------------------------------
    |
    | A report's window opens on the 1st of the month after its period,
    | and the deadline is end-of-day on submission_deadline_day of that
    | same following month. Submitted after that day: late. Not
    | submitted at all once that day has passed: overdue. Both values
    | are day-of-month integers (1-28) - keep submission_deadline_day
    | comfortably below 28 so it's valid in every month, including
    | February.
    |
    */

    'submission_window_start_day' => 1,

    'submission_deadline_day' => 5,

];
