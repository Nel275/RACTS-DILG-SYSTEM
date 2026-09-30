@extends('layouts.app')

@section('title', 'User Logs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Barangay personnel/officials roster - not login accounts.</p>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('user-logs.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Add Personnel
            </a>
        @endif
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total Personnel</div>
                    <div class="h3 mb-0">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Active</div>
                    <div class="h3 mb-0 text-success">{{ $stats['active'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Inactive</div>
                    <div class="h3 mb-0 text-secondary">{{ $stats['inactive'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('user-logs.index') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search name or designation">
                </div>
                <div class="col-md-3">
                    <select name="barangay_id" class="form-select">
                        <option value="">All Barangays</option>
                        @foreach ($barangayOptions as $option)
                            <option value="{{ $option->id }}" @selected($barangayId === $option->id)>{{ $option->barangay_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach (\App\Models\UserLog::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Search</button>
                    <a href="{{ route('user-logs.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Barangay</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->fullName() }}</td>
                            <td>{{ $log->designation }}</td>
                            <td>{{ $log->barangay?->barangay_name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $log->isActive() ? 'bg-success' : 'bg-secondary' }}">{{ $log->statusLabel() }}</span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user-logs.show', $log) }}">
                                                <i class="bi bi-eye me-1"></i> View
                                            </a>
                                        </li>
                                        @if (auth()->user()->isAdmin())
                                            <li>
                                                <a class="dropdown-item" href="{{ route('user-logs.edit', $log) }}">
                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('user-logs.destroy', $log) }}" method="POST"
                                                      onsubmit="return confirm('Delete this personnel record? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash3 me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <div class="racts-empty-state">
                                    <i class="bi bi-person-lines-fill"></i>
                                    No personnel records found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
@endsection
