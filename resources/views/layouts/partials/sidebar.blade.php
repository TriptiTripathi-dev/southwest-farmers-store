<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">
            @php
            $logo = asset('assets/images/logo.jpg');
            $settings = \App\Models\StoreSetting::first();
            if ($settings && $settings->logo) {
            $logo = asset('storage/' . $settings->logo);
            }

            // Helper to check permissions cleanly
            $user = auth()->user();
            $can = fn($perm) => $user->hasPermission($perm);
            @endphp

            {{-- LOGO SECTION --}}
            <div class="logo-box">
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ $logo }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $logo }}" alt="" height="24">
                    </span>
                </a>
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ $logo }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $logo }}" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="sidebar-menu">

                <li class="menu-title">Main</li>

                {{-- DASHBOARD --}}
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="tp-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:layout-dashboard"></iconify-icon>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>

                <li class="menu-title mt-2">Operations</li>

                {{-- INVENTORY --}}
                @if ($can('view_inventory') || $can('request_stock') || $can('adjust_stock'))
                <li>
                    @php
                    $isInventoryActive = request()->routeIs(
                    'inventory.index',
                    'inventory.requests',
                    'inventory.adjustments',
                    );
                    @endphp
                    <a href="#sidebarInventory" data-bs-toggle="collapse"
                        class="tp-link {{ $isInventoryActive ? 'active' : '' }}"
                        aria-expanded="{{ $isInventoryActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:box-seam"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Inventory </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ $isInventoryActive ? 'show' : '' }}" id="sidebarInventory">
                        <ul class="nav-second-level">
                            @if ($can('view_inventory'))
                            <li>
                                <a href="{{ route('inventory.index') }}"
                                    class="tp-link {{ request()->is('inventory/stock') ? 'active' : '' }}">
                                    Store Stock
                                </a>
                            </li>
                            @endif

                            @if ($can('request_stock'))
                            <li>
                                <a href="{{ route('inventory.requests') }}"
                                    class="tp-link {{ request()->is('inventory/requests') ? 'active' : '' }}">
                                    Stock Requests
                                </a>
                            </li>
                            @endif

                            @if ($can('adjust_stock'))
                            <li>
                                <a href="{{ route('inventory.adjustments') }}"
                                    class="tp-link {{ request()->is('inventory/adjustments') ? 'active' : '' }}">
                                    Stock Adjustments
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                {{-- PRODUCTS (Visible if user can view inventory) --}}
                @if ($can('view_inventory'))
                @php
                $isProductsActive = request()->routeIs('products.*');
                $isCategoryActive = request()->routeIs('store.categories.*');
                $isSubCategoryActive = request()->routeIs('store.subcategories.*');
                $allActive = $isProductsActive || $isCategoryActive || $isSubCategoryActive;
                @endphp
                <li class="menuitem-{{ $allActive ? 'active' : '' }} {{ $allActive ? 'show' : '' }}">
                    <a class="tp-link {{ $allActive ? 'active' : '' }}" href="#sidebarProducts"
                        data-bs-toggle="collapse" class="tp-link {{ $allActive ? 'active' : '' }}"
                        aria-expanded="{{ $allActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:tag"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Products</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ $allActive ? 'show' : '' }}" id="sidebarProducts">
                        <ul class="nav-second-level">
                            <li>
                                <a class="tp-link {{ $isCategoryActive ? 'active' : '' }}"
                                    href="{{ route('store.categories.index') }}">Categories</a>
                            </li>
                            <li>
                                <a class="tp-link {{ $isSubCategoryActive ? 'active' : '' }}"
                                    href="{{ route('store.subcategories.index') }}">Subcategories</a>
                            </li>
                            <li>
                                <a class="tp-link {{ $isProductsActive ? 'active' : '' }}"
                                    href="{{ route('store.products.index') }}">Product List</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                {{-- STOCK CONTROL --}}
                @if ($can('view_stock_control') || $can('view_stock_valuation') || $can('manage_recall_requests'))
                <li>
                    <a href="#sidebarStockControl" data-bs-toggle="collapse"
                        class="tp-link {{ request()->routeIs('store.stock-control.*') ? 'active' : '' }}"
                        aria-expanded="{{ request()->routeIs('store.stock-control.*') ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:package"></iconify-icon>
                        </span>
                        <span class="sidebar-text">Stock Control</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ request()->routeIs('store.stock-control.*') ? 'show' : '' }}"
                        id="sidebarStockControl">
                        <ul class="nav-second-level">
                            @if ($can('view_stock_control'))
                            <li>
                                <a href="{{ route('store.stock-control.overview') }}"
                                    class="tp-link {{ request()->routeIs('store.stock-control.overview') ? 'active' : '' }}">
                                    My Stock Overview
                                </a>
                            </li>
                            @endif

                            @if ($can('view_stock_valuation'))
                            <li>
                                <a href="{{ route('store.stock-control.valuation') }}"
                                    class="tp-link {{ request()->routeIs('store.stock-control.valuation') ? 'active' : '' }}">
                                    Valuation
                                </a>
                            </li>
                            @endif

                            @if ($can('manage_recall_requests'))
                            <li>
                                <a href="{{ route('store.stock-control.recall.index') }}"
                                    class="tp-link {{ request()->routeIs('store.stock-control.recall.*') ? 'active' : '' }}">
                                    Recall Requests
                                    @if (isset($pendingRecallCount) && $pendingRecallCount > 0)
                                    <span
                                        class="badge bg-danger ms-1 float-end">{{ $pendingRecallCount }}</span>
                                    @endif
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                {{-- ORDERS --}}
                @if ($can('access_pos') || $can('view_orders') || $can('process_return'))
                <li>
                    @php
                    $isPosActive = request()->routeIs('sales.pos');
                    $isOrdersRoute = request()->is('orders*') || request()->routeIs('store.sales.orders');
                    $isOrdersActive = $isPosActive || $isOrdersRoute;
                    @endphp

                    <a href="#sidebarOrders" data-bs-toggle="collapse"
                        class="tp-link {{ $isOrdersActive ? 'active' : '' }}"
                        aria-expanded="{{ $isOrdersActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:shopping-cart"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Orders </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isOrdersActive ? 'show' : '' }}" id="sidebarOrders">
                        <ul class="nav-second-level">
                            @if ($can('access_pos') || $can('create_order'))
                            <li>
                                <a href="{{ route('sales.pos') }}"
                                    class="tp-link {{ $isPosActive ? 'active' : '' }}">
                                    Create Order (POS)
                                </a>
                            </li>
                            @endif

                            @if ($can('view_orders'))
                            <li>
                                <a href="{{ route('store.sales.orders') }}"
                                    class="tp-link {{ request()->routeIs('store.sales.orders') ? 'active' : '' }}">
                                    All Orders
                                </a>
                            </li>
                            @endif

                            @if ($can('process_return'))
                            <li>
                                <a href="{{ route('store.sales.returns.index') }}"
                                    class="tp-link {{ request()->routeIs('store.sales.returns.*') ? 'active' : '' }}">
                                    Returns
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                {{-- SALES & BILLING --}}
                {{-- SALES & BILLING --}}
                @if ($can('view_daily_sales') || $can('view_monthly_sales') || $can('view_tax_summary'))
                <li>
                    @php
                    // Check if any sales route is active to keep menu open
                    $isSalesActive = request()->routeIs('store.sales.daily') || request()->routeIs('store.sales.weekly');
                    @endphp

                    <a href="#sidebarSales" data-bs-toggle="collapse"
                        class="tp-link {{ $isSalesActive ? 'active' : '' }}"
                        aria-expanded="{{ $isSalesActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:receipt"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Sales & Billing </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isSalesActive ? 'show' : '' }}" id="sidebarSales">
                        <ul class="nav-second-level">
                            @if ($can('view_daily_sales'))
                            <li>
                                <a href="{{ route('store.sales.daily') }}"
                                    class="tp-link {{ request()->routeIs('store.sales.daily') ? 'active' : '' }}">
                                    Daily Sales
                                </a>
                            </li>
                            @endif

                            @if ($can('view_monthly_sales')) {{-- Reusing permission for Weekly view if needed --}}
                            <li>
                                <a href="{{ route('store.sales.weekly') }}"
                                    class="tp-link {{ request()->routeIs('store.sales.weekly') ? 'active' : '' }}">
                                    Weekly Sales
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                {{-- CUSTOMERS --}}
                @if ($can('view_customers'))
                <li>
                    <a href="{{ route('customers.index') }}"
                        class="tp-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:users"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Customers </span>
                    </a>
                </li>
                @endif

                <li class="menu-title mt-2">Management</li>

                {{-- REPORTS --}}
                @if ($can('view_sales_report') || $can('view_stock_report'))
                <li>
                    @php $isReportsActive = request()->is('reports*'); @endphp
                    <a href="#sidebarReports" data-bs-toggle="collapse"
                        class="tp-link {{ $isReportsActive ? 'active' : '' }}"
                        aria-expanded="{{ $isReportsActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:chart-bar"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Reports </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ $isReportsActive ? 'show' : '' }}" id="sidebarReports">
                        <ul class="nav-second-level">
                            @if ($can('view_sales_report'))
                           <li>
                <a href="{{ route('store.reports.sales') }}" class="tp-link {{ request()->routeIs('store.reports.sales') ? 'active' : '' }}">
                    Sales Report
                </a>
            </li>
                            @endif

                            @if ($can('view_stock_report'))
                            <li>
                                <a href="{{ route('store.reports.stock') }}"
                                    class="tp-link {{ request()->routeIs('store.reports.stock') ? 'active' : '' }}">
                                    Stock Report
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                {{-- Support Module (Store) --}}
                @if (auth()->user()->hasPermission('raise_ticket'))
                <li class="menu-title mt-2">Support</li>
                <li>
                    <a href="#sidebarSupport" data-bs-toggle="collapse"
                        class="tp-link {{ request()->routeIs('store.support.*') ? 'active' : '' }}"
                        aria-expanded="{{ request()->routeIs('store.support.*') ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:headset"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Support </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ request()->routeIs('store.support.*') ? 'show' : '' }}"
                        id="sidebarSupport">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('store.support.create') }}"
                                    class="tp-link {{ request()->routeIs('store.support.create') ? 'active' : '' }}">
                                    Raise Ticket
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('store.support.index') }}"
                                    class="tp-link {{ request()->routeIs('store.support.index') ? 'active' : '' }}">
                                    My Tickets
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                {{-- STAFF MANAGEMENT --}}
                @if ($can('manage_staff') || $can('view_staff'))
                @php
                $isStaffActive = request()->routeIs('staff.*');
                @endphp
                <li class="menuitem-{{ $isStaffActive ? 'active' : '' }} {{ $isStaffActive ? 'show' : '' }}">

                    <a href="#sidebarStaff" data-bs-toggle="collapse"
                        class="tp-link {{ $isStaffActive ? 'active' : '' }}"
                        aria-expanded="{{ $isStaffActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:users-group"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Staff Management </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ $isStaffActive ? 'show' : '' }}" id="sidebarStaff">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('staff.index') }}"
                                    class="tp-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                                    Store Staff
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                {{-- ACCESS CONTROL (Roles) --}}
                @if ($can('manage_staff'))
                {{-- Restricted to Managers/Admins via manage_staff --}}
                @php
                $isRoleActive = request()->routeIs('roles.*');
                $isPermissionActive = request()->routeIs('permissions.*');
                $isAccessActive = $isRoleActive || $isPermissionActive;
                @endphp
                <li class="menuitem-{{ $isAccessActive ? 'active' : '' }} {{ $isAccessActive ? 'show' : '' }}">
                    <a href="#sidebarAccess" data-bs-toggle="collapse"
                        class="tp-link {{ $isAccessActive ? 'active' : '' }}"
                        aria-expanded="{{ $isAccessActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:lock"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Access Control </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isAccessActive ? 'show' : '' }}" id="sidebarAccess">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('roles.index') }}"
                                    class="tp-link {{ $isRoleActive ? 'active' : '' }}">
                                    Roles
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <li class="menu-title mt-2">System</li>

                {{-- SETTINGS --}}
                @if ($can('view_settings') || $can('update_store_settings'))
                @php
                $isProfileActive = request()->routeIs('settings.general');
                $isStoreSettingsActive = request()->routeIs('store.index') || request()->routeIs('store.edit');
                $isSettingsActive = $isProfileActive || $isStoreSettingsActive;
                @endphp
                <li
                    class="menuitem-{{ $isSettingsActive ? 'active' : '' }} {{ $isSettingsActive ? 'show' : '' }}">

                    <a href="#sidebarSettings" data-bs-toggle="collapse"
                        class="tp-link {{ $isSettingsActive ? 'active' : '' }}"
                        aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:settings"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Settings </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="sidebarSettings">
                        <ul class="nav-second-level">
                            @if ($can('view_settings'))
                            <li><a href="{{ route('settings.general') }}"
                                    class="tp-link {{ $isProfileActive ? 'active' : '' }}">General
                                    Settings</a></li>
                            @endif

                            @if ($can('update_store_settings'))
                            <li>
                                <a href="{{ route('store.index') }}"
                                    class="tp-link {{ $isStoreSettingsActive ? 'active' : '' }}">
                                    Store Settings
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

            </ul>
        </div>
    </div>
</div>