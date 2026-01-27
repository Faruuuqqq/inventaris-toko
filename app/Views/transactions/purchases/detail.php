<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/transactions/purchases') ?>" class="btn btn-outline-secondary">
                    <i data-lucide="arrow-left"></i>
                    Back
                </a>
                <?php if ($purchaseOrder['status'] === 'Dipesan'): ?>
                    <a href="<?= base_url('/transactions/purchases/edit/' . $purchaseOrder['id_po']) ?>" class="btn btn-warning">
                        <i data-lucide="edit"></i>
                        Edit
                    </a>
                <?php endif; ?>
                <?php if ($purchaseOrder['status'] !== 'Diterima Semua' && $purchaseOrder['status'] !== 'Dibatalkan'): ?>
                    <a href="<?= base_url('/transactions/purchases/receive/' . $purchaseOrder['id_po']) ?>" class="btn btn-success">
                        <i data-lucide="package-check"></i>
                        Receive
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Purchase Order Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>PO Number:</strong></td>
                            <td><?= $purchaseOrder['nomor_po'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td><?= format_date($purchaseOrder['tanggal_po']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Estimated Delivery:</strong></td>
                            <td><?= format_date($purchaseOrder['estimasi_tanggal']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Supplier:</strong></td>
                            <td><?= $purchaseOrder['supplier']['nama_supplier'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Warehouse:</strong></td>
                            <td><?= $purchaseOrder['warehouse']['nama_warehouse'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><?= status_badge($purchaseOrder['status']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Notes:</strong></td>
                            <td><?= $purchaseOrder['keterangan'] ?: '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                    <th>Received</th>
                                    <th>Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchaseOrder['details'] as $detail): ?>
                                    <tr>
                                        <td><?= $detail['kode_produk'] ?></td>
                                        <td><?= $detail['nama_produk'] ?></td>
                                        <td><?= $detail['jumlah'] ?></td>
                                        <td><?= format_currency($detail['harga_beli']) ?></td>
                                        <td><?= format_currency($detail['subtotal']) ?></td>
                                        <td><?= $detail['jumlah_diterima'] ?></td>
                                        <td><?= $detail['jumlah'] - $detail['jumlah_diterima'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th><?= format_currency($purchaseOrder['total_bayar']) ?></th>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Amount:</span>
                        <strong><?= format_currency($purchaseOrder['total_bayar']) ?></strong>
                    </div>
                    <?php 
                    $totalReceived = 0;
                    foreach ($purchaseOrder['details'] as $detail) {
                        $totalReceived += $detail['jumlah_diterima'] * $detail['harga_beli'];
                    }
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Received Value:</span>
                        <strong><?= format_currency($totalReceived) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Remaining Value:</span>
                        <strong><?= format_currency($purchaseOrder['total_bayar'] - $totalReceived) ?></strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Status:</span>
                        <?= status_badge($purchaseOrder['status']) ?>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($purchaseOrder['status'] === 'Dipesan'): ?>
                            <a href="<?= base_url('/transactions/purchases/receive/' . $purchaseOrder['id_po']) ?>" class="btn btn-success">
                                <i data-lucide="package-check"></i>
                                Receive Stock
                            </a>
                            <a href="<?= base_url('/transactions/purchases/edit/' . $purchaseOrder['id_po']) ?>" class="btn btn-warning">
                                <i data-lucide="edit"></i>
                                Edit PO
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= base_url('/info/stockcard?id_produk=all') ?>" class="btn btn-info">
                            <i data-lucide="file-text"></i>
                            View Stock Card
                        </a>
                        
                        <a href="<?= base_url('/info/stockcard?id_produk=all') ?>" class="btn btn-outline-secondary" target="_blank">
                            <i data-lucide="printer"></i>
                            Print
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>