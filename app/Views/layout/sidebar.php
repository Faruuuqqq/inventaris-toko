<?php
// Define menu items configuration
$menuItems = [
    [
        'title' => 'Dashboard',
        'icon' => 'dashboard',
        'path' => 'dashboard'
    ],
    [
        'title' => 'Data Utama',
        'icon' => 'database',
        'children' => [
            ['title' => 'Supplier', 'icon' => 'truck', 'path' => 'master/suppliers'],
            ['title' => 'Customer', 'icon' => 'users', 'path' => 'master/customers'],
            ['title' => 'Produk', 'icon' => 'package', 'path' => 'master/products'],
            ['title' => 'Gudang', 'icon' => 'warehouse', 'path' => 'master/warehouses'],
            ['title' => 'Sales', 'icon' => 'trending-up', 'path' => 'master/salespersons'],
        ]
    ],
    [
        'title' => 'Transaksi',
        'icon' => 'shopping-cart',
        'children' => [
            ['title' => 'Pembelian', 'icon' => 'shopping-bag', 'path' => 'transactions/purchases'],
            ['title' => 'Penjualan Tunai', 'icon' => 'credit-card', 'path' => 'transactions/sales/cash'],
            ['title' => 'Penjualan Kredit', 'icon' => 'tag', 'path' => 'transactions/sales/credit'],
            ['title' => 'Pembayaran Utang', 'icon' => 'send', 'path' => 'finance/payments/payable'],
            ['title' => 'Pembayaran Piutang', 'icon' => 'arrow-down-circle', 'path' => 'finance/payments/receivable'],
            ['title' => 'Retur Pembelian', 'icon' => 'undo', 'path' => 'transactions/purchase-returns'],
            ['title' => 'Retur Penjualan', 'icon' => 'undo', 'path' => 'transactions/sales-returns'],
            ['title' => 'Kontra Bon', 'icon' => 'clipboard', 'path' => 'finance/kontra-bon'],
        ]
    ],
    [
        'title' => 'Informasi',
        'icon' => 'file-text',
        'children' => [
            ['title' => 'Histori Pembelian', 'icon' => 'history', 'path' => 'info/history/purchases'],
            ['title' => 'Histori Penjualan', 'icon' => 'history', 'path' => 'info/history/sales'],
            ['title' => 'Histori Retur Pembelian', 'icon' => 'history', 'path' => 'info/history/return-purchases'],
            ['title' => 'Histori Retur Penjualan', 'icon' => 'history', 'path' => 'info/history/return-sales'],
            ['title' => 'Biaya/Jasa', 'icon' => 'wallet', 'path' => 'finance/expenses'],
            ['title' => 'Histori Bayar Utang', 'icon' => 'history', 'path' => 'info/history/payments-payable'],
            ['title' => 'Histori Bayar Piutang', 'icon' => 'history', 'path' => 'info/history/payments-receivable'],
        ]
    ],
    [
        'title' => 'Info Tambahan',
        'icon' => 'bar-chart-2',
        'children' => [
            ['title' => 'Saldo Piutang', 'icon' => 'wallet', 'path' => 'info/saldo/receivable'],
            ['title' => 'Saldo Utang', 'icon' => 'wallet', 'path' => 'info/saldo/payable'],
            ['title' => 'Saldo Stok', 'icon' => 'package', 'path' => 'info/saldo/stock'],
            ['title' => 'Kartu Stok', 'icon' => 'list', 'path' => 'info/stock/card'],
            ['title' => 'Laporan Harian', 'icon' => 'bar-chart-2', 'path' => 'info/reports/daily'],
        ]
    ],
];

// Robust Active State Helper
function isPathActive($path) {
    if (empty($path)) return false;
    $current = current_url(true)->getPath();
    return str_contains($current, $path); 
}

function isGroupActive($children) {
    foreach ($children as $child) {
        if (isPathActive($child['path'])) return true;
    }
    return false;
}

