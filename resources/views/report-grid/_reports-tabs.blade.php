{{--
    Shared tab bar for the Barangay Reports page and the Report Filing
    page. Two real pages (RACTS doesn't use AJAX, so every page is a
    full server-rendered response - see _grid.blade.php's own comment
    on this), not one page hiding/showing content client-side, but
    styled and laid out as a single set of tabs so they read as one
    "Barangay Reports" section with two views, per what was asked for.

    Follow-up round: relabeled "Log Details" -> "Report Filing" and
    added the one-line explainer below, so it's clear at a glance that
    these two tabs track two genuinely different things (not two views
    of the same data) rather than looking like a redundant duplicate of
    the grid - the actual source of confusion this round addresses.

    $active: 'grid' or 'log-details'.
--}}
<ul class="nav nav-tabs mb-2" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $active === 'grid' ? 'active' : '' }}" href="{{ route('barangay-reports.index') }}">
            <i class="bi bi-grid-3x3-gap-fill"></i> Compliance Grid
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $active === 'log-details' ? 'active' : '' }}" href="{{ route('barangay-log-details.index') }}">
            <i class="bi bi-journal-check"></i> Report Filing
        </a>
    </li>
</ul>

<p class="text-muted small mb-3">
    <i class="bi bi-info-circle"></i>
    @if ($active === 'grid')
        Recurring monthly/yearly compliance grading across every barangay at once - this is what feeds Analytics
        &amp; Ranking, the Dashboard, and the automated deadline notifications.
    @else
        Individual report tracking and filing, one barangay and one document at a time - lets you attach and file
        away the actual report file. Separate data from the Compliance Grid above; the two aren't meant to mirror
        each other.
    @endif
</p>
