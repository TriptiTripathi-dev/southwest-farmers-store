<?php

/*
|--------------------------------------------------------------------------
| Page headers, keyed by route name
|--------------------------------------------------------------------------
|
| One source for every page's title, breadcrumb path and description, so
| they always match the left-hand navigation (client 9/11 list, item 10).
| "section" and "title" are the sidebar's own labels, word for word; the
| breadcrumb is Dashboard > section > title, where the section links to its
| first page. <x-page-header /> reads this for the current route.
|
| When a sidebar label changes, change it here too.
*/

return [
    'dashboard' => ['title' => 'Dashboard', 'icon' => 'mdi mdi-view-dashboard-outline'],

    // Inventory control
    'inventory.index' => ['section' => 'Inventory control', 'title' => 'Store Inventory', 'icon' => 'mdi mdi-warehouse', 'description' => 'Manage and monitor your local inventory.'],
    'store.inventory.visibility' => ['section' => 'Inventory control', 'title' => 'Global Visibility', 'icon' => 'mdi mdi-earth', 'description' => 'Real-time stock levels across the Warehouse and all Store locations.'],
    'inventory.adjustments' => ['section' => 'Inventory control', 'title' => 'Inventory adjustment', 'icon' => 'mdi mdi-scale-balance', 'description' => 'Manually correct stock levels (damage, theft, returns).'],
    'transfers.index' => ['section' => 'Inventory control', 'title' => 'Store to store transfer', 'icon' => 'mdi mdi-swap-horizontal-bold', 'description' => 'Manage inventory transfers between locations.'],
    'kitchen-inventory.index' => ['section' => 'Inventory control', 'title' => 'Kitchen Inventory', 'icon' => 'mdi mdi-chef-hat', 'description' => 'Stock currently held in the kitchen, separate from the store shelf.'],
    'store.audits.index' => ['section' => 'Inventory control', 'title' => 'Inventory Audits', 'icon' => 'mdi mdi-clipboard-check-outline', 'description' => 'Manage and track physical inventory counts.'],

    // Warehouse Orders (PO)
    'inventory.requests' => ['section' => 'Warehouse Orders (PO)', 'title' => 'Store Requests (Unscheduled)', 'icon' => 'mdi mdi-clipboard-text-outline', 'description' => 'Manage unscheduled store requests and replenishment from the warehouse.'],
    'inventory.po.create' => ['section' => 'Warehouse Orders (PO)', 'title' => 'Store POs (Scheduled)', 'icon' => 'mdi mdi-calendar-check', 'description' => 'Place a large scheduled order for the warehouse.'],
    'store.orders.index' => ['section' => 'Warehouse Orders (PO)', 'title' => 'Receiving & History', 'icon' => 'mdi mdi-truck-delivery', 'description' => 'Track store purchase orders and incoming receiving shipments.'],

    // Products
    'departments.index' => ['section' => 'Products', 'title' => 'Departments', 'icon' => 'mdi mdi-domain', 'description' => 'Manage local and global catalog departments.'],
    'store.categories.index' => ['section' => 'Products', 'title' => 'Categories', 'icon' => 'mdi mdi-shape-outline', 'description' => 'Manage local and global catalog classifications.'],
    'store.subcategories.index' => ['section' => 'Products', 'title' => 'Subcategories', 'icon' => 'mdi mdi-sitemap', 'description' => 'Manage and organize product subcategories.'],
    'store.products.index' => ['section' => 'Products', 'title' => 'Product List', 'icon' => 'mdi mdi-package-variant', 'description' => 'Manage local store products and global warehouse items.'],

    // Prepared Menus
    'store.kitchen.kds.index' => ['section' => 'Prepared Menus', 'title' => 'KDS Screen', 'icon' => 'mdi mdi-monitor-dashboard', 'description' => 'Live view of active incoming kitchen orders.'],
    'menu-categories.index' => ['section' => 'Prepared Menus', 'title' => 'Menu Categories', 'icon' => 'mdi mdi-format-list-bulleted-type', 'description' => 'Manage categories for prepared meals and kitchen items.'],
    'menu-items.index' => ['section' => 'Prepared Menus', 'title' => 'Menu Items', 'icon' => 'mdi mdi-food', 'description' => 'Manage dishes and prepared food items sold in the store.'],
    'store.cookbook.index' => ['section' => 'Prepared Menus', 'title' => 'Cookbook Builder', 'icon' => 'mdi mdi-book-open-page-variant', 'description' => 'Recipes and step-by-step instructions for prepared menu items.'],
    'store.kitchen.production.index' => ['section' => 'Prepared Menus', 'title' => 'Production Logs', 'icon' => 'mdi mdi-pot-steam', 'description' => "Track batches of prepared menu items made in this store's kitchen."],
    'store.kitchen.production.leftovers' => ['section' => 'Prepared Menus', 'title' => 'Leftover Report', 'icon' => 'mdi mdi-food-off', 'description' => 'Daily produced vs. sold vs. leftover quantities per menu item.'],
    'store.kitchen.reports.sales-ranking' => ['section' => 'Prepared Menus', 'title' => 'Sales Ranking', 'icon' => 'mdi mdi-trophy-outline', 'description' => 'Best-selling menu items from point-of-sale data, to help plan daily kitchen production.'],
    'store.kitchen.availability.index' => ['section' => 'Prepared Menus', 'title' => 'Daily Availability & Calendar', 'icon' => 'mdi mdi-calendar-clock', 'description' => 'Daily menu scheduling, live stock toggles, advance notice periods and rush orders.'],
    'store.kitchen.staff.index' => ['section' => 'Prepared Menus', 'title' => 'Staff & Timesheets', 'icon' => 'mdi mdi-account-clock', 'description' => 'Shift assignments, kitchen stations, live clock-in/out logs and daily attendance.'],

    // Stock Control
    'store.stock-control.overview' => ['section' => 'Stock Control', 'title' => 'My Stock Overview', 'icon' => 'mdi mdi-package-variant-closed', 'description' => 'Real-time stock tracking and analytics.'],
    'store.stock-control.valuation' => ['section' => 'Stock Control', 'title' => 'Valuation', 'icon' => 'mdi mdi-cash-multiple', 'description' => "Track and analyze your inventory's monetary value."],
    'store.stock-control.recall.index' => ['section' => 'Stock Control', 'title' => 'Recall Requests', 'icon' => 'mdi mdi-alert-decagram', 'description' => 'Manage recalls, expiration alerts and low stock warnings.'],

    // POS System
    'store.sales.orders' => ['section' => 'POS System', 'title' => 'All POS Transactions', 'icon' => 'mdi mdi-receipt-text-outline', 'description' => 'Manage and view all customer transactions.'],
    'store.sales.returns.index' => ['section' => 'POS System', 'title' => 'POS Returns', 'icon' => 'mdi mdi-keyboard-return', 'description' => 'Manage and track all customer returns and refunds.'],

    'store.promotions.index' => ['title' => 'Promotions', 'icon' => 'mdi mdi-bullhorn-outline', 'description' => 'Create and manage promotional campaigns and discounts.'],
    'store.analytics.index' => ['title' => 'Analytics Dashboard', 'icon' => 'mdi mdi-chart-line', 'description' => 'Store performance and risk indicators for the current period.'],

    // Sales & Billing
    'store.sales.daily' => ['section' => 'Sales & Billing', 'title' => 'Daily Sales', 'icon' => 'mdi mdi-calendar-today', 'description' => 'Sales totals and transactions for the selected day.'],
    'store.sales.weekly' => ['section' => 'Sales & Billing', 'title' => 'Weekly Sales', 'icon' => 'mdi mdi-calendar-week', 'description' => 'Sales summary for the selected date range.'],

    'customers.index' => ['title' => 'Customers', 'icon' => 'mdi mdi-account-group', 'description' => 'Manage your store customers and track their information.'],
    'store.enquiries.index' => ['title' => 'Enquiries', 'icon' => 'mdi mdi-email-outline', 'description' => 'Customer enquiries received through the store website.'],

    // Reports
    'store.reports.sales' => ['section' => 'Reports', 'title' => 'Sales Report', 'icon' => 'mdi mdi-chart-bar', 'description' => 'Sales performance for the selected period.'],
    'store.reports.stock' => ['section' => 'Reports', 'title' => 'Stock Report', 'icon' => 'mdi mdi-clipboard-list-outline', 'description' => 'Current stock levels and inventory value.'],

    // Support
    'store.support.index' => ['section' => 'Support', 'title' => 'My Tickets', 'icon' => 'mdi mdi-ticket-confirmation-outline', 'description' => 'Track the support tickets raised by this store.'],
    'store.support.create' => ['section' => 'Support', 'title' => 'Raise Ticket', 'icon' => 'mdi mdi-lifebuoy', 'description' => 'Submit a new support request to the warehouse team.'],

    // Staff Management
    'staff.index' => ['section' => 'Staff Management', 'title' => 'Store Staff', 'icon' => 'mdi mdi-account-multiple', 'description' => 'Manage your team members and their roles.'],
    'store.time-clock.index' => ['section' => 'Staff Management', 'title' => 'Staff Time Clock', 'icon' => 'mdi mdi-clock-outline', 'description' => "Clock staff in and out using their store ID (see Store Staff for each employee's ID)."],

    // Access Control
    'roles.index' => ['section' => 'Access Control', 'title' => 'Roles', 'icon' => 'mdi mdi-shield-account-outline', 'description' => 'Manage staff roles and their associated system permissions.'],

    // Settings
    'settings.general' => ['section' => 'Settings', 'title' => 'General Settings', 'icon' => 'mdi mdi-cog-outline', 'description' => 'Store name, branding and general preferences.'],
    'settings.home-page' => ['section' => 'Settings', 'title' => 'Home Page', 'icon' => 'mdi mdi-home-outline', 'description' => 'Content shown on the store website home page.'],
    'settings.about-page' => ['section' => 'Settings', 'title' => 'About Page', 'icon' => 'mdi mdi-information-outline', 'description' => 'Content shown on the store website About page.'],
    'settings.contact-page' => ['section' => 'Settings', 'title' => 'Contact Page', 'icon' => 'mdi mdi-card-account-phone-outline', 'description' => 'Contact details shown on the store website.'],
    'settings.quick-pos' => ['section' => 'Settings', 'title' => 'Quick POS Page', 'icon' => 'mdi mdi-flash-outline', 'description' => 'Configure the Quick POS page.'],
    'settings.legal.index' => ['section' => 'Settings', 'title' => 'Legal Pages', 'icon' => 'mdi mdi-scale-balance', 'description' => 'Terms, privacy and other legal pages on the store website.'],
    'settings.newsletter.index' => ['section' => 'Settings', 'title' => 'Newsletter Subscribers', 'icon' => 'mdi mdi-email-newsletter', 'description' => 'People who subscribed to the store newsletter.'],
    'store.index' => ['section' => 'Settings', 'title' => 'Store Settings', 'icon' => 'mdi mdi-store-cog-outline', 'description' => 'Store locations and their details.'],
];
