<x-app-layout title="Kitchen Transfer Request">
    @php
        $fmt = fn ($q) => rtrim(rtrim(number_format((float) $q, 2), '0'), '.');
        $when = fn ($d) => $d ? $d->copy()->timezone($tz)->format('m/d/Y h:i A') : '—';
        $badge = ['pending' => 'bg-warning text-dark', 'approved' => 'bg-success', 'denied' => 'bg-danger', 'cancelled' => 'bg-secondary'][$req->status] ?? 'bg-light text-dark';
    @endphp
    <div class="content">
        <div class="container-fluid">

            <x-page-header>
                <a href="{{ route('kitchen-transfers.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Kitchen Transfers
                </a>
            </x-page-header>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">{{ $req->number() }} <small class="text-muted fw-normal">· {{ $req->store->store_name ?? '' }}</small></h5>
                            <span class="badge {{ $badge }} fs-6">{{ $req->statusLabel() }}</span>
                        </div>
                        <div class="card-body p-0">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 small text-muted fw-bold">ITEM</th>
                                        <th class="small text-muted fw-bold text-end">REQUESTED</th>
                                        <th class="small text-muted fw-bold text-end">ON SHELF NOW</th>
                                        <th class="pe-4 small text-muted fw-bold text-end">IN KITCHEN NOW</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($req->items as $line)
                                        @php $onShelf = (float) ($shelf[$line->product_id] ?? 0); @endphp
                                        <tr>
                                            <td class="ps-4 fw-semibold">{{ $line->product->product_name ?? 'Unknown' }}</td>
                                            <td class="text-end fw-bold">{{ $fmt($line->quantity) }} {{ $line->product->unit ?? '' }}</td>
                                            <td class="text-end {{ $req->status === 'pending' && $onShelf < (float) $line->quantity ? 'text-danger fw-bold' : '' }}">{{ $fmt($onShelf) }}</td>
                                            <td class="pe-4 text-end">{{ $fmt($kitchen[$line->product_id] ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($req->notes)
                            <div class="card-footer bg-white small"><strong>Reason / notes:</strong> {{ $req->notes }}</div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-body small">
                            <div class="mb-2"><span class="text-muted">Requested by</span><br><strong>{{ $req->requested_by_name ?? '—' }}</strong> · {{ $when($req->created_at) }}</div>
                            <div class="mb-2"><span class="text-muted">From</span><br>{{ $req->store->store_name ?? '' }} shelf <i class="mdi mdi-arrow-right"></i> {{ $req->store->store_name ?? '' }} kitchen</div>
                            @if ($req->status !== 'pending')
                                <div class="mb-2"><span class="text-muted">{{ $req->status === 'cancelled' ? 'Cancelled' : 'Decided' }} by</span><br><strong>{{ $req->decided_by_name ?? '—' }}</strong> · {{ $when($req->decided_at) }}</div>
                                @if ($req->decision_note)
                                    <div><span class="text-muted">{{ $req->status === 'denied' ? 'Reason' : 'Note' }}</span><br>{{ $req->decision_note }}</div>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if ($canDecide)
                        <div class="card border-0 shadow-sm rounded-3 mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold">Area Manager decision</h6>
                                <form method="POST" action="{{ route('kitchen-transfers.approve', $req->id) }}" class="mb-3">
                                    @csrf
                                    <input type="text" name="note" class="form-control form-control-sm mb-2" placeholder="Note (optional)" maxlength="500">
                                    <button class="btn btn-success w-100"><i class="mdi mdi-check me-1"></i> Approve and move stock</button>
                                </form>
                                <form method="POST" action="{{ route('kitchen-transfers.deny', $req->id) }}">
                                    @csrf
                                    <input type="text" name="reason" class="form-control form-control-sm mb-2" placeholder="Reason for denying (required)" maxlength="500" required>
                                    <button class="btn btn-outline-danger w-100"><i class="mdi mdi-close me-1"></i> Deny</button>
                                </form>
                            </div>
                        </div>
                    @elseif ($req->status === 'pending' && $isOwn)
                        <div class="alert alert-light border small">
                            Waiting for an Area Manager. You can't approve your own request.
                        </div>
                        <form method="POST" action="{{ route('kitchen-transfers.cancel', $req->id) }}" data-confirm="Cancel this request?">
                            @csrf
                            <button class="btn btn-outline-secondary w-100">Cancel request</button>
                        </form>
                    @elseif ($req->status === 'pending')
                        <div class="alert alert-light border small">Waiting for an Area Manager to approve.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
