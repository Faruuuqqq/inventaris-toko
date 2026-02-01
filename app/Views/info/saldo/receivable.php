<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h3 class="page-title"><?= $title ?></h3>
            <p class="text-muted"><?= $subtitle ?? '' ?></p>
        </div>
    </div>

<!-- Receivables Summary -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <!-- Summary Cards -->
        <div class="grid gap-4 md:grid-cols-4 mb-6">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">0-30 Hari</h4>
                <p class="text-sm text-muted-foreground"><?= count($agingData['0-30']['customers'] ?? []) ?> customer</p>
                <p class="text-xl font-bold"><?= format_currency($agingData['0-30']['total'] ?? 0) ?></p>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">31-60 Hari</h4>
                <p class="text-sm text-muted-foreground"><?= count($agingData['31-60']['customers'] ?? []) ?> customer</p>
                <p class="text-xl font-bold"><?= format_currency($agingData['31-60']['total'] ?? 0) ?></p>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">61-90 Hari</h4>
                <p class="text-sm text-muted-foreground"><?= count($agingData['61-90']['customers'] ?? []) ?> customer</p>
                <p class="text-xl font-bold"><?= format_currency($agingData['61-90']['total'] ?? 0) ?></p>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">90+ Hari</h4>
                <p class="text-sm text-muted-foreground"><?= count($agingData['90+']['customers'] ?? []) ?> customer</p>
                <p class="text-xl font-bold"><?= format_currency($agingData['90+']['total'] ?? 0) ?></p>
            </div>
        </div>

        <!-- Total Summary -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4 mb-6">
            <div class="flex justify-between">
                <h4 class="text-lg font-semibold">Total Piutang Seluruhnya</h4>
                <p class="text-2xl font-bold text-primary"><?= format_currency($totalReceivable ?? 0) ?></p>
            </div>
        </div>

        <!-- Customer Detail Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Customer</th>
                    <th class="text-right">Total Piutang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data piutang</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= $customer['name'] ?? '' ?></td>
                        <td class="text-right font-medium"><?= format_currency($customer['receivable_balance'] ?? 0) ?></td>
                        <td>
                            <div class="flex gap-1">
                                <a href="<?= base_url('/finance/payments/receivable?customer_id=' . ($customer['id'] ?? '')) ?>" class="btn btn-sm btn-primary">
                                    Bayar
                                </a>
                            </div>
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
