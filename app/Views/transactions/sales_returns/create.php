<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/transactions/sales-returns') ?>" class="btn btn-outline-secondary">
                    <i data-lucide="arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('/transactions/sales-returns/store') ?>" x-data="salesReturnForm()">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nomor_retur" class="form-label">Return Number</label>
                                    <input type="text" class="form-control" id="nomor_retur" name="nomor_retur" value="<?= old('nomor_retur', $nomor_retur) ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_retur" class="form-label">Return Date</label>
                                    <input type="date" class="form-control" id="tanggal_retur" name="tanggal_retur" value="<?= old('tanggal_retur', date('Y-m-d')) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_customer" class="form-label">Customer</label>
                                    <select class="form-select" id="id_customer" name="id_customer" required x-model="form.id_customer">
                                        <option value="">Select Customer</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['id_customer'] ?>" <?= selected($customer['id_customer'], old('id_customer')) ?>>
                                                <?= $customer['nama_customer'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_penjualan" class="form-label">Reference Sales (Optional)</label>
                                    <select class="form-select" id="id_penjualan" name="id_penjualan" x-on:change="loadSalesDetails()">
                                        <option value="">Select Sales</option>
                                        <?php foreach ($salesList as $sale): ?>
                                            <option value="<?= $sale['id_penjualan'] ?>" data-customer="<?= $sale['id_customer'] ?>">
                                                <?= $sale['nomor_penjualan'] ?> - <?= format_date($sale['tanggal_penjualan']) ?> (<?= $sale['nama_customer'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_warehouse_asal" class="form-label">Return From Warehouse</label>
                                    <select class="form-select" id="id_warehouse_asal" name="id_warehouse_asal" required>
                                        <option value="">Select Warehouse</option>
                                        <?php foreach ($warehouses as $warehouse): ?>
                                            <option value="<?= $warehouse['id_warehouse'] ?>" <?= selected($warehouse['id_warehouse'], old('id_warehouse_asal')) ?>>
                                                <?= $warehouse['nama_warehouse'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Menunggu Persetujuan" <?= selected('Menunggu Persetujuan', old('status')) ?>>Waiting Approval</option>
                                        <?php if (is_admin()): ?>
                                            <option value="Disetujui" <?= selected('Disetujui', old('status')) ?>>Approved</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Notes</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2"><?= old('keterangan') ?></textarea>
                        </div>

                        <hr>

                        <h5>Products to Return</h5>
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" x-on:click="addProduct()">
                                <i data-lucide="plus"></i>
                                Add Product
                            </button>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="productsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Reason</th>
                                        <th>Notes</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(product, index) in form.products" :key="index">
                                        <tr>
                                            <td>
                                                <select class="form-select" x-model="product.id_produk" required>
                                                    <option value="">Select Product</option>
                                                    <?php foreach ($products as $product_option): ?>
                                                        <option value="<?= $product_option['id_produk'] ?>">
                                                            <?= $product_option['nama_produk'] ?> (<?= $product_option['kode_produk'] ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" x-model="product.jumlah" min="1" required>
                                            </td>
                                            <td>
                                                <select class="form-select" x-model="product.alasan" required>
                                                    <option value="">Select Reason</option>
                                                    <option value="Cacat">Defective</option>
                                                    <option value="Salah Ukuran">Wrong Size</option>
                                                    <option value="Tidak Sesuai">Not Suitable</option>
                                                    <option value="Kadaluarsa">Expired</option>
                                                    <option value="Lainnya">Other</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" x-model="product.keterangan">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" x-on:click="removeProduct(index)" x-show="form.products.length > 1">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/transactions/sales-returns') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Sales Return</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('salesReturnForm', () => ({
        form: {
            id_customer: '',
            products: [{
                id_produk: '',
                jumlah: 1,
                alasan: '',
                keterangan: ''
            }]
        },
        
        init() {
            this.addProduct(); // Add one empty row initially
        },
        
        addProduct() {
            this.form.products.push({
                id_produk: '',
                jumlah: 1,
                alasan: '',
                keterangan: ''
            });
        },
        
        removeProduct(index) {
            this.form.products.splice(index, 1);
        },
        
        loadSalesDetails() {
            const select = document.getElementById('id_penjualan');
            const selectedOption = select.options[select.selectedIndex];
            const customerId = selectedOption.dataset.customer;
            
            if (customerId && !this.form.id_customer) {
                this.form.id_customer = customerId;
            }
            
            // Here you could load sales details via AJAX if needed
        }
    }));
});
</script>

<?= $this->endSection() ?>