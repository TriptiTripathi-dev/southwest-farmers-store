<x-app-layout title="Contact Page Settings">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h4 class="h3 fw-bold m-0 ">Contact Page Settings</h4>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Contact Page</li>
                </ol>
            </div>

            <form action="{{ route('settings.contact-page.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Header Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-page-layout-header me-2 text-primary"></i>Header Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Badge Text</label>
                                <input type="text" name="header_badge" class="form-control bg-light border-0"
                                    value="{{ old('header_badge', $settings->header_badge ?? '👋 GET IN TOUCH') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Title</label>
                                <input type="text" name="header_title" class="form-control bg-light border-0"
                                    value="{{ old('header_title', $settings->header_title ?? 'How can we help?') }}">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium text-muted">Subtitle</label>
                            <textarea name="header_subtitle" class="form-control bg-light border-0" rows="3">{{ old('header_subtitle', $settings->header_subtitle ?? 'Have questions about our products or your order? Our team is here to provide dedicated support.') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Contact Info Cards --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="mb-0 fw-bold text-dark">Address Info</h6>
                            </div>
                            <div class="card-body p-4">
                                <label class="form-label fw-medium text-muted small">Title</label>
                                <input type="text" name="address_title" class="form-control form-control-sm bg-light border-0 mb-3"
                                    value="{{ old('address_title', $settings->address_title ?? 'Visit Our Store') }}">
                                <label class="form-label fw-medium text-muted small">Content (HTML allowed)</label>
                                <textarea name="address_content" class="form-control form-control-sm bg-light border-0" rows="4">{{ old('address_content', $settings->address_content ?? '123 Fresh Way, Organic Valley,<br/>Green City, GC 56789') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="mb-0 fw-bold text-dark">Phone Info</h6>
                            </div>
                            <div class="card-body p-4">
                                <label class="form-label fw-medium text-muted small">Title</label>
                                <input type="text" name="phone_title" class="form-control form-control-sm bg-light border-0 mb-3"
                                    value="{{ old('phone_title', $settings->phone_title ?? 'Call Us Anytime') }}">
                                <label class="form-label fw-medium text-muted small">Content (HTML allowed)</label>
                                <textarea name="phone_content" class="form-control form-control-sm bg-light border-0" rows="4">{{ old('phone_content', $settings->phone_content ?? '+1 (555) fresh-store<br/>Mon-Sat: 8am - 8pm') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="mb-0 fw-bold text-dark">Email Info</h6>
                            </div>
                            <div class="card-body p-4">
                                <label class="form-label fw-medium text-muted small">Title</label>
                                <input type="text" name="email_title" class="form-control form-control-sm bg-light border-0 mb-3"
                                    value="{{ old('email_title', $settings->email_title ?? 'Email Support') }}">
                                <label class="form-label fw-medium text-muted small">Content (HTML allowed)</label>
                                <textarea name="email_content" class="form-control form-control-sm bg-light border-0" rows="4">{{ old('email_content', $settings->email_content ?? 'hello@freshstore.com<br/>support@freshstore.com') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-form-select me-2 text-info"></i>Form Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label fw-medium text-muted">Form Title</label>
                        <input type="text" name="form_title" class="form-control bg-light border-0"
                            value="{{ old('form_title', $settings->form_title ?? 'Send us a Message') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-end pb-5 mt-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-sm fw-bold rounded-pill">
                        <i class="mdi mdi-content-save me-2"></i> Save Contact Page Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
