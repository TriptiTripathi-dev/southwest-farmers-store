@props(['title' => 'FreshStore - Fresh Groceries'])
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="description" content="FreshStore – Fresh Groceries & Organic Products delivered at your doorstep.">

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- MDI Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @stack('styles-lib')
    @stack('styles')

    <style>
        :root {
            --theme-primary: #10b981;
            --theme-dark:    #064e3b;
            --theme-light:   #ecfdf5;
        }

        * { font-family: 'Inter', sans-serif; }

        body { background: #f8fafc; color: #1e293b; }

        /* Navbar */
        .navbar-website {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(16,185,129,0.12);
            transition: box-shadow 0.3s ease;
        }
        .navbar-website.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

        .navbar-brand-text {
            font-weight: 900;
            font-size: 1.4rem;
            background: linear-gradient(135deg, var(--theme-primary), var(--theme-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .nav-link-website {
            font-weight: 600;
            color: #475569;
            transition: color 0.2s ease;
            position: relative;
            padding: 0.4rem 0 !important;
        }
        .nav-link-website::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--theme-primary);
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        .nav-link-website:hover::after,
        .nav-link-website.active::after { width: 100%; }
        .nav-link-website:hover { color: var(--theme-primary); }

        /* Theme Buttons */
        .btn-theme {
            background: linear-gradient(135deg, var(--theme-primary), #059669);
            border: none;
            color: #fff;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .btn-theme:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.3);
            color: #fff;
        }

        .btn-outline-theme {
            border: 2px solid var(--theme-primary);
            color: var(--theme-primary);
            font-weight: 700;
            background: transparent;
            transition: all 0.3s ease;
        }
        .btn-outline-theme:hover {
            background: var(--theme-primary);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Customer Dropdown */
        .customer-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--theme-primary), #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Theme Utilities */
        .text-theme { color: var(--theme-primary) !important; }
        .bg-theme { background: var(--theme-primary) !important; }
        .bg-theme-light { background: var(--theme-light) !important; }
        .bg-theme-dark { background: var(--theme-dark) !important; }
        .border-theme { border-color: var(--theme-primary) !important; }
        .hover-lift { transition: transform 0.2s ease; }
        .hover-lift:hover { transform: translateY(-3px); }

        /* Footer */
        .footer-website {
            background: var(--theme-dark);
            color: rgba(255,255,255,0.8);
        }
    </style>
</head>
<body>

    {{-- ─── NAVBAR ─────────────────────────────────────────────────────── --}}
    <nav class="navbar navbar-website navbar-expand-lg sticky-top py-3" id="mainNavbar">
        <div class="container">
            {{-- Brand --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('website.home') }}">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-theme-light" style="width:40px;height:40px;">
                    <i class="mdi mdi-leaf text-theme fs-4"></i>
                </div>
                <span class="navbar-brand-text">FreshStore</span>
            </a>

            {{-- Mobile Toggler --}}
            <button class="navbar-toggler border-0 shadow-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <i class="mdi mdi-menu fs-3 text-dark"></i>
            </button>

            {{-- Nav Links --}}
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto gap-md-4 gap-2 py-3 py-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-website {{ request()->routeIs('website.home') ? 'active' : '' }}"
                           href="{{ route('website.home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-website {{ request()->routeIs('website.products.*') ? 'active' : '' }}"
                           href="{{ route('website.products.index') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-website" href="{{ route('website.home') }}#about">About</a>
                    </li>
                </ul>

                {{-- Right-side: Cart + Auth --}}
            {{-- Location Badge --}}
            <div id="userLocationDisplay" class="d-none d-lg-flex align-items-center gap-2 px-3 py-1 bg-light rounded-pill border ms-2">
                <i class="mdi mdi-map-marker-radius text-theme"></i>
                <small class="fw-bold text-muted" id="locationStatus">Checking location...</small>
            </div>

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">

                    {{-- Cart --}}
                    <a href="{{ route('website.cart.index') }}"
                       class="btn btn-light rounded-circle d-flex align-items-center justify-content-center position-relative shadow-sm"
                       style="width:42px;height:42px;" title="Cart">
                        <i class="mdi mdi-cart-outline fs-5 text-dark"></i>
                    </a>

                    {{-- Auth Buttons --}}
                    @auth('customer')
                        {{-- Logged-in Customer Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-light rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm fw-semibold dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="customer-avatar">
                                    {{ strtoupper(substr(Auth::guard('customer')->user()->name, 0, 1)) }}
                                </div>
                                <span class="d-none d-md-inline text-dark fw-semibold" style="max-width:100px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                                    {{ Auth::guard('customer')->user()->name }}
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4 mt-2 py-2" style="min-width:200px;">
                                <li class="px-3 pb-2">
                                    <p class="mb-0 small fw-bold text-dark">{{ Auth::guard('customer')->user()->name }}</p>
                                    <p class="mb-0 small text-muted">{{ Auth::guard('customer')->user()->email }}</p>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('website.dashboard') }}">
                                        <i class="mdi mdi-view-dashboard-outline me-2 text-theme"></i> My Account
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('website.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="mdi mdi-logout me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        {{-- Guest Buttons --}}
                        <a href="{{ route('website.login') }}"
                           class="btn btn-outline-light rounded-pill px-4 fw-bold">Login</a>
                        <a href="{{ route('website.register') }}"
                           class="btn btn-light rounded-pill px-4 fw-bold">Join Now</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    {{-- ────────────────────────────────────────────────────────────────── --}}

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 rounded-0 border-0 text-center fw-semibold" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0 border-0 text-center fw-semibold" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Page Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ─── FOOTER ─────────────────────────────────────────────────────── --}}
    <footer class="footer-website py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(255,255,255,0.1);">
                            <i class="mdi mdi-leaf text-white fs-5"></i>
                        </div>
                        <span class="text-white fw-bold fs-5">FreshStore</span>
                    </div>
                    <p class="opacity-75 small">Fresh groceries and organic products delivered fast to your doorstep.</p>
                </div>
                <div class="col-lg-4 col-6">
                    <h6 class="text-white fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="{{ route('website.home') }}" class="text-decoration-none opacity-75 small" style="color:inherit;">Home</a></li>
                        <li class="mb-2"><a href="{{ route('website.products.index') }}" class="text-decoration-none opacity-75 small" style="color:inherit;">Products</a></li>
                        <li class="mb-2"><a href="{{ route('website.cart.index') }}" class="text-decoration-none opacity-75 small" style="color:inherit;">Cart</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-6">
                    <h6 class="text-white fw-bold mb-3">My Account</h6>
                    <ul class="list-unstyled mb-0">
                        @auth('customer')
                            <li class="mb-2"><a href="{{ route('website.dashboard') }}" class="text-decoration-none opacity-75 small" style="color:inherit;">Dashboard</a></li>
                        @else
                            <li class="mb-2"><a href="{{ route('website.login') }}" class="text-decoration-none opacity-75 small" style="color:inherit;">Login</a></li>
                            <li class="mb-2"><a href="{{ route('website.register') }}" class="text-decoration-none opacity-75 small" style="color:inherit;">Sign Up</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
            <hr class="mt-4 opacity-25">
            <p class="text-center mb-0 opacity-50 small">&copy; {{ date('Y') }} FreshStore. All rights reserved.</p>
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts-lib')

    <script>
        // Sticky nav scroll shadow
        const navbar = document.getElementById('mainNavbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 10);
        });

        // Geolocation Handling
        function updateLocationInSession(lat, lng) {
            fetch("{{ route('website.location.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ latitude: lat, longitude: lng })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('locationStatus').textContent = "Location sync";
                    // Optional: reload the products if on the product page
                    if(window.location.pathname === '/products' || window.location.pathname === '/quick-shop') {
                        location.reload();
                    }
                }
            });
        }

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        updateLocationInSession(lat, lng);
                    },
                    (error) => {
                        console.error("Error getting location:", error);
                        document.getElementById('locationStatus').textContent = "Local access denied";
                    }
                );
            } else {
                document.getElementById('locationStatus').textContent = "Geo not supported";
            }
        }

        @if(!session('location_set'))
            document.addEventListener('DOMContentLoaded', getLocation);
        @else
            document.getElementById('locationStatus').textContent = "Nearby stores";
        @endif
    </script>

    @stack('scripts')
</body>
</html>
