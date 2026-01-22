<x-app-layout title="Stock History">
    <div class="content-wrapper">
        <div class="container-fluid">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                            <li class="breadcrumb-item active">History: {{ $product->sku }}</li>
                        </ol>
                    </nav>
                    <h4 class="mb-0 fw-bold">Stock Ledger</h4>
                </div>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left"></i> Back to Inventory
                </a>
            </div>

            {{-- PRODUCT INFO CARD --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                            <i class="mdi mdi-cube-outline fs-2 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ $product->product_name }}</h5>
                            <span class="badge bg-light text-dark border">SKU: {{ $product->sku }}</span>
                            <span class="badge bg-light text-dark border">Unit: {{ $product->unit }}</span>
                        </div>
                        <div class="ms-auto text-end">
                            <small class="text-muted d-block">Current Balance</small>
                            @php
                                $currentStock = \App\Models\StoreStock::where('store_id', Auth::user()->store_id ?? Auth::id())
                                                ->where('product_id', $product->id)->value('quantity') ?? 0;
                            @endphp
                            <h3 class="fw-bold text-primary mb-0">{{ $currentStock }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TRANSACTIONS TABLE --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Transaction History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>Transaction Type</th>
                                    <th>Reference</th>
                                    <th class="text-center">Change</th>
                                    <th class="text-center">Balance</th>
                                    <th>Done By</th>
                                    <th class="pe-4">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $txn)
                                    <tr>
                                        <td class="ps-4 text-muted small">
                                            {{ $txn->created_at->format('d M Y') }}<br>
                                            {{ $txn->created_at->format('h:i A') }}
                                        </td>
                                        <td>
                                            @php
                                                $badges = [
                                                    'transfer_in' => 'bg-success',  // Stock received
                                                    'sale' => 'bg-info',            // Sold (POS)
                                                    'adjustment_add' => 'bg-warning text-dark',
                                                    'adjustment_sub' => 'bg-danger',
                                                    'return' => 'bg-secondary'
                                                ];
                                                $labels = [
                                                    'transfer_in' => 'Stock Received',
                                                    'sale' => 'Sale',
                                                    'adjustment_add' => 'Adjustment (+)',
                                                    'adjustment_sub' => 'Adjustment (-)',
                                                    'return' => 'Return'
                                                ];
                                                $type = $txn->type;
                                            @endphp
                                            <span class="badge {{ $badges[$type] ?? 'bg-secondary' }}">
                                                {{ $labels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}
                                            </span>
                                        </td>
                                        <td class="font-monospace small">{{ $txn->reference_id ?? '-' }}</td>
                                        
                                        <td class="text-center fw-bold {{ $txn->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $txn->quantity_change > 0 ? '+' : '' }}{{ $txn->quantity_change }}
                                        </td>
                                        
                                        <td class="text-center fw-bold text-dark">
                                            {{ $txn->running_balance }}
                                        </td>

                                        <td class="small">
                                            <i class="mdi mdi-account-circle text-muted"></i> 
                                            {{ $txn->store->user->name ?? 'System' }}
                                        </td>
                                        
                                        <td class="pe-4 text-muted small" style="max-width: 250px;">
                                            {{ $txn->remarks ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-history fs-1 opacity-50 mb-2"></i>
                                            <p>No transactions found for this product yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>