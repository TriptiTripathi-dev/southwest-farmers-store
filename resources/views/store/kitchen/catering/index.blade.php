<x-app-layout title="Catering Orders">
    @php $fmt = fn ($q) => rtrim(rtrim(number_format((float) $q, 2), '0'), '.'); @endphp
    <div class="content">
        <div class="container-fluid">
            <x-page-header>
                <a href="{{ route('store.kitchen.catering.rules') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm me-2">
                    <i class="mdi mdi-calendar-alert me-1"></i> Catering Rules
                </a>
                <a href="{{ route('store.kitchen.kds.index') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                    <i class="mdi mdi-monitor-dashboard me-1"></i> KDS
                </a>
            </x-page-header>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body py-2 d-flex flex-wrap gap-2 align-items-center">
                    @foreach (['upcoming' => 'Upcoming', 'past' => 'Past', 'all' => 'All'] as $k => $label)
                        <a href="{{ route('store.kitchen.catering.index', ['view' => $k]) }}" class="btn btn-sm {{ $view === $k ? 'btn-dark' : 'btn-outline-secondary' }}">{{ $label }}</a>
                    @endforeach
                    <form method="GET" class="d-flex gap-2 ms-auto">
                        <input type="hidden" name="view" value="all">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
                        <button class="btn btn-sm btn-outline-dark">Event date</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 small text-muted fw-bold">EVENT DATE / TIME</th>
                                    <th class="small text-muted fw-bold">ORDER</th>
                                    <th class="small text-muted fw-bold">CONTACT</th>
                                    <th class="small text-muted fw-bold">ITEMS</th>
                                    <th class="small text-muted fw-bold text-end">NOTICE / SURCHARGE</th>
                                    <th class="small text-muted fw-bold text-end">TOTAL</th>
                                    <th class="pe-4 small text-muted fw-bold">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $o)
                                    <tr>
                                        <td class="ps-4 fw-semibold text-nowrap">{{ $o->due_at?->copy()->timezone($tz)->format('D m/d/Y h:i A') ?? '—' }}</td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('store.sales.orders.show', $o->id) }}" class="fw-bold">#{{ $o->invoice_number }}</a>
                                            <small class="d-block text-muted">placed {{ $o->created_at?->copy()->timezone($tz)->format('m/d h:i A') }}</small>
                                        </td>
                                        <td class="small">
                                            {{ $o->contact_name ?? $o->customer->name ?? '—' }}<br><span class="text-muted">{{ $o->contact_phone }}</span>
                                            @if ($o->guest_count)<br><span class="text-muted">{{ $o->guest_count }} guests</span>@endif
                                        </td>
                                        <td class="small">
                                            @foreach ($o->items as $line)
                                                <div>{{ $fmt($line->quantity) }} × {{ $line->menuItem->name ?? 'Item' }}</div>
                                            @endforeach
                                            @if ($o->special_instructions)<div class="text-muted fst-italic">{{ $o->special_instructions }}</div>@endif
                                        </td>
                                        <td class="text-end small text-nowrap">
                                            {{ $o->catering_notice_days ?? '—' }} days
                                            @if ((float) $o->catering_surcharge_amount > 0)
                                                <br><span class="text-danger">+{{ $fmt($o->catering_surcharge_percent) }}% = ${{ number_format($o->catering_surcharge_amount, 2) }}</span>
                                            @else
                                                <br><span class="text-muted">standard</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">${{ number_format($o->total_amount, 2) }}<small class="d-block text-muted fw-normal">{{ strtoupper($o->payment_method) }}{{ $o->payment_status === 'completed' ? ' · paid' : '' }}</small></td>
                                        <td class="pe-4"><span class="badge bg-light text-dark border">{{ $o->kitchen_status ?? 'New' }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-5">No catering orders here.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($orders->hasPages())
                    <div class="card-footer bg-white">{{ $orders->links() }}</div>
                @endif
            </div>
            <p class="text-muted small mt-2 mb-0">Catering orders also appear on the KDS, sorted by event time. Times are store local time (Central).</p>
        </div>
    </div>
</x-app-layout>
