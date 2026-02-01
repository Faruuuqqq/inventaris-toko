<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <form action="<?= base_url('/info/reports/customer-analysis') ?>" method="get" class="d-flex gap-2">
                    <input type="date" name="start_date" value="<?= $startDate ?>" class="form-control" placeholder="Start Date">
                    <input type="date" name="end_date" value="<?= $endDate ?>" class="form-control" placeholder="End Date">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Analisis Customer</h5>
            <p class="text-muted">Periode: <?= format_date($startDate) ?> - <?= format_date($endDate) ?></p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama Customer</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-right">Total Belanja</th>
                            <th class="text-right">Rata-rata/Transaksi</th>
                            <th>Transaksi Pertama</th>
                            <th>Transaksi Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customerAnalysis)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customerAnalysis as $customer): ?>
                                <tr>
                                    <td><?= $customer['name'] ?? '' ?></td>
                                    <td class="text-center"><?= number_format($customer['transaction_count'] ?? 0) ?></td>
                                    <td class="text-right font-weight-bold"><?= format_currency($customer['total_spent'] ?? 0) ?></td>
                                    <td class="text-right"><?= format_currency($customer['avg_transaction_value'] ?? 0) ?></td>
                                    <td><?= format_date($customer['first_transaction'] ?? '') ?></td>
                                    <td><?= format_date($customer['last_transaction'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($customerAnalysis)): ?>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td>Total</td>
                            <td class="text-center"><?= number_format(array_sum(array_column($customerAnalysis, 'transaction_count'))) ?></td>
                            <td class="text-right"><?= format_currency(array_sum(array_column($customerAnalysis, 'total_spent'))) ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