// Simple SVG icon generator
function getSvgIcon($name) {
    $icons = [
        'dashboard' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 6.908C9.75 6.287 10.254 5.783 10.875 5.783h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V6.908zm6.75 12.084c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V19.5c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V18.992z"/></svg>',
        'database' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 6.375c0 2.278-4.022 4.125-9 4.125S2.25 8.653 2.25 6.375m18 0A2.25 2.25 0 0020.25 4.125H3.75A2.25 2.25 0 002.25 6.375m18 0B9 12h11.25m-11-8.684c5.232 0 9.531 1.697 9.531 3.75 0 .385-.049.761-.144 1.126M7.5 14.25c5.232 0 9.531 1.697 9.531 3.75 0 .384-.049.761-.144 1.126m-15.882-3.876a9 9 0 014.586-3.876m0 0a9 9 0 014.586 3.876"/></svg>',
        'truck' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16h12m0 0l-3-3m3 3l-3 3M9 12H3m0 0l3-3m-3 3l3 3m9-8H3v10a2 2 0 002 2h14a2 2 0 002-2V4a2 2 0 00-2-2h-5l-2 3z"/></svg>',
        'users' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 001.591-.079 8.88 8.88 0 00.772-.024 8.378 8.378 0 002.664-.66 8.643 8.643 0 001.924-1.099 8.387 8.387 0 001.602-1.562 8.237 8.237 0 001.2-1.899 9.188 9.188 0 01-3.75-3.75M5 18.75a9.38 9.38 0 002.625.372 9.337 9.337 0 001.591-.079 8.88 8.88 0 00.772-.024 8.378 8.378 0 002.664-.66 8.643 8.643 0 001.924-1.099 8.387 8.387 0 001.602-1.562 8.237 8.237 0 001.2-1.899 9.188 9.188 0 01-3.75-3.75m-5.87 10.5a4.5 4.5 0 111.432-8.82 4.5 4.5 0 011.432 8.82zM16.5 9a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>',
        'package' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 6.375c0 2.278-4.022 4.125-9 4.125S2.25 8.653 2.25 6.375m18 0A2.25 2.25 0 0020.25 4.125H3.75A2.25 2.25 0 002.25 6.375m18 0B9 12h11.25m-11-8.684c5.232 0 9.531 1.697 9.531 3.75 0 .385-.049.761-.144 1.126M7.5 14.25c5.232 0 9.531 1.697 9.531 3.75 0 .384-.049.761-.144 1.126m-15.882-3.876a9 9 0 014.586-3.876m0 0a9 9 0 014.586 3.876"/></svg>',
        'warehouse' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>',
        'trending-up' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
        'shopping-cart' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m10-9l2 9m-9 0h14M7 22h10"/></svg>',
        'shopping-bag' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6a6 6 0 0112 0m-12 0a6 6 0 0012 0m-12 0H3m18 0h3m-3 6h3m-9 0h9m-9 6h9M9 16h6"/></svg>',
        'credit-card' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm4.5-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm4.5-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zM3.75 3h16.5A2.25 2.25 0 0122 5.25v13.5A2.25 2.25 0 0120.25 21H3.75A2.25 2.25 0 011.5 18.75V5.25A2.25 2.25 0 013.75 3z"/></svg>',
        'tag' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.883.883 2.318.883 3.201 0l6.914-6.914c.882-.883.882-2.318 0-3.201L11.322 3.659a2.25 2.25 0 00-1.591-.659zm0 0h8.117m0 0a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z"/></svg>',
        'send' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9-2m0 0l-9-12-9 12m9 2l9 2M3 7l6 3m0 0l6-3"/></svg>',
        'arrow-down-circle' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'undo' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>',
        'clipboard' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.148.42-.243.63-.97 2.905-.970 6.702.211 9.762.203.54.456 1.054.73 1.538M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'file-text' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.148.42-.243.63-.97 2.905-.970 6.702.211 9.762.203.54.456 1.054.73 1.538M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
        'history' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'wallet' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm4.5-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm4.5-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zM3.75 3h16.5A2.25 2.25 0 0122 5.25v13.5A2.25 2.25 0 0120.25 21H3.75A2.25 2.25 0 011.5 18.75V5.25A2.25 2.25 0 013.75 3z"/></svg>',
        'list' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 12a.75.75 0 100-1.5.75.75 0 000 1.5zM3.75 6.75a.75.75 0 100-1.5.75.75 0 000 1.5zM3.75 17.25a.75.75 0 100-1.5.75.75 0 000 1.5zM9 6h12M9 12h12m-12 6h12"/></svg>',
        'bar-chart-2' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 6.908C9.75 6.287 10.254 5.783 10.875 5.783h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V6.908zm6.75 12.084c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V19.5c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V18.992z"/></svg>',
        'settings' => '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>',
    ];
    return $icons[$name] ?? '<svg class="w-full h-full" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1" fill="currentColor"/><circle cx="19" cy="12" r="1" fill="currentColor"/><circle cx="5" cy="12" r="1" fill="currentColor"/></svg>';
}
?>

<!-- Sidebar - Modern Premium Enterprise Design -->
<aside 
    class="fixed inset-y-0 left-0 z-50 flex h-screen w-64 flex-col border-r border-sidebar-border bg-sidebar text-sidebar-fg transition-all duration-300 ease-out md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'"
