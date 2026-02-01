<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <form action="<?= base_url('/info/reports/daily') ?>" method="get" class="d-flex gap-2">
                    <input type="date" name="date" value="<?= $date ?>" class="form-control">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= format_currency($summary['total_sales'] ?? 0) ?></h4>
                            <span>Total Penjualan</span>
                        </div>
                        <div class="align-self-center">
                            <i data-lucide="shopping-cart" class="icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= format_currency($summary['total_purchases'] ?? 0) ?></h4>
                            <span>Total Pembelian</span>
                        </div>
                        <div class="align-self-center">
                            <i data-lucide="package" class="icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= format_currency($summary['total_returns'] ?? 0) ?></h4>
                            <span>Total Retur</span>
                        </div>
                        <div class="align-self-center">
                            <i data-lucide="rotate-ccw" class="icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= $summary['transaction_count'] ?? 0 ?></h4>
                            <span>Total Transaksi</span>
                        </div>
                        <div class="align-self-center">
                            <i data-lucide="file-text" class="icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Penjualan - <?= format_date($date) ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No. Faktur</th>
                                    <th>Customer</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sales)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada penjualan</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sales as $sale): ?>
                                        <tr>
                                            <td><?= $sale['invoice_number'] ?? '' ?></td>
                                            <td><?= $sale['customer_name'] ?? '' ?></td>
                                            <td><?= badge_status($sale['payment_type'] ?? '') ?></td>
                                            <td><?= badge_status($sale['payment_status'] ?? '') ?></td>
                                            <td class="text-right"><?= format_currency($sale['final_amount'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">Total Penjualan:</td>
                                    <td class="text-right"><?= format_currency($summary['total_sales'] ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pembelian - <?= format_date($date) ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No. PO</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($purchases)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada pembelian</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($purchases as $purchase): ?>
                                        <tr>
                                            <td><?= $purchase['po_number'] ?? '' ?></td>
                                            <td><?= $purchase['supplier_name'] ?? '' ?></td>
                                            <td><?= badge_status($purchase['status'] ?? '') ?></td>
                                            <td class="text-right"><?= format_currency($purchase['total_amount'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="3" class="text-right">Total Pembelian:</td>
                                    <td class="text-right"><?= format_currency($summary['total_purchases'] ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Returns Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Retur Penjualan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Alasan</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($returns['sales_returns'])): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada retur penjualan</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($returns['sales_returns'] as $return): ?>
                                        <tr>
                                            <td><?= $return['customer_name'] ?? '' ?></td>
                                            <td><?= $return['reason'] ?? '' ?></td>
                                            <td class="text-right"><?= format_currency($return['final_amount'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Retur Pembelian</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Alasan</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($returns['purchase_returns'])): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada retur pembelian</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($returns['purchase_returns'] as $return): ?>
                                        <tr>
                                            <td><?= $return['supplier_name'] ?? '' ?></td>
                                            <td><?= $return['reason'] ?? '' ?></td>
                                            <td class="text-right"><?= format_currency($return['final_amount'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
