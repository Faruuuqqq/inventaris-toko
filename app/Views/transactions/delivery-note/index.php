<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Alert Info -->
<div class="mb-6 flex items-center gap-3 rounded-lg border border-primary/50 bg-primary/10 p-4">
    <?= icon('FileText', 'h-5 w-5 text-primary') ?>
    <p class="text-sm text-primary">
        Surat jalan tidak mencantumkan harga. Digunakan sebagai bukti serah terima barang.
    </p>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Truck', 'h-5 w-5') ?>
                    Form Surat Jalan
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-6">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">No. Surat Jalan</label>
                        <input type="text" id="sj_number" value="<?= 'SJ-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) ?>" disabled class="form-input bg-muted">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Tanggal</label>
                        <input type="date" id="sj_date" value="<?= date('Y-m-d') ?>" class="form-input">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">No. Faktur</label>
                        <select id="invoice_id" class="form-input" onchange="loadInvoiceData()">
                            <option value="">Pilih faktur</option>
                            <?php foreach ($invoices ?? [] as $invoice): ?>
                            <option value="<?= $invoice['id'] ?>"
                                    data-customer="<?= esc($invoice['customer_name']) ?>"
                                    data-address="<?= esc($invoice['customer_address'] ?? '') ?>">
                                <?= esc($invoice['invoice_number']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Customer</label>
                        <input type="text" id="customer_name" value="" disabled class="form-input bg-muted">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Alamat Pengiriman</label>
                        <input type="text" id="delivery_address" placeholder="Alamat tujuan pengiriman" class="form-input">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Pengirim/Driver</label>
                        <select id="driver_id" class="form-input">
                            <option value="">Pilih pengirim</option>
                            <?php foreach ($drivers ?? [] as $driver): ?>
                            <option value="<?= $driver['id'] ?>"><?= esc($driver['name']) ?> - <?= esc($driver['vehicle_number'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Sales</label>
                        <select id="salesperson_id" class="form-input">
                            <option value="">Pilih sales</option>
                            <?php foreach ($salespersons ?? [] as $salesperson): ?>
                            <option value="<?= $salesperson['id'] ?>"><?= esc($salesperson['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Add Product Manually -->
                <div class="rounded-lg border p-4">
                    <h4 class="mb-4 font-medium">Tambah Barang</h4>
                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="md:col-span-2">
                            <select id="productSelect" class="form-input">
                                <option value="">Pilih produk</option>
                                <?php foreach ($products ?? [] as $product): ?>
                                <option value="<?= $product['id'] ?>" data-name="<?= esc($product['name']) ?>" data-unit="<?= esc($product['unit']) ?>">
                                    <?= esc($product['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="number" id="quantity" placeholder="Qty" min="1" class="form-input">
                        <button type="button" class="btn btn-primary" onclick="addItem()">
                            <?= icon('Plus', 'h-4 w-4 mr-2') ?>
                            Tambah
                        </button>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="rounded-lg border overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium">No</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Nama Barang</th>
                                <th class="px-4 py-3 text-right text-sm font-medium">Quantity</th>
                                <th class="px-4 py-3 text-right text-sm font-medium">Satuan</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Keterangan</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTable">
                            <tr id="emptyRow">
                                <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                    Belum ada barang ditambahkan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm sticky top-24">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold">Ringkasan</h3>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Total Jenis Barang</span>
                    <span class="font-medium"><span id="totalItems">0</span> produk</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Total Quantity</span>
                    <span class="font-medium"><span id="totalQuantity">0</span> pcs</span>
                </div>

                <div class="space-y-2 border-t pt-4">
                    <label class="text-sm font-medium">Catatan Pengiriman</label>
                    <input type="text" id="notes" placeholder="Catatan khusus..." class="form-input">
                </div>

                <div class="flex gap-2 pt-4">
                    <a href="<?= base_url('transactions/sales') ?>" class="btn btn-outline flex-1 text-center">
                        Batal
                    </a>
                    <button type="button" class="btn btn-primary flex-1" onclick="saveDeliveryNote()">
                        Simpan
                    </button>
                </div>

                <button type="button" class="btn btn-outline w-full" onclick="printDeliveryNote()">
                    <?= icon('Printer', 'h-4 w-4 mr-2') ?>
                    Cetak Surat Jalan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let items = [];

    function loadInvoiceData() {
        const select = document.getElementById('invoice_id');
        const option = select.options[select.selectedIndex];

        if (option.value) {
            document.getElementById('customer_name').value = option.dataset.customer || '';
            document.getElementById('delivery_address').value = option.dataset.address || '';

            // Load items from invoice via AJAX
            fetch(`<?= base_url('transactions/delivery-note/getInvoiceItems') ?>/${option.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        items = data.items.map(item => ({
                            id: item.product_id,
                            name: item.product_name,
                            quantity: item.quantity,
                            unit: item.unit || 'pcs',
                            notes: ''
                        }));
                        renderItems();
                        updateSummary();
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            document.getElementById('customer_name').value = '';
            document.getElementById('delivery_address').value = '';
        }
    }

    function addItem() {
        const select = document.getElementById('productSelect');
        const option = select.options[select.selectedIndex];
        const quantity = parseInt(document.getElementById('quantity').value) || 0;

        if (!option.value || quantity <= 0) {
            alert('Pilih produk dan masukkan quantity');
            return;
        }

        items.push({
            id: option.value,
            name: option.dataset.name,
            quantity: quantity,
            unit: option.dataset.unit || 'pcs',
            notes: ''
        });

        renderItems();
        updateSummary();

        // Reset form
        select.value = '';
        document.getElementById('quantity').value = '';
    }

    function removeItem(index) {
        items.splice(index, 1);
        renderItems();
        updateSummary();
    }

    function renderItems() {
        const tbody = document.getElementById('itemsTable');

        if (items.length === 0) {
            tbody.innerHTML = `
                <tr id="emptyRow">
                    <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                        Belum ada barang ditambahkan
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = '';
        items.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = 'border-t';
            row.innerHTML = `
                <td class="px-4 py-3">${index + 1}</td>
                <td class="px-4 py-3">${item.name}</td>
                <td class="px-4 py-3 text-right">${item.quantity}</td>
                <td class="px-4 py-3 text-right">${item.unit}</td>
                <td class="px-4 py-3 text-muted-foreground">${item.notes || '-'}</td>
                <td class="px-4 py-3">
                    <button type="button" class="text-destructive hover:text-destructive/80" onclick="removeItem(${index})">
                        <?= icon('Trash2', 'h-4 w-4') ?>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function updateSummary() {
        document.getElementById('totalItems').textContent = items.length;
        document.getElementById('totalQuantity').textContent = items.reduce((sum, item) => sum + item.quantity, 0);
    }

    function saveDeliveryNote() {
        if (items.length === 0) {
            alert('Tambah minimal 1 barang');
            return;
        }

        const formData = new FormData();
        formData.append('invoice_id', document.getElementById('invoice_id').value);
        formData.append('delivery_date', document.getElementById('sj_date').value);
        formData.append('delivery_address', document.getElementById('delivery_address').value);
        formData.append('driver_id', document.getElementById('driver_id').value);
        formData.append('salesperson_id', document.getElementById('salesperson_id').value);
        formData.append('notes', document.getElementById('notes').value);
        formData.append('items', JSON.stringify(items));

        fetch('<?= base_url('transactions/delivery-note/store') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Surat jalan berhasil disimpan');
                window.location.href = '<?= base_url('transactions/sales') ?>';
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan surat jalan');
        });
    }

    function printDeliveryNote() {
        if (items.length === 0) {
            alert('Tambah minimal 1 barang terlebih dahulu');
            return;
        }
        window.open('<?= base_url('transactions/delivery-note/print') ?>?preview=1', '_blank');
    }
</script>
<?= $this->endSection() ?>
