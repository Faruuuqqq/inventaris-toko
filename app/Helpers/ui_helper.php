<?php

use App\Models\ProductModel;
use App\Models\WarehouseModel;
use App\Models\StockMutationModel;

/**
 * Icon Helper
 * Returns SVG icon from Lucide
 */
function icon($name, $class = '') {
    $icons = [
        'LayoutDashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h7v7H3z M14 3h7v7h-7z M14 14h7v7h-7z M3 14h7v7H3z" />',
        'Users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
        'UserCheck' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z M9 17l2 2 4-4" />',
        'Package' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.27 6.96 12 12.01l8.73-5.05 M12 22.08V12" />',
        'Warehouse' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M5 21V7l8-4 8 4v14 M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4 M6 14H3" />',
        'BadgePercent' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9H4.5a2.5 2.5 0 01 0-5H6 M18 9h1.5a2.5 2.5 0 00 0-5H18 M6 15H4.5a2.5 2.5 0 00 0 5H6 M18 15h1.5a2.5 2.5 0 01 0 5H18 M7 6v1 M17 6v1 M7 17v1 M17 17v1" />',
        'ShoppingCart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />',
        'Banknote' => '<rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2" /><circle cx="12" cy="12" r="2" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01 M18 12h.01" />',
        'CreditCard' => '<rect x="1" y="4" width="22" height="16" rx="2" stroke-width="2" /><line x1="1" y1="10" x2="23" y2="10" stroke-width="2" />',
        'Receipt' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6l-2 2 2 2H4v6h6l-2 2 2 2H4v6h16v-6l-2-2 2-2H4v-6h6l-2-2 2-2H4V4z" />',
        'RotateCcw' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 4v6h6 M3.51 15a9 9 0 10 2.13-9.36L1 10" />',
        'FileText' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v6h6 M16 13H8 M16 17H8 M10 9H8" />',
        'ClipboardList' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
        'History' => '<circle cx="12" cy="12" r="10" stroke-width="2" /><polyline points="12 6 12 12 16 14" stroke-width="2" />',
        'Wallet' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12V7a2 2 0 00-2-2H5a2 2 0 00-2 2v5m18 0v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5m18 0h-3m-3 0h-3m3 0v-5m-3 5h-3" />',
        'BarChart3' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V10 M18 20V4 M6 20v-6" />',
        'Settings' => '<circle cx="12" cy="12" r="3" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />',
        'Truck' => '<rect x="1" y="3" width="15" height="13" stroke-width="2" /><rect x="9" y="13" width="13" height="4" stroke-width="2" /><rect x="13" y="13" width="2" height="4" stroke-width="2" /><circle cx="5.5" cy="18.5" r="2.5" stroke-width="2" /><circle cx="18.5" cy="18.5" r="2.5" stroke-width="2" />',
        'Search' => '<circle cx="11" cy="11" r="8" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35" />',
        'Plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14 M5 12h14" />',
        'Pencil' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />',
        'Trash2' => '<polyline points="3 6 5 6 21 6" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />',
        'TrendingUp' => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke-width="2" /><polyline points="17 6 23 6 23 12" stroke-width="2" />',
        'TrendingDown' => '<polyline points="23 18 13.5 8.5 8.5 13.5 1 6" stroke-width="2" /><polyline points="17 18 23 18 23 12" stroke-width="2" />',
        'Calculator' => '<rect x="4" y="2" width="16" height="20" rx="2" stroke-width="2" /><line x1="8" y1="6" x2="16" y2="6" stroke-width="2" /><line x1="16" y1="14" x2="16" y2="14" stroke-width="2" /><line x1="16" y1="18" x2="16" y2="18" stroke-width="2" /><line x1="12" y1="14" x2="12" y2="14" stroke-width="2" /><line x1="12" y1="18" x2="12" y2="18" stroke-width="2" /><line x1="8" y1="14" x2="8" y2="14" stroke-width="2" /><line x1="8" y1="18" x2="8" y2="18" stroke-width="2" />',
        'Printer' => '<polyline points="6 9 6 2 18 2 18 9" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" /><rect x="6" y="14" width="12" height="8" stroke-width="2" />',
        'Check' => '<polyline points="20 6 9 17 4 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />',
        'ChevronDown' => '<polyline points="6 9 12 15 18 9" stroke-width="2" />',
        'ChevronRight' => '<polyline points="9 18 15 12 9 6" stroke-width="2" />',
        'LogOut' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4m7 14l4 4m0 0l-4-4m4 4H9" />',
        'ArrowUpRight' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14l10-10m0 0l-10 10m10-10v8m0-8h-8" />',
        'ArrowDownRight' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6l10 10m0 0l-10-10m10 10v-8m0 8h-8" />',
        'Bell' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 0 1-3.46 0" />',
    ];

    $svg = $icons[$name] ?? '';

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
    
    $colorMap = [
        'success' => 'var(--success)',
        'destructive' => 'var(--destructive)',
        'warning' => 'var(--warning)',
        'secondary' => 'var(--muted-foreground)'
    ];

    return "<span class='badge badge-{$config['variant']}' style=\"background-color: {$colorMap[$config['variant']]}; color: white;\">{$config['text']}</span>";
}

/**
 * Calculate profit
 */
function calculate_profit($sales, $purchases) {
    return $sales - $purchases;
}