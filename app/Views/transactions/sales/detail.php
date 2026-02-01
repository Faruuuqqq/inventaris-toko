<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('FileText', 'h-8 w-8 text-primary') ?>
            Detail Penjualan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Informasi lengkap transaksi penjualan</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= base_url('transactions/sales') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            <?= icon('ArrowLeft', 'h-5 w-5') ?>
            Kembali
        </a>
        <?php if (is_admin()): ?>
        <a href="<?= base_url('transactions/sales/edit/' . $sale['id']) ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
            <?= icon('Edit', 'h-5 w-5') ?>
            Edit
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Sale Details (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Sale Information Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                    Informasi Penjualan
                </h2>
                <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="'<?= match($sale['payment_type']) {
                    'CASH' => 'bg-success/10 text-success',
                    'CREDIT' => 'bg-warning/10 text-warning',
                    default => 'bg-muted/10 text-muted'
                } ?>'">
                    <?= $sale['payment_type'] === 'CASH' ? 'Tunai' : 'Kredit' ?>
                </span>
            </div>

            <div class="p-6 grid gap-6 md:grid-cols-2">
                <!-- Invoice Number -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">No. Invoice</p>
                    <p class="text-lg font-mono font-bold text-foreground mt-1"><?= $sale['invoice_number'] ?></p>
                </div>

                <!-- Sale Date -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Tanggal Penjualan</p>
                    <p class="text-lg font-bold text-foreground mt-1"><?= format_date($sale['created_at']) ?></p>
                </div>

                <!-- Customer -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Pelanggan</p>
                    <p class="text-lg font-bold text-foreground mt-1"><?= $sale['customer_name'] ?></p>
                    <p class="text-sm text-muted-foreground mt-1"><?= $sale['customer_phone'] ?? '-' ?></p>
                </div>

                <!-- Salesperson -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Salesman</p>
                    <p class="text-lg font-bold text-foreground mt-1"><?= $sale['salesperson_name'] ?? '-' ?></p>
                </div>

                <!-- Status -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Status Pembayaran</p>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" :class="'<?= match($sale['payment_status']) {
                            'PAID' => 'bg-success/10 text-success',
                            'PARTIAL' => 'bg-warning/10 text-warning',
                            'UNPAID' => 'bg-destructive/10 text-destructive',
                            default => 'bg-muted/10 text-muted'
                        } ?>'">
                            <?= match($sale['payment_status']) {
                                'PAID' => 'Lunas',
                                'PARTIAL' => 'Sebagian',
                                'UNPAID' => 'Belum Bayar',
                                default => 'Unknown'
                            } ?>
                        </span>
                    </div>
                </div>

                <!-- Warehouse -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Gudang</p>
                    <p class="text-lg font-bold text-foreground mt-1"><?= $sale['warehouse_name'] ?></p>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Package', 'h-5 w-5 text-primary') ?>
                    Produk Penjualan
                </h2>
            </div>

            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Produk</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground w-20">Qty</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground w-24">Harga</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground w-28">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php foreach ($sale_items as $item): ?>
                        <tr class="hover:bg-muted/50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-foreground"><?= $item['product_name'] ?></p>
                                    <p class="text-xs text-muted-foreground"><?= $item['sku'] ?></p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold"><?= $item['qty'] ?></td>
                            <td class="px-6 py-4 text-right font-semibold"><?= format_currency($item['price']) ?></td>
                            <td class="px-6 py-4 text-right font-bold text-foreground"><?= format_currency($item['qty'] * $item['price']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Summary (1/3) -->
    <div class="space-y-6">
        
        <!-- Summary Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('CalculatorIcon', 'h-5 w-5 text-primary') ?>
                    Ringkasan
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <!-- Subtotal -->
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">Subtotal</span>
                    <span class="font-semibold text-foreground"><?= format_currency($sale['total_amount'] - ($sale['tax_amount'] ?? 0)) ?></span>
                </div>

                <!-- Tax -->
                <?php if ($sale['tax_amount'] > 0): ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">Pajak</span>
                    <span class="font-semibold text-foreground"><?= format_currency($sale['tax_amount']) ?></span>
                </div>
                <?php endif; ?>

                <!-- Total -->
                <div class="flex items-center justify-between pt-4 border-t border-border/50">
                    <span class="font-semibold text-foreground">Total</span>
                    <span class="text-2xl font-bold text-primary"><?= format_currency($sale['total_amount']) ?></span>
                </div>

                <!-- Payment Status -->
                <div class="pt-4 border-t border-border/50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Dibayar</span>
                        <span class="font-semibold text-success"><?= format_currency($sale['paid_amount']) ?></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Sisa Piutang</span>
                        <span class="font-semibold text-warning"><?= format_currency($sale['total_amount'] - $sale['paid_amount']) ?></span>
                    </div>
                </div>

                <!-- Payment Action -->
                <?php if ($sale['payment_status'] !== 'PAID' && is_admin()): ?>
                <a href="<?= base_url('finance/payments/receivable?customer_id=' . $sale['customer_id']) ?>" class="w-full mt-4 h-10 rounded-lg bg-primary text-white font-medium flex items-center justify-center hover:bg-primary/90 transition">
                    <?= icon('CreditCard', 'h-5 w-5 mr-2') ?>
                    Terima Pembayaran
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notes Card -->
        <?php if ($sale['notes']): ?>
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('MessageSquare', 'h-5 w-5 text-primary') ?>
                    Catatan
                </h2>
            </div>

            <div class="p-6">
                <p class="text-sm text-foreground whitespace-pre-wrap"><?= $sale['notes'] ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
