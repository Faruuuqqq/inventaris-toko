<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Stats Grid: Matched to Referensi-UI/Dashboard.tsx (md:cols-2, lg:cols-4) -->
<div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
    <!-- Sales -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('TrendingUp', 'h-5 w-5 text-primary') ?>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-foreground"><?= format_currency($todaySales) ?></p>
                <p class="text-sm text-muted-foreground">Total Penjualan Hari Ini</p>
            </div>
        </div>
    </div>

    <!-- Purchases -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('ShoppingCart', 'h-5 w-5 text-primary') ?>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-foreground"><?= format_currency($todayPurchases) ?></p>
                <p class="text-sm text-muted-foreground">Total Pembelian Hari Ini</p>
            </div>
        </div>
    </div>

    <!-- Stock -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('Package', 'h-5 w-5 text-primary') ?>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-foreground"><?= number_format($totalStock, 0, ',', '.') ?></p>
                <p class="text-sm text-muted-foreground">Stok Produk</p>
            </div>
        </div>
    </div>

    <!-- Customers -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('Users', 'h-5 w-5 text-primary') ?>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-foreground"><?= number_format($activeCustomers, 0, ',', '.') ?></p>
                <p class="text-sm text-muted-foreground">Customer Aktif</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid gap-6 grid-cols-1 lg:grid-cols-3">
    <!-- Recent Transactions (Col Span 2) -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm lg:col-span-2">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight flex items-center gap-2">
                <?= icon('ShoppingCart', 'h-5 w-5') ?>
                Transaksi Terbaru
            </h3>
        </div>
        <div class="p-6 pt-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b text-muted-foreground/70">
                            <th class="pb-3 font-medium">ID</th>
                            <th class="pb-3 font-medium">Customer</th>
                            <th class="pb-3 font-medium">Jumlah</th>
                            <th class="pb-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentTransactions)): ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-muted-foreground">Belum ada transaksi</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentTransactions as $tx): ?>
                            <tr class="border-b last:border-0 hover:bg-muted/50 transition-colors">
                                <td class="py-3 font-medium text-primary"><?= $tx['transaction_number'] ?? $tx['id'] ?></td>
                                <td class="py-3"><?= $tx['customer_name'] ?></td>
                                <td class="py-3 font-medium"><?= format_currency($tx['total_amount']) ?></td>
                                <td class="py-3"><?= badge_status($tx['payment_status'] ?? 'PENDING') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert (Col Span 1) -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight flex items-center gap-2">
                <?= icon('TrendingDown', 'h-5 w-5 text-destructive') ?>
                Stok Menipis
            </h3>
        </div>
        <div class="p-6 pt-0">
            <div class="space-y-4">
                <?php if (empty($lowStockItems)): ?>
                    <p class="text-sm text-muted-foreground text-center">Stok aman</p>
                <?php else: ?>
                    <?php foreach ($lowStockItems as $item): ?>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium"><?= $item['name'] ?></p>
                            <p class="text-xs text-muted-foreground">Min: <?= $item['min_stock_alert'] ?></p>
                        </div>
                        <span class="rounded-full bg-destructive/10 px-2 py-1 text-sm font-medium text-destructive">
                            <?= $item['quantity'] ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6">
    <h3 class="mb-4 text-lg font-semibold">Aksi Cepat</h3>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
        <a href="<?= base_url('transactions/sales/cash') ?>" class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 transition-all hover:bg-accent hover:text-accent-foreground hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary">
                <?= icon('ShoppingCart', 'h-5 w-5 text-primary-foreground') ?>
            </div>
            <span class="font-medium">Buat Penjualan</span>
        </a>
        <a href="<?= base_url('finance/payments/receivable') ?>" class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 transition-all hover:bg-accent hover:text-accent-foreground hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success">
                <?= icon('Wallet', 'h-5 w-5 text-primary-foreground') ?>
            </div>
            <span class="font-medium">Terima Pembayaran</span>
        </a>
        <a href="<?= base_url('master/products') ?>" class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 transition-all hover:bg-accent hover:text-accent-foreground hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-foreground">
                <?= icon('Package', 'h-5 w-5 text-primary-foreground') ?>
            </div>
            <span class="font-medium">Tambah Produk</span>
        </a>
        <a href="<?= base_url('info/reports/daily') ?>" class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 transition-all hover:bg-accent hover:text-accent-foreground hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning">
                <?= icon('TrendingUp', 'h-5 w-5 text-primary-foreground') ?>
            </div>
            <span class="font-medium">Lihat Laporan</span>
        </a>
    </div>
</div>

<?= $this->endSection() ?>