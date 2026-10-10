<x-app-layout title="Time Records">
    @php
        $dt = fn ($d) => $d ? $d->copy()->timezone($tz)->format('m/d h:i A') : '—';
        $local = fn ($d) => $d ? $d->copy()->timezone($tz)->format('Y-m-d\TH:i') : '';
    @endphp
    <div class="content">
        <div class="container-fluid">
            <x-page-header>
                <a href="{{ route('store.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-3 me-1"><i class="mdi mdi-calendar-month me-1"></i> Schedules</a>
                <a href="{{ route('store.time-records.cross-location') }}" class="btn btn-outline-secondary rounded-pill px-3 me-1"><i class="mdi mdi-map-marker-multiple me-1"></i> Cross-location</a>
                @if ($can['approve'])
                    <a href="{{ route('store.time-records.reports', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="btn btn-dark rounded-pill px-3"><i class="mdi mdi-file-chart me-1"></i> Reports</a>
                @endif
            </x-page-header>

            <div class="row g-3 mb-3">
                @if ($can['gm'])
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white fw-bold">General Manager: unscheduled employee</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('store.time-records.override') }}" class="row g-2 mb-3">
                                @csrf
                                <div class="col-5"><select name="store_user_id" class="form-select form-select-sm" required>
                                    <option value="">Employee…</option>@foreach ($staff as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                                </select></div>
                                <div class="col-5"><input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason" required maxlength="500"></div>
                                <div class="col-2"><button class="btn btn-sm btn-outline-primary w-100" title="Let them clock in today">Allow</button></div>
                            </form>
                            <form method="POST" action="{{ route('store.time-records.gm-enter') }}" class="row g-2">
                                @csrf
                                <div class="col-4"><select name="store_user_id" class="form-select form-select-sm" required>
                                    <option value="">Employee…</option>@foreach ($staff as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                                </select></div>
                                <div class="col-3"><input type="datetime-local" name="clock_in_at" class="form-control form-control-sm" title="Clock-in time (blank = now)"></div>
                                <div class="col-3"><input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason" required maxlength="500"></div>
                                <div class="col-2"><button class="btn btn-sm btn-outline-dark w-100" title="Record the clock-in yourself">Enter</button></div>
                            </form>
                            <small class="text-muted d-block mt-2"><strong>Allow</strong>: they can clock in at the terminal today. <strong>Enter</strong>: you record the clock-in for them (blank time = now).</small>
                            @if ($todayOverrides->isNotEmpty())
                                <ul class="small mt-2 mb-0">
                                    @foreach ($todayOverrides as $o)
                                        <li>{{ $o->employee->name ?? '—' }} allowed by {{ $o->approved_by_name }} ({{ $o->reason }}){{ $o->used_time_log_id ? ' · used' : ' · not used yet' }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white fw-bold">Clock rules for this store</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('store.time-records.settings') }}" class="row g-2 align-items-end">
                                @csrf @method('PUT')
                                <fieldset class="row g-2 align-items-end m-0 p-0" @disabled(!$can['settings'])>
                                    <div class="col-4"><label class="form-label small">Clock in up to (min before shift)</label><input type="number" name="clock_early_minutes" min="0" max="240" class="form-control form-control-sm" value="{{ $options['early_minutes'] }}"></div>
                                    <div class="col-3"><label class="form-label small">Late after (min)</label><input type="number" name="late_grace_minutes" min="0" max="120" class="form-control form-control-sm" value="{{ $options['late_grace'] }}"></div>
                                    <div class="col-3"><label class="form-label small">Early leave (min)</label><input type="number" name="early_leave_grace_minutes" min="0" max="120" class="form-control form-control-sm" value="{{ $options['early_leave_grace'] }}"></div>
                                    <div class="col-2">@if ($can['settings'])<button class="btn btn-sm btn-dark w-100">Save</button>@endif</div>
                                </fieldset>
                            </form>
                            <small class="text-muted d-block mt-2">Defaults until the client confirms: clock in from 15 min before the shift; late after 5 min; early departure if leaving 5+ min before the shift ends. Location = the store terminal used.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body py-2">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-2"><label class="form-label small">From</label><input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control form-control-sm"></div>
                        <div class="col-md-2"><label class="form-label small">To</label><input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control form-control-sm"></div>
                        <div class="col-md-3"><label class="form-label small">Employee</label><select name="employee" class="form-select form-select-sm"><option value="">All</option>@foreach ($staff as $s)<option value="{{ $s->id }}" @selected(request('employee') == $s->id)>{{ $s->name }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label small">Show</label><select name="flag" class="form-select form-select-sm">
                            <option value="">All records</option>
                            @foreach (['late' => 'Late arrivals', 'early' => 'Early departures', 'unapproved' => 'Not approved', 'corrected' => 'Corrected', 'exception' => 'Cross-location / GM'] as $k => $l)
                                <option value="{{ $k }}" @selected(request('flag') === $k)>{{ $l }}</option>
                            @endforeach
                        </select></div>
                        <div class="col-md-2"><button class="btn btn-sm btn-dark w-100">Filter</button></div>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('store.time-records.approve') }}" id="approveForm">
                @csrf
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Time records</span>
                        @if ($can['approve'])<button class="btn btn-sm btn-success"><i class="mdi mdi-check-all me-1"></i> Approve selected</button>@endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">@if ($can['approve'])<input type="checkbox" class="form-check-input" id="checkAll">@endif</th>
                                        <th>EMPLOYEE</th><th>SCHEDULED</th><th>IN</th><th>OUT</th><th class="text-end">HOURS</th><th>FLAGS</th><th>APPROVED</th><th class="pe-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        <tr>
                                            <td class="ps-3">@if ($can['approve'] && $log->clock_out_at && !$log->approved_at)<input type="checkbox" class="form-check-input rec" name="ids[]" value="{{ $log->id }}">@endif</td>
                                            <td class="fw-semibold">{{ $log->staff->name ?? '—' }}
                                                @if ($log->assigned_store_id && $log->assigned_store_id != $log->store_id)<br><span class="text-muted fw-normal">home: {{ $log->assignedStore->store_name ?? '' }}</span>@endif
                                            </td>
                                            <td>{{ $log->scheduled_start_at ? $log->scheduled_start_at->copy()->timezone($tz)->format('h:i A') . '–' . $log->scheduled_end_at?->copy()->timezone($tz)->format('h:i A') : '—' }}</td>
                                            <td>{{ $dt($log->clock_in_at) }}</td>
                                            <td>{{ $dt($log->clock_out_at) }}</td>
                                            <td class="text-end fw-bold">{{ $log->total_hours ?? '—' }}</td>
                                            <td>
                                                @if ($log->late_minutes)<span class="badge bg-warning text-dark">Late {{ $log->late_minutes }}m</span>@endif
                                                @if ($log->early_leave_minutes)<span class="badge bg-warning text-dark">Early {{ $log->early_leave_minutes }}m</span>@endif
                                                @if ($log->clock_basis !== 'scheduled')<span class="badge bg-info-subtle text-info border" title="{{ $log->override_reason }}">{{ $log->basisLabel() }}{{ $log->override_by_name ? ' · ' . $log->override_by_name : '' }}</span>@endif
                                                @if ($log->corrected)<span class="badge bg-secondary">Corrected</span>@endif
                                            </td>
                                            <td>{{ $log->approved_at ? $log->approved_by_name . ' · ' . $log->approved_at->copy()->timezone($tz)->format('m/d') : '—' }}</td>
                                            <td class="pe-3 text-end">
                                                @if ($can['approve'])
                                                    <button type="button" class="btn btn-sm btn-outline-secondary js-correct"
                                                        data-url="{{ route('store.time-records.correct', $log->id) }}" data-name="{{ $log->staff->name ?? '' }}"
                                                        data-in="{{ $local($log->clock_in_at) }}" data-out="{{ $local($log->clock_out_at) }}" data-break="{{ $log->break_minutes }}"
                                                        data-history="{{ $log->corrections->map(function ($c) use ($tz) { $v = fn ($x) => $x === null ? '—' : ($c->field === 'break_minutes' ? $x . ' min' : \Carbon\Carbon::parse($x, 'UTC')->timezone($tz)->format('m/d h:i A')); return $c->created_at->copy()->timezone($tz)->format('m/d h:i A') . ' · ' . str_replace('_', ' ', $c->field) . ': ' . $v($c->old_value) . ' → ' . $v($c->new_value) . ' (' . $c->reason . ', ' . $c->user_name . ')'; })->implode("\n") }}">Correct</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted py-5">No time records for these filters.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($logs->hasPages())<div class="card-footer bg-white">{{ $logs->links() }}</div>@endif
                </div>
            </form>
            <p class="text-muted small mt-2 mb-0">Times are store local time (Central). Corrections keep the original values (shown in the correction window).</p>
        </div>
    </div>

    @if ($can['approve'])
    <div class="modal fade" id="correctModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="correctForm">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title" id="cTitle">Correct time record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="row g-2 mb-2">
                            <div class="col-6"><label class="form-label small">Clock in</label><input type="datetime-local" name="clock_in_at" id="cIn" class="form-control" required></div>
                            <div class="col-6"><label class="form-label small">Clock out</label><input type="datetime-local" name="clock_out_at" id="cOut" class="form-control"></div>
                        </div>
                        <div class="mb-2"><label class="form-label small">Break (minutes)</label><input type="number" name="break_minutes" id="cBreak" min="0" max="600" class="form-control"></div>
                        <div class="mb-2"><label class="form-label small">Reason (required)</label><input type="text" name="reason" class="form-control" required maxlength="500" placeholder="e.g. forgot to clock out"></div>
                        <pre class="small text-muted bg-light p-2 rounded mb-0" id="cHistory" style="white-space: pre-wrap;"></pre>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button><button class="btn btn-dark">Save correction</button></div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        (function () {
            document.getElementById('checkAll')?.addEventListener('change', e => document.querySelectorAll('.rec').forEach(c => c.checked = e.target.checked));
            document.querySelectorAll('.js-correct').forEach(b => b.addEventListener('click', () => {
                const f = document.getElementById('correctForm'); f.reset(); f.action = b.dataset.url;
                document.getElementById('cTitle').textContent = 'Correct: ' + b.dataset.name;
                document.getElementById('cIn').value = b.dataset.in; document.getElementById('cOut').value = b.dataset.out; document.getElementById('cBreak').value = b.dataset.break;
                document.getElementById('cHistory').textContent = 'Corrections so far:\n' + (b.dataset.history || 'none');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('correctModal')).show();
            }));
        })();
    </script>
    @endpush
    @endif
</x-app-layout>
