<?php

use App\Models\ProductModel;
use App\Models\WarehouseModel;
use App\Models\StockMutationModel;

/**
 * Icon helper function
 * Renders inline SVG icons for the UI
 * 
 * @param string $name Name of the icon
 * @param string $class Additional CSS classes
 * @return string SVG HTML
 */
function icon($name, $class = 'w-4 h-4') {
    static $icons = [
        'Package' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7.5 4.27 9 5.15" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3.3 7 8.7 5 8.7-5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V12" />',
        'TrendingUp' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 6l-9.5 9.5-5-5L1 18" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 6h6v6" />',
        'ShoppingCart' => '<circle cx="9" cy="21" r="1" /><circle cx="20" cy="21" r="1" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" />',
        'ArrowUpRight' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10v10" />',
        'Users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 21v-2a4 4 0 00-3-3.87" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3.13a4 4 0 010 7.75" />',
        'TrendingDown' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 18l-9.5-9.5-5 5L1 6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 18h6V12" />',
        'Search' => '<circle cx="11" cy="11" r="8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3" />',
        'Bell' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0" />',
        'Settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.22 2h-.44a2 2 0 00-2 2v.18a2 2 0 01-1 1.73l-.43.25a2 2 0 01-2 0l-.15-.08a2 2 0 00-2.73.73l-.22.38a2 2 0 00.73 2.73l.15.1a2 2 0 011 1.72v.51a2 2 0 01-1 1.74l-.15.09a2 2 0 00-.73 2.73l.22.38a2 2 0 002.73.73l.15-.08a2 2 0 012 0l.43.25a2 2 0 011 1.73V20a2 2 0 002 2h.44a2 2 0 002-2v-.18a2 2 0 011-1.73l.43-.25a2 2 0 012 0l.15.08a2 2 0 002.73-.73l.22-.39a2 2 0 00-.73-2.73l-.15-.09a2 2 0 01-1-1.74v-.47a2 2 0 011-1.74l.15-.09a2 2 0 00.73-2.73l-.22-.39a2 2 0 00-2.73-.73l-.15.08a2 2 0 01-2 0l-.43-.25a2 2 0 01-1-1.73V4a2 2 0 00-2-2z" /><circle cx="12" cy="12" r="3" />',
        'LogOut' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" /><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="16 17 21 12 16 7" /><line stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="21" y1="12" x2="9" y2="12" />',
        'Menu' => '<line stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="4" y1="12" x2="20" y2="12" /><line stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="4" y1="6" x2="20" y2="6" /><line stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="4" y1="18" x2="20" y2="18" />',
        'Plus' => '<line stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="12" y1="5" x2="12" y2="19" /><line stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="5" y1="12" x2="19" y2="12" />',
        'Edit' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />',
        'Trash' => '<polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="3 6 5 6 21 6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />',
        'Eye' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="3" />',
        'Refresh' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 4v6h-6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.49 15a9 9 0 11-2.12-9.36L23 10" />',
        'LayoutDashboard' => '<rect width="7" height="9" x="3" y="3" rx="1" /><rect width="7" height="5" x="14" y="3" rx="1" /><rect width="7" height="9" x="14" y="12" rx="1" /><rect width="7" height="5" x="3" y="16" rx="1" />',
        'ChevronDown' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />',
        'ChevronRight' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6" />',
        'UserCheck' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 11l6 6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m23 11-6 6" />',
        'Truck' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17h4V5H2v12h3" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 17h2v-3.34a4 4 0 00-1.17-2.83L19 9h-5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 17h1" /><circle cx="7.5" cy="17.5" r="2.5" /><circle cx="17.5" cy="17.5" r="2.5" />',
        'Warehouse' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 21V7l8-4 8 4v14" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-8.5a2.5 2.5 0 00-5 0V21" />',
        'BadgePercent' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.85 8.62a4 4 0 014.78-4.77 4 4 0 016.74 0 4 4 0 014.78 4.78 4 4 0 010 6.74 4 4 0 01-4.78 4.78 4 4 0 01-6.74 0 4 4 0 01-4.78-4.78 4 4 0 010-6.74Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 9-6 6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 9 1 1" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14 14 2 2" />',
        'Wallet' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 11V9a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2" /><rect x="15" y="10" width="6" height="6" rx="1" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12h.01" />',
        'Banknote' => '<rect x="2" y="6" width="20" height="12" rx="2" /><circle cx="12" cy="12" r="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M18 12h.01" />',
        'Calculator' => '<rect x="4" y="2" width="16" height="20" rx="2" /><line x1="8" y1="6" x2="16" y2="6" /><line x1="16" y1="14" x2="16" y2="18" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M8 18h.01M12 18h.01" />',
        'Printer' => '<polyline points="6 9 6 2 18 2 18 9" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" /><rect x="6" y="14" width="12" height="8" />',
        'Filter' => '<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />',
        'CreditCard' => '<rect x="2" y="5" width="20" height="14" rx="2" /><line x1="2" y1="10" x2="22" y2="10" />',
        'Receipt' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8h-6M16 12h-6M14 16h-4" />',
        'History' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v5h5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.05 13A9 9 0 1 0 6 5.3L3 8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l4 2" />',
        'RotateCcw' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v5h5" />',
        'ClipboardList' => '<rect x="8" y="2" width="8" height="4" rx="1" ry="1" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11h4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16h4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11h.01" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h.01" />',
        'BarChart3' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 17V9" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17V5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17v-3" />',
        'Calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />',
        'Check' => '<polyline points="20 6 9 17 4 12" />',
        'X' => '<line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />',
        'FileText' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" /><polyline points="14 2 14 8 20 8" /><line x1="16" y1="13" x2="8" y2="13" /><line x1="16" y1="17" x2="8" y2="17" /><line x1="10" y1="9" x2="8" y2="9" />',
        'ArrowDownRight' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 7 10 10" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7v10H7" />',
    ];
    
    $svg = $icons[$name] ?? '';
    
    if (empty($svg)) {
        return '';
    }
    
    return "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='{$class}'>{$svg}</svg>";
}

