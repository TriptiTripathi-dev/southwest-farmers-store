{{-- Ticket 38: scheduled Store POs listed with the (unscheduled) requests on each tab. --}}
@foreach ($scheduledOrders as $po)
    @php
        $st = strtolower($po->status ?? 'pending');
        $qty = $po->items->sum('requested_qty');
        $badge = match ($st) {
            'completed' => 'bg-success',
            'cancelled', 'rejected' => 'bg-secondary',
            'partially_received' => 'bg-warning text-dark',
            'dispatched', 'in_transit', 'approved', 'receiving' => 'bg-info text-dark',
            default => 'bg-warning text-dark',
        };
    @endphp
    <tr>
        <td class="ps-4">
            <span class="fw-bold text-primary">{{ $po->po_number ?? 'PO-' . $po->id }}</span>
            <span class="badge bg-primary-subtle text-primary border ms-1">Scheduled</span>
        </td>
        <td><span class="text-muted">—</span></td>
        @if ($tab === 'in_transit')
            <td class="text-center fw-bold">{{ rtrim(rtrim(number_format($po->items->sum('dispatched_qty'), 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($qty, 2), '0'), '.') }}</td>
            <td>{{ $po->received_at ? $po->received_at->storeTime()->format('d M Y') : '---' }}</td>
            <td>---</td>
            <td class="text-center"><span class="badge {{ $badge }}">{{ ucwords(str_replace('_', ' ', $st)) }}</span></td>
            <td class="text-end pe-4">
                <a href="{{ route('store.orders.receive', $po->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">RECEIVE</a>
            </td>
        @else
            <td class="text-center fw-bold">{{ $po->items->count() }} Items</td>
            <td>{{ $po->created_at?->storeTime()->format('d M Y') }}</td>
            <td class="text-center">{{ $tab === 'history' ? ($po->received_at ? $po->received_at->storeTime()->format('d M Y') : '---') : '' }}</td>
            <td class="text-center"><span class="badge {{ $badge }}">{{ ucwords(str_replace('_', ' ', $st)) }}</span></td>
            <td class="text-end pe-4">
                <a href="{{ route('store.orders.show', $po->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="mdi mdi-eye"></i></a>
            </td>
        @endif
    </tr>
@endforeach