>
    <!-- Logo Header - Premium gradient design -->
    <div class="relative flex h-20 items-center gap-3 border-b border-sidebar-border px-6 bg-gradient-to-r from-sidebar-accent via-sidebar to-sidebar">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary-light shadow-lg flex-shrink-0">
            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 3h18v2H3V3zm0 3h18v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6zm2 2v10h2V8H5zm4 0v10h2V8H9zm4 0v10h2V8h-2zm4 0v10h2V8h-2z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-bold text-sidebar-fg tracking-tight leading-tight">TokoManager</h1>
            <p class="text-xs text-sidebar-fg/60 leading-none">Inventory & Retail</p>
        </div>
        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" 
                class="ml-auto md:hidden text-sidebar-fg/70 hover:text-sidebar-fg rounded-lg p-2 hover:bg-sidebar-accent transition-all duration-200"
                aria-label="Close sidebar">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Navigation List - Scrollable with modern styling -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4 space-y-1.5 scrollbar-hide">
        <?php foreach ($menuItems as $item): ?>
            <?php if (isset($item['children'])): ?>
                <!-- Collapsible Group -->
                <div x-data="{ open: <?= isGroupActive($item['children']) ? 'true' : 'false' ?> }">
                    <button @click="open = !open" 
                        class="flex w-full items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200"
                        :class="open ? 'text-sidebar-fg bg-sidebar-accent shadow-sm' : 'text-sidebar-fg/75 hover:bg-sidebar-accent/50 hover:text-sidebar-fg'">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex items-center justify-center h-5 w-5 flex-shrink-0 opacity-90">
                                <?= getSvgIcon($item['icon']) ?>
                            </span>
                            <span class="truncate"><?= $item['title'] ?></span>
                        </div>
                        <!-- Chevron Animation -->
                        <span class="transition-transform duration-300 ease-out flex-shrink-0"
                              :class="open ? 'rotate-180' : 'rotate-0'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7-7m0 0L5 14m7-7v12"/>
                            </svg>
                        </span>
                    </button>
                    
                    <!-- Submenu Items -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
                         x-transition:enter-end="opacity-100 translate-y-0 max-h-screen"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 max-h-screen"
                         x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
                         class="ml-4 mt-2 space-y-1.5 border-l-2 border-sidebar-accent/40 pl-3 overflow-hidden">
                        <?php foreach ($item['children'] as $child): ?>
                            <a href="<?= base_url($child['path']) ?>" 
                               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs font-medium transition-all duration-200 group"
                               :class="<?= isPathActive($child['path']) ? "'bg-primary text-white shadow-md'" : "'text-sidebar-fg/70 hover:bg-sidebar-accent/60 hover:text-sidebar-fg'" ?>">
                                <span class="flex items-center justify-center h-4 w-4 flex-shrink-0 opacity-80 group-hover:opacity-100">
                                    <?= getSvgIcon($child['icon']) ?>
                                </span>
                                <span class="truncate"><?= $child['title'] ?></span>
                                <?php if (isPathActive($child['path'])): ?>
                                    <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white/50"></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Single Link -->
                <a href="<?= base_url($item['path']) ?>" 
                   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 group"
                   :class="<?= isPathActive($item['path']) ? "'bg-primary text-white shadow-md'" : "'text-sidebar-fg/75 hover:bg-sidebar-accent/50 hover:text-sidebar-fg'" ?>">
                    <span class="flex items-center justify-center h-5 w-5 flex-shrink-0 opacity-90">
                        <?= getSvgIcon($item['icon']) ?>
                    </span>
                    <span class="truncate"><?= $item['title'] ?></span>
                    <?php if (isPathActive($item['path'])): ?>
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white/50"></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <!-- Bottom Divider -->
    <div class="h-px bg-sidebar-border/50"></div>

    <!-- User Profile Footer - Modern card design -->
    <div class="p-4">
        <!-- Settings Button -->
        <a href="<?= base_url('settings') ?>" 
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-sidebar-fg/75 hover:bg-sidebar-accent/50 hover:text-sidebar-fg transition-all duration-200 mb-2"
           :class="<?= isPathActive('settings') ? "'bg-primary text-white shadow-md'" : "''" ?>">
            <span class="flex items-center justify-center h-5 w-5 flex-shrink-0">
                <?= getSvgIcon('settings') ?>
            </span>
            <span>Pengaturan</span>
        </a>

        <!-- User Profile Card -->
        <div class="mt-3 rounded-xl bg-sidebar-accent/50 p-3 border border-sidebar-border/50">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-white text-sm font-bold flex-shrink-0 shadow-md">
                    <?= substr(session()->get('fullname') ?? 'U', 0, 1) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-sidebar-fg truncate leading-tight"><?= session()->get('fullname') ?? 'User' ?></p>
                    <p class="text-xs text-sidebar-fg/60 capitalize truncate leading-tight"><?= session()->get('role') ?? 'Role' ?></p>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" 
               class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-sidebar-fg/75 hover:bg-sidebar-accent hover:text-destructive transition-all duration-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar</span>
            </a>
        </div>
    </div>
</aside>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>