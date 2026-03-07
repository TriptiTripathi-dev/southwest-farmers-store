<x-app-layout title="{{ isset($page) ? 'Edit' : 'Create' }} Legal Page">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h4 class="h3 fw-bold m-0 ">{{ isset($page) ? 'Edit' : 'Create' }} Legal Page</h4>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('settings.legal.index') }}">Legal Pages</a></li>
                    <li class="breadcrumb-item active">{{ isset($page) ? 'Edit' : 'Create' }}</li>
                </ol>
            </div>

            <form action="{{ isset($page) ? route('settings.legal.update', $page->id) : route('settings.legal.store') }}" method="POST">
                @csrf
                @if(isset($page))
                    @method('PUT')
                @endif

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Page Title</label>
                                <input type="text" name="title" class="form-control bg-light border-0" id="title"
                                    value="{{ old('title', $page->title ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Slug</label>
                                <input type="text" name="slug" class="form-control bg-light border-0" id="slug"
                                    value="{{ old('slug', $page->slug ?? '') }}" required>
                                <small class="text-muted">Use <code>privacy-policy</code> or <code>terms-and-conditions</code></small>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium text-muted">Page Content</label>
                            <textarea name="content" class="form-control bg-light border-0 editor" rows="15">{{ old('content', $page->content ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pb-5 mt-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-sm fw-bold rounded-pill">
                        <i class="mdi mdi-content-save me-2"></i> Save Page
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            CKEDITOR.replace(document.querySelector('.editor'), {
                height: 400
            });

            @if(!isset($page))
            document.getElementById('title').addEventListener('input', function() {
                let slug = this.value.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('slug').value = slug;
            });
            @endif
        });
    </script>
    @endpush
</x-app-layout>
