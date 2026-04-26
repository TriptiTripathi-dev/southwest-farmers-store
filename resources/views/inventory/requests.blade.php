<x-app-layout title="Order Inventory">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-clipboard-text-outline text-primary me-2"></i> Order Inventory
            </h4>
            <small class="text-muted">Manage inventory orders and replenishment from warehouse</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.order.create') }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                <i class="mdi mdi-plus me-1"></i> New Order Inventory
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="mdi mdi-clock-outline fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 opacity-75">Inventory Request</h6>
                        <h3 class="mb-0 fw-bold">{{ $pendingCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success bg-gradient text-white">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="mdi mdi-truck-delivery fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 opacity-75">Receive Inventory</h6>
                        <h3 class="mb-0 fw-bold">{{ $inTransitCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success bg-gradient text-white">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="mdi mdi-history fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 opacity-75">Inventory History</h6>
                        <h3 class="mb-0 fw-bold">{{ $completedCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm d-inline-flex" id="inventoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link px-4 py-2 rounded-pill fw-bold {{ request('status', 'pending') == 'pending' ? 'active' : '' }}" 
               href="{{ route('inventory.requests', ['status' => 'pending']) }}">
                INVENTORY REQUEST
            </a>
        </li>
        <li class="nav-item mx-2" role="presentation">
            <a class="nav-link px-4 py-2 rounded-pill fw-bold {{ request('status') == 'in_transit' ? 'active' : '' }}" 
               href="{{ route('inventory.requests', ['status' => 'in_transit']) }}">
                RECEIVE INVENTORY
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link px-4 py-2 rounded-pill fw-bold {{ request('status') == 'history' ? 'active' : '' }}" 
               href="{{ route('inventory.requests', ['status' => 'history']) }}">
                INVENTORY HISTORY
            </a>
        </li>
    </ul>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Order ID / Product</label>
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if(request('status') == 'in_transit')
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Receiving Progress</label>
                    <select name="receiving_progress" class="form-select">
                        <option value="">All Progress</option>
                        <option value="open" {{ request('receiving_progress') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="partially_received" {{ request('receiving_progress') == 'partially_received' ? 'selected' : '' }}>Partially Received</option>
                        <option value="received" {{ request('receiving_progress') == 'received' ? 'selected' : '' }}>Received</option>
                    </select>
                </div>
                @elseif(request('status') == 'history')
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Received By</label>
                    <input type="text" name="received_by" class="form-control" placeholder="Name..." value="{{ request('received_by') }}">
                </div>
                @else
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select name="sub_status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="awaiting_approval">Awaiting Approval</option>
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="mdi mdi-filter"></i></button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('inventory.requests', ['status' => request('status', 'pending')]) }}" class="btn btn-outline-secondary w-100"><i class="mdi mdi-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        @if(request('status', 'pending') == 'pending')
                        <tr>
                            <th class="ps-4">ORDER ID</th>
                            <th>DEPARTMENT</th>
                            <th class="text-center">QTY</th>
                            <th>DATE</th>
                            <th class="text-center">REVIEW</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-end pe-4">ACTION</th>
                        </tr>
                        @elseif(request('status') == 'in_transit')
                        <tr>
                            <th class="ps-4">ORDER ID</th>
                            <th>DEPARTMENT</th>
                            <th class="text-center">RECEIVED QTY</th>
                            <th>RECEIVED DATE</th>
                            <th>RECEIVED BY</th>
                            <th class="text-center">RECEIVING PROGRESS</th>
                            <th class="text-end pe-4">ACTION</th>
                        </tr>
                        @else
                        <tr>
                            <th class="ps-4">ORDER ID</th>
                            <th>DEPARTMENT</th>
                            <th class="text-center">QTY</th>
                            <th>DATE</th>
                            <th>RECEIVED BY</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-end pe-4">ACTION</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-primary">{{ $request->request_number ?? 'REQ-'.str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $request->department->name ?? 'N/A' }}</span>
                            </td>

                            @if(request('status', 'pending') == 'pending')
                                <td class="text-center fw-bold">{{ $request->total_items ?? $request->items->count() }} Items</td>
                                <td>{{ $request->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    @if($request->reviewed)
                                        <i class="mdi mdi-checkbox-marked-circle text-success fs-4" title="Reviewed by {{ $request->reviewedBy->name ?? 'N/A' }}"></i>
                                    @else
                                        <button class="btn btn-sm btn-outline-warning rounded-pill px-3" onclick="reviewOrder({{ $request->id }})">
                                            Mark Review
                                        </button>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($request->status == 'awaiting_approval')
                                        <span class="badge bg-danger">Awaiting Approval</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('inventory.requests.show', $request->id) }}" class="btn btn-outline-primary"><i class="mdi mdi-eye"></i></a>
                                        @if(!$request->reviewed)
                                            <button class="btn btn-outline-danger" onclick="cancelOrder({{ $request->id }})"><i class="mdi mdi-delete"></i></button>
                                        @endif
                                    </div>
                                </td>

                            @elseif(request('status') == 'in_transit')
                                <td class="text-center fw-bold">{{ $request->received_qty ?? 0 }} / {{ $request->total_items ?? $request->items->count() }}</td>
                                <td>{{ $request->received_at ? $request->received_at->format('d M Y') : '---' }}</td>
                                <td>{{ $request->received_by_name ?? '---' }}</td>
                                <td class="text-center">
                                    @if($request->receiving_progress == 'received')
                                        <span class="badge bg-success">Received</span>
                                    @elseif($request->receiving_progress == 'partially_received')
                                        <span class="badge bg-warning text-dark">Partially Received</span>
                                    @else
                                        <span class="badge bg-danger">Open</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('inventory.requests.show', $request->id) }}?action=receive" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                        RECEIVE
                                    </a>
                                </td>

                            @else
                                <td class="text-center fw-bold">{{ $request->total_items ?? $request->items->count() }} Items</td>
                                <td>{{ $request->created_at->format('d M Y') }}</td>
                                <td>{{ $request->received_by_name ?? '---' }}</td>
                                <td class="text-center">
                                    @if($request->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($request->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" title="Print" onclick="window.open('{{ route('inventory.requests.show', $request->id) }}?print=1', '_blank')"><i class="mdi mdi-printer"></i></button>
                                        <button class="btn btn-outline-info" title="Send" onclick="Swal.fire('Sent!', 'Order details have been sent to registered emails.', 'success')"><i class="mdi mdi-send"></i></button>
                                        <a href="{{ route('inventory.requests.show', $request->id) }}" class="btn btn-outline-primary" title="View"><i class="mdi mdi-eye"></i></a>
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="mdi mdi-file-search-outline fs-1"></i>
                                <p class="mt-2 mb-0">No records found for the selected filter.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function reviewOrder(id) {
    Swal.fire({
        title: 'Review Order?',
        text: "Are you sure you want to mark this order as Reviewed?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Review It!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/inventory/requests/${id}/review`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    Swal.fire('Reviewed!', response.message, 'success').then(() => location.reload());
                },
                error: function(err) {
                    Swal.fire('Error!', err.responseJSON.message || 'Something went wrong.', 'error');
                }
            });
        }
    });
}

function cancelOrder(id) {
    Swal.fire({
        title: 'Cancel Order?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Cancel It!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/inventory/requests/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    Swal.fire('Cancelled!', 'Order has been cancelled.', 'success').then(() => location.reload());
                }
            });
        }
    });
}
</script>
@endpush

</x-app-layout>
