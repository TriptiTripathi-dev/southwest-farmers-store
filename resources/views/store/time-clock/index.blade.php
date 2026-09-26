<x-app-layout title="Staff Time Clock">
    <div class="content">
        <div class="container-fluid">

            <div class="py-4">
                <h4 class="h4 fw-bold m-0 text-dark"><i class="mdi mdi-clock-outline me-1"></i> Staff Time Clock</h4>
                <p class="text-muted small mb-0 mt-1">Clock staff in and out using their store ID (see Staff Management for each employee's ID).</p>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Clock In</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('store.time-clock.clock-in') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Store ID</label>
                                    <input type="text" name="staff_code" class="form-control" placeholder="e.g. EMP-0001" required autofocus>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Notes (optional)</label>
                                    <input type="text" name="notes" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="mdi mdi-login me-1"></i> Clock In
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Currently Clocked In</h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($staff->filter(fn($s) => isset($activeLogs[$s->id])) as $s)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $s->name }} <small class="text-muted">({{ $s->staff_code }})</small></span>
                                        <span class="badge bg-success">Clocked In</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted text-center py-4">No one is clocked in right now.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Recent Time Logs</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3 py-3 text-muted small fw-bold">STAFF</th>
                                            <th class="py-3 text-muted small fw-bold">CLOCK IN</th>
                                            <th class="py-3 text-muted small fw-bold">CLOCK OUT</th>
                                            <th class="py-3 text-muted small fw-bold">HOURS</th>
                                            <th class="pe-3 py-3 text-end text-muted small fw-bold">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentLogs as $log)
                                            <tr>
                                                <td class="ps-3">{{ $log->staff->name ?? 'Unknown' }}</td>
                                                <td>{{ $log->clock_in_at?->storeTime()->format('m/d/y h:i A') }}</td>
                                                <td>{{ $log->clock_out_at ? $log->clock_out_at->storeTime()->format('m/d/y h:i A') : '—' }}</td>
                                                <td>{{ $log->total_hours ?? '—' }}</td>
                                                <td class="pe-3 text-end">
                                                    @if(!$log->clock_out_at)
                                                        <form action="{{ route('store.time-clock.clock-out', $log->id) }}" method="POST" class="d-inline-flex gap-1">
                                                            @csrf
                                                            <input type="number" name="break_minutes" class="form-control form-control-sm" style="width: 80px;" placeholder="Break (min)" min="0">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="mdi mdi-logout"></i> Clock Out
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-light text-dark border">Complete</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">No time logs yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($recentLogs->hasPages())
                            <div class="card-footer bg-white border-top">
                                {{ $recentLogs->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
