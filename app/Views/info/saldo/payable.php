<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h3 class="page-title"><?= $title ?></h3>
            <p class="text-muted"><?= $subtitle ?? '' ?></p>
        </div>
    </div>

<!-- Payables Summary -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <!-- Summary Card -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4 mb-6">
            <div class="flex justify-between">
                <h4 class="text-lg font-semibold">Total Utang Seluruhnya</h4>
                <p class="text-2xl font-bold text-primary"><?= format_currency($totalPayable ?? 0) ?></p>
            </div>
        </div>

        <!-- Supplier Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Supplier</th>
                    <th class="text-right">Total Utang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data utang</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?= $supplier['name'] ?? '' ?></td>
                        <td class="text-right font-medium"><?= format_currency($supplier['debt_balance'] ?? 0) ?></td>
                        <td>
                            <div class="flex gap-1">
                                <a href="<?= base_url('/finance/payments/payable?supplier_id=' . ($supplier['id'] ?? '')) ?>" class="btn btn-sm btn-primary">
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
