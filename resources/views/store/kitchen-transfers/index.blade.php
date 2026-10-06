<x-app-layout title="Kitchen Transfers">
    @php
        $fmt = fn ($q) => rtrim(rtrim(number_format((float) $q, 2), '0'), '.');
        $when = fn ($d) => $d ? $d->copy()->timezone($tz)->format('m/d/Y h:i A') : '—';
        $badge = ['pending' => 'bg-warning text-dark', 'approved' => 'bg-success', 'denied' => 'bg-danger', 'cancelled' => 'bg-secondary'];
    @endphp
    <div class="content">
        <div class="container-fluid">

            <x-page-header>
                <a href="{{ route('kitchen-inventory.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm me-2">
                    <i class="mdi mdi-fridge-outline me-1"></i> Kitchen Inventory
                </a>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-success rounded-pill px-4 shadow-sm me-2">
                    <i class="mdi mdi-file-excel-outline me-1"></i> Export (Excel / CSV)
                </a>
                @if ($canRequest)
                    <a href="{{ route('kitchen-transfers.create') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                        <i class="mdi mdi-plus me-1"></i> Request Transfer
                    </a>
                @endif
            </x-page-header>

            @if ($awaitingMe)
                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                    <span><i class="mdi mdi-bell-ring-outline me-1"></i> <strong>{{ $awaitingMe }}</strong> request(s) waiting for your approval.</span>
                    <a href="{{ route('kitchen-transfers.index', ['status' => 'pending']) }}" class="btn btn-sm btn-dark">Show pending</a>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        @if ($stores->count() > 1)
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Store</label>
                                <select name="store_id" class="form-select">
                                    <option value="">All my stores</option>
                                    @foreach ($stores as $s)
                                        <option value="{{ $s->id }}" @selected(request('store_id') == $s->id)>{{ $s->store_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                @foreach ($statuses as $k => $label)
                                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $k === 'pending' ? 'Pending' : $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Item</label>
                            <input type="text" name="item" value="{{ request('item') }}" class="form-control" placeholder="Product name">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">From</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">To</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                        </div>
                        <div class="col-md-1 d-flex gap-1">
                            <button class="btn btn-dark flex-fill">Go</button>
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
                                    <th class="ps-4 py-3 text-muted small fw-bold">REQUEST</th>
                                    <th class="py-3 text-muted small fw-bold">STORE</th>
                                    <th class="py-3 text-muted small fw-bold">ITEM</th>
                                    <th class="py-3 text-muted small fw-bold text-end">QTY</th>
                                    <th class="py-3 text-muted small fw-bold">REQUESTED BY / AT</th>
                                    <th class="py-3 text-muted small fw-bold">STATUS</th>
                                    <th class="py-3 text-muted small fw-bold">APPROVED / DENIED BY</th>
                                    <th class="pe-4 py-3 text-muted small fw-bold text-end">KITCHEN NOW</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $r)
                                    @foreach ($r->items as $i => $line)
                                        <tr>
                                            @if ($i === 0)
                                                <td class="ps-4" rowspan="{{ $r->items->count() }}">
                                                    <a href="{{ route('kitchen-transfers.show', $r->id) }}" class="fw-bold">{{ $r->number() }}</a>
                                                </td>
                                                <td rowspan="{{ $r->items->count() }}">{{ $r->store->store_name ?? '—' }}</td>
                                            @endif
                                            <td>{{ $line->product->product_name ?? 'Unknown' }}</td>
                                            <td class="text-end fw-semibold">{{ $fmt($line->quantity) }}</td>
                                            @if ($i === 0)
                                                <td rowspan="{{ $r->items->count() }}">{{ $r->requested_by_name ?? '—' }}<small class="d-block text-muted">{{ $when($r->created_at) }}</small></td>
                                                <td rowspan="{{ $r->items->count() }}"><span class="badge {{ $badge[$r->status] ?? 'bg-light text-dark' }}">{{ $r->statusLabel() }}</span></td>
                                                <td rowspan="{{ $r->items->count() }}">{{ $r->status === 'pending' ? '—' : ($r->decided_by_name ?? '—') }}<small class="d-block text-muted">{{ $r->status === 'pending' ? '' : $when($r->decided_at) }}</small></td>
                                            @endif
                                            <td class="pe-4 text-end">{{ $fmt($current($r, $line->product_id) ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr><td colspan="8" class="text-center py-5 text-muted">No kitchen transfer requests found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($requests->hasPages())
                    <div class="card-footer bg-white">{{ $requests->links() }}</div>
                @endif
            </div>
            <p class="text-muted small mt-2 mb-0">Times are store local time (Central). Kitchen stock changes only when a request is approved.</p>
        </div>
    </div>
</x-app-layout>
