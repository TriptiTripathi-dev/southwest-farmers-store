<x-app-layout title="Customer List">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4 d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Customer List</h4>
                    <p class="text-muted mb-0 small mt-1">Manage your store customers and track their information</p>
                </div>
                @if(Auth::user()->hasPermission('create_customer') || Auth::user()->hasPermission('manage_customers'))
                <a href="{{ route('customers.create') }}" class="btn btn-success shadow-sm rounded-pill px-4">
                    <i class="mdi mdi-plus-circle me-2"></i> Add Customer
                </a>
                @endif
            </div>

            {{-- Stats Cards & Filter Section (Unchanged) --}}
            {{-- ... --}}

            <div class="card border-0 shadow-sm rounded-3">
                {{-- Table Header --}}
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
                                    {{-- Data Columns (Unchanged) --}}
                                    <td class="ps-4 py-3"><img src="{{ $customer->image_url }}" class="rounded-circle border" width="45" height="45"></td>
                                    <td class="py-3"><div class="fw-semibold text-dark">{{ $customer->name }}</div><small class="text-muted">{{ $customer->party_type }}</small></td>
                                    <td class="py-3">{{ $customer->phone }}</td>
                                    <td class="py-3">{{ $customer->email ?? 'N/A' }}</td>
                                    <td class="py-3">
                                        @if($customer->due > 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-3 py-2">${{ number_format($customer->due, 2) }}</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2">$0.00</span>
                                        @endif
                                    </td>
                                    
                                    <td class=" pe-4 py-3">
                                        <div class="btn-group shadow-sm" role="group">
                                            @if(Auth::user()->hasPermission('manage_customers'))
                                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-light border" title="Edit">
                                                <i class="mdi mdi-pencil text-primary"></i>
                                            </a>
                                            
                                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border" title="Delete">
                                                    <i class="mdi mdi-delete text-danger"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                {{-- Empty state (Unchanged) --}}
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-top py-3 px-4">
                    {{-- Pagination (Unchanged) --}}
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
    {{-- Scripts (Unchanged) --}}
</x-app-layout>