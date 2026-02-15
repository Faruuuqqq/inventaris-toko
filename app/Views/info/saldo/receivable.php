<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('DollarSign', 'h-8 w-8 text-success') ?>
            <?= $title ?? 'Saldo Piutang' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Kelola piutang dari customer' ?></p>
    </div>
    <a href="<?= base_url('/finance') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ChevronLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Aging Analysis Cards -->
<div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
    <!-- 0-30 Days -->
    <div class="rounded-lg border bg-surface shadow-sm p-6">
        <p class="text-sm font-medium text-muted-foreground">0-30 Hari</p>
        <p class="text-xs text-muted-foreground mt-1"><?= count($agingData['0-30']['customers'] ?? []) ?> customer</p>
        <p class="text-2xl font-bold text-success mt-3"><?= format_currency($agingData['0-30']['total'] ?? 0) ?></p>
    </div>

    <!-- 31-60 Days -->
    <div class="rounded-lg border bg-surface shadow-sm p-6">
        <p class="text-sm font-medium text-muted-foreground">31-60 Hari</p>
        <p class="text-xs text-muted-foreground mt-1"><?= count($agingData['31-60']['customers'] ?? []) ?> customer</p>
        <p class="text-2xl font-bold text-warning mt-3"><?= format_currency($agingData['31-60']['total'] ?? 0) ?></p>
    </div>

    <!-- 61-90 Days -->
    <div class="rounded-lg border bg-surface shadow-sm p-6">
        <p class="text-sm font-medium text-muted-foreground">61-90 Hari</p>
        <p class="text-xs text-muted-foreground mt-1"><?= count($agingData['61-90']['customers'] ?? []) ?> customer</p>
        <p class="text-2xl font-bold text-warning mt-3"><?= format_currency($agingData['61-90']['total'] ?? 0) ?></p>
    </div>

    <!-- 90+ Days -->
    <div class="rounded-lg border bg-surface shadow-sm p-6">
        <p class="text-sm font-medium text-muted-foreground">90+ Hari</p>
        <p class="text-xs text-muted-foreground mt-1"><?= count($agingData['90+']['customers'] ?? []) ?> customer</p>
        <p class="text-2xl font-bold text-destructive mt-3"><?= format_currency($agingData['90+']['total'] ?? 0) ?></p>
    </div>
</div>

<!-- Total Summary Card -->
<div class="mb-8 rounded-lg border bg-gradient-to-br from-success/5 to-success/2 shadow-sm overflow-hidden">
    <div class="p-6 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-muted-foreground">Total Piutang Seluruhnya</p>
            <p class="text-3xl font-bold text-foreground mt-2"><?= format_currency($totalReceivable ?? 0) ?></p>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-success/10">
            <?= icon('DollarSign', 'h-8 w-8 text-success') ?>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Daftar Customer</h2>
    </div>
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Nama Customer</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Total Piutang</th>
                    <th class="px-6 py-3 text-center font-semibold text-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <?= icon('FileText', 'h-12 w-12 text-muted-foreground/50') ?>
                                <p class="text-sm font-medium text-muted-foreground">Tidak ada data piutang</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-foreground"><?= esc($customer->name ?? '') ?></td>
                        <td class="px-6 py-4 text-right font-medium text-success"><?= format_currency($customer->receivable_balance ?? 0) ?></td>
                        <td class="px-6 py-4 text-center">
                            <a href="<?= base_url('/finance/payments/receivable?customer_id=' . esc($customer->id ?? '')) ?>" class="inline-flex items-center justify-center gap-2 h-9 px-4 rounded-lg bg-primary text-white font-medium text-sm hover:bg-primary/90 transition">
                                <?= icon('Plus', 'h-4 w-4') ?>
                                Terima Pembayaran
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
