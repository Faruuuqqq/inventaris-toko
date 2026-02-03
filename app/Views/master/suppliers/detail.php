<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Building2', 'h-8 w-8 text-primary') ?>
            Detail Supplier
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Informasi lengkap dan riwayat supplier</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= base_url('master/suppliers') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            <?= icon('ArrowLeft', 'h-5 w-5') ?>
            Kembali
        </a>
        <?php if (is_admin()): ?>
        <a href="<?= base_url('master/suppliers/edit/' . $supplier['id']) ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
            <?= icon('Edit', 'h-5 w-5') ?>
            Edit
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Supplier Details (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Supplier Information Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Building', 'h-5 w-5 text-primary') ?>
                    Informasi Supplier
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Supplier Name -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Supplier</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $supplier['name'] ?></p>
                </div>

                <!-- Contact Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nomor Telepon</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $supplier['phone'] ?? '-' ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Email</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $supplier['email'] ?? '-' ?></p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Alamat</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $supplier['address'] ?? '-' ?></p>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Syarat Pembayaran</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $supplier['payment_terms'] ?? '-' ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">PIC</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $supplier['pic_name'] ?? '-' ?></p>
                    </div>
                </div>

                <!-- Debt Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Total Hutang</p>
                        <p class="text-lg font-bold text-destructive mt-1"><?= format_currency($supplier['debt_balance']) ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Total Pembelian</p>
                        <p class="text-lg font-bold text-foreground mt-1"><?= format_currency($supplier['total_purchases'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Purchase Orders -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('ShoppingCart', 'h-5 w-5 text-primary') ?>
                    Purchase Order Terbaru
                </h2>
            </div>

            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">No. PO</th>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Tanggal</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground w-28">Total</th>
                            <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php if (empty($recent_pos)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                Belum ada purchase order
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recent_pos as $po): ?>
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-6 py-4 font-mono font-semibold text-foreground"><?= $po['po_number'] ?></td>
                                <td class="px-6 py-4"><?= format_date($po['created_at']) ?></td>
                                <td class="px-6 py-4 text-right font-semibold"><?= format_currency($po['total_amount']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="'<?= match($po['status']) {
                                        'RECEIVED' => 'bg-success/10 text-success',
                                        'PENDING' => 'bg-warning/10 text-warning',
                                        'CANCELLED' => 'bg-destructive/10 text-destructive',
                                        default => 'bg-muted/10 text-muted'
                                    } ?>'">
                                        <?= match($po['status']) {
                                            'RECEIVED' => 'Terima',
                                            'PENDING' => 'Pending',
                                            'CANCELLED' => 'Batal',
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
                <a href="<?= base_url('transactions/purchases/create?supplier_id=' . $supplier['id']) ?>" class="w-full h-10 rounded-lg bg-primary text-white font-medium flex items-center justify-center hover:bg-primary/90 transition">
                    <?= icon('Plus', 'h-5 w-5 mr-2') ?>
                    Buat Purchase Order
                </a>

                <a href="<?= base_url('finance/payments/payable?supplier_id=' . $supplier['id']) ?>" class="w-full h-10 rounded-lg border border-primary/50 text-primary font-medium flex items-center justify-center hover:bg-primary/5 transition">
                    <?= icon('CreditCard', 'h-5 w-5 mr-2') ?>
                    Bayar Hutang
                </a>

                <a href="<?= base_url('info/history/purchases?supplier_id=' . $supplier['id']) ?>" class="w-full h-10 rounded-lg border border-border/50 text-foreground font-medium flex items-center justify-center hover:bg-muted transition">
                    <?= icon('History', 'h-5 w-5 mr-2') ?>
                    Lihat Riwayat
                </a>
            </div>
        </div>

        <!-- Debt Status -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('AlertCircle', 'h-5 w-5 text-primary') ?>
                    Status Hutang
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="p-4 rounded-lg bg-destructive/10 border border-destructive/20">
                    <p class="text-xs text-destructive font-semibold uppercase">Total Hutang</p>
                    <p class="text-2xl font-bold text-destructive mt-2"><?= format_currency($supplier['debt_balance']) ?></p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Jumlah PO Belum Bayar</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $supplier['pending_po_count'] ?? 0 ?></p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Rata-rata PO</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= format_currency($supplier['average_po'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <!-- Bank Information (if available) -->
        <?php if ($supplier['bank_name'] ?? null): ?>
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Banknote', 'h-5 w-5 text-primary') ?>
                    Rekening Bank
                </h2>
            </div>

            <div class="p-6 space-y-3">
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase mb-1">Bank</p>
                    <p class="text-sm font-semibold text-foreground"><?= $supplier['bank_name'] ?></p>
                </div>

                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase mb-1">Nomor Rekening</p>
                    <p class="text-sm font-mono font-semibold text-foreground"><?= $supplier['bank_account'] ?></p>
                </div>

                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase mb-1">Atas Nama</p>
                    <p class="text-sm font-semibold text-foreground"><?= $supplier['bank_account_name'] ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
