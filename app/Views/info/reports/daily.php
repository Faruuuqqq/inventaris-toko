<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header with Filter -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Calendar', 'h-8 w-8 text-primary') ?>
            Laporan Harian
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Ringkasan transaksi penjualan dan pembelian per hari</p>
    </div>
    
    <form action="<?= base_url('/info/reports/daily') ?>" method="get" class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="space-y-2 w-full sm:w-auto">
            <label for="date" class="text-sm font-medium text-foreground">Pilih Tanggal</label>
            <div class="flex gap-2">
                <input type="date" id="date" name="date" value="<?= $date ?>" class="h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <button type="submit" class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition whitespace-nowrap">
                    <?= icon('Filter', 'h-5 w-5') ?>
                    Tampilkan
                </button>
            </div>
        </div>
        
        <!-- Export and Hidden Options -->
        <div class="flex flex-col sm:flex-row gap-2 items-end">
            <?php if ($isOwner): ?>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="includeHidden" name="include_hidden" value="1" 
                       class="rounded border-gray-300 text-primary focus:ring-primary"
                       onchange="this.form.submit()">
                <label for="includeHidden" class="text-sm text-gray-600 cursor-pointer">
                    Sertakan transaksi tersembunyi
                </label>
            </div>
            <?php endif; ?>
            
            <a href="<?= current_url() ?>?export=csv&date=<?= $date ?>&include_hidden=<?= isset($_GET['include_hidden']) ? $_GET['include_hidden'] : '0' ?>" 
               class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                <?= icon('FileText', 'w-4 h-4') ?>
                Export CSV
            </a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Sales -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Penjualan</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency($summary['total_sales'] ?? 0) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-primary/10">
                    <?= icon('ShoppingCart', 'h-6 w-6 text-primary') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Purchases -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Pembelian</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency($summary['total_purchases'] ?? 0) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-success/10">
                    <?= icon('Package', 'h-6 w-6 text-success') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Returns -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Retur</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency($summary['total_returns'] ?? 0) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-warning/10">
                    <?= icon('RotateCcw', 'h-6 w-6 text-warning') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Transaksi</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= $summary['transaction_count'] ?? 0 ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-info/10">
                    <?= icon('FileText', 'h-6 w-6 text-info') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('ShoppingCart', 'h-5 w-5 text-primary') ?>
            Penjualan - <?= format_date($date) ?>
        </h2>
    </div>
    <div class="p-6 overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/50 border-b border-border/50">
                <tr>
                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">No. Faktur</th>
                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Customer</th>
                    <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Tipe</th>
                    <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Status</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <?= icon('ShoppingCart', 'h-8 w-8 text-muted-foreground/50') ?>
                                <span class="text-sm">Tidak ada penjualan pada tanggal ini</span>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr class="hover:bg-muted/50 transition">
                            <td class="px-4 py-3 font-semibold text-foreground"><?= $sale['invoice_number'] ?? '' ?></td>
                            <td class="px-4 py-3 text-foreground"><?= $sale['customer_name'] ?? '' ?></td>
                            <td class="px-4 py-3 text-center"><?= badge_status($sale['payment_type'] ?? '') ?></td>
                            <td class="px-4 py-3 text-center"><?= badge_status($sale['payment_status'] ?? '') ?></td>
                            <td class="px-4 py-3 text-right font-semibold text-primary">
                                <?= format_currency($sale['final_amount'] ?? 0) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot class="bg-muted/30 border-t border-border/50 font-semibold">
                <tr>
                    <th colspan="4" class="px-4 py-3 text-right text-foreground">Total Penjualan:</th>
                    <th class="px-4 py-3 text-right text-primary">
                        <?= format_currency($summary['total_sales'] ?? 0) ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Purchases Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Package', 'h-5 w-5 text-primary') ?>
            Pembelian - <?= format_date($date) ?>
        </h2>
    </div>
    <div class="p-6 overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/50 border-b border-border/50">
                <tr>
                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">No. PO</th>
                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Supplier</th>
                    <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Status</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                <?php if (empty($purchases)): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <?= icon('Package', 'h-8 w-8 text-muted-foreground/50') ?>
                                <span class="text-sm">Tidak ada pembelian pada tanggal ini</span>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($purchases as $purchase): ?>
                        <tr class="hover:bg-muted/50 transition">
                            <td class="px-4 py-3 font-semibold text-foreground"><?= $purchase['po_number'] ?? '' ?></td>
                            <td class="px-4 py-3 text-foreground"><?= $purchase['supplier_name'] ?? '' ?></td>
                            <td class="px-4 py-3 text-center"><?= badge_status($purchase['status'] ?? '') ?></td>
                            <td class="px-4 py-3 text-right font-semibold text-primary">
                                <?= format_currency($purchase['total_amount'] ?? 0) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot class="bg-muted/30 border-t border-border/50 font-semibold">
                <tr>
                    <th colspan="3" class="px-4 py-3 text-right text-foreground">Total Pembelian:</th>
                    <th class="px-4 py-3 text-right text-primary">
                        <?= format_currency($summary['total_purchases'] ?? 0) ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Returns Section -->
<div class="grid gap-6 lg:grid-cols-2">
    <!-- Sales Returns -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('RotateCcw', 'h-5 w-5 text-primary') ?>
                Retur Penjualan
            </h2>
        </div>
        <div class="p-6 overflow-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 border-b border-border/50">
                    <tr>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Customer</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Alasan</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    <?php if (empty($returns['sales_returns'])): ?>
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-muted-foreground">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <?= icon('RotateCcw', 'h-6 w-6 text-muted-foreground/50') ?>
                                    <span class="text-xs">Tidak ada retur penjualan</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($returns['sales_returns'] as $return): ?>
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-4 py-3 font-medium text-foreground"><?= $return['customer_name'] ?? '' ?></td>
                                <td class="px-4 py-3 text-sm text-muted-foreground"><?= $return['reason'] ?? '' ?></td>
                                <td class="px-4 py-3 text-right font-semibold text-primary">
                                    <?= format_currency($return['final_amount'] ?? 0) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Purchase Returns -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('RotateCcw', 'h-5 w-5 text-primary') ?>
                Retur Pembelian
            </h2>
        </div>
        <div class="p-6 overflow-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 border-b border-border/50">
                    <tr>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Supplier</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Alasan</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    <?php if (empty($returns['purchase_returns'])): ?>
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-muted-foreground">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <?= icon('RotateCcw', 'h-6 w-6 text-muted-foreground/50') ?>
                                    <span class="text-xs">Tidak ada retur pembelian</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($returns['purchase_returns'] as $return): ?>
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-4 py-3 font-medium text-foreground"><?= $return['supplier_name'] ?? '' ?></td>
                                <td class="px-4 py-3 text-sm text-muted-foreground"><?= $return['reason'] ?? '' ?></td>
                                <td class="px-4 py-3 text-right font-semibold text-primary">
                                    <?= format_currency($return['final_amount'] ?? 0) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
