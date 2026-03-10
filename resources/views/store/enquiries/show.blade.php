<x-app-layout title="Enquiry Details">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('store.enquiries.index') }}" class="btn btn-soft-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="mdi mdi-arrow-left fs-4"></i>
                    </a>
                    <h3 class="h3 fw-bold m-0 ">Enquiry Details</h3>
                </div>
                <ol class="breadcrumb mt-2 ms-5">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('store.enquiries.index') }}">Enquiries</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-email-open-outline me-2 text-primary"></i>Message Content</h5>
                            <span class="text-muted small">{{ $enquiry->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Subject</label>
                                <h5 class="fw-bold text-dark">{{ $enquiry->subject }}</h5>
                            </div>
                            <hr class="my-4 opacity-25">
                            <div>
                                <label class="form-label fw-bold text-muted small text-uppercase mb-3">Message Body</label>
                                <div class="bg-light rounded-4 p-4 text-dark" style="white-space: pre-wrap; line-height: 1.6;">{{ $enquiry->message }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-account-circle-outline me-2 text-info"></i>Sender Info</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Full Name</label>
                                <div class="h6 fw-bold text-dark mb-0">{{ $enquiry->name }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Email Address</label>
                                <div class="h6 fw-bold text-dark mb-0">
                                    <a href="mailto:{{ $enquiry->email }}" class="text-primary text-decoration-none">
                                        {{ $enquiry->email }} <i class="mdi mdi-open-in-new small ms-1"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold text-muted small text-uppercase">Status</label>
                                <div>
                                    @if($enquiry->is_read)
                                        <span class="badge bg-soft-success text-success rounded-pill px-3">Read</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger rounded-pill px-3">New</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top py-3">
                            <form action="{{ route('store.enquiries.destroy', $enquiry->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this enquiry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold">
                                    <i class="mdi mdi-trash-can-outline me-2"></i> Delete Enquiry
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
