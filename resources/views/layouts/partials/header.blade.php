<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            @php
                $brandLogo = asset('assets/images/logo.jpg');
                $brandName = 'Home Food Distributors';
                $settings = \App\Models\StoreSetting::first();
                if ($settings) {
                    if (!empty($settings->logo)) {
                        $brandLogo = Storage::disk('r2')->url($settings->logo);
                    }
                    if (!empty($settings->app_name)) {
                        $brandName = $settings->app_name;
                    }
                }
            @endphp

            {{-- LEFT --}}
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                @if(!auth()->user()->hasRole('Cashier'))
                <li>
                    <button type="button" class="button-toggle-menu nav-link">
                        <iconify-icon icon="tabler:align-left"
                            class="fs-20 align-middle text-dark topbar-button"></iconify-icon>
                    </button>
                </li>
                @endif
                <li class="ms-2 d-flex align-items-center">
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="rounded border bg-white p-1 me-2" style="width: 34px; height: 34px; object-fit: contain;">
                    <span class="fw-bold text-dark d-none d-md-inline">{{ $brandName }}</span>
                </li>

                @if(auth()->user()->hasRole('Cashier'))
                <li class="ms-4 d-none d-lg-flex align-items-center gap-2">
                    <a href="{{ route('store.sales.pos') }}" class="btn btn-sm btn-{{ request()->routeIs('store.sales.pos') ? 'primary' : 'outline-primary' }} rounded-pill px-3">
                        <i class="mdi mdi-calculator me-1"></i> POS
                    </a>
                    <a href="{{ route('store.sales.orders') }}" class="btn btn-sm btn-{{ request()->routeIs('store.sales.orders') ? 'outline-secondary' : 'outline-secondary' }} border-0 px-3">
                        <i class="mdi mdi-basket-outline me-1"></i> All Orders
                    </a>
                    <a href="{{ route('store.sales.returns.index') }}" class="btn btn-sm btn-outline-secondary border-0 px-3">
                        <i class="mdi mdi-keyboard-return me-1"></i> Returns
                    </a>
                </li>
                @endif
            </ul>

            {{-- RIGHT --}}
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <style>
                    .notification-dropdown-menu {
                        min-width: 320px;
                        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
                    }
                    .notification-item {
                        transition: all 0.2s ease;
                        border-bottom: 1px solid #f1f1f1;
                    }
                    .notification-item:last-child {
                        border-bottom: none;
                    }
                    .notification-item:hover {
                        background-color: #f8f9fa !important;
                    }
                    .notification-item.unread {
                        background-color: rgba(1, 153, 52, 0.04);
                    }
                    .avatar-title.bg-soft-primary {
                        background-color: rgba(1, 153, 52, 0.1) !important;
                        color: #019934 !important;
                    }
                    .avatar-title.bg-soft-danger {
                        background-color: rgba(239, 71, 111, 0.1) !important;
                        color: #ef476f !important;
                    }
                    .avatar-title.bg-soft-warning {
                        background-color: rgba(255, 209, 102, 0.1) !important;
                        color: #ffd166 !important;
                    }
                </style>

                {{-- NOTIFICATIONS --}}
                {{-- Notification Dropdown --}}
                <li class="dropdown d-inline-block d-lg-inline-block">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="mdi mdi-bell-outline font-size-22"></i>
                        @if(isset($headerUnreadCount) && $headerUnreadCount > 0)
                        <span class="badge rounded-pill bg-danger float-end" style="position: absolute; top: 12px; right: 5px; font-size: 10px;">
                            {{ $headerUnreadCount > 99 ? '99+' : $headerUnreadCount }}
                        </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg p-0 border-0 shadow notification-dropdown-menu rounded-3">
                        <div class="p-3 border-bottom bg-white rounded-top-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fw-bold fs-15">Notifications <span class="badge bg-soft-primary text-primary ms-1">{{ $headerUnreadCount ?? 0 }}</span></h6>
                                </div>
                                <div class="col-auto">
                                    <form action="{{ route('store.notifications.readAll') }}" method="POST">
                                        @csrf
                                        <button class="btn btn-link p-0 text-primary text-decoration-none small fw-medium fs-13">Mark all as read</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 280px;">
                            @if(isset($headerNotifications) && $headerNotifications->count() > 0)
                            @foreach($headerNotifications as $notif)
                            <a href="{{ route('store.notifications.read', $notif->id) }}" class="text-reset notification-item d-block {{ $notif->read_at ? '' : 'unread' }}">
                                <div class="d-flex align-items-start p-3">
                                    <div class="avatar-xs me-3 flex-shrink-0">
                                        <span class="avatar-title bg-soft-{{ $notif->type == 'error' ? 'danger' : ($notif->type == 'warning' ? 'warning' : 'primary') }} rounded-circle fs-16">
                                            <i class="mdi {{ $notif->type == 'error' ? 'mdi-alert-circle-outline' : 'mdi-bell-outline' }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="mt-0 mb-1 fs-14 fw-semibold text-dark">{{ $notif->title }}</h6>
                                        <div class="fs-12 text-muted">
                                            <p class="mb-1 text-truncate">{{ $notif->message }}</p>
                                            <p class="mb-0 fs-11 text-muted-80"><i class="mdi mdi-clock-outline me-1"></i>{{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                            @else
                            <div class="p-5 text-center text-muted">
                                <div class="avatar-md mx-auto mb-3">
                                    <span class="avatar-title bg-light text-muted rounded-circle fs-24">
                                        <i class="mdi mdi-bell-sleep-outline"></i>
                                    </span>
                                </div>
                                <h6 class="fw-medium">No new notifications</h6>
                                <p class="mb-0 fs-13">We'll let you know when something happens.</p>
                            </div>
                            @endif
                        </div>
                        <div class="p-2 border-top bg-light-subtle d-grid rounded-bottom-3">
                            <a class="btn btn-sm btn-link fs-13 text-center text-primary fw-bold" href="{{ route('store.notifications.index') }}">
                                View all <i class="mdi mdi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </li>
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" href="{{ route('store.sales.pos') }}" role="button">
                        <i class="mdi mdi-cart-outline noti-icon"></i>
                        @if(isset($cartCount) && $cartCount > 0)
                        <span class="badge bg-danger rounded-circle noti-icon-badge mb-5">
                            {{ $cartCount }}
                        </span>
                        @endif
                    </a>
                </li>
                {{-- USER PROFILE --}}
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#">
                        <img src="{{ asset('assets/images/users/profile.jpg') }}" alt="User" height="22">
                    </a>

                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">
                                Welcome {{ auth()->user()->name ?? 'Admin' }}!
                            </h6>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="dropdown-item notify-item">
                            <iconify-icon icon="tabler:user-square-rounded"
                                class="fs-18 align-middle"></iconify-icon>
                            <span>My Account</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        {{-- LOGOUT --}}
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf

                            <button type="submit" class="dropdown-item notify-item text-danger">
                                <iconify-icon icon="tabler:logout"
                                    class="fs-18 align-middle"></iconify-icon>
                                <span>Logout</span>
                            </button>
                        </form>

                    </div>
                </li>

            </ul>

        </div>
    </div>
</div>

<script>
    function updateHeaderCartCount() {
        // 1. Calculate the sum of quantities
        let totalQuantity = cart.reduce((sum, item) => sum + parseInt(item.quantity), 0);

        // 2. Find the cart icon in the topbar
        let $cartIcon = $('i.mdi-cart-outline');
        let $badge = $('.noti-icon-badge', $cartIcon.parent());

        if (totalQuantity > 0) {
            if ($badge.length === 0) {
                $cartIcon.after(`<span class="badge badge-danger rounded-circle noti-icon-badge">${totalQuantity}</span>`);
            } else {
                $badge.text(totalQuantity);
            }
        } else {
            $badge.remove();
        }
    }

    // Call this function inside your renderCart() function
    function renderCart() {
        // ... your existing logic ...
        updateHeaderCartCount(); // Add this line here
    }
</script>
