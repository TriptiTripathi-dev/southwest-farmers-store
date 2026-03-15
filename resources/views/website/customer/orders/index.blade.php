<x-website-layout title="My Orders - Southwest Farmers">
    <div class="py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <div class="container">
            <div class="d-flex align-items-center gap-3 mb-2">
                <i class="mdi mdi-package-variant-closed fs-3 text-primary"></i>
                <h1 class="fw-black mb-0 text-dark">My Orders</h1>
            </div>
            <p class="text-muted mb-0">Review and track your past purchases</p>
        </div>
    </div>

    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="bg-white rounded-4 border-0 shadow-sm overflow-hidden">
                        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Order History</h5>
                            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                                {{ $orders->total() }} Total Orders
                            </span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 text-muted small fw-black border-0">ORDER #</th>
                                        <th class="py-3 text-muted small fw-black border-0">DATE</th>
                                        <th class="py-3 text-muted small fw-black border-0">ITEMS</th>
                                        <th class="py-3 text-muted small fw-black border-0">TOTAL</th>
                                        <th class="py-3 text-muted small fw-black border-0 text-center">STATUS</th>
                                        <th class="px-4 py-3 text-muted small fw-black border-0 text-end">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td class="px-4 py-4 fw-bold text-dark">#{{ $order->invoice_number }}</td>
                                        <td class="py-4 text-muted small">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="py-4 fw-semibold text-dark">{{ $order->items->count() }} Items</td>
                                        <td class="py-4 fw-black text-primary">${{ number_format($order->total_amount, 2) }}</td>
                                        <td class="py-4 text-center">
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-warning-subtle text-warning',
                                                    'completed' => 'bg-success-subtle text-success',
                                                    'cancelled' => 'bg-danger-subtle text-danger',
                                                ][$order->status ?? 'pending'] ?? 'bg-secondary-subtle text-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }} fw-bold px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.7rem;">
                                                {{ $order->status ?? 'pending' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-end">
                                            <a href="{{ route('website.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="mdi mdi-package-variant-closed text-muted opacity-25" style="font-size: 5rem;"></i>
                                                <h4 class="fw-bold mt-4 text-dark">No orders found</h4>
                                                <p class="text-muted mb-4 fs-5">You haven't placed any orders yet.</p>
                                                <a href="{{ route('website.products.index') }}" class="btn btn-primary rounded-pill fw-bold px-4">
                                                    Start Shopping
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($orders->hasPages())
                        <div class="p-4 border-top bg-light">
                            {{ $orders->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-website-layout>
