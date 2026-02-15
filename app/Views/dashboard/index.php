<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header with Actions -->
<div class="mb-8 flex items-start justify-between">
    <div>
        <h2 class="text-2xl font-bold text-foreground">Selamat datang kembali! 👋</h2>
        <p class="mt-1 text-muted-foreground">Pantau performa bisnis Anda secara real-time</p>
    </div>
    <div class="text-right text-sm text-muted-foreground">
        <p><?= date('d M Y, H:i') ?></p>
    </div>
</div>

<!-- Primary KPI Cards Grid - Hero Stats with Gradients -->
<div class="mb-8 grid gap-5 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
    
    <!-- Card 1: Today's Sales - Emerald Gradient -->
    <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all hover:shadow-2xl hover:scale-[1.02] duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 via-emerald-600 to-green-600"></div>
        <div class="relative p-6">
            <!-- Background accent -->
            <div class="absolute right-0 top-0 -mr-12 -mt-12 h-32 w-32 rounded-full bg-white/10 transition-all group-hover:scale-125 duration-300"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 h-28 w-28 rounded-full bg-white/5"></div>
            
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-emerald-100">Penjualan Hari Ini</p>
                        <p class="mt-2 text-3xl font-bold text-white"><?= format_currency($todaySales) ?></p>
                        <div class="mt-3 flex items-center gap-1">
                            <?= icon('TrendingUp', 'h-4 w-4 text-emerald-200') ?>
                            <p class="text-xs text-emerald-100 font-semibold">↑ 12.5% dari kemarin</p>
                        </div>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                        <?= icon('TrendingUp', 'h-7 w-7 text-white') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
        <a href="<?= base_url('info/history/sales') ?>" class="absolute inset-0 z-20"></a>
    </div>

    <!-- Card 2: Today's Purchases - Blue Gradient -->
    <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all hover:shadow-2xl hover:scale-[1.02] duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600"></div>
        <div class="relative p-6">
            <div class="absolute right-0 top-0 -mr-12 -mt-12 h-32 w-32 rounded-full bg-white/10 transition-all group-hover:scale-125 duration-300"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 h-28 w-28 rounded-full bg-white/5"></div>
            
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-blue-100">Pembelian Hari Ini</p>
                        <p class="mt-2 text-3xl font-bold text-white"><?= format_currency($todayPurchases) ?></p>
                        <p class="mt-3 text-xs text-blue-100 font-medium">5 transaksi</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                        <?= icon('ShoppingCart', 'h-7 w-7 text-white') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
        <a href="<?= base_url('transactions/purchases') ?>" class="absolute inset-0 z-20"></a>
    </div>

    <!-- Card 3: Total Stock - Orange Gradient -->
    <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all hover:shadow-2xl hover:scale-[1.02] duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600"></div>
        <div class="relative p-6">
            <div class="absolute right-0 top-0 -mr-12 -mt-12 h-32 w-32 rounded-full bg-white/10 transition-all group-hover:scale-125 duration-300"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 h-28 w-28 rounded-full bg-white/5"></div>
            
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-orange-100">Total Stok</p>
                        <p class="mt-2 text-3xl font-bold text-white"><?= number_format($totalStock, 0, ',', '.') ?></p>
                        <p class="mt-3 text-xs text-orange-100 font-medium"><?= $activeCustomers ?? 0 ?> produk aktif</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                        <?= icon('Package', 'h-7 w-7 text-white') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
        <a href="<?= base_url('info/saldo/stock') ?>" class="absolute inset-0 z-20"></a>
    </div>

    <!-- Card 4: Active Customers - Purple Gradient -->
    <div class="group relative overflow-hidden rounded-xl shadow-lg transition-all hover:shadow-2xl hover:scale-[1.02] duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600"></div>
        <div class="relative p-6">
            <div class="absolute right-0 top-0 -mr-12 -mt-12 h-32 w-32 rounded-full bg-white/10 transition-all group-hover:scale-125 duration-300"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 h-28 w-28 rounded-full bg-white/5"></div>
            
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-purple-100">Customer Aktif</p>
                        <p class="mt-2 text-3xl font-bold text-white"><?= number_format($activeCustomers, 0, ',', '.') ?></p>
                        <div class="mt-3 flex items-center gap-1">
                            <?= icon('TrendingUp', 'h-4 w-4 text-purple-200') ?>
                            <p class="text-xs text-purple-100 font-semibold">↑ 3 pelanggan baru minggu ini</p>
                        </div>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                        <?= icon('Users', 'h-7 w-7 text-white') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
        <a href="<?= base_url('master/customers') ?>" class="absolute inset-0 z-20"></a>
    </div>
