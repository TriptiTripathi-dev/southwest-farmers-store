<x-app-layout title="Schedules">
    <div class="content">
        <div class="container-fluid">
            <x-page-header>
                <a href="{{ route('store.schedules.index', ['week' => $weekStart->copy()->subWeek()->toDateString()]) }}" class="btn btn-outline-secondary rounded-pill px-3 me-1"><i class="mdi mdi-chevron-left"></i></a>
                <a href="{{ route('store.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-3 me-1">This week</a>
                <a href="{{ route('store.schedules.index', ['week' => $weekStart->copy()->addWeek()->toDateString()]) }}" class="btn btn-outline-secondary rounded-pill px-3 me-2"><i class="mdi mdi-chevron-right"></i></a>
                @if ($canManage)
                    <button class="btn btn-dark rounded-pill px-4 js-add" data-date="{{ now($tz)->toDateString() }}"><i class="mdi mdi-plus me-1"></i> Add shift</button>
                @endif
            </x-page-header>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white fw-bold">Week of {{ $weekStart->format('M j, Y') }}</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-top mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3" style="min-width: 160px;">STAFF</th>
                                    @foreach ($days as $d)
                                        <th class="text-center {{ $d->isToday() ? 'bg-primary-subtle' : '' }}" style="min-width: 120px;">{{ $d->format('D') }}<br><span class="fw-normal text-muted">{{ $d->format('m/d') }}</span></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($staff as $person)
                                    <tr>
                                        <td class="ps-3 fw-semibold">{{ $person->name }}<br><span class="text-muted fw-normal">{{ $person->staff_code }}</span></td>
                                        @foreach ($days as $d)
                                            <td class="text-center">
                                                @foreach ($grid->get($person->id . '|' . $d->toDateString(), collect()) as $shift)
                                                    <div class="mb-1">
                                                        <button type="button" class="btn btn-sm w-100 {{ $shift->status === 'cancelled' ? 'btn-outline-secondary text-decoration-line-through' : 'btn-outline-primary' }} {{ $canManage ? 'js-edit' : '' }}"
                                                            data-url="{{ route('store.schedules.update', $shift->id) }}" data-cancel="{{ route('store.schedules.cancel', $shift->id) }}"
                                                            data-user="{{ $shift->store_user_id }}" data-date="{{ $shift->shift_date->toDateString() }}"
                                                            data-start="{{ substr($shift->start_time, 0, 5) }}" data-end="{{ substr($shift->end_time, 0, 5) }}"
                                                            data-role="{{ $shift->role }}" data-notes="{{ $shift->notes }}" data-status="{{ $shift->status }}"
                                                            data-history="{{ $shift->changes->map(fn ($c) => $c->created_at->copy()->timezone($tz)->format('m/d h:i A') . ' · ' . $c->action . ' by ' . ($c->user_name ?? '—'))->implode("\n") }}">
                                                            {{ substr($shift->start_time, 0, 5) }}–{{ substr($shift->end_time, 0, 5) }}
                                                            @if ($shift->role)<br><span class="small">{{ $shift->role }}</span>@endif
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">No staff at this store.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <p class="text-muted small mt-2 mb-0">Times are store local time (Central). Staff can clock in only during their scheduled shift (see Time Records for the rules).</p>
        </div>
    </div>

    @if ($canManage)
    <div class="modal fade" id="shiftModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="shiftForm" action="{{ route('store.schedules.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="shiftMethod" value="POST">
                    <div class="modal-header"><h5 class="modal-title" id="shiftTitle">Add shift</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Employee</label>
                            <select name="store_user_id" id="fUser" class="form-select" required>
                                @foreach ($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><label class="form-label small fw-semibold">Date</label><input type="date" name="shift_date" id="fDate" class="form-control" required></div>
                            <div class="col-3"><label class="form-label small fw-semibold">Start</label><input type="time" name="start_time" id="fStart" class="form-control" required></div>
                            <div class="col-3"><label class="form-label small fw-semibold">End</label><input type="time" name="end_time" id="fEnd" class="form-control" required></div>
                        </div>
                        <div class="mb-2"><label class="form-label small fw-semibold">Role / station</label><input type="text" name="role" id="fRole" class="form-control" placeholder="e.g. Cashier, Bakery, Soup station" maxlength="100"></div>
                        <div class="mb-2"><label class="form-label small fw-semibold">Notes</label><input type="text" name="notes" id="fNotes" class="form-control" maxlength="500"></div>
                        <div class="mb-2" id="repeatBox">
                            <label class="form-label small fw-semibold">Also on (same week)</label>
                            <div class="d-flex flex-wrap gap-2 small">
                                @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $i => $dn)
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="repeat_days[]" value="{{ $i }}" id="rd{{ $i }}"><label class="form-check-label" for="rd{{ $i }}">{{ $dn }}</label></div>
                                @endforeach
                            </div>
                        </div>
                        <pre class="small text-muted bg-light p-2 rounded d-none mb-0" id="fHistory" style="white-space: pre-wrap;"></pre>
                        <small class="text-muted">An end time at or before the start means the shift ends the next day.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger me-auto d-none" id="fCancelShift">Cancel shift</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark">Save</button>
                    </div>
                </form>
                <form method="POST" id="cancelShiftForm" class="d-none">@csrf</form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const modal = () => bootstrap.Modal.getOrCreateInstance(document.getElementById('shiftModal'));
            const form = document.getElementById('shiftForm');
            const set = (id, v) => document.getElementById(id).value = v ?? '';
            document.querySelectorAll('.js-add').forEach(b => b.addEventListener('click', () => {
                form.reset(); form.action = "{{ route('store.schedules.store') }}"; set('shiftMethod', 'POST');
                document.getElementById('shiftTitle').textContent = 'Add shift';
                set('fDate', b.dataset.date); set('fStart', '09:00'); set('fEnd', '17:00');
                document.getElementById('repeatBox').classList.remove('d-none');
                document.getElementById('fHistory').classList.add('d-none');
                document.getElementById('fCancelShift').classList.add('d-none');
                modal().show();
            }));
            document.querySelectorAll('.js-edit').forEach(b => b.addEventListener('click', () => {
                form.reset(); form.action = b.dataset.url; set('shiftMethod', 'PUT');
                document.getElementById('shiftTitle').textContent = 'Edit shift';
                set('fUser', b.dataset.user); set('fDate', b.dataset.date); set('fStart', b.dataset.start); set('fEnd', b.dataset.end);
                set('fRole', b.dataset.role); set('fNotes', b.dataset.notes);
                document.getElementById('repeatBox').classList.add('d-none');
                const h = document.getElementById('fHistory');
                h.textContent = 'History:\n' + (b.dataset.history || '—'); h.classList.remove('d-none');
                const c = document.getElementById('fCancelShift');
                c.classList.toggle('d-none', b.dataset.status === 'cancelled');
                c.onclick = () => { const f = document.getElementById('cancelShiftForm'); f.action = b.dataset.cancel; f.submit(); };
                modal().show();
            }));
        })();
    </script>
    @endpush
    @endif
</x-app-layout>
