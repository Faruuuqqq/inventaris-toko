<!-- Receivables Payment Form -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-semibold">Pembayaran Piutang</h3>
                <p class="text-sm text-muted-foreground">Catat pembayaran piutang customer</p>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Form Pembayaran</h3>
            
            <form action="/finance/payments/storeReceivable" method="post" class="space-y-4">
                <div class="space-y-2">
                    <label for="customer_id">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-input" onchange="loadCustomerInvoices()">
                        <option value="">Pilih customer</option>
                        <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?> (Piutang: <?= format_currency($customer['receivable_balance']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="invoiceSection" class="hidden space-y-2">
                    <label for="reference_id">Referensi</label>
                    <select name="reference_id" id="reference_id" class="form-input">
                        <option value="">-- Pilih tipe referensi --</option>
                        <option value="sale">Penjualan Langsung</option>
                        <option value="kontra_bon">Kontra Bon</option>
                    </select>
                    
                    <div id="saleInvoiceList" class="hidden">
                        <select name="sale_invoice_id" id="sale_invoice_id" class="form-input">
                            <option value="">Pilih Invoice Penjualan</option>
                        </select>
                    </div>
                    
                    <div id="kontraBonList" class="hidden">
                        <select name="kontra_bon_id" id="kontra_bon_id" class="form-input">
                            <option value="">Pilih Kontra Bon</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="amount">Jumlah Pembayaran</label>
                        <input type="number" name="amount" id="amount" class="form-input" placeholder="0" step="0.01" required>
                    </div>
                    <div class="space-y-2">
                        <label for="payment_method">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="form-input">
                            <option value="CASH">Tunai</option>
                            <option value="TRANSFER">Transfer Bank</option>
                            <option value="CHECK">Cek/Giro</option>
                        </select>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes" class="form-input" placeholder="Catatan (opsional)" rows="3"></textarea>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="btn btn-outline">
                        Kembali
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function loadCustomerInvoices() {
        const customerId = document.getElementById('customer_id').value;
        const referenceType = document.getElementById('reference_id').value;
        
        if (!customerId || !referenceType) {
            hideInvoiceSections();
            return;
        }
        
        // Show appropriate section
        hideInvoiceSections();
        if (referenceType === 'sale') {
            document.getElementById('saleInvoiceList').classList.remove('hidden');
            loadSaleInvoices(customerId);
        } else if (referenceType === 'kontra_bon') {
            document.getElementById('kontraBonList').classList.remove('hidden');
            loadKontraBons(customerId);
        }
    }
    
    function hideInvoiceSections() {
        document.getElementById('invoiceSection').classList.add('hidden');
        document.getElementById('saleInvoiceList').classList.add('hidden');
        document.getElementById('kontraBonList').classList.add('hidden');
    }
    
    function loadSaleInvoices(customerId) {
        fetch(`/finance/payments/getCustomerInvoices?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('sale_invoice_id');
                select.innerHTML = '<option value="">Pilih Invoice</option>';
                
                data.forEach(invoice => {
                    const option = document.createElement('option');
                    option.value = invoice.id;
                    option.textContent = `${invoice.invoice_number} - Total: ${formatCurrency(invoice.total_amount)} - Belum: ${formatCurrency(invoice.total_amount - invoice.paid_amount)}`;
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading invoices:', error);
                alert('Gagal memuat data invoice');
            });
    }
    
    function loadKontraBons(customerId) {
        // Fetch Kontra Bon data for this customer
        // Simplified for now
        fetch('/finance/kontra-bon')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('kontra_bon_id');
                select.innerHTML = '<option value="">Pilih Kontra Bon</option>';
                
                data
                    .filter(kb => kb.customer_id == customerId && kb.status !== 'PAID')
                    .forEach(kb => {
                        const option = document.createElement('option');
                        option.value = kb.id;
                        option.textContent = `${kb.document_number} - Total: ${formatCurrency(kb.total_amount)} - Status: ${kb.status}`;
                        select.appendChild(option);
                    });
            })
            .catch(error => {
                console.error('Error loading Kontra Bons:', error);
                alert('Gagal memuat data Kontra Bon');
            });
    }
    
    function formatCurrency(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }
    
    // Show invoice section when reference type changes
    document.getElementById('reference_id').addEventListener('change', loadCustomerInvoices);
</script>