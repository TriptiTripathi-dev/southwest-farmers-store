<x-website-layout title="Create Account - Southwest Farmers">

    @push('styles')
    <style>
        /* Custom Animations */
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) forwards; opacity: 0; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .floating-badge        { animation: float 4s ease-in-out infinite; }
        .floating-badge-delayed { animation: float 4s ease-in-out infinite; animation-delay: 2s; }

        /* Custom inputs */
        .field-group { position: relative; margin-bottom: 1.1rem; }
        .field-icon {
            position: absolute; left: 1rem; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; font-size: 1.2rem; z-index: 5;
            pointer-events: none;
            transition: color 0.25s;
        }
        .field-group:focus-within .field-icon { color: var(--theme-primary); }
        .custom-field {
            border: 2px solid #e2e8f0;
            border-radius: 0.9rem;
            padding: 0.8rem 1rem 0.8rem 2.9rem;
            background: #f8fafc;
            font-weight: 500;
            transition: all 0.25s ease;
            width: 100%;
        }
        .custom-field:focus {
            outline: none;
            border-color: var(--theme-primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(1, 153, 52, 0.12);
        }
        .custom-field.is-invalid { border-color: #ef4444; }
        .custom-field.is-invalid:focus { box-shadow: 0 0 0 4px rgba(239,68,68,0.12); }
        .invalid-note { color: #ef4444; font-size: 0.78rem; font-weight: 600; margin-top: 4px; margin-left: 4px; }

        /* select padding */
        select.custom-field { padding-left: 2.9rem; appearance: none; }

        /* Section headers */
        .section-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--theme-primary);
            margin-bottom: 0.75rem;
            padding-bottom: 0.3rem;
            border-bottom: 1.5px solid rgba(0, 154, 54, 0.2);
        }

        /* Auth card */
        .auth-card {
            border-radius: 2rem;
            background: rgba(255,255,255,0.97);
            border: 1px solid rgba(255,255,255,0.6);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        /* Submit button */
        .btn-submit-register {
            background: linear-gradient(135deg, #019934, #01802b);
            border: none; color: #fff;
            font-weight: 700; font-size: 1.05rem;
            border-radius: 2rem; padding: 0.9rem 2rem;
            transition: all 0.3s ease;
        }
        .btn-submit-register:hover {
            opacity: 0.92; transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 154, 54, 0.35);
            color: #fff;
        }
    </style>
    @endpush

    <section class="py-5 position-relative min-vh-100 d-flex align-items-center overflow-hidden"
             style="background: linear-gradient(135deg, var(--theme-light) 0%, #f1f5f9 100%);">

        {{-- Background blobs --}}
        <div class="position-absolute rounded-circle"
             style="width:500px;height:500px;top:-150px;left:-150px;background:rgba(0, 154, 54, 0.12);filter:blur(80px);"></div>
        <div class="position-absolute rounded-circle"
             style="width:400px;height:400px;bottom:-100px;right:-100px;background:rgba(251,191,36,0.12);filter:blur(80px);"></div>

        <div class="container position-relative z-1 py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">
                    <div class="auth-card">
                        <div class="row g-0 align-items-stretch">

                            {{-- ── FORM SIDE ─────────────────────────────── --}}
                            <div class="col-lg-7 p-4 p-md-5">
                                <div class="animate-fade-up mb-4">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-theme-light text-theme rounded-3 mb-3"
                                         style="width:54px;height:54px;">
                                        <i class="mdi mdi-account-plus-outline fs-3"></i>
                                    </div>
                                    <h2 class="fw-bolder text-dark mb-1">Create your account</h2>
                                    <p class="text-muted mb-0">Join Southwest Farmers and get fresh groceries delivered fast.</p>
                                </div>

                                <form id="registrationForm" method="POST" action="{{ route('website.register') }}"
                                      class="animate-fade-up delay-100" autocomplete="off">
                                    @csrf

                                    {{-- ─ Personal Info ─ --}}
                                    <p class="section-label"><i class="mdi mdi-account-outline me-1"></i> Personal Information</p>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="field-group">
                                                <i class="mdi mdi-account-outline field-icon"></i>
                                                <input type="text" name="name"
                                                       class="custom-field @error('name') is-invalid @enderror"
                                                       placeholder="Full Name *"
                                                       value="{{ old('name') }}" required autofocus>
                                            </div>
                                            @error('name')<p class="invalid-note">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="field-group">
                                                <i class="mdi mdi-email-outline field-icon"></i>
                                                <input type="email" name="email"
                                                       class="custom-field @error('email') is-invalid @enderror"
                                                       placeholder="Email Address *"
                                                       value="{{ old('email') }}" required>
                                            </div>
                                            @error('email')<p class="invalid-note">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="col-md-12">
                                            <div class="field-group">
                                                <i class="mdi mdi-phone-outline field-icon"></i>
                                                <input type="tel" name="phone"
                                                       class="custom-field @error('phone') is-invalid @enderror"
                                                       placeholder="Phone Number *"
                                                       value="{{ old('phone') }}" required>
                                            </div>
                                            @error('phone')<p class="invalid-note">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    {{-- ─ Location ─ --}}
                                    <p class="section-label mt-3"><i class="mdi mdi-map-marker-outline me-1"></i> Location</p>
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="field-group">
                                                <i class="mdi mdi-home-outline field-icon"></i>
                                                <input type="text" name="address"
                                                       class="custom-field @error('address') is-invalid @enderror"
                                                       placeholder="Street Address"
                                                       value="{{ old('address') }}">
                                            </div>
                                            @error('address')<p class="invalid-note">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="col-12">
                                            <div class="field-group">
                                                <i class="mdi mdi-map-outline field-icon"></i>
                                                <input type="text" name="area"
                                                       class="custom-field @error('area') is-invalid @enderror"
                                                       placeholder="Area / Location"
                                                       value="{{ old('area') }}">
                                            </div>
                                            @error('area')<p class="invalid-note">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    {{-- ─ Password ─ --}}
                                    <p class="section-label mt-3"><i class="mdi mdi-lock-outline me-1"></i> Security</p>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="field-group">
                                                <i class="mdi mdi-lock-outline field-icon"></i>
                                                <input type="password" name="password"
                                                       class="custom-field @error('password') is-invalid @enderror"
                                                       placeholder="Password *" required
                                                       id="password">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="field-group">
                                                <i class="mdi mdi-lock-check-outline field-icon"></i>
                                                <input type="password" name="password_confirmation"
                                                       class="custom-field"
                                                       placeholder="Confirm Password *" required>
                                            </div>
                                        </div>
                                        @error('password')<p class="invalid-note col-12 mt-0">{{ $message }}</p>@enderror
                                    </div>

                                    {{-- Submit --}}
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">

                                    <div class="mt-4 animate-fade-up delay-200">
                                        <button type="submit" id="submitBtn" class="btn-submit-register w-100 d-flex align-items-center justify-content-center gap-2">
                                            <span id="btnText">Create My Account</span>
                                            <i class="mdi mdi-check-circle-outline fs-5"></i>
                                        </button>
                                    </div>

                                    <p class="text-center mt-4">
                                        Already a member?
                                        <a href="{{ route('website.login') }}" class="text-theme fw-bold text-decoration-none">Log In</a>
                                    </p>

                                </form>
                            </div>

                            {{-- ── PANEL SIDE ───────────────────────────── --}}
                            <div class="col-lg-5 d-none d-lg-block position-relative" style="min-height:650px;">
                                <img src="https://images.unsplash.com/photo-1604719312566-8912e9227c6a?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80"
                                     alt="Fresh Groceries"
                                     class="position-absolute top-0 start-0 w-100 h-100"
                                     style="object-fit:cover; filter:brightness(0.8);">
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                     style="background:linear-gradient(to top, rgba(0,77,27,0.92) 0%, rgba(0, 154, 54, 0.1) 100%);"></div>

                                {{-- Floating cards --}}
                                <div class="position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center floating-badge"
                                     style="top:12%;left:-18px;z-index:10;">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-shield-check fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark small">Secure Data</h6>
                                        <small class="text-muted">100% Encrypted</small>
                                    </div>
                                </div>
                                <div class="position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center floating-badge-delayed"
                                     style="bottom:18%;right:-18px;z-index:10;">
                                    <div class="bg-theme-light text-theme rounded-circle p-2 me-2 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-truck-fast fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark small">Priority Delivery</h6>
                                        <small class="text-muted">For members</small>
                                    </div>
                                </div>

                                <div class="position-absolute bottom-0 start-0 w-100 p-5 z-1 text-white">
                                    <h3 class="fw-bold mb-2">Quality Guaranteed</h3>
                                    <p class="opacity-75 mb-3 small">Fresh, organic products — hand-picked for you daily.</p>
                                    <div class="d-flex gap-3">
                                        <div class="d-flex align-items-center gap-2 opacity-80 small">
                                            <i class="mdi mdi-check-circle text-success"></i> Free Delivery
                                        </div>
                                        <div class="d-flex align-items-center gap-2 opacity-80 small">
                                            <i class="mdi mdi-check-circle text-success"></i> Easy Returns
                                        </div>
                                        <div class="d-flex align-items-center gap-2 opacity-80 small">
                                            <i class="mdi mdi-check-circle text-success"></i> 100% Organic
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const form = document.getElementById('registrationForm');
            const btnText = document.getElementById('btnText');

            function captureLocation(callback = null) {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        latInput.value = position.coords.latitude;
                        lngInput.value = position.coords.longitude;
                        console.log('Location captured:', position.coords.latitude, position.coords.longitude);
                        if (callback) callback();
                    }, function(error) {
                        console.warn('Geolocation error:', error.message);
                        if (callback) callback();
                    }, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    });
                } else if (callback) {
                    callback();
                }
            }

            // Capture on focus of any field (proactive)
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    if (!latInput.value) captureLocation();
                }, { once: true });
            });

            // Initial capture attempt
            captureLocation();

            // Intercept submit
            form.addEventListener('submit', function(e) {
                if (!latInput.value || !lngInput.value) {
                    e.preventDefault(); // Stop form
                    btnText.innerText = "Capturing location...";
                    
                    // Force a capture attempt
                    captureLocation(function() {
                        btnText.innerText = "Redirecting...";
                        form.submit(); // Now real submit
                    });
                }
            });
        });
    </script>
    @endpush

</x-website-layout>