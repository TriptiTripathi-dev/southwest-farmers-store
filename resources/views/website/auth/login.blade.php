<x-website-layout title="Customer Login">
    
    <section class="py-5 bg-theme-light min-vh-100 d-flex align-items-center">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                        <div class="row g-0">
                            
                            <div class="col-lg-6 d-none d-lg-block position-relative bg-success bg-opacity-25">
                                <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'); background-size: cover; background-position: center; opacity: 0.8;"></div>
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-theme-dark bg-opacity-50"></div>
                                <div class="position-relative z-1 h-100 p-5 d-flex flex-column justify-content-center text-white">
                                    <h2 class="display-5 fw-bold mb-4">Welcome Back!</h2>
                                    <p class="lead opacity-75">Log in to your account to track your orders, manage your wishlist, and enjoy a faster checkout experience.</p>
                                </div>
                            </div>

                            <div class="col-lg-6 p-5 p-xl-5 bg-white">
                                <div class="text-center mb-5">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-theme-light text-theme rounded-circle mb-3" style="width: 60px; height: 60px;">
                                        <i class="mdi mdi-lock-outline fs-2"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark">Customer Sign In</h3>
                                    <p class="text-muted">Enter your email and password to access your account.</p>
                                </div>

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div class="form-floating mb-4">
                                        <input type="email" class="form-control bg-light border-0 shadow-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus autocomplete="username">
                                        <label for="email" class="text-muted"><i class="mdi mdi-email-outline me-1"></i> Email address</label>
                                        @error('email')
                                            <div class="invalid-feedback fw-bold mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-floating mb-4">
                                        <input type="password" class="form-control bg-light border-0 shadow-sm @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="current-password">
                                        <label for="password" class="text-muted"><i class="mdi mdi-key-outline me-1"></i> Password</label>
                                        @error('password')
                                            <div class="invalid-feedback fw-bold mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-5">
                                        <div class="form-check">
                                            <input class="form-check-input border-secondary" type="checkbox" name="remember" id="remember_me">
                                            <label class="form-check-label text-muted" for="remember_me">
                                                Remember me
                                            </label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <a class="text-theme text-decoration-none fw-bold small" href="{{ route('password.request') }}">
                                                Forgot password?
                                            </a>
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-theme w-100 py-3 rounded-pill fw-bold fs-5 shadow-sm mb-4">
                                        Login to Account
                                    </button>

                                    <p class="text-center text-muted mb-0">
                                        Don't have an account? 
                                        <a href="{{ route('register') }}" class="text-theme fw-bold text-decoration-none">Sign up here</a>
                                    </p>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-website-layout>