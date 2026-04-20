<x-app-layout title="Enquiries">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h3 class="h3 fw-bold m-0 ">Customer Enquiries</h3>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Enquiries</li>
                </ol>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-email-outline me-2 text-primary"></i>All Enquiries</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enquiries as $enquiry)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $enquiry->name }}</div>
                                    </td>
                                    <td>{{ $enquiry->email }}</td>
                                    <td>{{ $enquiry->subject }}</td>
                                    <td>{{ $enquiry->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if($enquiry->is_read)
                                            <span class="badge bg-soft-success text-success rounded-pill px-3">Read</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger rounded-pill px-3">New</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('store.enquiries.show', $enquiry->id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="View Enquiry">
                                                <i class="mdi mdi-eye fs-6"></i>
                                            </a>
                                            <form action="{{ route('store.enquiries.destroy', $enquiry->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-light border shadow-sm text-danger delete-btn" title="Delete Enquiry">
                                                    <i class="mdi mdi-trash-can fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No enquiries found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($enquiries->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $enquiries->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
