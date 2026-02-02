<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header with Filter -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Users', 'h-8 w-8 text-primary') ?>
            Analisis Pelanggan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Segmentasi dan behavioral analysis pelanggan</p>
    </div>
    <a href="<?= base_url('/info/reports') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Filter Form -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Filter', 'h-5 w-5 text-primary') ?>
            Filter Periode
        </h2>
    </div>
    <div class="p-6">
        <form action="<?= base_url('/info/reports/customer-analysis') ?>" method="get" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="space-y-2 flex-1">
                <label for="start_date" class="text-sm font-medium text-foreground">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="<?= $startDate ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <div class="space-y-2 flex-1">
                <label for="end_date" class="text-sm font-medium text-foreground">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="<?= $endDate ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition whitespace-nowrap">
                <?= icon('Filter', 'h-5 w-5') ?>
                Tampilkan
            </button>
        </form>
    </div>
</div>

<!-- Customer Analysis Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('FileText', 'h-5 w-5 text-primary') ?>
            Analisis Pelanggan
        </h2>
        <p class="text-sm text-muted-foreground mt-2">Periode: <?= format_date($startDate) ?> - <?= format_date($endDate) ?></p>
    </div>
    <div class="p-6 overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/50 border-b border-border/50">
                <tr>
                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Nama Pelanggan</th>
                    <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Transaksi</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Total Belanja</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Rata-rata/Transaksi</th>
                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Transaksi Pertama</th>
                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Transaksi Terakhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                <?php if (empty($customerAnalysis)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <?= icon('Users', 'h-8 w-8 text-muted-foreground/50') ?>
                                <span class="text-sm">Tidak ada data untuk periode ini</span>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customerAnalysis as $customer): ?>
                        <tr class="hover:bg-muted/50 transition">
                            <td class="px-4 py-3 font-medium text-foreground"><?= $customer['name'] ?? '' ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-primary/10 text-primary text-xs font-semibold">
                                    <?= number_format($customer['transaction_count'] ?? 0) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-primary">
                                <?= format_currency($customer['total_spent'] ?? 0) ?>
                            </td>
                            <td class="px-4 py-3 text-right text-foreground">
                                <?= format_currency($customer['avg_transaction_value'] ?? 0) ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                <?= format_date($customer['first_transaction'] ?? '') ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                <?= format_date($customer['last_transaction'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($customerAnalysis)): ?>
                <tfoot class="bg-muted/30 border-t border-border/50 font-semibold">
                    <tr>
                        <td class="px-4 py-3 text-foreground">Total</td>
                        <td class="px-4 py-3 text-center text-foreground">
                            <?= number_format(array_sum(array_column($customerAnalysis, 'transaction_count'))) ?>
                        </td>
                        <td class="px-4 py-3 text-right text-primary">
                            <?= format_currency(array_sum(array_column($customerAnalysis, 'total_spent'))) ?>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
