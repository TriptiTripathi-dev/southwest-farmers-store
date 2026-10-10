<x-app-layout title="Cross-location Clock-in">
    <div class="content">
        <div class="container-fluid">
            <x-page-header>
                <a href="{{ route('store.time-records.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="mdi mdi-arrow-left me-1"></i> Time Records</a>
            </x-page-header>

            <div class="alert alert-light border small">
                By default staff clock in only at their own store. Give a person permission for specific other stores here (kitchen spec 9.3);
                the clock-in then records the store used and the permission.
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light"><tr><th class="ps-4 small text-muted">EMPLOYEE</th><th class="small text-muted">MAY ALSO CLOCK IN AT</th><th class="pe-4 small text-muted" style="width: 320px;"></th></tr></thead>
                        <tbody>
                            @forelse ($staff as $person)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $person->name }} <small class="text-muted">{{ $person->staff_code }}</small></td>
                                    <td>
                                        @forelse ($permissions->get($person->id, collect()) as $p)
                                            <span class="badge bg-info-subtle text-info border me-1 mb-1">{{ $p->store->store_name ?? '—' }}
                                                @if ($canEdit)
                                                    <form method="POST" action="{{ route('store.time-records.cross-location.revoke', $p->id) }}" class="d-inline" data-confirm="Remove this permission?">@csrf @method('DELETE')<button class="btn btn-link btn-sm p-0 ms-1 text-danger" title="Remove">&times;</button></form>
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-muted small">Own store only</span>
                                        @endforelse
                                    </td>
                                    <td class="pe-4">
                                        @if ($canEdit && $stores->isNotEmpty())
                                            <form method="POST" action="{{ route('store.time-records.cross-location.grant') }}" class="d-flex gap-2">
                                                @csrf
                                                <input type="hidden" name="store_user_id" value="{{ $person->id }}">
                                                <select name="store_id" class="form-select form-select-sm" required>
                                                    <option value="">Add store…</option>
                                                    @foreach ($stores as $s)<option value="{{ $s->id }}">{{ $s->store_name }}</option>@endforeach
                                                </select>
                                                <button class="btn btn-sm btn-dark">Allow</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No active staff.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
