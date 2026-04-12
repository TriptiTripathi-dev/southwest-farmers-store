<x-app-layout title="About Page Settings">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h4 class="h3 fw-bold m-0 ">About Page Settings</h4>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">About Page</li>
                </ol>
            </div>

            <form action="{{ route('settings.about-page.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Hero Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-rocket-launch me-2 text-primary"></i>Hero Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">Hero Title</label>
                            <input type="text" name="hero_title" class="form-control bg-light border-0"
                                value="{{ old('hero_title', $settings->hero_title ?? 'Our Growing Story') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium text-muted">Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control bg-light border-0" rows="3">{{ old('hero_subtitle', $settings->hero_subtitle ?? 'Empowering farmers and nourishing communities since 1995.') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Mission Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-target me-2 text-success"></i>Mission Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Mission Badge</label>
                                <input type="text" name="mission_badge" class="form-control bg-light border-0"
                                    value="{{ old('mission_badge', $settings->mission_badge ?? 'SINCE 1995') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted">Mission Title</label>
                                <input type="text" name="mission_title" class="form-control bg-light border-0"
                                    value="{{ old('mission_title', $settings->mission_title ?? 'Nurturing the Land, Empowering People') }}">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">Mission Text</label>
                            <textarea name="mission_text" class="form-control bg-light border-0 editor" rows="4">{{ old('mission_text', $settings->mission_text ?? 'Southwest Farmers Store began as a small family-owned cooperative...') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted mb-2">Mission Image</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="border rounded p-2 bg-white d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                    <img id="missionPreview"
                                        src="{{ $settings->mission_image ? Storage::disk('r2')->url($settings->mission_image) : 'https://placehold.co/400x400?text=Mission' }}"
                                        alt="Mission Image" class="img-fluid" style="max-height: 100%;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="mission_image" class="form-control bg-light border-0"
                                        onchange="previewImage(this, 'missionPreview')">
                                    <small class="text-muted d-block mt-1">Recommended: 1200x800px</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-chart-bar me-2 text-info"></i>Stats Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            @for($i = 1; $i <= 4; $i++)
                            <div class="col-md-3">
                                <label class="form-label fw-medium text-muted small">Stat {{ $i }} Value</label>
                                <input type="text" name="stat_{{ $i }}_value" class="form-control bg-light border-0 mb-2"
                                    value="{{ old('stat_'.$i.'_value', $settings->{'stat_'.$i.'_value'} ?? ($i==1 ? '25+' : ($i==2 ? '10k+' : ($i==3 ? '500+' : '50+')))) }}">
                                <label class="form-label fw-medium text-muted small">Stat {{ $i }} Label</label>
                                <input type="text" name="stat_{{ $i }}_label" class="form-control bg-light border-0"
                                    value="{{ old('stat_'.$i.'_label', $settings->{'stat_'.$i.'_label'} ?? ($i==1 ? 'Years of Trust' : ($i==2 ? 'Farmers Served' : ($i==3 ? 'Products' : 'Local Partners')))) }}">
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Values Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-heart-outline me-2 text-danger"></i>Values Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">Values Title</label>
                            <input type="text" name="values_title" class="form-control bg-light border-0"
                                value="{{ old('values_title', $settings->values_title ?? 'Our Core Values') }}">
                        </div>
                        <div class="row g-4">
                            @for($i = 1; $i <= 3; $i++)
                            <div class="col-md-4">
                                <div class="p-3 border rounded">
                                    <label class="form-label fw-medium text-muted small">Value {{ $i }} Icon (MDI)</label>
                                    <input type="text" name="value_{{ $i }}_icon" class="form-control form-control-sm bg-light border-0 mb-2"
                                        value="{{ old('value_'.$i.'_icon', $settings->{'value_'.$i.'_icon'} ?? ($i==1 ? 'mdi-heart' : ($i==2 ? 'mdi-lightbulb-on' : 'mdi-account-group'))) }}">
                                    <label class="form-label fw-medium text-muted small">Value {{ $i }} Title</label>
                                    <input type="text" name="value_{{ $i }}_title" class="form-control form-control-sm bg-light border-0 mb-2"
                                        value="{{ old('value_'.$i.'_title', $settings->{'value_'.$i.'_title'} ?? ($i==1 ? 'Integrity' : ($i==2 ? 'Innovation' : 'Community'))) }}">
                                    <label class="form-label fw-medium text-muted small">Value {{ $i }} Description</label>
                                    <textarea name="value_{{ $i }}_text" class="form-control form-control-sm bg-light border-0" rows="3">{{ old('value_'.$i.'_text', $settings->{'value_'.$i.'_text'} ?? '...') }}</textarea>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- CTA Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-bullhorn me-2 text-warning"></i>CTA Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted">CTA Title</label>
                            <input type="text" name="cta_title" class="form-control bg-light border-0"
                                value="{{ old('cta_title', $settings->cta_title ?? 'Ready to grow with us?') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium text-muted">CTA Subtitle</label>
                            <textarea name="cta_subtitle" class="form-control bg-light border-0" rows="2">{{ old('cta_subtitle', $settings->cta_subtitle ?? 'Join the thousands of farmers who trust Southwest Farmers Store.') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pb-5 mt-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-sm fw-bold rounded-pill">
                        <i class="mdi mdi-content-save me-2"></i> Save About Page Settings
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
