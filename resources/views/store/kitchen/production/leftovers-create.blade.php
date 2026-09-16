<x-app-layout title="Log Leftover">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">

            <div class="mb-4">
                <nav class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="page-breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('store.kitchen.production.leftovers') }}" class="page-breadcrumb-link">Leftover Food Report</a></li>
                        <li class="breadcrumb-item text-muted">Log Leftover</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-dark">Log Leftover</h4>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('store.kitchen.production.leftovers.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Menu Item <span class="text-danger">*</span></label>
                                <select name="menu_item_id" class="form-select" required>
                                    <option value="">-- Select Item --</option>
                                    @foreach($menuItems as $item)
                                        <option value="{{ $item->id }}" @selected(old('menu_item_id') == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="log_date" class="form-control" value="{{ old('log_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quantity Produced <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_produced" class="form-control" step="0.01" min="0" value="{{ old('quantity_produced') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quantity Sold <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_sold" class="form-control" step="0.01" min="0" value="{{ old('quantity_sold') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Leftover quantity is calculated automatically as Produced &minus; Sold.</p>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-bold px-4">Save Leftover Log</button>
                            <a href="{{ route('store.kitchen.production.leftovers') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
