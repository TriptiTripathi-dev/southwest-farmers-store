<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">
            @php

            $logo = asset('assets/images/logo.jpg');

            $settings = \App\Models\StoreSetting::first();
            if($settings && $settings->logo) {
            $logo = asset('storage/' . $settings->logo);
            }
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
                    <a href="{{ route('dashboard') }}" class="tp-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:layout-dashboard"></iconify-icon>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>

                <li class="menu-title mt-2">Operations</li>

                {{-- INVENTORY --}}
                <li>
                    @php
                    $isInventoryActive = request()->routeIs('inventory.index','inventory.requests','inventory.adjustments');
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
                            <li>
                                <a href="{{route('inventory.index')}}" class="tp-link {{ request()->is('inventory/stock') ? 'active' : '' }}">
                                    Store Stock
                                </a>
                            </li>
                            <li>
                                <a href="{{route('inventory.requests')}}" class="tp-link {{ request()->is('inventory/requests') ? 'active' : '' }}">
                                    Stock Requests
                                </a>
                            </li>
                            <li>
                                <a href="{{route('inventory.adjustments')}}" class="tp-link {{ request()->is('inventory/adjustments') ? 'active' : '' }}">
                                    Stock Adjustments
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- PRODUCTS --}}
                @php
                $isProductsActive = request()->routeIs('products.*');
                $isCategoryActive = request()->routeIs('store.categories.*');
                $isSubCategoryActive = request()->routeIs('store.subcategories.*');
                $allActive = $isProductsActive || $isCategoryActive || $isSubCategoryActive;
                @endphp
                <li class="menuitem-{{ $allActive  ? 'active' : '' }} {{ $allActive ? 'show' : '' }}">
                    <a class="tp-link {{ $allActive ? 'active' : '' }}" href="#sidebarProducts"  data-bs-toggle="collapse"
                        class="tp-link {{ $allActive ? 'active' : '' }}"
                        aria-expanded="{{ $allActive ? 'true' : 'false' }}" >
                        <i class="mdi mdi-package-variant-closed menu-icon"></i>
                        <span class="menu-title">Products</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse {{ $allActive ? 'show' : '' }}" id="sidebarProducts">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="tp-link {{ $isCategoryActive ? 'active' : '' }}" href="{{ route('store.categories.index') }}">Categories</a>
                            </li>
                            <li class="nav-item">
                                <a class="tp-link {{ $isSubCategoryActive ? 'active' : '' }}" href="{{ route('store.subcategories.index') }}">Subcategories</a>
                            </li>
                            <li class="nav-item">
                                <a class="tp-link {{ $isProductsActive ? 'active' : '' }}" href="{{ route('store.products.index') }}">Product List</a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- ORDERS --}}
                <li>
                    @php $isOrdersActive = request()->is('orders*'); @endphp
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
                            <li>
                                <a href="#" class="tp-link {{ request()->is('orders/create') ? 'active' : '' }}">
                                    Create Order
                                </a>
                            </li>
                            <li>
                                <a href="#" class="tp-link {{ request()->is('orders') && !request()->is('orders/create', 'orders/returns') ? 'active' : '' }}">
                                    All Orders
                                </a>
                            </li>
                            <li>
                                <a href="#" class="tp-link {{ request()->is('orders/returns') ? 'active' : '' }}">
                                    Returns
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- SALES & BILLING --}}
                <li>
                    @php $isSalesActive = request()->is('sales*'); @endphp
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
                            <li>
                                <a href="#" class="tp-link {{ request()->is('sales/daily') ? 'active' : '' }}">
                                    Daily Sales
                                </a>
                            </li>
                            <li>
                                <a href="#" class="tp-link {{ request()->is('sales/monthly') ? 'active' : '' }}">
                                    Monthly Sales
                                </a>
                            </li>
                            <li>
                                <a href="#" class="tp-link {{ request()->is('sales/tax') ? 'active' : '' }}">
                                    Tax Summary
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- CUSTOMERS --}}
                <li>
                    <a href="{{ route('customers.index') }}" class="tp-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:users-group"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Customers </span>
                    </a>
                </li>

                <li class="menu-title mt-2">Management</li>

                {{-- REPORTS --}}
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
                            <li>
                                <a href="#" class="tp-link {{ request()->is('reports/sales') ? 'active' : '' }}">
                                    Sales Report
                                </a>
                            </li>
                            <li>
                                <a href="#" class="tp-link {{ request()->is('reports/stock') ? 'active' : '' }}">
                                    Stock Report
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- SUPPORT --}}
                <li>
                    @php $isSupportActive = request()->is('support*'); @endphp
                    <a href="#sidebarSupport" data-bs-toggle="collapse"
                        class="tp-link {{ $isSupportActive ? 'active' : '' }}"
                        aria-expanded="{{ $isSupportActive ? 'true' : 'false' }}">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:headset"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Support </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ $isSupportActive ? 'show' : '' }}" id="sidebarSupport">
                        <ul class="nav-second-level">
                            <li>
                                <a href="#" class="tp-link {{ request()->is('support/create') ? 'active' : '' }}">
                                    Raise Ticket
                                </a>
                            </li>
                            <li>
                                <a href="#" class="tp-link {{ request()->is('support/history') ? 'active' : '' }}">
                                    Ticket History
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- STAFF MANAGEMENT --}}
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

                {{-- ACCESS CONTROL (Roles & Permissions) --}}
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
                            <!-- <li>
                                <a href="{{ route('permissions.index') }}"
                                    class="tp-link {{ $isPermissionActive ? 'active' : '' }}">
                                    Permissions
                                </a>
                            </li> -->
                        </ul>
                    </div>
                </li>

                <li class="menu-title mt-2">System</li>

                {{-- SETTINGS --}}
                @php
                $isProfileActive = request()->routeIs('settings.general');
                $isStoreSettingsActive = request()->routeIs('store.index') || request()->routeIs('store.edit');
                $isSettingsActive = $isProfileActive || $isStoreSettingsActive;
                @endphp
                <li class="menuitem-{{ $isSettingsActive ? 'active' : '' }} {{ $isSettingsActive ? 'show' : '' }}">

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
                            <li><a href="{{ route('settings.general') }}" class="tp-link {{ $isProfileActive ? 'active' : '' }}">General Settings</a></li>
                            <li>
                                <a href="{{ route('store.index') }}"
                                    class="tp-link {{ $isStoreSettingsActive ? 'active' : '' }}">
                                    Store Settings
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>