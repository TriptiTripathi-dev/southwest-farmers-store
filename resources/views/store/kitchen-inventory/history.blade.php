<x-app-layout title="Kitchen Inventory History">
    @php $fmt = fn ($q) => rtrim(rtrim(number_format((float) $q, 2), '0'), '.'); @endphp
    <div class="content">
        <div class="container-fluid">

            <x-page-header>
                <a href="{{ route('kitchen-inventory.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Kitchen Inventory
                </a>
            </x-page-header>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Item</label>
                            <select name="stock" class="form-select">
                                <option value="">All items</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @selected(request('stock') == $item->id)>{{ $item->product->product_name ?? 'Unknown' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Type</label>
                            <select name="type" class="form-select">
                                <option value="">All types</option>
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">From</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">To</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button class="btn btn-dark flex-fill">Filter</button>
                            <a href="{{ route('kitchen-inventory.history') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">DATE / TIME</th>
                                    <th class="py-3 text-muted small fw-bold">ITEM</th>
                                    <th class="py-3 text-muted small fw-bold">TYPE</th>
                                    <th class="py-3 text-muted small fw-bold text-end">CHANGE</th>
                                    <th class="py-3 text-muted small fw-bold text-end">BALANCE AFTER</th>
                                    <th class="py-3 text-muted small fw-bold">REASON / NOTES</th>
                                    <th class="pe-4 py-3 text-muted small fw-bold">BY</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $t)
                                    <tr>
                                        <td class="ps-4">{{ $t->created_at?->copy()->timezone($tz)->format('m/d/Y h:i A') }}</td>
                                        <td class="fw-semibold">{{ $t->stock->product->product_name ?? 'Unknown' }}</td>
                                        <td>{{ $t->typeLabel() }}</td>
                                        <td class="text-end fw-bold {{ $t->quantity_change >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $t->quantity_change >= 0 ? '+' : '−' }}{{ $fmt(abs($t->quantity_change)) }}
                                        </td>
                                        <td class="text-end">{{ $fmt($t->balance_after) }}</td>
                                        <td class="text-wrap" style="max-width: 320px;">
                                            {{ $t->reason }}@if ($t->reason && $t->notes) — @endif<span class="text-muted">{{ $t->notes }}</span>
                                        </td>
                                        <td class="pe-4">{{ $t->user_name ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-5 text-muted">No kitchen stock movements found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer bg-white">{{ $transactions->links() }}</div>
                @endif
            </div>
            <p class="text-muted small mt-2 mb-0">Times are store local time (Central). Movements are never edited; a correction is a new line.</p>

        </div>
    </div>
</x-app-layout>
