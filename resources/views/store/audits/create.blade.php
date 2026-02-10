<x-app-layout title="Start Audit">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Start New Physical Count</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('store.audits.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Audit Scope</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories (Full Store Audit)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select a category to audit only specific items, or leave blank for full inventory.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Monthly cycle count for Frozen section"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Generate Audit Sheet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>