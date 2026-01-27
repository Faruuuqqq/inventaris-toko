<!-- Stats Grid -->
<div class="mb-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    <?php helper('ui_helper'); ?>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('TrendingUp', 'h-5 w-5 text-primary') ?>
                </div>
                <div class="flex items-center gap-1 text-sm" style="color: var(--success);">
                    +12.5%
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor'><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14l10-10m0 0l-10 10m10-10v8m0-8h-8" /></svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-foreground"><?= format_currency($todaySales) ?></p>
                <p class="text-sm text-muted-foreground">Total Penjualan Hari Ini</p>
            </div>
        </div>
    </div>

    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('ShoppingCart', 'h-5 w-5 text-primary') ?>
                </div>
                <div class="flex items-center gap-1 text-sm text-success">
                    +5.2%
                    <?= icon('ArrowUpRight', 'h-4 w-4') ?>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-foreground"><?= format_currency($todayPurchases) ?></p>
                <p class="text-sm text-muted-foreground">Total Pembelian Hari Ini</p>
            </div>
        </div>
    </div>

    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('Package', 'h-5 w-5 text-primary') ?>
                </div>
                <div class="flex items-center gap-1 text-sm" style="color: var(--destructive);">
                    -2.1%
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor'><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6l10 10m0 0l-10-10m10 10v-8m0 8h-8" /></svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-foreground"><?= $totalStock ?></p>
                <p class="text-sm text-muted-foreground">Stok Produk</p>
            </div>
        </div>
    </div>

    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('Users', 'h-5 w-5 text-primary') ?>
                </div>
                <div class="flex items-center gap-1 text-sm text-success">
                    +8.3%
                    <?= icon('ArrowUpRight', 'h-4 w-4') ?>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-foreground"><?= $activeCustomers ?></p>
                <p class="text-sm text-muted-foreground">Customer Aktif</p>
            </div>
        </div>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Recent Transactions -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm lg:col-span-2">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                <?= icon('ShoppingCart', 'h-5 w-5') ?>
                Transaksi Terbaru
            </h3>
        </div>
        <div class="p-6 pt-0">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <th class="pb-3 font-medium text-left">No. Invoice</th>
                        <th class="pb-3 font-medium text-left">Customer</th>
                        <th class="pb-3 font-medium text-left">Jumlah</th>
                        <th class="pb-3 font-medium text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    <?php foreach ($recentTransactions as $tx): ?>
                    <tr class="border-b last:border-0">
                        <td class="py-3 text-sm font-medium text-primary"><?= $tx['invoice_number'] ?></td>
                        <td class="py-3 text-sm"><?= $tx['customer_name'] ?></td>
                        <td class="py-3 text-sm font-medium"><?= format_currency($tx['total_amount']) ?></td>
                        <td class="py-3">
                            <?= badge_status($tx['payment_status']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                <?= icon('TrendingDown', 'h-5 w-5 text-destructive') ?>
                Stok Menipis
            </h3>
        </div>
        <div class="p-6 pt-0">
            <div class="space-y-4">
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
            </div>
        </div>
    </div>
</div>