<x-app-layout title="Raise Ticket">
    <div class="container-fluid">
        
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0 bg-white p-3 rounded shadow-sm">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-dark">
                        <i class="mdi mdi-home-outline me-1"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('store.support.index') }}" class="text-decoration-none text-dark">
                        Support Tickets
                    </a>
                </li>
                <li class="breadcrumb-item active fw-bold" aria-current="page">
                    Create New
                </li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                            <i class="mdi mdi-plus-box-outline fs-4"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Create Support Ticket</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="{{ route('store.support.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            
                            {{-- Subject --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" placeholder="Brief summary of the issue..." required>
                                <div class="invalid-feedback">
                                    Please provide a subject line.
                                </div>
                            </div>

                            {{-- Category & Priority --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Stock Issue">Stock Inventory Issue</option>
                                        <option value="Technical">Technical / POS System</option>
                                        <option value="Logistics">Logistics / Delivery</option>
                                        <option value="Billing">Billing / Finance</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a category.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Priority <span class="text-danger">*</span></label>
                                    <select name="priority" class="form-select" required>
                                        <option value="">-- Select Priority --</option>
                                        <option value="low">Low (General Query)</option>
                                        <option value="medium">Medium (Requires Attention)</option>
                                        <option value="high">High (Urgent Impact)</option>
                                        <option value="critical">Critical (Store Operations Halted)</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a priority level.
                                    </div>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="6" placeholder="Describe your issue in detail..." required></textarea>
                                <div class="invalid-feedback">
                                    Please provide a detailed description.
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                                <a href="{{ route('store.support.index') }}" class="btn btn-light border px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="mdi mdi-send me-1"></i> Submit Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>