/**
 * Format currency to IDR
 */
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date to Indonesian locale
 */
function format_date($date) {
    if (empty($date)) return '-';
    return date('d M Y', strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime($datetime) {
    if (empty($datetime)) return '-';
    return date('d M Y H:i', strtotime($datetime));
}

/**
 * Get status badge HTML
 */
function badge_status($status) {
    $statuses = [
        'PAID' => ['variant' => 'success', 'text' => 'Lunas'],
        'UNPAID' => ['variant' => 'destructive', 'text' => 'Belum Bayar'],
        'PARTIAL' => ['variant' => 'warning', 'text' => 'Sebagian'],
        'CREDIT' => ['variant' => 'warning', 'text' => 'Kredit'],
        'CASH' => ['variant' => 'success', 'text' => 'Tunai'],
        'PENDING' => ['variant' => 'secondary', 'text' => 'Pending'],
        'COMPLETED' => ['variant' => 'success', 'text' => 'Selesai'],
        'CANCELLED' => ['variant' => 'destructive', 'text' => 'Batal'],
    ];

    $config = $statuses[$status] ?? ['variant' => 'secondary', 'text' => $status];
    
    // Map variants to Tailwind arbitrary classes for transparent background
    $classMap = [
        'success' => 'bg-[var(--success)]/10 text-[var(--success)]',
        'destructive' => 'bg-[var(--destructive)]/10 text-[var(--destructive)]',
        'warning' => 'bg-[var(--warning)]/10 text-[var(--warning)]',
        'secondary' => 'bg-muted text-muted-foreground'
    ];
    
    $classes = $classMap[$config['variant']] ?? $classMap['secondary'];

    return "<span class='inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {$classes}'>{$config['text']}</span>";
}

/**
 * Calculate profit
 */
function calculate_profit($sales, $purchases) {
    return $sales - $purchases;
}

/**
 * Alias for badge_status for backwards compatibility
 */
function status_badge($status) {
    return badge_status($status);
}

/**
 * Selected helper for form select options
 */
function selected($value, $compare) {
    return $value == $compare ? 'selected' : '';
}

/**
 * Check if current user is admin
 */
function is_admin() {
    $role = session()->get('role');
    return in_array($role, ['ADMIN', 'OWNER']);
}

/**
 * Check if current user is owner
 */
function is_owner() {
    return session()->get('role') === 'OWNER';
}

/**
 * Get warehouse name by ID
 */
function get_warehouse_name($warehouseId) {
    if (empty($warehouseId)) return '-';
    $model = new \App\Models\WarehouseModel();
    $warehouse = $model->find($warehouseId);
    return $warehouse ? $warehouse['name'] : '-';
}