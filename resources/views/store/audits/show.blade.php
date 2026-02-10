<x-app-layout title="Audit: {{ $audit->audit_number }}">
<div class="container-fluid">
    
    <form action="{{ route('store.audits.update', $audit->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Audit: {{ $audit->audit_number }}</h4>
                <span class="badge bg-{{ $audit->status == 'completed' ? 'success' : 'warning' }}">
                    {{ ucfirst($audit->status) }}
                </span>
            </div>
            @if($audit->status != 'completed')
                <div>
                    <button type="submit" name="save" value="1" class="btn btn-secondary me-2">Save Progress</button>
                    <button type="submit" name="finalize" value="1" class="btn btn-success" onclick="return confirm('This will finalize the audit and adjust inventory levels. Continue?')">Finalize & Adjust Stock</button>
                </div>
            @endif
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40%">Product</th>
                            <th class="text-center" style="width: 15%">System Qty</th>
                            <th class="text-center" style="width: 15%">Physical Qty</th>
                            <th class="text-center" style="width: 15%">Variance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audit->items as $item)
                        <tr class="{{ $item->variance_qty < 0 ? 'table-danger' : '' }}">
                            <td>
                                <strong>{{ $item->product->product_name }}</strong><br>
                                <small class="text-muted">{{ $item->product->sku }}</small>
                            </td>
                            <td class="text-center bg-light">
                                {{ $item->system_qty }}
                            </td>
                            <td class="text-center">
                                @if($audit->status == 'completed')
                                    <strong>{{ $item->physical_qty }}</strong>
                                @else
                                    <input type="number" 
                                           name="items[{{ $item->id }}]" 
                                           value="{{ $item->physical_qty ?? '' }}" 
                                           class="form-control text-center audit-input"
                                           placeholder="0">
                                @endif
                            </td>
                            <td class="text-center fw-bold {{ $item->variance_qty < 0 ? 'text-danger' : ($item->variance_qty > 0 ? 'text-success' : '') }}">
                                {{ $item->variance_qty > 0 ? '+' : '' }}{{ $item->variance_qty }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
</x-app-layout>