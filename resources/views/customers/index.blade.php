<x-app-layout title="Customer List">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4 d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Customer List</h4>
                    <p class="text-muted mb-0 small mt-1">Manage your store customers and track their information</p>
                </div>
                <a href="{{ route('customers.create') }}" class="btn btn-success shadow-sm rounded-pill px-4">
                    <i class="mdi mdi-plus-circle me-2"></i> Add Customer
                </a>
            </div>

            <div class="row g-3 mb-4">
                {{-- ... (Your existing Stats Cards code) ... --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-25 rounded-circle p-3">
                                        <i class="mdi mdi-account-multiple text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted small mb-1">Total Customers</h6>
                                    <h4 class="mb-0 fw-bold">{{ $customers->total() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-25 rounded-circle p-3">
                                        <i class="mdi mdi-cash-multiple text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted small mb-1">Total Due</h6>
                                    <h4 class="mb-0 fw-bold">${{ number_format($customers->sum('due'), 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="mdi mdi-magnify text-muted"></i>
                                </span>
                                <input type="text" 
                                       id="searchInput"
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Search by name, phone or email..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <select class="form-select" id="sortFilter">
                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Recently Added</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="due" {{ request('sort') == 'due' ? 'selected' : '' }}>Highest Due Amount</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary w-100" onclick="applyFilters()">
                                    <i class="mdi mdi-filter-variant me-1"></i> Filter
                                </button>
                                <button class="btn btn-light border" onclick="clearFilters()" title="Reset">
                                    <i class="mdi mdi-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="mdi mdi-format-list-bulleted text-success me-2"></i>All Customers
                        </h6>
                        </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-semibold">IMAGE</th>
                                    <th class="py-3 text-muted small fw-semibold">CUSTOMER NAME</th>
                                    <th class="py-3 text-muted small fw-semibold">PHONE</th>
                                    <th class="py-3 text-muted small fw-semibold">EMAIL</th>
                                    <th class="py-3 text-muted small fw-semibold">DUE AMOUNT</th>
                                    <th class="pe-4 py-3 text-muted small fw-semibold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3">
                                        <div class="position-relative d-inline-block">
                                            <img src="{{ $customer->image_url }}" class="rounded-circle border border-2 border-light shadow-sm" width="45" height="45" alt="Customer">
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold text-dark">{{ $customer->name }}</div>
                                        <small class="text-muted">{{ $customer->party_type }}</small>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-dark">
                                            <i class="mdi mdi-phone text-muted me-1"></i>{{ $customer->phone }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-muted small">
                                            <i class="mdi mdi-email-outline me-1"></i>{{ $customer->email ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @if($customer->due > 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-3 py-2">
                                                ${{ number_format($customer->due, 2) }}
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2">
                                                $0.00
                                            </span>
                                        @endif
                                    </td>
                                    <td class=" pe-4 py-3">
                                        <div class="btn-group shadow-sm" role="group">
                                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-light border" title="Edit">
                                                <i class="mdi mdi-pencil text-primary"></i>
                                            </a>
                                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border" title="Delete">
                                                    <i class="mdi mdi-delete text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="mdi mdi-account-off-outline text-muted" style="font-size: 4rem;"></i>
                                            <h5 class="text-muted mt-3">No Customers Found</h5>
                                            <p class="text-muted small mb-3">
                                                @if(request('search'))
                                                    No results for "{{ request('search') }}"
                                                @else
                                                    Start by adding your first customer
                                                @endif
                                            </p>
                                            <a href="{{ route('customers.create') }}" class="btn btn-success rounded-pill px-4">
                                                <i class="mdi mdi-plus-circle me-2"></i> Add New Customer
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-top py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted small">
                            Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                        </div>
                        <div>
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Apply Filters Function
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const sort = document.getElementById('sortFilter').value;

            const url = new URL(window.location.href);
            
            if (search) url.searchParams.set('search', search);
            else url.searchParams.delete('search');

            if (sort) url.searchParams.set('sort', sort);
            else url.searchParams.delete('sort');

            // Reset to page 1 on new filter
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }

        // Clear Filters Function
        function clearFilters() {
            window.location.href = '{{ route('customers.index') }}';
        }

        // Event Listeners
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        document.getElementById('sortFilter').addEventListener('change', function() {
            applyFilters();
        });
    </script>
    @endpush
</x-app-layout>