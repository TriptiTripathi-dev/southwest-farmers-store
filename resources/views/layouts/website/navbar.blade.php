<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-theme" href="{{ route('website.home') }}">
            <i class="mdi mdi-leaf fs-3 me-2"></i> FreshStore
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item px-2">
                    <a class="nav-link {{ request()->routeIs('website.home') ? 'text-theme' : 'text-dark' }}" href="{{ route('website.home') }}">Home</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ request()->routeIs('website.products.*') ? 'text-theme' : 'text-dark' }}" href="{{ route('website.products.index') }}">Shop Products</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link text-dark" href="{{ route('website.home') }}#about">About Us</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link text-dark" href="{{ route('website.home') }}#contact">Contact</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                
                <a href="#" class="text-dark text-decoration-none hover-lift p-2 d-none d-lg-block">
                    <i class="mdi mdi-magnify fs-5"></i>
                </a>

                @guest
                    <div class="d-flex align-items-center gap-2 ms-lg-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold btn-sm">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-theme rounded-pill px-4 fw-bold btn-sm">Sign Up</a>
                    </div>
                @else
                    <div class="dropdown ms-lg-2">
                        <a class="text-dark text-decoration-none hover-lift p-2 dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-account-circle fs-4 text-theme me-1"></i>
                            <span class="fw-bold small d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3 rounded-4">
                            <li>
                                <a class="dropdown-item py-2 fw-semibold" href="{{ route('dashboard') }}">
                                    <i class="mdi mdi-view-dashboard-outline me-2 text-muted"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 fw-semibold" href="{{ route('profile.edit') }}">
                                    <i class="mdi mdi-cog-outline me-2 text-muted"></i> Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 fw-bold text-danger">
                                        <i class="mdi mdi-logout me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest

                <a href="{{ route('website.cart.index') }}" class="btn btn-light position-relative rounded-circle p-2 border hover-lift ms-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-cart-outline fs-5 text-dark"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success border border-white border-2 shadow-sm">
                        0
                    </span>
                </a>
                
            </div>
        </div>
    </div>
</nav>