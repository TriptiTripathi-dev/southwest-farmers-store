<x-app-layout title="Catering Rules">
    <div class="content">
        <div class="container-fluid">
            <x-page-header>
                <a href="{{ route('store.kitchen.catering.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="mdi mdi-party-popper me-1"></i> Catering Orders
                </a>
            </x-page-header>

            @unless ($canEdit)
                <div class="alert alert-light border small">You can view these rules. Changing them needs the "update settings" permission.</div>
            @endunless

            <div class="row g-3">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white fw-bold">Standard rules</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('store.kitchen.catering.rules.update') }}">
                                @csrf
                                @method('PUT')
                                <fieldset @disabled(!$canEdit)>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" name="catering_enabled" value="1" id="catEnabled" @checked($options['enabled'])>
                                        <label class="form-check-label fw-semibold" for="catEnabled">Accept catering orders on the website</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Standard notice (calendar days)</label>
                                        <input type="number" name="catering_notice_days" min="0" max="60" class="form-control" value="{{ old('catering_notice_days', $options['notice_days']) }}" required>
                                        <small class="text-muted">Orders with at least this many days' notice pay normal prices. Contract default: 7.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Daily cutoff time</label>
                                        <input type="time" name="catering_cutoff_time" class="form-control" value="{{ old('catering_cutoff_time', $options['cutoff_time']) }}" required>
                                        <small class="text-muted">Orders placed after this time (store local time) count from the next day.</small>
                                    </div>
                                    @if ($canEdit)<button class="btn btn-dark">Save</button>@endif
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Urgency tiers (less than {{ $options['notice_days'] }} days' notice)</span>
                            @if ($canEdit && $tiers->isEmpty())
                                <form method="POST" action="{{ route('store.kitchen.catering.tiers.examples') }}">@csrf
                                    <button class="btn btn-sm btn-outline-primary">Load example tiers</button>
                                </form>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr><th class="ps-4 small text-muted">NOTICE</th><th class="small text-muted">RULE</th><th class="pe-4"></th></tr>
                                </thead>
                                <tbody>
                                    @forelse ($tiers as $t)
                                        <tr>
                                            <td class="ps-4">{{ $t->min_days === $t->max_days ? $t->min_days : $t->min_days . '–' . $t->max_days }} day(s)</td>
                                            <td>
                                                @if ($t->action === 'block')
                                                    <span class="badge bg-danger">Not accepted</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">+{{ rtrim(rtrim(number_format($t->percent, 2), '0'), '.') }}% surcharge</span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-end">
                                                @if ($canEdit)
                                                    <form method="POST" action="{{ route('store.kitchen.catering.tiers.destroy', $t->id) }}" data-confirm="Remove this tier?">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger"><i class="mdi mdi-delete-outline"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-4">No tiers: every order with less than {{ $options['notice_days'] }} days' notice is <strong>not accepted</strong>.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($canEdit)
                            <div class="card-footer bg-white">
                                <form method="POST" action="{{ route('store.kitchen.catering.tiers.store') }}" class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-3"><label class="form-label small">From (days)</label><input type="number" name="min_days" min="0" class="form-control form-control-sm" value="{{ old('min_days') }}" required></div>
                                    <div class="col-3"><label class="form-label small">To (days)</label><input type="number" name="max_days" min="0" class="form-control form-control-sm" value="{{ old('max_days') }}" required></div>
                                    <div class="col-3">
                                        <label class="form-label small">Rule</label>
                                        <select name="action" class="form-select form-select-sm">
                                            <option value="surcharge">Surcharge %</option>
                                            <option value="block">Not accepted</option>
                                        </select>
                                    </div>
                                    <div class="col-2"><label class="form-label small">%</label><input type="number" name="percent" min="0" max="200" step="0.5" class="form-control form-control-sm" value="{{ old('percent') }}"></div>
                                    <div class="col-1"><button class="btn btn-sm btn-dark w-100" title="Add tier"><i class="mdi mdi-plus"></i></button></div>
                                </form>
                                <small class="text-muted d-block mt-2">The client sets the real tiers and percentages. The surcharge and new total are shown to the customer before checkout.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
