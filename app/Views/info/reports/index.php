<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header with Navigation -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('BarChart3', 'h-8 w-8 text-primary') ?>
            Dashboard Laporan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Analisis komprehensif kinerja bisnis Anda</p>
    </div>
</div>

<!-- Quick Navigation - Report Shortcuts -->
<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 mb-8">
    <a href="<?= base_url('/info/reports/daily') ?>" class="group rounded-lg border border-border/50 bg-surface p-5 shadow-sm hover:border-primary/50 hover:bg-primary/5 transition">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="font-semibold text-foreground group-hover:text-primary transition">Laporan Harian</h3>
                <p class="text-xs text-muted-foreground mt-1">Ringkasan penjualan & pembelian harian</p>
            </div>
            <div class="p-2 rounded-lg bg-primary/10 text-primary group-hover:bg-primary/20 transition">
                <?= icon('Calendar', 'h-5 w-5') ?>
            </div>
        </div>
    </a>

    <a href="<?= base_url('/info/reports/monthly-summary') ?>" class="group rounded-lg border border-border/50 bg-surface p-5 shadow-sm hover:border-success/50 hover:bg-success/5 transition">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="font-semibold text-foreground group-hover:text-success transition">Ringkasan Bulanan</h3>
                <p class="text-xs text-muted-foreground mt-1">Analisis tren bulanan dan performa</p>
            </div>
            <div class="p-2 rounded-lg bg-success/10 text-success group-hover:bg-success/20 transition">
                <?= icon('TrendingUp', 'h-5 w-5') ?>
            </div>
        </div>
    </a>

    <a href="<?= base_url('/info/reports/cash-flow') ?>" class="group rounded-lg border border-border/50 bg-surface p-5 shadow-sm hover:border-warning/50 hover:bg-warning/5 transition">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="font-semibold text-foreground group-hover:text-warning transition">Arus Kas</h3>
                <p class="text-xs text-muted-foreground mt-1">Analisis cash flow dan likuiditas</p>
            </div>
            <div class="p-2 rounded-lg bg-warning/10 text-warning group-hover:bg-warning/20 transition">
                <?= icon('DollarSign', 'h-5 w-5') ?>
            </div>
        </div>
    </a>

    <a href="<?= base_url('/info/reports/profit-loss') ?>" class="group rounded-lg border border-border/50 bg-surface p-5 shadow-sm hover:border-destructive/50 hover:bg-destructive/5 transition">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="font-semibold text-foreground group-hover:text-destructive transition">Laba & Rugi</h3>
                <p class="text-xs text-muted-foreground mt-1">Laporan profit & loss statement</p>
            </div>
            <div class="p-2 rounded-lg bg-destructive/10 text-destructive group-hover:bg-destructive/20 transition">
                <?= icon('TrendingDown', 'h-5 w-5') ?>
            </div>
        </div>
    </a>

    <a href="<?= base_url('/info/reports/product-performance') ?>" class="group rounded-lg border border-border/50 bg-surface p-5 shadow-sm hover:border-info/50 hover:bg-info/5 transition">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="font-semibold text-foreground group-hover:text-info transition">Performa Produk</h3>
                <p class="text-xs text-muted-foreground mt-1">Analisis produk terlaris dan ROI</p>
            </div>
            <div class="p-2 rounded-lg bg-info/10 text-info group-hover:bg-info/20 transition">
                <?= icon('Package', 'h-5 w-5') ?>
            </div>
        </div>
    </a>

    <a href="<?= base_url('/info/reports/customer-analysis') ?>" class="group rounded-lg border border-border/50 bg-surface p-5 shadow-sm hover:border-primary/50 hover:bg-primary/5 transition">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="font-semibold text-foreground group-hover:text-primary transition">Analisis Pelanggan</h3>
                <p class="text-xs text-muted-foreground mt-1">Segmentasi dan behavior pelanggan</p>
            </div>
            <div class="p-2 rounded-lg bg-primary/10 text-primary group-hover:bg-primary/20 transition">
                <?= icon('Users', 'h-5 w-5') ?>
            </div>
        </div>
    </a>
</div>

