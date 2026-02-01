<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/transactions/purchases/create') ?>" class="btn btn-primary">
                    <i data-lucide="plus"></i>
                    Create Purchase Order
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title">Purchase Orders List</h5>
                        </div>
                        <div class="col-auto">
                            <form method="get" class="d-flex gap-2">
                                <select name="supplier" class="form-select form-select-sm" style="width: 200px;">
                                    <option value="">All Suppliers</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['id_supplier'] ?>" <?= selected($supplier['id_supplier'], old('supplier', $this->request->getGet('supplier'))) ?>>
                                            <?= $supplier['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i data-lucide="filter"></i>
                                    Filter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No. PO</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Warehouse</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Received</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($purchaseOrders)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No purchase orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($purchaseOrders as $po): ?>
                                        <?php
                                        $receivedAmount = 0;
                                        $details = $this->purchaseOrderDetailModel->where('id_po', $po['id_po'])->findAll();
                                        foreach ($details as $detail) {
                                            $receivedAmount += $detail['jumlah_diterima'] * $detail['harga_beli'];
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('/transactions/purchases/detail/' . $po['id_po']) ?>" class="fw-bold">
                                                    <?= $po['nomor_po'] ?>
                                                </a>
                                            </td>
                                            <td><?= format_date($po['tanggal_po']) ?></td>
                                            <td><?= $po['name'] ?></td>
                                            <td><?= get_warehouse_name($po['id_warehouse']) ?></td>
                                            <td><?= status_badge($po['status']) ?></td>
                                            <td><?= format_currency($po['total_bayar']) ?></td>
                                            <td><?= format_currency($receivedAmount) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('/transactions/purchases/detail/' . $po['id_po']) ?>" class="btn btn-sm btn-info">
                                                        <i data-lucide="eye"></i>
                                                    </a>
                                                    <?php if ($po['status'] !== 'Diterima Semua' && $po['status'] !== 'Dibatalkan'): ?>
                                                        <a href="<?= base_url('/transactions/purchases/receive/' . $po['id_po']) ?>" class="btn btn-sm btn-success">
                                                            <i data-lucide="package-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($po['status'] === 'Dipesan'): ?>
                                                        <a href="<?= base_url('/transactions/purchases/edit/' . $po['id_po']) ?>" class="btn btn-sm btn-warning">
                                                            <i data-lucide="edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('<?= $po['id_po'] ?>', '<?= $po['nomor_po'] ?>')">
                                                            <i data-lucide="trash-2"></i>
                                                        </button>
                                                    <?php endif; ?>
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
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete purchase order <strong id="poNumber"></strong>? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, nomor) {
    document.getElementById('poNumber').textContent = nomor;
    document.getElementById('deleteForm').action = '/transactions/purchases/delete/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?= $this->endSection() ?>