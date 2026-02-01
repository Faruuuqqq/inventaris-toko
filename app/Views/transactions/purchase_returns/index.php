<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/transactions/purchase-returns/create') ?>" class="btn btn-primary">
                    <i data-lucide="plus"></i>
                    Create Purchase Return
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
                            <h5 class="card-title">Purchase Returns List</h5>
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
                                    <th>Return Number</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th>Refund Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($purchaseReturns)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No purchase returns found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($purchaseReturns as $retur): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('/transactions/purchase-returns/detail/' . $retur['id_retur_pembelian']) ?>" class="fw-bold">
                                                    <?= $retur['nomor_retur'] ?>
                                                </a>
                                            </td>
                                            <td><?= format_date($retur['tanggal_retur']) ?></td>
                                            <td><?= $retur['name'] ?></td>
                                            <td><?= status_badge($retur['status']) ?></td>
                                            <td><?= format_currency($retur['total_refund']) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('/transactions/purchase-returns/detail/' . $retur['id_retur_pembelian']) ?>" class="btn btn-sm btn-info">
                                                        <i data-lucide="eye"></i>
                                                    </a>
                                                    <?php if ($retur['status'] === 'Menunggu Persetujuan'): ?>
                                                        <a href="<?= base_url('/transactions/purchase-returns/approve/' . $retur['id_retur_pembelian']) ?>" class="btn btn-sm btn-success">
                                                            <i data-lucide="check"></i>
                                                        </a>
                                                        <a href="<?= base_url('/transactions/purchase-returns/edit/' . $retur['id_retur_pembelian']) ?>" class="btn btn-sm btn-warning">
                                                            <i data-lucide="edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('<?= $retur['id_retur_pembelian'] ?>', '<?= $retur['nomor_retur'] ?>')">
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
                Are you sure you want to delete purchase return <strong id="returNumber"></strong>? This action cannot be undone.
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
    document.getElementById('returNumber').textContent = nomor;
    document.getElementById('deleteForm').action = '/transactions/purchase-returns/delete/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?= $this->endSection() ?>