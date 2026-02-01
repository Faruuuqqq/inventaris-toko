<?php
// Define menu items configuration
$menuItems = [
    [
        'title' => 'Dashboard',
        'icon' => 'LayoutDashboard',
        'path' => 'dashboard'
    ],
    [
        'title' => 'Data Utama',
        'icon' => 'Users',
        'children' => [
            ['title' => 'Supplier', 'icon' => 'Truck', 'path' => 'master/suppliers'],
            ['title' => 'Customer', 'icon' => 'UserCheck', 'path' => 'master/customers'],
            ['title' => 'Produk', 'icon' => 'Package', 'path' => 'master/products'],
            ['title' => 'Gudang', 'icon' => 'Warehouse', 'path' => 'master/warehouses'],
            ['title' => 'Sales', 'icon' => 'BadgePercent', 'path' => 'master/salespersons'],
        ]
    ],
    [
        'title' => 'Transaksi',
        'icon' => 'ShoppingCart',
        'children' => [
            ['title' => 'Pembelian', 'icon' => 'ShoppingCart', 'path' => 'transactions/purchases'],
            ['title' => 'Penjualan Tunai', 'icon' => 'Banknote', 'path' => 'transactions/sales/cash'],
            ['title' => 'Penjualan Kredit', 'icon' => 'CreditCard', 'path' => 'transactions/sales/credit'],
            ['title' => 'Pembayaran Utang', 'icon' => 'Receipt', 'path' => 'finance/payments/payable'],
            ['title' => 'Pembayaran Piutang', 'icon' => 'Receipt', 'path' => 'finance/payments/receivable'],
            ['title' => 'Retur Pembelian', 'icon' => 'RotateCcw', 'path' => 'transactions/purchase-returns'],
            ['title' => 'Retur Penjualan', 'icon' => 'RotateCcw', 'path' => 'transactions/sales-returns'],
            ['title' => 'Kontra Bon', 'icon' => 'ClipboardList', 'path' => 'finance/kontra-bon'],
        ]
    ],
    [
        'title' => 'Informasi',
        'icon' => 'History',
        'children' => [
            ['title' => 'Histori Pembelian', 'icon' => 'History', 'path' => 'info/history/purchases'],
            ['title' => 'Histori Penjualan', 'icon' => 'History', 'path' => 'info/history/sales'],
            ['title' => 'Histori Retur Pembelian', 'icon' => 'History', 'path' => 'info/history/return-purchases'],
            ['title' => 'Histori Retur Penjualan', 'icon' => 'History', 'path' => 'info/history/return-sales'],
            ['title' => 'Biaya/Jasa', 'icon' => 'Wallet', 'path' => 'finance/expenses'],
            ['title' => 'Histori Bayar Utang', 'icon' => 'History', 'path' => 'info/history/payments-payable'],
            ['title' => 'Histori Bayar Piutang', 'icon' => 'History', 'path' => 'info/history/payments-receivable'],
        ]
    ],
    [
        'title' => 'Info Tambahan',
        'icon' => 'BarChart3',
        'children' => [
            ['title' => 'Saldo Piutang', 'icon' => 'Wallet', 'path' => 'info/saldo/receivable'],
            ['title' => 'Saldo Utang', 'icon' => 'Wallet', 'path' => 'info/saldo/payable'],
            ['title' => 'Saldo Stok', 'icon' => 'Package', 'path' => 'info/saldo/stock'],
            ['title' => 'Kartu Stok', 'icon' => 'ClipboardList', 'path' => 'info/stock/card'],
            ['title' => 'Laporan Harian', 'icon' => 'BarChart3', 'path' => 'info/reports/daily'],
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
?>

<!-- Sidebar - Modern Professional Design -->
<aside 
    class="fixed inset-y-0 left-0 z-50 flex h-screen w-64 flex-col border-r border-sidebar-border bg-sidebar text-sidebar-foreground transition-all duration-300 ease-out md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'"
>
    <!-- Logo Header - Enhanced with gradient background -->
    <div class="relative flex h-16 items-center gap-3 border-b border-sidebar-border px-6 bg-gradient-to-r from-sidebar-accent to-sidebar bg-sidebar">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-sidebar-primary to-blue-600 shadow-md">
            <?= icon('Package', 'h-5 w-5 text-white') ?>
        </div>
        <div class="flex-1">
            <h1 class="text-lg font-bold text-sidebar-foreground tracking-tight">TokoManager</h1>
            <p class="text-xs text-sidebar-foreground/60 leading-none">Manajemen Toko</p>
        </div>
        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" 
                class="ml-auto md:hidden text-sidebar-foreground/70 hover:text-sidebar-foreground rounded-lg p-1.5 hover:bg-sidebar-accent transition-all duration-200"
                aria-label="Close sidebar">
            <?= icon('X', 'h-5 w-5') ?>
        </button>
    </div>

    <!-- Navigation List - Scrollable -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-1">
        <?php foreach ($menuItems as $item): ?>
            <?php if (isset($item['children'])): ?>
                <!-- Collapsible Group -->
                <div x-data="{ open: <?= isGroupActive($item['children']) ? 'true' : 'false' ?> }">
                    <button @click="open = !open" 
                        class="flex w-full items-center justify-between gap-3 rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all duration-200 hover:bg-sidebar-accent/80 active:scale-95"
                        :class="open ? 'text-sidebar-foreground bg-sidebar-accent/50' : 'text-sidebar-foreground/80 hover:text-sidebar-foreground'">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center h-5 w-5 flex-shrink-0">
                                <?= icon($item['icon'], 'h-5 w-5') ?>
                            </span>
                            <span><?= $item['title'] ?></span>
                        </div>
                        <!-- Chevron Animation -->
                        <span class="transition-transform duration-300 ease-out flex-shrink-0"
                              :class="open ? 'rotate-180' : 'rotate-0'">
                            <?= icon('ChevronDown', 'h-4 w-4') ?>
                        </span>
                    </button>
                    
                    <!-- Submenu Items -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1 max-h-0"
                         x-transition:enter-end="opacity-100 translate-y-0 max-h-screen"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 max-h-screen"
                         x-transition:leave-end="opacity-0 -translate-y-1 max-h-0"
                         class="ml-3.5 mt-1.5 space-y-1 border-l border-sidebar-accent/40 pl-4 overflow-hidden">
                        <?php foreach ($item['children'] as $child): ?>
                            <a href="<?= base_url($child['path']) ?>" 
                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200 group"
                               :class="<?= isPathActive($child['path']) ? "'bg-sidebar-primary text-white shadow-md'" : "'text-sidebar-foreground/80 hover:bg-sidebar-accent/60 hover:text-sidebar-foreground'" ?>">
                                <span class="flex items-center justify-center h-4 w-4 flex-shrink-0 opacity-80 group-hover:opacity-100">
                                    <?= icon($child['icon'], 'h-4 w-4') ?>
                                </span>
                                <span class="truncate"><?= $child['title'] ?></span>
                                <?php if (isPathActive($child['path'])): ?>
                                    <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white/40"></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Single Link -->
                <a href="<?= base_url($item['path']) ?>" 
                   class="flex items-center gap-3 rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all duration-200 hover:bg-sidebar-accent/80 active:scale-95"
                   :class="<?= isPathActive($item['path']) ? "'bg-sidebar-primary text-white shadow-md'" : "'text-sidebar-foreground/80 hover:text-sidebar-foreground'" ?>">
                    <span class="flex items-center justify-center h-5 w-5 flex-shrink-0">
                        <?= icon($item['icon'], 'h-5 w-5') ?>
                    </span>
                    <span><?= $item['title'] ?></span>
                    <?php if (isPathActive($item['path'])): ?>
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white/40"></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Settings Link -->
        <a href="<?= base_url('settings') ?>" 
           class="flex items-center gap-3 rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all duration-200 hover:bg-sidebar-accent/80 active:scale-95"
           :class="<?= isPathActive('settings') ? "'bg-sidebar-primary text-white shadow-md'" : "'text-sidebar-foreground/80 hover:text-sidebar-foreground'" ?>">
            <span class="flex items-center justify-center h-5 w-5 flex-shrink-0">
                <?= icon('Settings', 'h-5 w-5') ?>
            </span>
            <span>Pengaturan</span>
            <?php if (isPathActive('settings')): ?>
                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white/40"></span>
            <?php endif; ?>
        </a>
    </nav>

    <!-- User Profile Footer - Enhanced with better spacing -->
    <div class="border-t border-sidebar-border bg-gradient-to-t from-sidebar-accent/30 to-transparent p-4">
        <div class="mb-3 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sidebar-primary text-sm font-bold text-white shadow-md">
                <?= substr(session()->get('fullname') ?? 'U', 0, 1) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-sidebar-foreground truncate leading-tight"><?= session()->get('fullname') ?? 'User' ?></p>
                <p class="text-xs text-sidebar-foreground/60 capitalize truncate leading-tight"><?= session()->get('role') ?? 'Role' ?></p>
            </div>
        </div>
        <a href="<?= base_url('logout') ?>" 
           class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-foreground/80 hover:bg-sidebar-accent/80 hover:text-sidebar-foreground transition-all duration-200 active:scale-95">
            <?= icon('LogOut', 'h-4 w-4') ?>
            <span>Keluar</span>
        </a>
    </div>
</aside>