<x-app-layout title="Physical Audit">
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">Monthly Cycle Count</div>
        <div class="card-body">
            <form action="{{ route('store.inventory.audit.submit') }}" method="POST">
                @csrf
                <table class="table">
                    <thead><tr><th>Product</th><th>System Qty</th><th>Physical Count</th></tr></thead>
                    <tbody>
                        @foreach($products as $p)
                        <tr>
                            <td>{{ $p->product->product_name }} <small>({{ $p->product->sku }})</small></td>
                            <td>{{ $p->quantity }}</td>
                            <td>
                                <input type="hidden" name="products[{{$p->product_id}}][id]" value="{{$p->product_id}}">
                                <input type="number" name="products[{{$p->product_id}}][physical_qty]" class="form-control" value="{{ $p->quantity }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button class="btn btn-success mt-3">Submit Audit</button>
            </form>
        </div>
    </div>
</div>
</x-app-layout>