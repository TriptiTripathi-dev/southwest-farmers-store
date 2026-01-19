<x-app-layout title="Stock Requests">
    <div class="content">
        <div class="container-fluid">
            
            <div class="py-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Stock Requests</h4>
                    <p class="text-muted small mb-0 mt-1">Track and manage your Store requisitions</p>
                </div>
                <button type="button" class="btn btn-success btn-sm text-white me-2" data-bs-toggle="modal" data-bs-target="#importModal">
    <i class="mdi mdi-file-excel"></i> Bulk Import
</button>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newRequestModal">
                    <i class="mdi mdi-plus me-2"></i> New Request
                </button>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="{{ route('inventory.requests') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search product...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100">Filter</button>
                        </div>
                        @if(request()->hasAny(['search', 'status']))
                        <div class="col-md-1">
                            <a href="{{ route('inventory.requests') }}" class="btn btn-light border w-100" title="Clear Filters"><i class="mdi mdi-close"></i></a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">PRODUCT DETAILS</th>
                                    <th class="py-3 text-muted small fw-bold">REQUESTED QTY</th>
                                    <th class="py-3 text-muted small fw-bold">STATUS</th>
                                    <th class="py-3 text-muted small fw-bold">DATE</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-semibold text-dark">{{ $req->product->product_name }}</div>
                                        <div class="small text-muted font-monospace">{{ $req->product->sku }}</div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $req->requested_quantity }}</span> <span class="small text-muted">{{ $req->product->unit }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $badges = [
                                                'pending' => 'bg-warning text-dark',
                                                'approved' => 'bg-info text-white',
                                                'rejected' => 'bg-danger text-white',
                                                'completed' => 'bg-success text-white'
                                            ];
                                            $badgeClass = $badges[$req->status] ?? 'bg-secondary text-white';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 rounded-pill text-uppercase" style="font-size: 0.75rem;">
                                            {{ $req->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $req->created_at->format('M d, Y') }}<br>
                                        <span class="text-xs">{{ $req->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($req->status == 'pending')
                                            <form action="{{ route('inventory.requests.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Cancel Request">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small"><i class="mdi mdi-lock"></i></span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="mdi mdi-clipboard-text-off fs-1 opacity-50 mb-2"></i>
                                        <p>No requests found matching your filters.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    {{ $requests->links() }}
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="newRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('inventory.request') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">New Stock Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3">
                            <i class="mdi mdi-information-outline me-1"></i> Request items from the Central Store.
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Select Product</label>
                            <select name="product_id" class="form-select form-select-lg" required>
                                <option value="" disabled selected>Choose a product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->product_name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Quantity Needed</label>
                            <input type="number" name="quantity" class="form-control form-control-lg" min="1" placeholder="e.g., 50" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Import Stock Requests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('inventory.requests.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    
                    <div class="alert alert-info small mb-3">
                        <i class="mdi mdi-information-outline"></i> 
                        Please download the sample file to ensure correct formatting.
                        <br>
                        <strong>Required Columns:</strong> sku, quantity
                    </div>

                    <div class="mb-3 text-end">
                        <a href="{{ route('inventory.requests.sample') }}" class="btn btn-sm btn-outline-primary">
                            <i class="mdi mdi-download"></i> Download Sample CSV
                        </a>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload File (CSV/Excel)</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success text-white">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>