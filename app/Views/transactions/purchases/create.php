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
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('/transactions/purchases/store') ?>" x-data="purchaseOrderForm()">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nomor_po" class="form-label">Purchase Order Number</label>
                                    <input type="text" class="form-control" id="nomor_po" name="nomor_po" value="<?= old('nomor_po', $nomor_po) ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_po" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="tanggal_po" name="tanggal_po" value="<?= old('tanggal_po', date('Y-m-d')) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_supplier" class="form-label">Supplier</label>
                                    <select class="form-select" id="id_supplier" name="id_supplier" required x-model="form.id_supplier" x-on:change="updatePrices()">
                                        <option value="">Select Supplier</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?= $supplier['id_supplier'] ?>" <?= selected($supplier['id_supplier'], old('id_supplier')) ?>>
                                                <?= $supplier['nama_supplier'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_warehouse" class="form-label">Warehouse</label>
                                    <select class="form-select" id="id_warehouse" name="id_warehouse" required>
                                        <option value="">Select Warehouse</option>
                                        <?php foreach ($warehouses as $warehouse): ?>
                                            <option value="<?= $warehouse['id_warehouse'] ?>" <?= selected($warehouse['id_warehouse'], old('id_warehouse')) ?>>
                                                <?= $warehouse['nama_warehouse'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estimasi_tanggal" class="form-label">Estimated Delivery</label>
                                    <input type="date" class="form-control" id="estimasi_tanggal" name="estimasi_tanggal" value="<?= old('estimasi_tanggal') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Notes</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2"><?= old('keterangan') ?></textarea>
                        </div>

                        <hr>

                        <h5>Products</h5>
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
                                        <th>Purchase Price</th>
                                        <th>Subtotal</th>
                                        <th>Notes</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(product, index) in form.products" :key="index">
                                        <tr>
                                            <td>
                                                <select class="form-select" x-model="product.id_produk" x-on:change="updateProductPrice(index)" required>
                                                    <option value="">Select Product</option>
                                                    <?php foreach ($products as $product_option): ?>
                                                        <option value="<?= $product_option['id_produk'] ?>" x-bind:data-price="<?= $product_option['harga_beli_terakhir'] ?>">
                                                            <?= $product_option['nama_produk'] ?> (<?= $product_option['kode_produk'] ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" x-model="product.jumlah" x-on:input="calculateSubtotal(index)" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" x-model="product.harga_beli" x-on:input="calculateSubtotal(index)" min="0" step="0.01" required>
                                            </td>
                                            <td>
                                                <span class="fw-bold" x-text="formatCurrency(product.subtotal)"></span>
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
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th class="fw-bold" x-text="formatCurrency(total)"></th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <input type="hidden" name="status" value="Dipesan">

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/transactions/purchases') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Purchase Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('purchaseOrderForm', () => ({
        form: {
            id_supplier: '',
            products: [{
                id_produk: '',
                jumlah: 1,
                harga_beli: 0,
                subtotal: 0,
                keterangan: ''
            }]
        },
        total: 0,
        
        init() {
            this.$watch('form.products', () => {
                this.calculateTotal();
            });
            this.addProduct(); // Add one empty row initially
        },
        
        addProduct() {
            this.form.products.push({
                id_produk: '',
                jumlah: 1,
                harga_beli: 0,
                subtotal: 0,
                keterangan: ''
            });
        },
        
        removeProduct(index) {
            this.form.products.splice(index, 1);
        },
        
        updateProductPrice(index) {
            const select = event.target;
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            
            this.form.products[index].harga_beli = price;
            this.calculateSubtotal(index);
        },
        
        calculateSubtotal(index) {
            const product = this.form.products[index];
            product.subtotal = product.jumlah * product.harga_beli;
            this.calculateTotal();
        },
        
        calculateTotal() {
            this.total = this.form.products.reduce((sum, product) => sum + (product.subtotal || 0), 0);
        },
        
        updatePrices() {
            // This can be extended to update prices based on supplier
        },
        
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value || 0);
        }
    }));
});
</script>

<?= $this->endSection() ?>