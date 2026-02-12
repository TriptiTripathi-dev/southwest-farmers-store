<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">

            {{-- LEFT --}}
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button type="button" class="button-toggle-menu nav-link">
                        <iconify-icon icon="tabler:align-left"
                            class="fs-20 align-middle text-dark topbar-button"></iconify-icon>
                    </button>
                </li>
            </ul>

            {{-- RIGHT --}}
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

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
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg p-0">
                        <div class="p-3 border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fw-bold"> Notifications </h6>
                                </div>
                                <div class="col-auto">
                                    <form action="{{ route('store.notifications.readAll') }}" method="POST">
                                        @csrf
                                        <button class="text-reset small btn btn-link p-0 text-decoration-none"> Mark all as read</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 230px;">
                            @if(isset($headerNotifications) && $headerNotifications->count() > 0)
                            @foreach($headerNotifications as $notif)
                            <a href="{{ route('store.notifications.read', $notif->id) }}" class="text-reset notification-item">
                                <div class="d-flex align-items-start p-3 border-bottom bg-light">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-{{ $notif->type == 'error' ? 'danger' : ($notif->type == 'warning' ? 'warning' : 'primary') }} rounded-circle font-size-16">
                                            <i class="mdi {{ $notif->type == 'error' ? 'mdi-alert-circle-outline' : 'mdi-bell-outline' }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mt-0 mb-1 font-size-14">{{ $notif->title }}</h6>
                                        <div class="font-size-12 text-muted">
                                            <p class="mb-1">{{ Str::limit($notif->message, 50) }}</p>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> {{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                            @else
                            <div class="p-4 text-center text-muted">
                                <i class="mdi mdi-bell-sleep-outline fs-1"></i>
                                <p class="mb-0">No new notifications</p>
                            </div>
                            @endif
                        </div>
                        <div class="p-2 border-top d-grid">
                            <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('store.notifications.index') }}">
                                <i class="mdi mdi-arrow-right-circle me-1"></i> View all
                            </a>
                        </div>
                    </div>
                </li>
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" href="{{ route('sales.pos') }}" role="button">
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