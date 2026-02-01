<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <form action="<?= base_url('/info/reports/product-performance') ?>" method="get" class="d-flex gap-2">
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
            <h5 class="card-title">Performa Produk</h5>
            <p class="text-muted">Periode: <?= format_date($startDate) ?> - <?= format_date($endDate) ?></p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th class="text-center">Qty Terjual</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-right">Harga Rata-rata</th>
                            <th class="text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productPerformance)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productPerformance as $product): ?>
                                <tr>
                                    <td><code><?= $product['sku'] ?? '' ?></code></td>
                                    <td><?= $product['name'] ?? '' ?></td>
                                    <td class="text-center"><?= number_format($product['total_sold'] ?? 0) ?></td>
                                    <td class="text-center"><?= number_format($product['sales_count'] ?? 0) ?></td>
                                    <td class="text-right"><?= format_currency($product['avg_price'] ?? 0) ?></td>
                                    <td class="text-right font-weight-bold"><?= format_currency($product['revenue'] ?? 0) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($productPerformance)): ?>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td colspan="2">Total</td>
                            <td class="text-center"><?= number_format(array_sum(array_column($productPerformance, 'total_sold'))) ?></td>
                            <td class="text-center"><?= number_format(array_sum(array_column($productPerformance, 'sales_count'))) ?></td>
                            <td></td>
                            <td class="text-right"><?= format_currency(array_sum(array_column($productPerformance, 'revenue'))) ?></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
