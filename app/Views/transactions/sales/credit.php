<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Alert Warning -->
<div class="mb-6 flex items-center gap-3 rounded-lg border border-yellow-500/50 bg-yellow-500/10 p-4">
    <?= icon('AlertCircle', 'h-5 w-5 text-yellow-600') ?>
    <p class="text-sm text-yellow-700">
        Penjualan kredit akan menambah piutang customer. Pastikan data customer sudah benar dan limit kredit mencukupi.
    </p>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Form Section -->
    <div class="lg:col-span-2">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('CreditCard', 'h-5 w-5') ?>
                    Form Penjualan Kredit
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-6">
                <!-- Header Info -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">No. Faktur</label>
                        <input type="text" id="invoice_number" value="<?= 'PK-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) ?>" disabled class="form-input bg-muted">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Tanggal</label>
                        <input type="date" id="sale_date" value="<?= date('Y-m-d') ?>" class="form-input">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Customer *</label>
                        <select name="customer_id" id="customer_id" class="form-input" onchange="checkCreditLimit()">
                            <option value="">Pilih customer</option>
                            <?php foreach ($customers ?? [] as $customer): ?>
                            <option value="<?= $customer['id'] ?>"
                                    data-credit-limit="<?= $customer['credit_limit'] ?? 0 ?>"
                                    data-receivable="<?= $customer['receivable_balance'] ?? 0 ?>">
                                <?= esc($customer['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Jatuh Tempo *</label>
                        <input type="date" id="due_date" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" class="form-input">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Sales</label>
                        <select name="salesperson_id" id="salesperson_id" class="form-input">
                            <option value="">Pilih sales</option>
                            <?php foreach ($salespersons ?? [] as $salesperson): ?>
                            <option value="<?= $salesperson['id'] ?>"><?= esc($salesperson['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Gudang *</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-input">
                            <option value="">Pilih gudang</option>
                            <?php foreach ($warehouses ?? [] as $warehouse): ?>
                            <option value="<?= $warehouse['id'] ?>"><?= esc($warehouse['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Credit Info Box -->
                <div id="creditInfoBox" class="hidden rounded-lg border border-blue-300 bg-blue-50 p-4">
                    <h4 class="font-medium mb-2 flex items-center gap-2 text-blue-800">
                        <?= icon('Wallet', 'h-4 w-4') ?>
                        Informasi Kredit Customer
                    </h4>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-blue-600">Limit Kredit:</span>
                            <p class="font-semibold text-blue-800" id="creditLimit">Rp 0</p>
                        </div>
                        <div>
                            <span class="text-blue-600">Piutang Saat Ini:</span>
                            <p class="font-semibold text-yellow-600" id="currentReceivable">Rp 0</p>
                        </div>
                        <div>
                            <span class="text-blue-600">Sisa Limit:</span>
                            <p class="font-semibold text-green-600" id="remainingLimit">Rp 0</p>
                        </div>
                    </div>
                </div>

                <!-- Owner Only: Hidden Transaction -->
                <?php if (session()->get('role') === 'OWNER'): ?>
                <div class="flex items-center gap-2 p-3 rounded-lg bg-muted/50 border">
                    <input type="checkbox" id="is_hidden" name="is_hidden" class="h-4 w-4 rounded border-input">
                    <label for="is_hidden" class="text-sm">Sembunyikan transaksi dari laporan Admin (Hanya Owner yang bisa melihat)</label>
                </div>
                <?php endif; ?>

                <!-- Add Product -->
                <div class="rounded-lg border p-4">
                    <h4 class="mb-4 font-medium">Tambah Produk</h4>
                    <div class="grid gap-4 md:grid-cols-5">
                        <div class="md:col-span-2">
                            <select id="productSelect" class="form-input">
                                <option value="">Pilih produk</option>
                                <?php foreach ($products ?? [] as $product): ?>
                                <option value="<?= $product['id'] ?>"
                                        data-price="<?= $product['price_sell'] ?>"
                                        data-name="<?= esc($product['name']) ?>">
                                    <?= esc($product['name']) ?> - <?= format_currency($product['price_sell']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="number" id="quantity" placeholder="Qty" min="1" class="form-input">
                        <input type="number" id="discount" placeholder="Diskon" min="0" class="form-input">
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
                                <th class="px-4 py-3 text-left text-sm font-medium">Produk</th>
                                <th class="px-4 py-3 text-right text-sm font-medium">Qty</th>
                                <th class="px-4 py-3 text-right text-sm font-medium">Harga</th>
                                <th class="px-4 py-3 text-right text-sm font-medium">Diskon</th>
                                <th class="px-4 py-3 text-right text-sm font-medium">Subtotal</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTable">
                            <tr id="emptyRow">
                                <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                                    Belum ada produk ditambahkan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div>
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm sticky top-24">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Calculator', 'h-5 w-5') ?>
                    Ringkasan
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Subtotal (<span id="totalItems">0</span> item)</span>
                    <span id="subtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Diskon</span>
                    <span id="totalDiscount">Rp 0</span>
                </div>
                <div class="border-t pt-4">
                    <div class="flex justify-between">
                        <span class="font-semibold">Total Piutang</span>
                        <span class="text-xl font-bold text-yellow-600" id="grandTotal">Rp 0</span>
                    </div>
                </div>

                <div class="space-y-2 pt-4">
                    <label class="text-sm font-medium">Uang Muka (DP)</label>
                    <input type="number" id="downPayment" placeholder="0" class="form-input text-right" oninput="updateSummary()">
                </div>

                <div class="rounded-lg border bg-muted/50 p-3">
                    <div class="flex justify-between text-sm">
                        <span>Sisa Piutang</span>
                        <span class="font-bold text-red-600" id="remainingDebt">Rp 0</span>
                    </div>
                </div>

                <!-- Credit Limit Warning -->
                <div id="creditWarning" class="hidden rounded-lg border border-red-300 bg-red-50 p-3">
                    <div class="flex items-center gap-2 text-sm text-red-600">
                        <?= icon('AlertCircle', 'h-4 w-4') ?>
                        <span>Total melebihi limit kredit customer!</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Catatan</label>
                    <input type="text" id="notes" placeholder="Catatan tambahan..." class="form-input">
                </div>

                <div class="flex gap-2 pt-4">
                    <a href="<?= base_url('transactions/sales') ?>" class="btn btn-outline flex-1 text-center">
                        Batal
                    </a>
                    <button type="button" class="btn btn-primary flex-1" onclick="saveTransaction()" id="btnSave">
                        Simpan
                    </button>
                </div>

                <button type="button" class="btn btn-outline w-full" onclick="printInvoice()">
                    <?= icon('Printer', 'h-4 w-4 mr-2') ?>
                    Cetak Faktur
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let items = [];
    let customerData = {};

    function checkCreditLimit() {
        const select = document.getElementById('customer_id');
        const option = select.options[select.selectedIndex];
        const creditBox = document.getElementById('creditInfoBox');

        if (option.value) {
            const creditLimit = parseFloat(option.dataset.creditLimit) || 0;
            const receivable = parseFloat(option.dataset.receivable) || 0;
            const remaining = creditLimit - receivable;

            customerData = {
                creditLimit: creditLimit,
                receivable: receivable,
                remaining: remaining
            };

            document.getElementById('creditLimit').textContent = formatCurrency(creditLimit);
            document.getElementById('currentReceivable').textContent = formatCurrency(receivable);
            document.getElementById('remainingLimit').textContent = formatCurrency(remaining);
            creditBox.classList.remove('hidden');
        } else {
            creditBox.classList.add('hidden');
            customerData = {};
        }

        updateSummary();
    }

    function addItem() {
        const select = document.getElementById('productSelect');
        const option = select.options[select.selectedIndex];
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;

        if (!option.value || quantity <= 0) {
            alert('Pilih produk dan masukkan quantity');
            return;
        }

        const price = parseFloat(option.dataset.price) || 0;
        const subtotal = (price * quantity) - discount;

        items.push({
            id: option.value,
            name: option.dataset.name,
            price: price,
            quantity: quantity,
            discount: discount,
            subtotal: subtotal
        });

        renderItems();
        updateSummary();

        // Reset form
        select.value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('discount').value = '';
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
                    <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                        Belum ada produk ditambahkan
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
                <td class="px-4 py-3 text-right">${formatCurrency(item.price)}</td>
                <td class="px-4 py-3 text-right">${formatCurrency(item.discount)}</td>
                <td class="px-4 py-3 text-right font-medium">${formatCurrency(item.subtotal)}</td>
                <td class="px-4 py-3">
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removeItem(${index})">
                        <?= icon('Trash2', 'h-4 w-4') ?>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function updateSummary() {
        const totalItems = items.length;
        const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const totalDiscount = items.reduce((sum, item) => sum + item.discount, 0);
        const grandTotal = subtotal - totalDiscount;
        const downPayment = parseFloat(document.getElementById('downPayment').value) || 0;
        const remainingDebt = grandTotal - downPayment;

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('subtotal').textContent = formatCurrency(subtotal);
        document.getElementById('totalDiscount').textContent = formatCurrency(totalDiscount);
        document.getElementById('grandTotal').textContent = formatCurrency(grandTotal);
        document.getElementById('remainingDebt').textContent = formatCurrency(Math.max(0, remainingDebt));

        // Check credit limit
        const warning = document.getElementById('creditWarning');
        const btnSave = document.getElementById('btnSave');

        if (customerData.remaining !== undefined && grandTotal > customerData.remaining) {
            warning.classList.remove('hidden');
            btnSave.disabled = true;
            btnSave.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            warning.classList.add('hidden');
            btnSave.disabled = false;
            btnSave.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function saveTransaction() {
        const customerId = document.getElementById('customer_id').value;
        const salespersonId = document.getElementById('salesperson_id').value;
        const warehouseId = document.getElementById('warehouse_id').value;
        const dueDate = document.getElementById('due_date').value;
        const downPayment = parseFloat(document.getElementById('downPayment').value) || 0;
        const notes = document.getElementById('notes').value;
        const isHidden = document.getElementById('is_hidden')?.checked ? 1 : 0;

        if (!customerId) {
            alert('Pilih customer terlebih dahulu');
            return;
        }

        if (!warehouseId) {
            alert('Pilih gudang terlebih dahulu');
            return;
        }

        if (!dueDate) {
            alert('Masukkan tanggal jatuh tempo');
            return;
        }

        if (items.length === 0) {
            alert('Tambah minimal 1 produk');
            return;
        }

        const totalAmount = items.reduce((sum, item) => sum + item.subtotal, 0);

        // Check credit limit before saving
        if (customerData.remaining !== undefined && totalAmount > customerData.remaining) {
            alert(`Total melebihi limit kredit. Sisa limit: ${formatCurrency(customerData.remaining)}`);
            return;
        }

        // Create form data
        const formData = new FormData();
        formData.append('customer_id', customerId);
        formData.append('salesperson_id', salespersonId);
        formData.append('warehouse_id', warehouseId);
        formData.append('due_date', dueDate);
        formData.append('total_amount', totalAmount);
        formData.append('paid_amount', downPayment);
        formData.append('notes', notes);
        formData.append('is_hidden', isHidden);
        formData.append('items', JSON.stringify(items));

        fetch('<?= base_url('transactions/sales/storeCredit') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Transaksi berhasil disimpan');
                window.location.href = '<?= base_url('transactions/sales') ?>';
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan transaksi');
        });
    }

    function printInvoice() {
        alert('Fitur cetak faktur akan tersedia setelah transaksi disimpan');
    }

    function formatCurrency(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }
</script>
<?= $this->endSection() ?>
