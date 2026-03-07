<x-app-layout title="Legal Pages">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h4 class="h3 fw-bold m-0 ">Legal Pages</h4>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Legal Pages</li>
                </ol>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-file-document-outline me-2 text-primary"></i>Pages List</h5>
                    <a href="{{ route('settings.legal.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="mdi mdi-plus me-1"></i> Add New Page
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Title</th>
                                    <th>Slug</th>
                                    <th>Last Updated</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pages as $page)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $page->title }}</td>
                                    <td><code>{{ $page->slug }}</code></td>
                                    <td>{{ $page->updated_at->format('M d, Y H:i') }}</td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('settings.legal.edit', $page->id) }}" class="btn btn-sm btn-light-primary btn-icon rounded-circle">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('settings.legal.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light-danger btn-icon rounded-circle">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No legal pages found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
