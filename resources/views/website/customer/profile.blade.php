<x-website-layout title="My Profile - Southwest Farmers">

    @push('styles')
    <style>
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        
        .account-card {
            border: 1px solid rgba(0, 154, 54, 0.12);
            border-radius: 1.5rem;
            transition: all 0.3s ease;
        }

        .avatar-large {
            width: 120px; height: 120px;
            background: linear-gradient(135deg, #019934, #00802b);
            --theme-primary: #019934;
            --theme-light: #e6fff0;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-weight: 900;
            font-size: 3rem;
            box-shadow: 0 10px 25px rgba(0, 154, 54, 0.3);
            object-fit: cover;
            border: 4px solid #fff;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
        }
        
        .image-upload-wrapper {
            position: relative;
            display: inline-block;
        }
        .image-upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #019934;
            color: white;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .image-upload-btn:hover {
            background: #00802b;
        }
    </style>
    @endpush

    <section class="py-5" style="min-height: 80vh; background: linear-gradient(135deg, var(--theme-light) 0%, #f8fafc 100%);">
        <div class="container py-4">
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-pill mb-4" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger rounded-4 mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="account-card bg-white p-5 animate-fade-up">
                        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-theme-light text-theme" style="width:52px;height:52px;">
                                <i class="mdi mdi-account-cog-outline fs-3"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">My Profile</h4>
                                <small class="text-muted">Update your personal information</small>
                            </div>
                        </div>

                        <form action="{{ route('website.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="text-center mb-5">
                                <div class="image-upload-wrapper">
                                    @if($customer->image)
                                        <img id="profilePreview" src="{{ Storage::disk('r2')->url($customer->image) }}" class="avatar-large" alt="Profile Image">
                                    @else
                                        <div id="profilePlaceholder" class="avatar-large">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                        <img id="profilePreview" src="" class="avatar-large d-none" alt="Profile Image">
                                    @endif
                                    
                                    <label for="imageUpload" class="image-upload-btn">
                                        <i class="mdi mdi-camera"></i>
                                    </label>
                                    <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*" onchange="previewImage(this)">
                                </div>
                                <div class="mt-2 text-muted small">Allowed formats: JPG, PNG, WEBP (Max 2MB)</div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Area/Locality</label>
                                    <input type="text" name="area" class="form-control" value="{{ old('area', $customer->area) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Full Address</label>
                                    <textarea name="address" class="form-control" rows="3">{{ old('address', $customer->address) }}</textarea>
                                </div>
                            </div>

                            <div class="mt-5 text-end">
                                <a href="{{ route('website.dashboard') }}" class="btn btn-light rounded-pill px-4 fw-bold me-2">Cancel</a>
                                <button type="submit" class="btn btn-theme rounded-pill px-4 fw-bold">
                                    <i class="mdi mdi-content-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>

    @push('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById('profilePreview');
                    var placeholder = document.getElementById('profilePlaceholder');
                    
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    
                    if(placeholder) {
                        placeholder.classList.add('d-none');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush

</x-website-layout>
