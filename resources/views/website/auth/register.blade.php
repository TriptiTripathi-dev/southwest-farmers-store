<x-website-layout title="Create an Account - FreshStore">
    
    @push('styles')
    <style>
        /* Custom Animations */
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .floating-badge { animation: float 4s ease-in-out infinite; }
        .floating-badge-delayed { animation: float 4s ease-in-out infinite; animation-delay: 2s; }

        /* Custom Input Styles */
        .custom-input {
            border: 2px solid #e2e8f0;
            border-radius: 1rem;
            padding: 0.85rem 1.25rem;
            padding-left: 3.2rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
            font-weight: 500;
        }
        .custom-input:focus {
            border-color: var(--theme-primary);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        }
        .input-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.3rem;
            transition: all 0.3s ease;
            z-index: 5;
        }
        .form-group:focus-within .input-icon {
            color: var(--theme-primary);
        }

        /* Glassmorphism Card */
        .auth-card {
            border-radius: 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        }
    </style>
    @endpush

    <section class="py-5 position-relative min-vh-100 d-flex align-items-center overflow-hidden" style="background: linear-gradient(135deg, var(--theme-light) 0%, #f1f5f9 100%);">
        
        <div class="position-absolute rounded-circle bg-success bg-opacity-20" style="width: 500px; height: 500px; top: -150px; left: -150px; filter: blur(80px);"></div>
        <div class="position-absolute rounded-circle bg-warning bg-opacity-20" style="width: 400px; height: 400px; bottom: -100px; right: -100px; filter: blur(80px);"></div>

        <div class="container position-relative z-1 py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">
                    <div class="auth-card overflow-hidden">
                        <div class="row g-0 align-items-center">

                            <div class="col-lg-6 p-4 p-md-5 p-xl-5">
                                <div class="px-md-3">
                                    <div class="animate-fade-up">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-theme-light text-theme rounded-circle mb-4" style="width: 65px; height: 65px;">
                                            <i class="mdi mdi-leaf fs-2"></i>
                                        </div>
                                        <h2 class="display-6 fw-bolder text-dark mb-2">Create Account</h2>
                                        <p class="text-muted mb-5">Join us to get fresh organics delivered fast to your doorstep.</p>
                                    </div>

                                    <form method="POST" action="{{ route('register') }}" class="animate-fade-up delay-100">
                                        @csrf

                                        <div class="form-group position-relative mb-4">
                                            <i class="mdi mdi-account-outline input-icon"></i>
                                            <input type="text" name="name" class="form-control custom-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Full Name" required autofocus>
                                            @error('name')
                                                <div class="invalid-feedback ms-3 mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group position-relative mb-4">
                                            <i class="mdi mdi-email-outline input-icon"></i>
                                            <input type="email" name="email" class="form-control custom-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Email Address" required>
                                            @error('email')
                                                <div class="invalid-feedback ms-3 mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="form-group position-relative">
                                                    <i class="mdi mdi-lock-outline input-icon"></i>
                                                    <input type="password" name="password" class="form-control custom-input @error('password') is-invalid @enderror" placeholder="Password" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group position-relative">
                                                    <i class="mdi mdi-lock-check-outline input-icon"></i>
                                                    <input type="password" name="password_confirmation" class="form-control custom-input" placeholder="Confirm Password" required>
                                                </div>
                                            </div>
                                            @error('password')
                                                <div class="col-12 mt-1">
                                                    <div class="text-danger small ms-3 fw-bold">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="mt-5 animate-fade-up delay-200">
                                            <button type="submit" class="btn btn-theme w-100 py-3 rounded-pill fw-bold fs-5 shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift">
                                                <span>Sign Up Now</span>
                                                <i class="mdi mdi-arrow-right"></i>
                                            </button>
                                        </div>

                                        <div class="text-center mt-4 animate-fade-up delay-300">
                                            <p class="text-muted fw-medium mb-0">
                                                Already a member?
                                                <a href="{{ route('login') }}" class="text-theme fw-bold text-decoration-none border-bottom border-success border-2 pb-1 ms-1 transition-all" style="border-color: var(--theme-primary) !important;">Log In</a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-6 d-none d-lg-block p-4 p-xl-5">
                                <div class="position-relative h-100 w-100 rounded-[2rem] overflow-hidden animate-fade-up delay-200" style="min-height: 650px; border-radius: 2rem;">
                                    
                                    <img src="https://images.unsplash.com/photo-1604719312566-8912e9227c6a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Fresh Groceries" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover; filter: brightness(0.85);">
                                    
                                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(6, 78, 59, 0.9) 0%, rgba(16, 185, 129, 0.1) 100%);"></div>

                                    <div class="position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center floating-badge" style="top: 15%; left: -20px; z-index: 10;">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3 d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-shield-check fs-4"></i>
                                        </div>
                                        <div class="pe-2">
                                            <h6 class="mb-0 fw-bold text-dark">Secure Data</h6>
                                            <small class="text-muted">100% Encrypted</small>
                                        </div>
                                    </div>

                                    <div class="position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center floating-badge-delayed" style="bottom: 15%; right: -20px; z-index: 10;">
                                        <div class="bg-theme-light text-theme rounded-circle p-3 me-3 d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-truck-fast fs-4"></i>
                                        </div>
                                        <div class="pe-2">
                                            <h6 class="mb-0 fw-bold text-dark">Priority Delivery</h6>
                                            <small class="text-muted">For registered users</small>
                                        </div>
                                    </div>

                                    <div class="position-absolute bottom-0 start-0 w-100 p-5 text-white z-1">
                                        <h3 class="fw-bold mb-2">Quality Guarantee</h3>
                                        <p class="opacity-75 mb-0">Experience the difference with our hand-picked selections.</p>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-website-layout>