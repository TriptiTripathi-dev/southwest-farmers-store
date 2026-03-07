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
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-cash-register me-2 text-primary"></i>POS Header</h5>
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

                <div class="d-flex justify-content-end pb-5 mt-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-sm fw-bold rounded-pill">
                        <i class="mdi mdi-content-save me-2"></i> Save POS Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
