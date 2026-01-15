<x-app-layout title="Customer Details">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4 d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Customer Profile</h4>
                    <p class="text-muted small mb-0 mt-1">View complete details of {{ $customer->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('customers.index') }}" class="btn btn-light border shadow-sm rounded-pill px-4">
                        <i class="mdi mdi-arrow-left me-1"></i> Back
                    </a>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                        <i class="mdi mdi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 text-center h-100">
                        <div class="card-body p-4">
                            <div class="mb-4 position-relative d-inline-block">
                                <img src="{{ $customer->image_url }}" 
                                     class="rounded-circle img-thumbnail border-0 shadow-sm" 
                                     style="width: 120px; height: 120px; object-fit: cover;" 
                                     alt="{{ $customer->name }}">
                                <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle">
                                    <span class="visually-hidden">Active</span>
                                </span>
                            </div>
                            
                            <h5 class="fw-bold text-dark mb-1">{{ $customer->name }}</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                                {{ $customer->party_type ?? 'Retail Customer' }}
                            </span>

                            <div class="d-flex justify-content-center gap-2 mb-4">
                                <a href="tel:{{ $customer->phone }}" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="Call">
                                    <i class="mdi mdi-phone"></i>
                                </a>
                                <a href="mailto:{{ $customer->email }}" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="Email">
                                    <i class="mdi mdi-email"></i>
                                </a>
                            </div>

                            <div class="bg-light rounded-3 p-3 border border-dashed">
                                <small class="text-muted text-uppercase fw-bold d-block mb-1">Current Due Amount</small>
                                @if($customer->due > 0)
                                    <h3 class="text-danger fw-bold mb-0">${{ number_format($customer->due, 2) }}</h3>
                                    <small class="text-danger"><i class="mdi mdi-alert-circle-outline"></i> Payment Pending</small>
                                @else
                                    <h3 class="text-success fw-bold mb-0">$0.00</h3>
                                    <small class="text-success"><i class="mdi mdi-check-circle-outline"></i> All Clear</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="mdi mdi-information-outline text-primary me-2"></i>General Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Phone Number</label>
                                    <p class="h6 text-dark fw-medium">{{ $customer->phone }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Email Address</label>
                                    <p class="h6 text-dark fw-medium">{{ $customer->email ?? 'N/A' }}</p>
                                </div>
                                
                                <div class="col-12">
                                    <hr class="text-muted opacity-25 my-1">
                                </div>

                                <div class="col-md-12">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Billing Address</label>
                                    <div class="d-flex align-items-start">
                                        <i class="mdi mdi-map-marker text-muted me-2 mt-1"></i>
                                        <p class="h6 text-dark fw-medium mb-0 lh-base">
                                            {{ $customer->address ?? 'No address provided.' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="text-muted opacity-25 my-1">
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Customer Since</label>
                                    <p class="h6 text-dark fw-medium">
                                        <i class="mdi mdi-calendar-check text-success me-1"></i>
                                        {{ $customer->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Last Updated</label>
                                    <p class="h6 text-dark fw-medium">
                                        <i class="mdi mdi-clock-outline text-info me-1"></i>
                                        {{ $customer->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-light border-top p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-danger fw-bold mb-1">Delete Customer</h6>
                                    <p class="text-muted small mb-0">Once deleted, this action cannot be undone.</p>
                                </div>
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this customer?');">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                                        <i class="mdi mdi-delete me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
    @endpush
</x-app-layout>