<x-app-layout title="Staff Time Clock">
    @php $t = fn ($d) => $d ? $d->copy()->timezone($tz)->format('h:i A') : '—'; @endphp
    <div class="content">
        <div class="container-fluid">

            <x-page-header />

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0 fw-bold">Clock In / Out</h6></div>
                        <div class="card-body p-4">
                            <form action="{{ route('store.time-clock.clock-in') }}" method="POST" class="mb-3">
                                @csrf
                                <label class="form-label fw-semibold">Store ID</label>
                                <input type="text" name="staff_code" class="form-control mb-2" placeholder="e.g. EMP-0001" value="{{ old('staff_code') }}" required autofocus>
                                <input type="text" name="notes" class="form-control form-control-sm mb-2" placeholder="Note (optional)">
                                <button type="submit" class="btn btn-success w-100"><i class="mdi mdi-login me-1"></i> Clock In</button>
                            </form>
                            <form action="{{ route('store.time-clock.clock-out-code') }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="staff_code" class="form-control" placeholder="Store ID" required>
                                    <input type="number" name="break_minutes" class="form-control" placeholder="Break min" min="0" style="max-width: 110px;">
                                    <button class="btn btn-outline-danger"><i class="mdi mdi-logout"></i> Clock Out</button>
                                </div>
                            </form>
                            <small class="text-muted d-block mt-3">Clock-in is allowed during a scheduled shift at this store. Working elsewhere needs cross-location permission; an unscheduled shift needs a General Manager's approval.</small>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0 fw-bold">Today's shifts here</h6></div>
                        <ul class="list-group list-group-flush">
                            @forelse ($todayShifts as $s)
                                <li class="list-group-item d-flex justify-content-between small">
                                    <span>{{ $s->employee->name ?? '—' }}</span><span class="text-muted">{{ $s->label() }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-3 small">No shifts scheduled today.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white border-bottom py-3"><h6 class="mb-0 fw-bold">Clocked in now</h6></div>
                        <div class="card-body p-0">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    @forelse ($activeLogs as $log)
                                        <tr>
                                            <td class="ps-3">{{ $log->staff->name ?? 'Unknown' }} <small class="text-muted">({{ $log->staff->staff_code ?? '' }})</small></td>
                                            <td class="small">in {{ $t($log->clock_in_at) }}
                                                @if ($log->late_minutes)<span class="badge bg-warning text-dark ms-1">Late {{ $log->late_minutes }}m</span>@endif
                                                @if ($log->clock_basis !== 'scheduled')<span class="badge bg-info-subtle text-info border ms-1">{{ $log->basisLabel() }}</span>@endif
                                            </td>
                                            <td class="pe-3 text-end">
                                                <form action="{{ route('store.time-clock.clock-out', $log->id) }}" method="POST" class="d-inline-flex gap-1">
                                                    @csrf
                                                    <input type="number" name="break_minutes" class="form-control form-control-sm" style="width: 90px;" placeholder="Break min" min="0">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-logout"></i> Clock Out</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-muted text-center py-4">No one is clocked in right now.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between">
                            <h6 class="mb-0 fw-bold">Recent time records</h6>
                            <a href="{{ route('store.time-records.index') }}" class="small">All time records &rarr;</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3 small text-muted fw-bold">STAFF</th>
                                            <th class="small text-muted fw-bold">IN</th>
                                            <th class="small text-muted fw-bold">OUT</th>
                                            <th class="small text-muted fw-bold">HOURS</th>
                                            <th class="pe-3 small text-muted fw-bold">FLAGS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentLogs as $log)
                                            <tr>
                                                <td class="ps-3">{{ $log->staff->name ?? 'Unknown' }}</td>
                                                <td class="small">{{ $log->clock_in_at?->copy()->timezone($tz)->format('m/d h:i A') }}</td>
                                                <td class="small">{{ $log->clock_out_at?->copy()->timezone($tz)->format('m/d h:i A') ?? '—' }}</td>
                                                <td>{{ $log->total_hours ?? '—' }}</td>
                                                <td class="pe-3 small">
                                                    @if ($log->late_minutes)<span class="badge bg-warning text-dark">Late {{ $log->late_minutes }}m</span>@endif
                                                    @if ($log->early_leave_minutes)<span class="badge bg-warning text-dark">Early {{ $log->early_leave_minutes }}m</span>@endif
                                                    @if ($log->clock_basis !== 'scheduled')<span class="badge bg-light text-dark border">{{ $log->basisLabel() }}</span>@endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center py-5 text-muted">No time records yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($recentLogs->hasPages())
                            <div class="card-footer bg-white border-top">{{ $recentLogs->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
