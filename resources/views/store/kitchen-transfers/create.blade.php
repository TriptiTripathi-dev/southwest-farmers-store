<x-app-layout title="Request Kitchen Transfer">
    @php $fmt = fn ($q) => rtrim(rtrim(number_format((float) $q, 2), '0'), '.'); @endphp
    <div class="content">
        <div class="container-fluid">

            <x-page-header>
                <a href="{{ route('kitchen-transfers.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Kitchen Transfers
                </a>
            </x-page-header>

            <div class="alert alert-info small">
                <i class="mdi mdi-information-outline me-1"></i>
                Ask for stock to move from this store's shelf to its kitchen. The request stays <strong>pending</strong> until an
                Area Manager approves it; the kitchen quantity changes only after approval. You can't approve your own request.
            </div>

            <form method="POST" action="{{ route('kitchen-transfers.store') }}" class="card border-0 shadow-sm rounded-3">
                @csrf
                <div class="card-body">
                    <table class="table align-middle mb-2" id="linesTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="small text-muted fw-bold">ITEM (FROM STORE SHELF)</th>
                                <th class="small text-muted fw-bold" style="width: 180px;">QUANTITY</th>
                                <th style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $old = old('items', [['product_id' => '', 'quantity' => '']]); @endphp
                            @foreach ($old as $idx => $line)
                                <tr class="line">
                                    <td>
                                        <select name="items[{{ $idx }}][product_id]" class="form-select" required>
                                            <option value="">-- Select item --</option>
                                            @foreach ($storeStocks as $s)
                                                <option value="{{ $s->product_id }}" @selected(($line['product_id'] ?? '') == $s->product_id)>
                                                    {{ $s->product->product_name ?? 'Unknown' }} (on shelf: {{ $fmt($s->quantity) }} {{ $s->product->unit ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0.01" name="items[{{ $idx }}][quantity]" value="{{ $line['quantity'] ?? '' }}" class="form-control" required></td>
                                    <td class="text-end"><button type="button" class="btn btn-outline-danger btn-sm remove-line" title="Remove"><i class="mdi mdi-close"></i></button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addLine"><i class="mdi mdi-plus me-1"></i> Add item</button>

                    <div class="mt-4">
                        <label class="form-label fw-semibold">Reason / notes</label>
                        <textarea name="notes" class="form-control" rows="2" maxlength="1000" placeholder="e.g. for Saturday's soup batch">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <button type="submit" class="btn btn-dark px-4"><i class="mdi mdi-send me-1"></i> Send for approval</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const tbody = document.querySelector('#linesTable tbody');
            let next = tbody.querySelectorAll('tr.line').length;
            document.getElementById('addLine').addEventListener('click', () => {
                const row = tbody.querySelector('tr.line').cloneNode(true);
                row.querySelectorAll('select, input').forEach(el => {
                    el.name = el.name.replace(/items\[\d+\]/, `items[${next}]`);
                    el.value = '';
                });
                tbody.appendChild(row);
                next++;
            });
            tbody.addEventListener('click', e => {
                const btn = e.target.closest('.remove-line');
                if (btn && tbody.querySelectorAll('tr.line').length > 1) btn.closest('tr').remove();
            });
        })();
    </script>
    @endpush
</x-app-layout>
