<x-app-layout title="Log New Production">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">

            <div class="mb-4">
                <nav class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="page-breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('store.kitchen.production.index') }}" class="page-breadcrumb-link">Production Logs</a></li>
                        <li class="breadcrumb-item text-muted">Log New Production</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-dark">Log New Production</h4>
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
                    <form action="{{ route('store.kitchen.production.store') }}" method="POST">
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
                                <label class="form-label fw-semibold">Time of Production <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="produced_at" class="form-control" required value="{{ old('produced_at', now()->format('Y-m-d\TH:i')) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Quantity Made <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_made" class="form-control" step="0.01" value="{{ old('quantity_made') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Unit</label>
                                <select name="quantity_unit" class="form-select">
                                    <option value="batch">Batch</option>
                                    <option value="kg">KG</option>
                                    <option value="litres">Litres</option>
                                    <option value="trays">Trays</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Yield (Plates / Portions)</label>
                                <input type="number" name="yield_plates" class="form-control" step="0.01" value="{{ old('yield_plates') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Today's Target Quantity</label>
                                <input type="number" name="daily_target" class="form-control" min="0" value="{{ old('daily_target') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-bold px-4">Save Production Log</button>
                            <a href="{{ route('store.kitchen.production.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
