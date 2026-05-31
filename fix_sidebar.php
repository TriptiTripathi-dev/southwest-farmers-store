<?php
$file = '/Users/abhaydwivedi/southwest-farmers-store/resources/views/layouts/partials/sidebar.blade.php';
$content = file_get_contents($file);

// 1. Dashboard
$content = preg_replace('/(<li>\s*<a href="{{ route\(\'dashboard\'\) }})/', '<li class="menuitem-{{ request()->routeIs(\'dashboard\') ? \'active\' : \'\' }}">$1', $content);

// 2. Inventory
$inventory_php = <<<'PHP'
                    @php
                    $isInventoryActive = request()->is('inventory*') || request()->is('transfers*') || request()->routeIs('store.audits.*') || request()->routeIs('store.inventory.*');
                    @endphp
                    <li class="menuitem-{{ $isInventoryActive ? 'active' : '' }} {{ $isInventoryActive ? 'show' : '' }}">
PHP;
$content = preg_replace('/<li>\s*@php\s*\$isInventoryActive = request\(\)->routeIs\([^;]+;\s*@endphp/s', $inventory_php, $content);

// Update inventory sub-items active states
$content = str_replace("request()->is('inventory/stock')", "request()->routeIs('inventory.index')", $content);
$content = str_replace("request()->is('inventory/requests')", "request()->routeIs('inventory.requests') || request()->routeIs('inventory.order.*')", $content);
$content = str_replace("request()->is('inventory/adjustments')", "request()->routeIs('inventory.adjustments')", $content);
$content = str_replace("request()->is('inventory/transfers')", "request()->is('transfers*')", $content);

// 3. Stock Control
$content = preg_replace('/<li>\s*(<a href="#sidebarStockControl")/', '<li class="menuitem-{{ request()->routeIs(\'store.stock-control.*\') ? \'active\' : \'\' }} {{ request()->routeIs(\'store.stock-control.*\') ? \'show\' : \'\' }}">' . "\n" . '                    $1', $content);

// 4. Orders
$orders_php = <<<'PHP'
                @php
                    $isPosActive = request()->routeIs('store.sales.pos');
                    $isOrdersRoute = request()->is('orders*') || request()->routeIs('store.sales.orders') || request()->routeIs('store.sales.returns.*');
                    $isOrdersActive = $isPosActive || $isOrdersRoute;
                @endphp
                <li class="menuitem-{{ $isOrdersActive ? 'active' : '' }} {{ $isOrdersActive ? 'show' : '' }}">
PHP;
$content = preg_replace('/<li>\s*@php\s*\$isPosActive = request\(\)->routeIs\(\'store.sales.pos\'\);\s*\$isOrdersRoute = request\(\)->is\(\'orders\*\'\) \|\| request\(\)->routeIs\(\'store.sales.orders\'\);\s*\$isOrdersActive = \$isPosActive \|\| \$isOrdersRoute;\s*@endphp/s', $orders_php, $content);

// 5. Promotions
$content = preg_replace('/<li>\s*(<a href="{{ route\(\'store\.promotions\.index\'\) }})/', '<li class="menuitem-{{ request()->routeIs(\'store.promotions.*\') ? \'active\' : \'\' }}">' . "\n" . '                    $1', $content);

// 6. Analytics Dashboard
$content = preg_replace('/<li>\s*(<a href="{{ route\(\'store\.analytics\.index\'\) }})/', '<li class="menuitem-{{ request()->routeIs(\'store.analytics.*\') ? \'active\' : \'\' }}">' . "\n" . '                    $1', $content);

// 7. Sales & Billing
$sales_php = <<<'PHP'
                @php
                    $isSalesActive = request()->routeIs('store.sales.daily') || request()->routeIs('store.sales.weekly');
                @endphp
                <li class="menuitem-{{ $isSalesActive ? 'active' : '' }} {{ $isSalesActive ? 'show' : '' }}">
PHP;
$content = preg_replace('/<li>\s*@php\s*\/\/[^\n]+\n\s*\$isSalesActive = request\(\)->routeIs\(\'store.sales.daily\'\) \|\| request\(\)->routeIs\(\'store.sales.weekly\'\);\s*@endphp/s', $sales_php, $content);

// 8. Customers
$content = preg_replace('/<li>\s*(<a href="{{ route\(\'customers\.index\'\) }})/', '<li class="menuitem-{{ request()->routeIs(\'customers.*\') ? \'active\' : \'\' }}">' . "\n" . '                    $1', $content);

// 9. Enquiries
$content = preg_replace('/<li>\s*(<a href="{{ route\(\'store\.enquiries\.index\'\) }})/', '<li class="menuitem-{{ request()->routeIs(\'store.enquiries.*\') ? \'active\' : \'\' }}">' . "\n" . '                    $1', $content);

// 10. Reports
$reports_php = <<<'PHP'
                @php $isReportsActive = request()->is('reports*') || request()->routeIs('store.reports.*'); @endphp
                <li class="menuitem-{{ $isReportsActive ? 'active' : '' }} {{ $isReportsActive ? 'show' : '' }}">
PHP;
$content = preg_replace('/<li>\s*@php \$isReportsActive = request\(\)->is\(\'reports\*\'\); @endphp/s', $reports_php, $content);

// 11. Support
$content = preg_replace('/<li>\s*(<a href="#sidebarSupport")/', '<li class="menuitem-{{ request()->routeIs(\'store.support.*\') ? \'active\' : \'\' }} {{ request()->routeIs(\'store.support.*\') ? \'show\' : \'\' }}">' . "\n" . '                    $1', $content);

file_put_contents($file, $content);
echo "Sidebar updated successfully.\n";
