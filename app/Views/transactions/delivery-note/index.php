<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Info Banner -->
<div class="mb-6 flex items-center gap-3 rounded-lg border border-primary/50 bg-primary/10 p-4">
    <?= icon('FileText', 'h-5 w-5 text-primary') ?>
    <p class="text-sm text-primary">
        Surat jalan tidak mencantumkan harga. Digunakan sebagai bukti serah terima barang.
    </p>
</div>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Truck', 'h-8 w-8 text-primary') ?>
            Buat Surat Jalan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Buat dokumen bukti serah terima barang</p>
    </div>
    <a href="<?= base_url('transactions/sales') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Delivery Note Form -->
<form method="post" action="<?= base_url('transactions/delivery-note/store') ?>" x-data="deliveryNoteForm()" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Header Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Informasi Surat Jalan
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Document Number and Date -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">No. Surat Jalan</label>
                    <input type="text" value="<?= 'SJ-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) ?>" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground font-mono">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tanggal *</label>
                    <input type="date" name="delivery_date" value="<?= date('Y-m-d') ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">No. Faktur *</label>
                    <select name="invoice_id" @change="loadInvoiceData()" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Faktur</option>
                        <?php foreach ($invoices ?? [] as $invoice): ?>
                            <option value="<?= $invoice['id'] ?>" data-customer="<?= esc($invoice['customer_name']) ?>" data-address="<?= esc($invoice['customer_address'] ?? '') ?>">
                                <?= esc($invoice['invoice_number']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Customer and Delivery Address -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Customer</label>
                    <input type="text" x-model="form.customer_name" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Alamat Pengiriman *</label>
                    <input type="text" name="delivery_address" x-model="form.delivery_address" placeholder="Alamat tujuan pengiriman" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
            </div>

            <!-- Driver and Salesperson -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Pengirim/Driver *</label>
                    <select name="driver_id" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Pengirim</option>
                        <?php foreach ($drivers ?? [] as $driver): ?>
                            <option value="<?= $driver['id'] ?>">
                                <?= esc($driver['name']) ?> - <?= esc($driver['vehicle_number'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Sales *</label>
                    <select name="salesperson_id" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Sales</option>
                        <?php foreach ($salespersons ?? [] as $salesperson): ?>
                            <option value="<?= $salesperson['id'] ?>">
                                <?= esc($salesperson['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Catatan Pengiriman (Opsional)</label>
                <textarea name="notes" x-model="form.notes" rows="2" placeholder="Catatan khusus pengiriman..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Package', 'h-5 w-5 text-primary') ?>
                Daftar Barang
            </h2>
        </div>

        <!-- Add Product Section -->
        <div class="p-6 border-b border-border/50 bg-muted/20">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-medium text-foreground">Pilih Produk</label>
                    <select @change="addItem()" x-ref="productSelect" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Produk</option>
                        <?php foreach ($products ?? [] as $product): ?>
                            <option value="<?= $product['id'] ?>" data-name="<?= esc($product['name']) ?>" data-unit="<?= esc($product['unit']) ?>">
                                <?= esc($product['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Quantity</label>
                    <input type="number" x-ref="quantityInput" min="1" placeholder="Qty" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <div class="flex items-end">
                    <button type="button" @click="addItemManual()" class="w-full h-10 inline-flex items-center justify-center gap-2 bg-primary text-white font-medium text-sm rounded-lg hover:bg-primary/90 transition">
                        <?= icon('Plus', 'h-4 w-4') ?>
                        Tambah
                    </button>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="p-6">
            <div class="relative w-full overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-4 text-center align-middle font-medium text-muted-foreground w-12">No</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Nama Barang</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Quantity</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-20">Satuan</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground flex-1">Keterangan</th>
                            <th class="h-12 px-4 text-center align-middle font-medium text-muted-foreground w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <template x-for="(item, index) in form.items" :key="index">
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-4 py-3 text-center text-muted-foreground" x-text="index + 1"></td>
                                <td class="px-4 py-3" x-text="item.name"></td>
                                <td class="px-4 py-3 text-right font-medium" x-text="item.quantity"></td>
                                <td class="px-4 py-3" x-text="item.unit"></td>
                                <td class="px-4 py-3">
                                    <input type="text" x-model="item.notes" placeholder="Keterangan..." class="w-full h-8 rounded border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeItem(index)" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-destructive hover:bg-destructive/10 transition">
                                        <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <template x-if="form.items.length === 0">
                <div class="flex flex-col items-center justify-center py-8 text-muted-foreground">
                    <?= icon('Package', 'h-12 w-12 mb-3 opacity-50') ?>
                    <p class="text-sm">Belum ada barang ditambahkan. Pilih produk di atas dan klik "Tambah".</p>
                </div>
            </template>
        </div>
    </div>

    <!-- Summary Section (Sticky Sidebar) -->
    <div class="lg:sticky lg:top-24">
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground">Ringkasan</h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-muted-foreground">Total Jenis Barang</span>
                    <span class="font-semibold text-foreground" x-text="form.items.length + ' produk'"></span>
                </div>

                <div class="flex justify-between items-center pb-4 border-b border-border/50">
                    <span class="text-sm text-muted-foreground">Total Quantity</span>
                    <span class="font-semibold text-foreground" x-text="getTotalQuantity() + ' pcs'"></span>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 flex-col">
                    <a href="<?= base_url('transactions/sales') ?>" class="h-10 px-6 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="h-10 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center justify-center gap-2">
                        <?= icon('Save', 'h-4 w-4') ?>
                        Simpan Surat Jalan
                    </button>
                    <button type="button" @click="printPreview()" class="h-10 px-6 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition flex items-center justify-center gap-2">
                        <?= icon('Printer', 'h-4 w-4') ?>
                        Cetak Preview
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('deliveryNoteForm', () => ({
            form: {
                customer_name: '',
                delivery_address: '',
                notes: '',
                items: []
            },

            addItem() {
                const select = this.$refs.productSelect;
                const option = select.options[select.selectedIndex];
                const qty = parseInt(this.$refs.quantityInput.value) || 0;

                if (!option.value || qty <= 0) {
                    alert('Pilih produk dan masukkan quantity');
                    return;
                }

                this.form.items.push({
                    id: option.value,
                    name: option.dataset.name,
                    quantity: qty,
                    unit: option.dataset.unit || 'pcs',
                    notes: ''
                });

                // Reset
                select.value = '';
                this.$refs.quantityInput.value = '';
            },

            addItemManual() {
                this.addItem();
            },

            removeItem(index) {
                this.form.items.splice(index, 1);
            },

            getTotalQuantity() {
                return this.form.items.reduce((sum, item) => sum + item.quantity, 0);
            },

            loadInvoiceData() {
                const select = document.querySelector('select[name="invoice_id"]');
                const option = select.options[select.selectedIndex];

                if (option.value) {
                    this.form.customer_name = option.dataset.customer || '';
                    this.form.delivery_address = option.dataset.address || '';

                    // Load items from invoice via AJAX if needed
                    fetch(`<?= base_url('transactions/delivery-note/getInvoiceItems') ?>/${option.value}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.form.items = data.items.map(item => ({
                                    id: item.product_id,
                                    name: item.product_name,
                                    quantity: item.quantity,
                                    unit: item.unit || 'pcs',
                                    notes: ''
                                }));
                            }
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    this.form.customer_name = '';
                    this.form.delivery_address = '';
                }
            },

            printPreview() {
                if (this.form.items.length === 0) {
                    alert('Tambah minimal 1 barang terlebih dahulu');
                    return;
                }
                window.open('<?= base_url('transactions/delivery-note/print') ?>?preview=1', '_blank');
            }
        }));
    });
</script>

<?= $this->endSection() ?>