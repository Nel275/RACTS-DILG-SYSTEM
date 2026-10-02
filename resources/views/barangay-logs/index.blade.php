@extends('layouts.app')

@section('title', 'Barangay Logs')

@section('content')
    <p class="text-muted mb-3">
        The catalog of trackable report/document items barangays are logged against. This list feeds
        the "Report" dropdown on the <a href="{{ route('barangay-reports.index', ['tab' => 'log-details']) }}">Barangay Log Details</a> tab.
    </p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item / Report</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Last Processed</th>
                        @if (auth()->user()->isAdmin())
                            <th class="text-end">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            @if (auth()->user()->isAdmin())
                                <td style="min-width: 220px;">
                                    <input type="text" form="log-edit-{{ $item->id }}" name="item_description" value="{{ $item->item_description }}" class="form-control form-control-sm">
                                </td>
                                <td style="min-width: 160px;">
                                    <input type="date" form="log-edit-{{ $item->id }}" name="deadline_date" value="{{ $item->deadline_date->format('Y-m-d') }}" class="form-control form-control-sm">
                                </td>
                                <td style="min-width: 130px;">
                                    <select form="log-edit-{{ $item->id }}" name="status" class="form-select form-select-sm">
                                        @foreach (\App\Models\BarangayLog::STATUSES as $value => $label)
                                            <option value="{{ $value }}" @selected($item->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            @else
                                <td>{{ $item->item_description }}</td>
                                <td>{{ $item->deadline_date->format('M j, Y') }}</td>
                                <td>
                                    <span class="badge {{ $item->isActive() ? 'bg-success' : 'bg-secondary' }}">{{ $item->statusLabel() }}</span>
                                </td>
                            @endif
                            <td class="text-muted small">
                                {{ $item->date_process?->format('M j, Y') ?? '—' }}
                                @if ($item->processed_by)
                                    <br>by {{ $item->processed_by }}
                                @endif
                            </td>
                            @if (auth()->user()->isAdmin())
                                <td class="text-end">
                                    <form id="log-edit-{{ $item->id }}" method="POST" action="{{ route('barangay-logs.update', $item) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-primary btn-sm">Save</button>
                                    </form>
                                    <form method="POST" action="{{ route('barangay-logs.toggle', $item) }}" class="d-inline"
                                          onsubmit="return confirm('{{ $item->isActive() ? 'Deactivate' : 'Reactivate' }} &quot;{{ $item->item_description }}&quot;?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                                            {{ $item->isActive() ? 'Deactivate' : 'Reactivate' }}
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="p-0">
                                <div class="racts-empty-state">
                                    <i class="bi bi-journal-check"></i>
                                    No barangay log items yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (auth()->user()->isAdmin())
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <h2 class="h6 mb-3">Add New Log Item</h2>
                <form method="POST" action="{{ route('barangay-logs.store') }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Item / Report Description</label>
                        <input type="text" name="item_description" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Deadline</label>
                        <input type="date" name="deadline_date" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
