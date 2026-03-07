<x-website-layout title="My Account - FreshStore">

    @push('styles')
    <style>
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        .account-card {
            border: 1px solid rgba(0, 154, 54, 0.12);
            border-radius: 1.5rem;
            transition: all 0.3s ease;
        }
        .account-card:hover { box-shadow: 0 10px 30px rgba(0, 154, 54, 0.1); transform: translateY(-4px); }

        .avatar-large {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, #019934, #00802b);
            --theme-primary: #019934;
            --theme-light: #e6fff0;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-weight: 900;
            font-size: 2rem;
            box-shadow: 0 10px 25px rgba(0, 154, 54, 0.3);
        }

        .stat-badge {
            background: var(--theme-light);
            border: 1px solid rgba(0, 154, 54, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
        }

        .detail-row { display: flex; border-bottom: 1px solid #f1f5f9; padding: 0.45rem 0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { width: 120px; min-width: 120px; font-size: 0.78rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; }
        .detail-value { font-weight: 600; color: #1e293b; font-size: 0.88rem; }
    </style>
    @endpush

    <section class="py-5" style="min-height: 80vh; background: linear-gradient(135deg, var(--theme-light) 0%, #f8fafc 100%);">
        <div class="container py-4">

            {{-- ── Header ── --}}
            <div class="row align-items-center mb-5 animate-fade-up">
                <div class="col-auto">
                    <div class="avatar-large">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                </div>
                <div class="col">
                    <h2 class="fw-bold text-dark mb-1">
                        Welcome back, <span class="text-theme">{{ $customer->name }}</span>! 👋
                    </h2>
                    <p class="text-muted mb-0 small">
                        <i class="mdi mdi-email-outline me-1"></i>{{ $customer->email }}
                        @if($customer->phone)
                            &nbsp;·&nbsp;<i class="mdi mdi-phone-outline me-1"></i>{{ $customer->phone }}
                        @endif
                        @if($customer->city)
                            &nbsp;·&nbsp;<i class="mdi mdi-map-marker-outline me-1"></i>{{ $customer->city }}{{ $customer->state ? ', '.$customer->state : '' }}
                        @endif
                    </p>
                </div>
                <div class="col-auto mt-3 mt-md-0">
                    <form method="POST" action="{{ route('website.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                            <i class="mdi mdi-logout me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Quick Stats ── --}}
            <div class="row g-4 mb-5 animate-fade-up delay-100">
                <div class="col-6 col-md-3">
                    <a href="{{ route('website.orders.index') }}" class="text-decoration-none">
                        <div class="stat-badge hover-lift transition">
                            <i class="mdi mdi-cart-outline fs-1 text-theme mb-2 d-block"></i>
                            <h4 class="fw-bold text-dark mb-0">{{ \App\Models\Sale::where('customer_id', $customer->id)->count() }}</h4>
                            <small class="text-muted fw-semibold">Total Orders</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-badge">
                        <i class="mdi mdi-heart-outline fs-1 text-danger mb-2 d-block"></i>
                        <h4 class="fw-bold text-dark mb-0">0</h4>
                        <small class="text-muted fw-semibold">Wishlist Items</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-badge">
                        <i class="mdi mdi-star-outline fs-1 text-warning mb-2 d-block"></i>
                        <h4 class="fw-bold text-dark mb-0">0</h4>
                        <small class="text-muted fw-semibold">Reviews</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-badge">
                        <i class="mdi mdi-trophy-outline fs-1 text-theme mb-2 d-block"></i>
                        <h4 class="fw-bold text-dark mb-0">New</h4>
                        <small class="text-muted fw-semibold">Loyalty Level</small>
                    </div>
                </div>
            </div>

            {{-- ── Cards Row ── --}}
            <div class="row g-4 animate-fade-up delay-200">

                {{-- My Orders --}}
                <div class="col-md-4">
                    <div class="account-card bg-white p-4 h-100 d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-theme-light text-theme" style="width:52px;height:52px;">
                                <i class="mdi mdi-package-variant-closed fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">My Orders</h5>
                                <small class="text-muted">Track your purchases</small>
                            </div>
                        </div>
                        <a href="{{ route('website.orders.index') }}" class="btn btn-theme w-100 rounded-pill fw-bold py-2 mt-auto">
                            <i class="mdi mdi-package-variant-closed me-2"></i> View My Orders
                        </a>
                    </div>
                </div>

                {{-- Account Details --}}
                <div class="col-md-8">
                    <div class="account-card bg-white p-4 h-100">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-theme-light text-theme" style="width:52px;height:52px;">
                                <i class="mdi mdi-account-edit-outline fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Account Details</h5>
                                <small class="text-muted">Your registered profile information</small>
                            </div>
                        </div>

                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <span class="detail-label">Name</span>
                                    <span class="detail-value">{{ $customer->name }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Email</span>
                                    <span class="detail-value">{{ $customer->email }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Phone</span>
                                    <span class="detail-value">{{ $customer->phone ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Gender</span>
                                    <span class="detail-value">{{ $customer->gender ? ucfirst($customer->gender) : '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Date of Birth</span>
                                    <span class="detail-value">
                                        {{ $customer->date_of_birth ? $customer->date_of_birth->format('d M Y') : '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <div class="detail-row">
                                    <span class="detail-label">Address</span>
                                    <span class="detail-value">{{ $customer->address ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">City</span>
                                    <span class="detail-value">{{ $customer->city ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">State</span>
                                    <span class="detail-value">{{ $customer->state ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">ZIP Code</span>
                                    <span class="detail-value">{{ $customer->zip_code ?? '—' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Country</span>
                                    <span class="detail-value">{{ $customer->country ?? '—' }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>

</x-website-layout>
