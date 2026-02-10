<x-app-layout title="Create Promotion">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Create New Campaign</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('store.promotions.store') }}" method="POST">
                        @csrf
                        
                        {{-- Basic Info --}}
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Campaign Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Summer Sale, Clearance" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Promo Code (Optional)</label>
                                <input type="text" name="code" class="form-control" placeholder="Leave empty for Auto-Apply">
                            </div>
                        </div>

                        {{-- Discount Logic --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Discount Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="percentage">Percentage (%) Off</option>
                                    <option value="fixed_amount">Fixed Amount ($) Off</option>
                                    <option value="bogo">Buy 1 Get 1 Free</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Value</label>
                                <input type="number" name="value" class="form-control" step="0.01" placeholder="e.g. 10 for 10%" required>
                            </div>
                        </div>

                        {{-- Scope (Targeting) --}}
                        <div class="mb-3">
                            <label class="form-label">Apply To</label>
                            <select name="scope" id="scopeSelector" class="form-select" onchange="toggleScope()">
                                <option value="global">Entire Store (Global)</option>
                                <option value="category">Specific Category</option>
                                <option value="product">Specific Product</option>
                            </select>
                        </div>

                        {{-- Hidden Inputs for Scope --}}
                        <div class="mb-3 d-none" id="categoryInput">
                            <label class="form-label">Select Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="productInput">
                            <label class="form-label">Select Product</label>
                            <select name="product_id" class="form-select">
                                <option value="">-- Select Product --</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->product_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dates --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('store.promotions.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Launch Campaign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleScope() {
        let scope = document.getElementById('scopeSelector').value;
        document.getElementById('categoryInput').classList.add('d-none');
        document.getElementById('productInput').classList.add('d-none');

        if(scope === 'category') {
            document.getElementById('categoryInput').classList.remove('d-none');
        } else if (scope === 'product') {
            document.getElementById('productInput').classList.remove('d-none');
        }
    }
</script>
</x-app-layout>