<!-- Key Metrics Section -->
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Sales This Month Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Penjualan Bulan Ini</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $salesThisMonth['count'] ?? 0 ?></p>
                    <p class="text-xs text-muted-foreground mt-2">Jumlah transaksi</p>
                </div>
                <div class="p-3 rounded-lg bg-primary/10">
                    <?= icon('ShoppingCart', 'h-6 w-6 text-primary') ?>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-border/50">
                <p class="text-sm font-semibold text-primary">
                    <?= format_currency($salesThisMonth['total'] ?? 0) ?>
                </p>
                <p class="text-xs text-muted-foreground mt-1">Revenue bulan ini</p>
            </div>
        </div>
    </div>

    <!-- Purchases This Month Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Pembelian Bulan Ini</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $purchasesThisMonth['count'] ?? 0 ?></p>
                    <p class="text-xs text-muted-foreground mt-2">Jumlah transaksi</p>
                </div>
                <div class="p-3 rounded-lg bg-success/10">
                    <?= icon('Package', 'h-6 w-6 text-success') ?>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-border/50">
                <p class="text-sm font-semibold text-success">
                    <?= format_currency($purchasesThisMonth['total'] ?? 0) ?>
                </p>
                <p class="text-xs text-muted-foreground mt-1">Total pembelian</p>
            </div>
        </div>
    </div>

    <!-- Total Sales Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Penjualan</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency($totalSales) ?>
                    </p>
                    <p class="text-xs text-muted-foreground mt-2">Semua waktu</p>
                </div>
                <div class="p-3 rounded-lg bg-info/10">
                    <?= icon('TrendingUp', 'h-6 w-6 text-info') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Purchases Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Pembelian</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency($totalPurchases) ?>
                    </p>
                    <p class="text-xs text-muted-foreground mt-2">Semua waktu</p>
                </div>
                <div class="p-3 rounded-lg bg-warning/10">
                    <?= icon('TrendingDown', 'h-6 w-6 text-warning') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid gap-6 lg:grid-cols-2 mb-6">
    <!-- Top Products -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Package', 'h-5 w-5 text-primary') ?>
                Produk Terlaris
            </h2>
        </div>
        <div class="p-6">
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr class="border-b border-border/50">
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                            <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Qty</th>
                            <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php if (empty($topProducts)): ?>
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <?= icon('Package', 'h-8 w-8 text-muted-foreground/50') ?>
                                        <span class="text-sm">Belum ada data</span>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topProducts as $product): ?>
                                <tr class="hover:bg-muted/50 transition">
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-semibold text-foreground"><?= $product['name'] ?? '' ?></p>
                                            <p class="text-xs text-muted-foreground"><?= $product['sku'] ?? '' ?></p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-foreground">
                                        <?= $product['total_sold'] ?? 0 ?>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-primary">
                                        <?= format_currency($product['total_revenue'] ?? 0) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Users', 'h-5 w-5 text-primary') ?>
                Pelanggan Setia
            </h2>
        </div>
        <div class="p-6">
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr class="border-b border-border/50">
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Pelanggan</th>
                            <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Transaksi</th>
                            <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php if (empty($topCustomers)): ?>
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <?= icon('Users', 'h-8 w-8 text-muted-foreground/50') ?>
                                        <span class="text-sm">Belum ada data</span>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topCustomers as $customer): ?>
                                <tr class="hover:bg-muted/50 transition">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-foreground"><?= $customer['name'] ?? '' ?></p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-primary/10 text-primary text-xs font-semibold">
                                            <?= $customer['transaction_count'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-primary">
                                        <?= format_currency($customer['total_spent'] ?? 0) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
<?php if (!empty($lowStockProducts)): ?>
    <div class="rounded-lg border border-warning/30 bg-warning/5 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-warning/30 bg-warning/10">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('AlertTriangle', 'h-5 w-5 text-warning') ?>
                Peringatan Stok Rendah
            </h2>
            <p class="text-sm text-muted-foreground mt-1"><?= count($lowStockProducts) ?> produk memiliki stok di bawah batas minimum</p>
        </div>
        <div class="p-6">
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr class="border-b border-border/50">
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                            <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Stok Saat Ini</th>
                            <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Min Stok</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php foreach ($lowStockProducts as $product): ?>
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="font-semibold text-foreground"><?= $product['name'] ?? '' ?></p>
                                        <p class="text-xs text-muted-foreground"><?= $product['sku'] ?? '' ?></p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-semibold <?= ($product['total_stock'] ?? 0) > 0 ? 'bg-warning/20 text-warning' : 'bg-destructive/20 text-destructive' ?>">
                                        <?= $product['total_stock'] ?? 0 ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-medium text-foreground">
                                    <?= $product['min_stock_alert'] ?? 0 ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if (($product['total_stock'] ?? 0) == 0): ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-destructive/20 text-destructive">
                                            <?= icon('X', 'h-3 w-3') ?>
                                            Stok Habis
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-warning/20 text-warning">
                                            <?= icon('AlertTriangle', 'h-3 w-3') ?>
                                            Stok Rendah
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>