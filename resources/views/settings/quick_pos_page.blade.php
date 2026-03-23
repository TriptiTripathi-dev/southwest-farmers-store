<x-app-layout title="Quick POS Settings">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h4 class="h3 fw-bold m-0 ">Quick POS Settings</h4>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Quick POS</li>
                </ol>
            </div>

            <form action="{{ route('settings.quick-pos.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-cash-register me-2 text-primary"></i>POS
                            Header</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0"
                                value="{{ old('title', $settings->title ?? 'Quick Shop POS') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium text-muted">Subtitle/Description</label>
                            <textarea name="subtitle" class="form-control bg-light border-0" rows="3">{{ old('subtitle', $settings->subtitle ?? 'Quickly browse and add products to your cart.') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div
                        class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark"><i
                                class="mdi mdi-access-point-network me-2 text-primary"></i>Hardware Integration</h5>
                        <div>
                            @if (!$isTerminalConnected)
                                <span class="badge bg-danger ms-2"><i class="mdi mdi-circle-medium"></i> Offline</span>
                            @else
                                <span
                                    class="badge {{ $store->pos_terminal_status === 'online' ? 'bg-success' : 'bg-warning' }} ms-2">
                                    <i class="mdi mdi-check-circle"></i> {{ ucfirst($store->pos_terminal_status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium text-muted">POS Terminal ID (Required for
                                    Server)</label>
                                <input type="text" name="pos_terminal_id" class="form-control bg-light border-0"
                                    value="{{ old('pos_terminal_id', $store->pos_terminal_id ?? '') }}"
                                    placeholder="e.g. TERM-2B02D153AF6C">
                                <small class="text-muted">Save the Terminal ID first before connecting to the
                                    server.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium text-muted">POS Agent Secret Key</label>
                                <input type="password" name="pos_agent_secret" class="form-control bg-light border-0"
                                    value="{{ old('pos_agent_secret', $store->pos_agent_secret ?? '') }}"
                                    placeholder="Enter your security secret">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium text-muted">POS Agent Store ID</label>
                                <input type="text" name="pos_store_id" class="form-control bg-light border-0"
                                    value="{{ old('pos_store_id', $store->pos_store_id ?? '') }}"
                                    placeholder="e.g. 0500039605484205000396054842">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium text-muted">Local Hardware Device URL</label>
                                <input type="url" name="pos_hardware_url" class="form-control bg-light border-0"
                                    value="{{ old('pos_hardware_url', $store->pos_hardware_url ?? 'http://localhost:3001') }}"
                                    placeholder="http://localhost:3001">
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                @if (!$isTerminalConnected && !empty($store->pos_terminal_id))
                                    <!-- Register Terminal Button -->
                                    <button type="button" class="btn btn-warning px-4 fw-bold"
                                        onclick="document.getElementById('connect-server-form').submit();">
                                        <i class="mdi mdi-link"></i> Register Terminal
                                    </button>
                                @endif

                                @if ($isTerminalConnected)
                                    <span class="text-success fw-bold ms-5 mt-2"><i class="mdi mdi-check-circle"></i>
                                        Terminal Registered</span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="printer_enabled" id="printer_enabled"
                                        {{ old('printer_enabled', $settings->printer_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 mt-1" for="printer_enabled">Receipt
                                        Printer</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="scanner_enabled" id="scanner_enabled"
                                        {{ old('scanner_enabled', $settings->scanner_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 mt-1" for="scanner_enabled">Barcode
                                        Scanner</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" role="switch" name="scale_enabled"
                                        id="scale_enabled"
                                        {{ old('scale_enabled', $settings->scale_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 mt-1" for="scale_enabled">Weighing Scale</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="cash_drawer_enabled" id="cash_drawer_enabled"
                                        {{ old('cash_drawer_enabled', $settings->cash_drawer_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 mt-1" for="cash_drawer_enabled">Cash
                                        Drawer</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="auto_print_receipt" id="auto_print_receipt"
                                        {{ old('auto_print_receipt', $settings->auto_print_receipt) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 mt-1" for="auto_print_receipt">Auto Print
                                        Receipt</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="pax_enabled" id="pax_enabled"
                                        {{ old('pax_enabled', $settings->pax_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 mt-1 text-primary fw-bold" for="pax_enabled">PAX Card Payment</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pb-5 mt-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-sm fw-bold rounded-pill">
                        <i class="mdi mdi-content-save me-2"></i> Save POS Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Connect Form -->
    <form id="connect-server-form" action="{{ route('settings.quick-pos.connect') }}" method="POST"
        style="display: none;">
        @csrf
    </form>
</x-app-layout>
