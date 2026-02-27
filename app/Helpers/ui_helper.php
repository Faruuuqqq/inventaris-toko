<?php

/**
 * Format currency to IDR
 */
function format_currency($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date to Indonesian locale
 */
function format_date($date)
{
    if (empty($date)) {
        return '-';
    }
    return date('d M Y', strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime($datetime)
{
    if (empty($datetime)) {
        return '-';
    }
    return date('d M Y H:i', strtotime($datetime));
}

/**
 * Get status badge HTML
 */
function badge_status($status)
{
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
        'secondary' => 'bg-muted text-muted-foreground',
    ];

    $classes = $classMap[$config['variant']] ?? $classMap['secondary'];

    return "<span class='inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {$classes}'>{$config['text']}</span>";
}

/**
 * Alias for badge_status for backwards compatibility
 */
function status_badge($status)
{
    return badge_status($status);
}

/**
 * Selected helper for form select options
 */
function selected($value, $compare)
{
    return $value === $compare ? 'selected' : '';
}

/**
 * Check if current user is admin
 */
function is_admin()
{
    $role = session()->get('role');
    return in_array($role, ['ADMIN', 'OWNER']);
}

function app_name()
{
    return config('App')->appName ?? 'TokoManager';
}

function app_version()
{
    return config('App')->appVersion ?? '1.0.0';
}

function app_edition()
{
    return config('App')->appEdition ?? 'Enterprise';
}
