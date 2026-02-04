<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Users', 'h-8 w-8 text-primary') ?>
            Detail Pelanggan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Informasi lengkap dan riwayat pelanggan</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= base_url('master/customers') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            <?= icon('ArrowLeft', 'h-5 w-5') ?>
            Kembali
        </a>
        <?php if (is_admin()): ?>
        <a href="<?= base_url('master/customers/edit/' . $customer->id) ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
            <?= icon('Edit', 'h-5 w-5') ?>
            Edit
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Customer Details (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Customer Information Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('User', 'h-5 w-5 text-primary') ?>
                    Informasi Pelanggan
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Customer Name and Type -->
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Pelanggan</p>
                        <p class="text-2xl font-bold text-foreground mt-2"><?= $customer->name ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Tipe Pelanggan</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                <?= $customer->type === 'B2B' ? 'Bisnis' : 'Konsumen' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nomor Telepon</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $customer->phone ?? '-' ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Email</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $customer->email ?? '-' ?></p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Alamat</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $customer->address ?? '-' ?></p>
                    </div>
                </div>

                <!-- Credit Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Limit Kredit</p>
                        <p class="text-lg font-bold text-foreground mt-1"><?= format_currency($customer['credit_limit']) ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Piutang Saat Ini</p>
                        <p class="text-lg font-bold" :class="<?= $customer['receivable_balance'] > $customer['credit_limit'] ? "'text-destructive'" : "'text-foreground'" ?>">
                            <?= format_currency($customer['receivable_balance']) ?>
                        </p>
                    </div>
                </div>

                <!-- Credit Status -->
                <?php 
                $used_percent = ($customer['receivable_balance'] / $customer['credit_limit']) * 100;
                $status_class = $used_percent > 80 ? 'destructive' : ($used_percent > 50 ? 'warning' : 'success');
                ?>
                <div class="pt-4 border-t border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide mb-2">Status Kredit</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-foreground">Penggunaan Kredit</span>
                            <span class="font-semibold"><?= round($used_percent, 1) ?>%</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-<?= $status_class ?> h-2 rounded-full" style="width: <?= min($used_percent, 100) ?>%"></div>
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Sisa Kredit: <span class="font-semibold text-foreground"><?= format_currency($customer['credit_limit'] - $customer['receivable_balance']) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('History', 'h-5 w-5 text-primary') ?>
                    Transaksi Terbaru
                </h2>
            </div>

            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">No. Invoice</th>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Tanggal</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground w-28">Total</th>
                            <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php if (empty($recent_sales)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                Belum ada transaksi
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recent_sales as $sale): ?>
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-6 py-4 font-mono font-semibold text-foreground"><?= $sale['invoice_number'] ?></td>
                                <td class="px-6 py-4"><?= format_date($sale['created_at']) ?></td>
                                <td class="px-6 py-4 text-right font-semibold"><?= format_currency($sale['total_amount']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="'<?= match($sale['payment_status']) {
                                        'PAID' => 'bg-success/10 text-success',
                                        'PARTIAL' => 'bg-warning/10 text-warning',
                                        'UNPAID' => 'bg-destructive/10 text-destructive',
                                        default => 'bg-muted/10 text-muted'
                                    } ?>'">
                                        <?= match($sale['payment_status']) {
                                            'PAID' => 'Lunas',
                                            'PARTIAL' => 'Sebagian',
                                            'UNPAID' => 'Belum',
                                            default => 'Unknown'
                                        } ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Quick Actions & Summary (1/3) -->
    <div class="space-y-6">
        
        <!-- Quick Actions -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Zap', 'h-5 w-5 text-primary') ?>
                    Aksi Cepat
                </h2>
            </div>

            <div class="p-6 space-y-3">
                <a href="<?= base_url('transactions/sales/credit?customer_id=' . $customer->id) ?>" class="w-full h-10 rounded-lg bg-primary text-white font-medium flex items-center justify-center hover:bg-primary/90 transition">
                    <?= icon('Plus', 'h-5 w-5 mr-2') ?>
                    Penjualan Kredit
                </a>

                <a href="<?= base_url('finance/payments/receivable?customer_id=' . $customer->id) ?>" class="w-full h-10 rounded-lg border border-primary/50 text-primary font-medium flex items-center justify-center hover:bg-primary/5 transition">
                    <?= icon('CreditCard', 'h-5 w-5 mr-2') ?>
                    Terima Pembayaran
                </a>

                <a href="<?= base_url('info/history/sales?customer_id=' . $customer->id) ?>" class="w-full h-10 rounded-lg border border-border/50 text-foreground font-medium flex items-center justify-center hover:bg-muted transition">
                    <?= icon('History', 'h-5 w-5 mr-2') ?>
                    Lihat Riwayat
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('BarChart3', 'h-5 w-5 text-primary') ?>
                    Statistik
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Total Penjualan</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= format_currency($customer['total_sales'] ?? 0) ?></p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Jumlah Transaksi</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $customer['transaction_count'] ?? 0 ?></p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Rata-rata Transaksi</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= format_currency($customer['average_transaction'] ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
