@extends('layouts.app')

@section('title', 'Login')

@php
    $portal = old('portal', $portal ?? 'dilg');
    $isBarangay = $portal === 'barangay';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 text-center mb-1">RACTS Login</h1>
                    <p class="text-center text-muted mb-3" id="portal-subtitle">
                        {{ $isBarangay ? 'Barangay Portal' : 'DILG Santiago City Office' }}
                    </p>

                    {{-- Which kind of account is signing in. The account itself
                         decides what the user sees afterwards; the tab only
                         changes the wording and catches the wrong-tab mistake. --}}
                    <ul class="nav nav-pills nav-fill mb-4" id="portal-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link {{ $isBarangay ? '' : 'active' }}" data-portal="dilg">
                                <i class="bi bi-building"></i> DILG Personnel
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link {{ $isBarangay ? 'active' : '' }}" data-portal="barangay">
                                <i class="bi bi-signpost-2"></i> Barangay
                            </button>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf
                        <input type="hidden" name="portal" id="portal" value="{{ $portal }}">

                        <p class="small text-muted" id="portal-hint">
                            {{ $isBarangay
                                ? 'Sign in with the account the DILG office gave your barangay. You will be taken straight to your barangay dashboard.'
                                : 'For DILG Administrators and Monitoring Staff.' }}
                        </p>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                value="{{ old('username') }}"
                                class="form-control @error('username') is-invalid @enderror"
                                maxlength="30"
                                pattern="[A-Za-z0-9._\-]+"
                                data-input-filter="username"
                                autocomplete="username"
                                autofocus
                                required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                maxlength="64"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="current-password"
                                required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <span id="login-label">{{ $isBarangay ? 'Sign in to Barangay Portal' : 'Sign in' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            const text = {
                dilg: {
                    subtitle: 'DILG Santiago City Office',
                    hint: 'For DILG Administrators and Monitoring Staff.',
                    button: 'Sign in',
                },
                barangay: {
                    subtitle: 'Barangay Portal',
                    hint: 'Sign in with the account the DILG office gave your barangay. You will be taken straight to your barangay dashboard.',
                    button: 'Sign in to Barangay Portal',
                },
            };

            document.querySelectorAll('#portal-tabs [data-portal]').forEach(function (tab) {
                tab.addEventListener('click', function () {
                    const portal = tab.dataset.portal;
                    document.getElementById('portal').value = portal;
                    document.querySelectorAll('#portal-tabs .nav-link').forEach(function (t) {
                        t.classList.toggle('active', t === tab);
                    });
                    document.getElementById('portal-subtitle').textContent = text[portal].subtitle;
                    document.getElementById('portal-hint').textContent = text[portal].hint;
                    document.getElementById('login-label').textContent = text[portal].button;
                });
            });
        })();
    </script>
@endsection
