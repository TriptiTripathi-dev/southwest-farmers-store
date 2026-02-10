<x-app-layout title="Store Analytics">
<div class="container-fluid">
    <h2 class="h4 mb-4">Store Performance & Risks</h2>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 border-{{ $wasteRatio > 5 ? 'danger' : 'success' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted">Waste vs Sales Ratio (This Month)</h6>
                    <h1 class="display-4 fw-bold {{ $wasteRatio > 5 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($wasteRatio, 1) }}%
                    </h1>
                    <p class="mb-0 small">
                        Sales: ${{ number_format($totalSales) }} <br>
                        Waste: <span class="text-danger">${{ number_format($wasteValue) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Sales by Department</div>
                <div class="card-body">
                    <canvas id="deptChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle me-1"></i> Expiring Soon (Next 14 Days)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Batch</th>
                                    <th>Expiry</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiringBatches as $batch)
                                <tr>
                                    <td>{{ $batch->product->product_name }}</td>
                                    <td>{{ $batch->batch_number }}</td>
                                    <td class="text-danger fw-bold">
                                        {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M') }}
                                    </td>
                                    <td>{{ $batch->quantity }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No immediate expiry risks.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-undo me-1"></i> Recent Refunds
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Return #</th>
                                    <th>Reason</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReturns as $ret)
                                <tr>
                                    <td>{{ $ret->return_no }}</td>
                                    <td>{{ Str::limit($ret->reason, 20) }}</td>
                                    <td class="text-danger">-${{ number_format($ret->total_refund, 2) }}</td>
                                    <td>{{ $ret->created_at->format('d M H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No recent returns.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center small">
                    <a href="{{ route('store.sales.returns.index') }}">View All Returns</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('deptChart').getContext('2d');
    
    const labels = {!! json_encode($deptSales->pluck('name')) !!};
    const data = {!! json_encode($deptSales->pluck('total_sales')) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue ($)',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
</x-app-layout>