</div>

<!-- Main Grid: Transactions + Alerts -->
<div class="grid gap-6 grid-cols-1 lg:grid-cols-3 mb-8">
    
    <!-- Recent Transactions Table (2/3 width) -->
    <div class="lg:col-span-2">
        <div class="card">
            <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-foreground flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                        <?= icon('ShoppingCart', 'h-5 w-5 text-primary') ?>
                    </div>
                    Transaksi Terbaru
                </h3>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">5 terakhir</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border/50 bg-background/50">
                            <th class="px-6 py-4 text-left font-semibold text-foreground uppercase text-xs tracking-wide">ID</th>
                            <th class="px-6 py-4 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Customer</th>
                            <th class="px-6 py-4 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Jumlah</th>
                            <th class="px-6 py-4 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Status</th>
                            <th class="px-6 py-4 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentTransactions)): ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                    <?= icon('Package', 'h-12 w-12 opacity-30 text-muted-foreground') ?>
                                        <p class="font-medium text-muted-foreground">Belum ada transaksi</p>
                                        <p class="text-xs text-muted-foreground/70">Transaksi akan muncul di sini setelah Anda membuat yang pertama</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentTransactions as $tx): ?>
                                <tr class="border-b border-border/30 hover:bg-primary/5 transition-colors duration-200">
                                    <td class="px-6 py-4 font-semibold text-primary"><?= $tx->invoice_number ?? $tx->id ?></td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-foreground/80">
                                            <?= date('d M Y', strtotime($tx->created_at)) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-foreground"><?= $tx->customer_name ?? 'N/A' ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-lg text-foreground">
                                            <?= format_currency($tx->total_amount) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $statusClass = match($tx->payment_status ?? 'UNPAID') {
                                            'PAID' => 'bg-success/10 text-success border-success/30',
                                            'PARTIAL' => 'bg-warning/10 text-warning border-warning/30',
                                            default => 'bg-danger/10 text-danger border-danger/30'
                                        };
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border <?= $statusClass ?>">
                                            <?= $tx->payment_status ?? 'UNPAID' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="#" class="text-primary hover:text-primary-light font-semibold text-xs transition duration-200 hover:underline">Lihat</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-border/50 bg-background/30 px-6 py-4">
                <a href="<?= base_url('info/history/sales') ?>" class="text-sm font-semibold text-primary hover:text-primary-light transition duration-200 flex items-center gap-1">
                    Lihat semua transaksi
                    <?= icon('ArrowRight', 'h-4 w-4') ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert - Enhanced -->
    <div class="card overflow-hidden">
        <div class="border-b border-border/50 px-6 py-4">
                <h3 class="text-lg font-bold text-foreground flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-destructive/10">
                        <?= icon('AlertCircle', 'h-5 w-5 text-destructive') ?>
                    </div>
                    Stok Menipis
            </h3>
        </div>
        
        <div class="p-6 space-y-3">
            <?php if (empty($lowStockItems)): ?>
                <div class="text-center py-8">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-success/10 mx-auto mb-3">
                        <?= icon('CheckCircle', 'h-8 w-8 text-success') ?>
                    </div>
                    <p class="text-sm font-semibold text-success">Stok aman!</p>
                    <p class="text-xs text-muted-foreground mt-1">Semua stok dalam kondisi sehat</p>
                </div>
            <?php else: ?>
                <?php foreach ($lowStockItems as $item): ?>
                    <div class="flex items-center justify-between p-4 rounded-lg border-l-4 border-l-destructive bg-destructive/5 hover:bg-destructive/10 transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-foreground truncate"><?= $item->name ?></p>
                            <p class="text-xs text-muted-foreground mt-1">Min: <?= $item->min_stock_alert ?? $item->min_stock ?? 0 ?> unit</p>
                        </div>
                        <span class="rounded-lg bg-destructive text-white px-3 py-1 text-sm font-bold flex-shrink-0 ml-3">
                            <?= $item->current_stock ?? $item->quantity ?? 0 ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="border-t border-border/50 bg-background/30 px-6 py-4">
            <a href="<?= base_url('info/saldo/stock') ?>" class="text-sm font-semibold text-destructive hover:text-destructive-light transition duration-200 flex items-center gap-1">
                Kelola stok
                <?= icon('ArrowRight', 'h-4 w-4') ?>
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions Section - Enhanced -->
<div class="mb-6">
    <h3 class="text-lg font-bold text-foreground mb-4 flex items-center gap-2">
        <?= icon('Zap', 'h-6 w-6 text-primary') ?>
        Aksi Cepat
    </h3>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
        <!-- New Sale -->
        <a href="<?= base_url('transactions/sales/cash') ?>" 
           class="group relative overflow-hidden rounded-xl border-2 border-primary/20 bg-gradient-to-br from-primary/5 to-transparent p-5 hover:border-primary/50 hover:shadow-lg transition-all duration-300 hover:scale-105">
            <div class="absolute top-0 right-0 -mr-6 -mt-6 h-20 w-20 rounded-full bg-primary/5 group-hover:bg-primary/10 transition-all"></div>
                <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/15 group-hover:bg-primary/25 transition">
                    <?= icon('Plus', 'h-6 w-6 text-primary') ?>
                </div>
                <div>
                    <p class="font-bold text-foreground group-hover:text-primary transition">Buat Penjualan</p>
                    <p class="text-xs text-muted-foreground">Transaksi baru</p>
                </div>
            </div>
        </a>

        <!-- Receive Payment -->
        <a href="<?= base_url('finance/payments/receivable') ?>" 
           class="group relative overflow-hidden rounded-xl border-2 border-success/20 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/50 hover:shadow-lg transition-all duration-300 hover:scale-105">
            <div class="absolute top-0 right-0 -mr-6 -mt-6 h-20 w-20 rounded-full bg-success/5 group-hover:bg-success/10 transition-all"></div>
                <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success/15 group-hover:bg-success/25 transition">
                    <?= icon('DollarSign', 'h-6 w-6 text-success') ?>
                </div>
                <div>
                    <p class="font-bold text-foreground group-hover:text-success transition">Terima Pembayaran</p>
                    <p class="text-xs text-muted-foreground">Piutang masuk</p>
                </div>
            </div>
        </a>

        <!-- Add Product -->
        <a href="<?= base_url('master/products') ?>" 
           class="group relative overflow-hidden rounded-xl border-2 border-secondary/20 bg-gradient-to-br from-secondary/5 to-transparent p-5 hover:border-secondary/50 hover:shadow-lg transition-all duration-300 hover:scale-105">
            <div class="absolute top-0 right-0 -mr-6 -mt-6 h-20 w-20 rounded-full bg-secondary/5 group-hover:bg-secondary/10 transition-all"></div>
                <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-secondary/15 group-hover:bg-secondary/25 transition">
                    <?= icon('Plus', 'h-6 w-6 text-secondary') ?>
                </div>
                <div>
                    <p class="font-bold text-foreground group-hover:text-secondary transition">Tambah Produk</p>
                    <p class="text-xs text-muted-foreground">Katalog produk</p>
                </div>
            </div>
        </a>

        <!-- View Report -->
        <a href="<?= base_url('info/reports/daily') ?>" 
           class="group relative overflow-hidden rounded-xl border-2 border-warning/20 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/50 hover:shadow-lg transition-all duration-300 hover:scale-105">
            <div class="absolute top-0 right-0 -mr-6 -mt-6 h-20 w-20 rounded-full bg-warning/5 group-hover:bg-warning/10 transition-all"></div>
                <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning/15 group-hover:bg-warning/25 transition">
                    <?= icon('BarChart', 'h-6 w-6 text-warning') ?>
                </div>
                <div>
                    <p class="font-bold text-foreground group-hover:text-warning transition">Lihat Laporan</p>
                    <p class="text-xs text-muted-foreground">Analisis harian</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>