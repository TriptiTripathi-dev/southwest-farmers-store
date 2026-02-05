<x-app-layout title="General Settings">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h4 class="h3 fw-bold m-0 text-white">General Settings</h4>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="#" class="text-white-50">Dashboard</a></li>
                    <li class="breadcrumb-item active text-white">Settings</li>
                </ol>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark">App Configuration</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">App Name</label>
                                <input type="text" name="app_name" class="form-control form-control-lg bg-light border-0" 
                                       value="{{ old('app_name', $settings->app_name) }}" placeholder="e.g. Store POS">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">App Phone</label>
                                <input type="text" name="app_phone" class="form-control form-control-lg bg-light border-0" 
                                       value="{{ old('app_phone', $settings->app_phone) }}" placeholder="e.g. +1 234 567 890">
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Support Email</label>
                                <input type="email" name="support_email" class="form-control form-control-lg bg-light border-0" 
                                       value="{{ old('support_email', $settings->support_email) }}" placeholder="support@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Address</label>
                                <input type="text" name="address" class="form-control form-control-lg bg-light border-0" 
                                       value="{{ old('address', $settings->address) }}" placeholder="e.g. New York, USA">
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 my-5">

                        <div class="row g-4 align-items-center mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted mb-2">Main Logo</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="border rounded p-2 bg-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <img id="logoPreview" 
                                             src="{{ $settings->logo ? asset('storage/'.$settings->logo) : 'https://placehold.co/100x100?text=Logo' }}" 
                                             alt="Logo" class="img-fluid" style="max-height: 100%;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="logo" class="form-control bg-light border-0" 
                                               onchange="previewImage(this, 'logoPreview')">
                                        <small class="text-muted d-block mt-1">Recommended size: 150x50px (PNG)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted mb-2">Favicon</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="border rounded p-2 bg-dark d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <img id="faviconPreview" 
                                             src="{{ $settings->favicon ? asset('storage/'.$settings->favicon) : 'https://placehold.co/50x50?text=Fav' }}" 
                                             alt="Favicon" class="img-fluid" style="max-height: 100%;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="favicon" class="form-control bg-light border-0" 
                                               onchange="previewImage(this, 'faviconPreview')">
                                        <small class="text-muted d-block mt-1">Recommended size: 32x32px (ICO/PNG)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Currency Symbol</label>
                                <input type="text" name="currency" class="form-control form-control-lg bg-light border-0" 
                                       value="{{ old('currency', $settings->currency) }}" placeholder="e.g. $, £, €">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">VAT Percentage (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="vat_percentage" class="form-control form-control-lg bg-light border-0" 
                                           value="{{ old('vat_percentage', $settings->vat_percentage) }}" placeholder="0.00">
                                    <span class="input-group-text bg-light border-0 text-muted">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted mb-2">Login Page Logo</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="border rounded p-2 bg-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <img id="loginLogoPreview" 
                                             src="{{ $settings->login_logo ? asset('storage/'.$settings->login_logo) : 'https://placehold.co/100x100?text=Login' }}" 
                                             alt="Login Logo" class="img-fluid" style="max-height: 100%;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="login_logo" class="form-control bg-light border-0" 
                                               onchange="previewImage(this, 'loginLogoPreview')">
                                        <small class="text-muted d-block mt-1">Logo displayed on sign-in screen</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-medium">
                                <i class="mdi mdi-content-save me-1"></i> Update Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-app-layout>