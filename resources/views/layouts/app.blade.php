<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RACTS') | Report Automation and Compliance Tracking System</title>

    <!-- Bootstrap 5 CSS (via CDN, no npm build step required) -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <!-- RACTS design system: sidebar shell, header, DILG-referenced palette.
         See public/css/racts.css - loaded after Bootstrap so it can
         re-theme Bootstrap's own CSS variables. -->
    <link href="{{ asset('css/racts.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>

@auth
    @php
        // Single source of truth for the sidebar AND the header breadcrumb,
        // so adding/renaming a page only ever means editing this one list.
        // `pattern` is matched with request()->routeIs() - using the
        // group's wildcard (e.g. "barangays.*") means create/edit/show
        // sub-pages all correctly highlight their parent menu item.
        $navItems = [
            ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'dashboard', 'pattern' => 'dashboard'],
            ['label' => 'Barangays', 'icon' => 'bi-signpost-2-fill', 'route' => 'barangays.index', 'pattern' => 'barangays.*'],
            ['label' => 'User Logs', 'icon' => 'bi-person-lines-fill', 'route' => 'user-logs.index', 'pattern' => 'user-logs.*'],
            // Pattern matches both routes so the sidebar stays highlighted
            // on the new Log Details tab too - see report-grid/_reports-tabs.blade.php.
            ['label' => 'Barangay Reports', 'icon' => 'bi-grid-3x3-gap-fill', 'route' => 'barangay-reports.index', 'pattern' => ['barangay-reports.*', 'barangay-log-details.*']],
            // Follow-up round: Report Filing now has an SK side too
            // (sk-log-details.*), same pattern as Barangay Reports above.
            ['label' => 'SK Reports', 'icon' => 'bi-grid-3x3-gap', 'route' => 'sk-reports.index', 'pattern' => ['sk-reports.*', 'sk-log-details.*']],
            ['label' => 'Records', 'icon' => 'bi-journal-text', 'route' => 'records.index', 'pattern' => 'records.*'],
            ['label' => 'Analytics & Ranking', 'icon' => 'bi-graph-up', 'route' => 'analytics.index', 'pattern' => 'analytics.*'],
            ['label' => 'Notifications', 'icon' => 'bi-bell-fill', 'route' => 'notifications.index', 'pattern' => 'notifications.*', 'badge' => true],
        ];
        if (auth()->user()->isAdmin()) {
            $navItems[] = ['label' => 'Users', 'icon' => 'bi-people-fill', 'route' => 'users.index', 'pattern' => 'users.*'];
        }
        $activeItem = collect($navItems)->first(fn ($item) => request()->routeIs(...(array) $item['pattern']));
    @endphp

    <div class="d-flex racts-shell">

        <!-- Sidebar: a static column at lg+ screens, a slide-out drawer
             (Bootstrap's offcanvas-lg) below that - no custom JS needed. -->
        <div class="offcanvas-lg offcanvas-start racts-sidebar" tabindex="-1" id="racts-sidebar" aria-labelledby="racts-sidebar-label">
            <div class="racts-brand">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none flex-grow-1">
                    <img src="{{ asset('images/dilg-logo.png') }}" alt="DILG Santiago City Office seal">
                    <span class="racts-brand-text">
                        <span class="racts-brand-title" id="racts-sidebar-label">RACTS</span>
                        <span class="racts-brand-subtitle">
                            Report Automation and<br>Compliance Tracking System<br>DILG Santiago City
                        </span>
                    </span>
                </a>
                <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#racts-sidebar" aria-label="Close menu"></button>
            </div>

            <nav class="racts-sidebar-nav nav flex-column">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs(...(array) $item['pattern']) ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        @if (!empty($item['badge']) && $unreadNotificationsCount > 0)
                            <span class="badge rounded-pill bg-danger nav-badge">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="racts-sidebar-footer">
                &copy; {{ date('Y') }} DILG Santiago City Office
            </div>
        </div>

        <!-- Main column -->
        <div class="flex-grow-1 d-flex flex-column racts-main">

            <div class="racts-topbar">
                <button class="btn racts-sidebar-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#racts-sidebar" aria-controls="racts-sidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div class="racts-topbar-title flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="racts-breadcrumb d-flex list-unstyled gap-1 mb-1">
                            <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-house-door-fill"></i></a></li>
                            @if ($activeItem)
                                <li>/</li>
                                <li class="active">{{ $activeItem['label'] }}</li>
                            @endif
                        </ol>
                    </nav>
                    <h1>@yield('title', 'RACTS')</h1>
                </div>

                <a href="{{ route('notifications.index') }}" class="racts-bell" title="Notifications">
                    <i class="bi bi-bell-fill"></i>
                    @if ($unreadNotificationsCount > 0)
                        <span class="badge rounded-pill bg-danger">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                    @endif
                </a>

                <div class="dropdown">
                    <button class="racts-user-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="racts-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="d-none d-sm-block text-start">
                            <span class="racts-user-name d-block">{{ auth()->user()->name }}</span>
                            <span class="racts-user-role d-block">{{ auth()->user()->roleLabel() }}</span>
                        </span>
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ auth()->user()->name }} &mdash; {{ auth()->user()->roleLabel() }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <main class="racts-content flex-grow-1">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="text-center app-footer">
                &copy; {{ date('Y') }} Report Automation and Compliance Tracking System (RACTS) &mdash; DILG Santiago City Office
            </footer>
        </div>
    </div>
@else
    <!-- Guest (login) view: no sidebar to show yet, but the same branding
         appears up top so RACTS/DILG identity is consistent from the
         first screen a user sees. -->
    <nav class="navbar navbar-dark" style="background-color: var(--racts-sidebar-bg);">
        <div class="container-fluid justify-content-center py-2">
            <span class="navbar-brand d-flex align-items-center gap-2 mb-0">
                <img src="{{ asset('images/dilg-logo.png') }}" alt="DILG Santiago City Office seal" style="height: 40px; width: auto;">
                <span class="text-start">
                    <span class="d-block fw-bold">RACTS</span>
                    <span class="d-block small text-white-50">DILG Santiago City Office</span>
                </span>
            </span>
        </div>
    </nav>

    <main class="container my-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center app-footer">
        &copy; {{ date('Y') }} Report Automation and Compliance Tracking System (RACTS) &mdash; DILG Santiago City Office
    </footer>
@endauth

<!-- Bootstrap 5 JS Bundle (includes Popper, via CDN) -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

@yield('scripts')
</body>
</html>
