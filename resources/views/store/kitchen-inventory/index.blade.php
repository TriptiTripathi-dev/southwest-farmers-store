<x-app-layout title="Kitchen Inventory">
    @php $fmt = fn ($q) => rtrim(rtrim(number_format((float) $q, 2), '0'), '.'); @endphp
    <div class="content">
        <div class="container-fluid">

            <x-page-header>
                <a href="{{ route('kitchen-inventory.history') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm me-2">
                    <i class="mdi mdi-history me-1"></i> History
                </a>
                <a href="{{ route('kitchen-transfers.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm me-2">
                    <i class="mdi mdi-clipboard-list-outline me-1"></i> Transfers
                </a>
                @if (auth()->user()->hasPermission('kitchen_transfer_request'))
                    <a href="{{ route('kitchen-transfers.create') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                        <i class="mdi mdi-swap-horizontal me-2"></i> Request Transfer
                    </a>
                @endif
            </x-page-header>

            {{-- Summary --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3"><div class="card-body py-3">
                        <div class="text-muted small fw-bold text-uppercase">Kitchen items</div>
                        <div class="fs-3 fw-bold">{{ $stats['items'] }}</div>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3"><div class="card-body py-3">
                        <div class="text-muted small fw-bold text-uppercase">Below minimum</div>
                        <div class="fs-3 fw-bold {{ $stats['below_min'] ? 'text-warning' : '' }}">{{ $stats['below_min'] }}</div>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3"><div class="card-body py-3">
                        <div class="text-muted small fw-bold text-uppercase">Out of stock</div>
                        <div class="fs-3 fw-bold {{ $stats['out'] ? 'text-danger' : '' }}">{{ $stats['out'] }}</div>
                    </div></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">PRODUCT</th>
                                    <th class="py-3 text-muted small fw-bold text-end">ON HAND</th>
                                    <th class="py-3 text-muted small fw-bold text-end">RESERVED</th>
                                    <th class="py-3 text-muted small fw-bold text-end">AVAILABLE</th>
                                    <th class="py-3 text-muted small fw-bold text-end">MINIMUM</th>
                                    <th class="py-3 text-muted small fw-bold">STATUS</th>
                                    <th class="py-3 text-muted small fw-bold">UNIT</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kitchenStocks as $stock)
                                    @php
                                        $badge = ['Available' => 'bg-success', 'Below Minimum' => 'bg-warning text-dark', 'Out of Stock' => 'bg-danger'][$stock->stock_status];
                                        $name = $stock->product->product_name ?? 'Unknown product';
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $name }}</td>
                                        <td class="text-end">{{ $fmt($stock->quantity) }}</td>
                                        <td class="text-end text-muted">{{ $fmt($stock->reserved_quantity) }}</td>
                                        <td class="text-end fw-bold">{{ $fmt($stock->available_quantity) }}</td>
                                        <td class="text-end text-muted">{{ (float) $stock->min_quantity > 0 ? $fmt($stock->min_quantity) : '—' }}</td>
                                        <td><span class="badge {{ $badge }}">{{ $stock->stock_status }}</span></td>
                                        <td class="text-muted">{{ $stock->unit ?? '—' }}</td>
                                        <td class="pe-4 text-end">
                                            <div class="btn-group btn-group-sm">
                                                @foreach (['receive' => ['Receive', 'mdi-tray-arrow-down'], 'use' => ['Use', 'mdi-silverware-fork-knife'], 'adjust' => ['Adjust', 'mdi-plus-minus-variant'], 'waste' => ['Waste', 'mdi-delete-outline'], 'count' => ['Count', 'mdi-counter']] as $action => [$label, $icon])
                                                    <button type="button" class="btn btn-outline-secondary js-movement"
                                                        data-url="{{ route('kitchen-inventory.movement', $stock->id) }}"
                                                        data-action="{{ $action }}" data-label="{{ $label }}"
                                                        data-name="{{ $name }}" data-qty="{{ $fmt($stock->quantity) }}" data-unit="{{ $stock->unit }}"
                                                        title="{{ $label }}"><i class="mdi {{ $icon }}"></i> {{ $label }}</button>
                                                @endforeach
                                                <button type="button" class="btn btn-outline-secondary js-levels"
                                                    data-url="{{ route('kitchen-inventory.levels', $stock->id) }}" data-name="{{ $name }}"
                                                    data-min="{{ $fmt($stock->min_quantity) }}" data-reserved="{{ $fmt($stock->reserved_quantity) }}"
                                                    title="Minimum / reserved"><i class="mdi mdi-tune"></i> Levels</button>
                                                <a class="btn btn-outline-secondary" href="{{ route('kitchen-inventory.history', ['stock' => $stock->id]) }}" title="History"><i class="mdi mdi-history"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            No stock has been transferred to the kitchen yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <p class="text-muted small mt-2 mb-0">Available = On Hand − Reserved. Every change is recorded in History with who did it and when. Stock comes from the store shelf through a transfer request approved by an Area Manager.</p>

        </div>
    </div>

    {{-- Receive / Use / Adjust / Waste / Count --}}
    <div class="modal fade" id="movementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="movementForm">
                    @csrf
                    <input type="hidden" name="action" id="mvAction">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mvTitle">Kitchen stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small" id="mvHelp"></p>

                        <div class="mb-3 d-none" id="mvDirectionGroup">
                            <label class="form-label fw-semibold">Adjustment</label>
                            <div class="d-flex gap-4">
                                <div class="form-check"><input class="form-check-input" type="radio" name="direction" value="in" id="mvDirIn"><label class="form-check-label" for="mvDirIn">Add stock (+)</label></div>
                                <div class="form-check"><input class="form-check-input" type="radio" name="direction" value="out" id="mvDirOut"><label class="form-check-label" for="mvDirOut">Remove stock (−)</label></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" id="mvQtyLabel">Quantity</label>
                            <input type="number" step="0.01" min="0" name="quantity" id="mvQty" class="form-control" required>
                        </div>

                        <div class="mb-3 d-none" id="mvReasonGroup">
                            <label class="form-label fw-semibold">Reason</label>
                            <select name="reason" id="mvReason" class="form-select no-select2">
                                <option value="">-- Choose a reason --</option>
                            </select>
                        </div>

                        <div class="mb-1">
                            <label class="form-label fw-semibold">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="notes" class="form-control" maxlength="500">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark" id="mvSubmit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Minimum / Reserved --}}
    <div class="modal fade" id="levelsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="levelsForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="lvTitle">Levels</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Minimum quantity</label>
                            <input type="number" step="0.01" min="0" name="min_quantity" id="lvMin" class="form-control" required>
                            <small class="text-muted">Status shows "Below Minimum" when Available drops under this. 0 = no minimum.</small>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold">Reserved quantity</label>
                            <input type="number" step="0.01" min="0" name="reserved_quantity" id="lvReserved" class="form-control">
                            <small class="text-muted">Held back for orders already taken (e.g. a catering order). Available = On Hand − Reserved.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const reasons = {
                waste: @json($wasteReasons),
                adjust: @json($adjustReasons),
            };
            const help = {
                receive: 'Stock arriving in the kitchen from outside the store shelf (e.g. a delivery). For stock from the shelf, use Transfer to Kitchen.',
                use: 'Stock used in cooking. This lowers the kitchen quantity.',
                adjust: 'Correct the kitchen quantity up or down. A reason is required.',
                waste: 'Stock thrown away. A reason is required.',
                count: 'Enter what you physically counted. The difference is recorded as a count correction.',
            };

            document.querySelectorAll('.js-movement').forEach(btn => btn.addEventListener('click', () => {
                const a = btn.dataset.action;
                const form = document.getElementById('movementForm');
                form.action = btn.dataset.url;
                form.reset();
                document.getElementById('mvAction').value = a;
                document.getElementById('mvTitle').textContent = btn.dataset.label + ': ' + btn.dataset.name;
                document.getElementById('mvHelp').textContent = help[a] + ' Kitchen now has ' + btn.dataset.qty + (btn.dataset.unit ? ' ' + btn.dataset.unit : '') + '.';
                document.getElementById('mvQtyLabel').textContent = a === 'count' ? 'Counted quantity' : 'Quantity';
                document.getElementById('mvQty').min = a === 'count' ? '0' : '0.01';
                document.getElementById('mvSubmit').textContent = btn.dataset.label;

                const dir = document.getElementById('mvDirectionGroup');
                dir.classList.toggle('d-none', a !== 'adjust');
                dir.querySelectorAll('input').forEach(i => i.required = a === 'adjust');

                const rg = document.getElementById('mvReasonGroup'), sel = document.getElementById('mvReason');
                const list = reasons[a] || null;
                rg.classList.toggle('d-none', !list);
                sel.required = !!list;
                sel.innerHTML = '<option value="">-- Choose a reason --</option>' + (list || []).map(r => `<option value="${r}">${r}</option>`).join('');

                bootstrap.Modal.getOrCreateInstance(document.getElementById('movementModal')).show();
            }));

            document.querySelectorAll('.js-levels').forEach(btn => btn.addEventListener('click', () => {
                const form = document.getElementById('levelsForm');
                form.action = btn.dataset.url;
                document.getElementById('lvTitle').textContent = 'Levels: ' + btn.dataset.name;
                document.getElementById('lvMin').value = btn.dataset.min;
                document.getElementById('lvReserved').value = btn.dataset.reserved;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('levelsModal')).show();
            }));
        })();
    </script>
    @endpush
</x-app-layout>
