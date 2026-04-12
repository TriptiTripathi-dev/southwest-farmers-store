<x-app-layout title="Home Page Settings">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h4 class="h3 fw-bold m-0 ">Home Page Settings</h4>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Home Page</li>
                </ol>
            </div>

            <form action="{{ route('settings.home-page.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Hero Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-rocket-launch me-2 text-primary"></i>Hero Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Hero Badge Text</label>
                                <input type="text" name="hero_badge" class="form-control bg-light border-0"
                                    value="{{ old('hero_badge', $settings->hero_badge ?? '✨ THE MODERN GROCERY EXPERIENCE') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Hero Title</label>
                                <input type="text" name="hero_title" class="form-control bg-light border-0"
                                    value="{{ old('hero_title', $settings->hero_title ?? 'Freshness Redefined for Your Home.') }}">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control bg-light border-0 editor" rows="3">{{ old('hero_subtitle', $settings->hero_subtitle ?? 'Experience the pinnacle of quality with our curated selection of organic produce and daily essentials.') }}</textarea>
                        </div>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Button Text</label>
                                <input type="text" name="hero_button_text" class="form-control bg-light border-0"
                                    value="{{ old('hero_button_text', $settings->hero_button_text ?? 'Browse Products') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Button URL</label>
                                <input type="text" name="hero_button_url" class="form-control bg-light border-0"
                                    value="{{ old('hero_button_url', $settings->hero_button_url ?? route('website.products.index')) }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted mb-2">Hero Image</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="border rounded p-2 bg-white d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                    <img id="heroPreview"
                                        src="{{ $settings->hero_image ? Storage::disk('r2')->url($settings->hero_image) : 'https://placehold.co/400x400?text=Hero' }}"
                                        alt="Hero Image" class="img-fluid" style="max-height: 100%;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="hero_image" class="form-control bg-light border-0"
                                        onchange="previewImage(this, 'heroPreview')">
                                    <small class="text-muted d-block mt-1">Large high-quality image. Recommended: 800x800px</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Features Section Header --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-star-circle me-2 text-warning"></i>Features Section Header</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">Section Title</label>
                            <input type="text" name="features_title" class="form-control bg-light border-0"
                                value="{{ old('features_title', $settings->features_title ?? 'Why Shop With Us?') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium text-muted">Section Subtitle</label>
                            <textarea name="features_subtitle" class="form-control bg-light border-0" rows="2">{{ old('features_subtitle', $settings->features_subtitle ?? "We've optimized every step of the process to ensure you get the freshest items at the best prices.") }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Features Grid --}}
                <div class="row g-4 mb-4">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="mb-0 fw-bold text-dark">Feature {{ $i }}</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted small">Icon (MDI Class)</label>
                                    <input type="text" name="feature_{{ $i }}_icon" class="form-control form-control-sm bg-light border-0"
                                        value="{{ old('feature_'.$i.'_icon', $settings->{'feature_'.$i.'_icon'} ?? ($i==1 ? 'mdi-leaf-circle-outline' : ($i==2 ? 'mdi-shield-check-outline' : 'mdi-clock-fast'))) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted small">Title</label>
                                    <input type="text" name="feature_{{ $i }}_title" class="form-control form-control-sm bg-light border-0"
                                        value="{{ old('feature_'.$i.'_title', $settings->{'feature_'.$i.'_title'} ?? ($i==1 ? 'Eco-Friendly' : ($i==2 ? 'Quality First' : 'Always On Time'))) }}">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-medium text-muted small">Description</label>
                                    <textarea name="feature_{{ $i }}_text" class="form-control form-control-sm bg-light border-0" rows="3">{{ old('feature_'.$i.'_text', $settings->{'feature_'.$i.'_text'} ?? ($i==1 ? '100% plastic-free packaging options.' : ($i==2 ? '5-point quality check.' : 'Real-time tracking.'))) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- Trending Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-trending-up me-2 text-success"></i>Trending Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Trending Title</label>
                                <input type="text" name="trending_title" class="form-control bg-light border-0"
                                    value="{{ old('trending_title', $settings->trending_title ?? 'Trending Products') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Trending Subtitle</label>
                                <input type="text" name="trending_subtitle" class="form-control bg-light border-0"
                                    value="{{ old('trending_subtitle', $settings->trending_subtitle ?? "The local community's favorites this week.") }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CTA Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-bullhorn me-2 text-danger"></i>CTA Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">CTA Title</label>
                            <input type="text" name="cta_title" class="form-control bg-light border-0"
                                value="{{ old('cta_title', $settings->cta_title ?? 'Ready for a Healthy Change?') }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">CTA Subtitle</label>
                            <textarea name="cta_subtitle" class="form-control bg-light border-0 editor" rows="2">{{ old('cta_subtitle', $settings->cta_subtitle ?? 'Join thousands of families getting farm-fresh organics delivered straight to their kitchen.') }}</textarea>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label fw-medium text-muted small">Button 1 Text</label>
                                <input type="text" name="cta_button_1_text" class="form-control bg-light border-0"
                                    value="{{ old('cta_button_1_text', $settings->cta_button_1_text ?? 'Join us Today') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium text-muted small">Button 1 URL</label>
                                <input type="text" name="cta_button_1_url" class="form-control bg-light border-0"
                                    value="{{ old('cta_button_1_url', $settings->cta_button_1_url ?? route('website.register')) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium text-muted small">Button 2 Text</label>
                                <input type="text" name="cta_button_2_text" class="form-control bg-light border-0"
                                    value="{{ old('cta_button_2_text', $settings->cta_button_2_text ?? 'Shop Now') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium text-muted small">Button 2 URL</label>
                                <input type="text" name="cta_button_2_url" class="form-control bg-light border-0"
                                    value="{{ old('cta_button_2_url', $settings->cta_button_2_url ?? route('website.products.index')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pb-5 mt-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-sm fw-bold rounded-pill">
                        <i class="mdi mdi-content-save me-2"></i> Save Home Page Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editors = document.querySelectorAll('.editor');
            editors.forEach(function(editor) {
                CKEDITOR.replace(editor, {
                    removeButtons: 'Source,Save,NewPage,ExportPdf,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CopyFormatting,RemoveFormat,Subscript,Superscript,Outdent,Indent,Blockquote,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Link,Unlink,Anchor,Image,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Font,FontSize,TextColor,BGColor,Maximize,ShowBlocks,About'
                });
            });
        });

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
