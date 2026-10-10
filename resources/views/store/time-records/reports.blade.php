<x-app-layout title="Time Reports">
    <div class="content">
        <div class="container-fluid">
            <x-page-header>
                <a href="{{ route('store.time-records.index', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="btn btn-outline-secondary rounded-pill px-3 me-2"><i class="mdi mdi-arrow-left me-1"></i> Time Records</a>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-success rounded-pill px-3"><i class="mdi mdi-file-excel-outline me-1"></i> Export (Excel / CSV)</a>
            </x-page-header>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body py-2">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3"><label class="form-label small">From</label><input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control form-control-sm"></div>
                        <div class="col-md-3"><label class="form-label small">To</label><input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control form-control-sm"></div>
                        <div class="col-md-2"><button class="btn btn-sm btn-dark w-100">Show</button></div>
                        <div class="col-md-4 text-md-end small text-muted">Location: <strong>{{ $store->store_name ?? '' }}</strong></div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Attendance and payroll hours · {{ $from->format('m/d/Y') }} – {{ $to->format('m/d/Y') }}</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">EMPLOYEE</th>
                                    <th class="text-end">SCHEDULED</th><th class="text-end">WORKED</th><th class="text-end">ABSENT</th>
                                    <th class="text-end">LATE</th><th class="text-end">EARLY LEAVE</th><th class="text-end">EXCEPTIONS</th><th class="text-end">CORRECTED</th>
                                    <th class="text-end">HOURS</th><th class="text-end pe-3">APPROVED HOURS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $r)
                                    <tr>
                                        <td class="ps-3 fw-semibold">{{ $r['name'] }}@if ($r['open'])<span class="badge bg-success ms-1">clocked in</span>@endif</td>
                                        <td class="text-end">{{ $r['scheduled'] }}</td>
                                        <td class="text-end">{{ $r['worked'] }}</td>
                                        <td class="text-end {{ $r['absent'] ? 'text-danger fw-bold' : '' }}">{{ $r['absent'] }}</td>
                                        <td class="text-end {{ $r['late'] ? 'text-warning fw-bold' : '' }}">{{ $r['late'] }}{{ $r['late'] ? ' (' . $r['late_minutes'] . 'm)' : '' }}</td>
                                        <td class="text-end {{ $r['early'] ? 'text-warning fw-bold' : '' }}">{{ $r['early'] }}</td>
                                        <td class="text-end">{{ $r['exceptions'] }}</td>
                                        <td class="text-end">{{ $r['corrected'] }}</td>
                                        <td class="text-end fw-bold">{{ number_format($r['hours'], 2) }}</td>
                                        <td class="text-end pe-3">{{ number_format($r['approved_hours'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="text-center text-muted py-5">No schedules or time records in this period.</td></tr>
                                @endforelse
                            </tbody>
                            @if ($rows->isNotEmpty())
                                <tfoot class="bg-light fw-bold"><tr>
                                    <td class="ps-3">Total</td><td class="text-end">{{ $rows->sum('scheduled') }}</td><td class="text-end">{{ $rows->sum('worked') }}</td><td class="text-end">{{ $rows->sum('absent') }}</td>
                                    <td class="text-end">{{ $rows->sum('late') }}</td><td class="text-end">{{ $rows->sum('early') }}</td><td class="text-end">{{ $rows->sum('exceptions') }}</td><td class="text-end">{{ $rows->sum('corrected') }}</td>
                                    <td class="text-end">{{ number_format($rows->sum('hours'), 2) }}</td><td class="text-end pe-3">{{ number_format($rows->sum('approved_hours'), 2) }}</td>
                                </tr></tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <p class="text-muted small mt-2 mb-0">Operational report only (no payroll processing). Absent = a scheduled shift that has ended with no clock-in. Exceptions = cross-location, GM-approved or GM-entered clock-ins.</p>
        </div>
    </div>
</x-app-layout>
