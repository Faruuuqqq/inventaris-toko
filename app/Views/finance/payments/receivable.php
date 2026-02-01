<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('TrendingUp', 'h-8 w-8 text-success') ?>
            Pembayaran Piutang
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Catat pembayaran piutang dari customer</p>
    </div>
    <a href="<?= base_url('finance/payments') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Payment Form Card -->
<div class="max-w-2xl mx-auto">
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Form Pembayaran Piutang
            </h2>
        </div>

        <form action="<?= base_url('finance/payments/storeReceivable') ?>" method="post" class="p-6 space-y-6" x-data="receivableForm()" x-init="loadCustomers()">
            <?= csrf_field() ?>

            <!-- Customer Selection -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Customer *</label>
                <select name="customer_id" 
                        x-model="selectedCustomer" 
                        @change="onCustomerChange()"
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Pilih customer</option>
                    <template x-for="customer in customers" :key="customer.id">
                        <option :value="customer.id">
                            <span x-text="customer.name + ' (Piutang: Rp ' + formatNumber(customer.receivable_balance) + ')'"></span>
                        </option>
                    </template>
                </select>
            </div>

            <!-- Customer Info Box -->
            <div x-show="selectedCustomer" class="rounded-lg bg-primary/5 border border-primary/50 p-4 space-y-2">
                <h3 class="font-semibold text-sm text-primary flex items-center gap-2">
                    <?= icon('Info', 'h-4 w-4') ?>
                    Informasi Customer
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-muted-foreground">Piutang Saat Ini</p>
                        <p class="font-bold text-lg text-primary" x-text="'Rp ' + formatNumber(customerReceivable)"></p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Jatuh Tempo</p>
                        <p class="font-bold text-lg" :class="isOverdue ? 'text-destructive' : 'text-warning'" x-text="dueDate"></p>
                    </div>
                </div>
            </div>

            <!-- Reference Type Selection -->
            <div class="space-y-2" x-show="selectedCustomer">
                <label class="text-sm font-medium text-foreground">Referensi Pembayaran *</label>
                <select x-model="referenceType" 
                        @change="onReferenceChange()"
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">-- Pilih Tipe Referensi --</option>
                    <option value="sale">Penjualan Kredit (PK)</option>
                    <option value="kontra_bon">Kontra Bon</option>
                </select>
            </div>

            <!-- Sales Invoices List -->
            <div x-show="referenceType === 'sale'" class="space-y-2">
                <label class="text-sm font-medium text-foreground">Invoice Penjualan *</label>
                <select name="sale_invoice_id" 
                        x-model="selectedInvoice"
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Pilih Invoice</option>
                    <template x-for="invoice in invoices" :key="invoice.id">
                        <option :value="invoice.id">
                            <span x-text="invoice.invoice_number + ' - Rp ' + formatNumber(invoice.remaining) + ' (sisa)'"></span>
                        </option>
                    </template>
                </select>
            </div>

            <!-- Kontra Bon List -->
            <div x-show="referenceType === 'kontra_bon'" class="space-y-2">
                <label class="text-sm font-medium text-foreground">Kontra Bon *</label>
                <select name="kontra_bon_id" 
                        x-model="selectedKontraBon"
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Pilih Kontra Bon</option>
                    <template x-for="kb in kontraBons" :key="kb.id">
                        <option :value="kb.id">
                            <span x-text="kb.document_number + ' - Rp ' + formatNumber(kb.total_amount) + ' - Status: ' + kb.status"></span>
                        </option>
                    </template>
                </select>
            </div>

            <!-- Payment Amount and Method -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Jumlah Pembayaran *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">Rp</span>
                        <input type="number" 
                               name="amount" 
                               x-model.number="amount"
                               placeholder="0" 
                               step="0.01" 
                               required
                               class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 pl-10 text-right text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Metode Pembayaran *</label>
                    <select name="payment_method" 
                            x-model="paymentMethod"
                            class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="CASH">Tunai</option>
                        <option value="TRANSFER">Transfer Bank</option>
                        <option value="CHECK">Cek/Giro</option>
                    </select>
                </div>
            </div>

            <!-- Payment Notes -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Catatan (Opsional)</label>
                <textarea name="notes" 
                          x-model="notes"
                          placeholder="Masukkan catatan pembayaran..."
                          rows="3"
                          class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t border-border/50">
                <a href="<?= base_url('finance/payments') ?>" class="h-10 px-6 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition">
                    Batal
                </a>
                <button type="submit" class="h-10 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function receivableForm() {
        return {
            customers: [],
            invoices: [],
            kontraBons: [],
            selectedCustomer: '',
            referenceType: '',
            selectedInvoice: '',
            selectedKontraBon: '',
            amount: 0,
            paymentMethod: 'CASH',
            notes: '',
            customerReceivable: 0,
            dueDate: '',
            isOverdue: false,

            async loadCustomers() {
                try {
                    const res = await fetch('<?= base_url('master/customers/getList') ?>');
                    this.customers = await res.json();
                } catch (e) {
                    console.error("Failed to load customers", e);
                }
            },

            async onCustomerChange() {
                const customer = this.customers.find(c => c.id == this.selectedCustomer);
                if (customer) {
                    this.customerReceivable = parseFloat(customer.receivable_balance) || 0;
                    // Get due date from customer's oldest unpaid invoice
                    this.dueDate = customer.oldest_due_date || 'N/A';
                    this.isOverdue = customer.is_overdue || false;
                    this.referenceType = '';
                    this.invoices = [];
                    this.kontraBons = [];
                } else {
                    this.customerReceivable = 0;
                    this.dueDate = '';
                    this.isOverdue = false;
                }
            },

            async onReferenceChange() {
                if (!this.selectedCustomer) return;

                if (this.referenceType === 'sale') {
                    await this.loadInvoices();
                } else if (this.referenceType === 'kontra_bon') {
                    await this.loadKontraBons();
                }
            },

            async loadInvoices() {
                try {
                    const res = await fetch(`<?= base_url('finance/payments/getCustomerInvoices') ?>?customer_id=${this.selectedCustomer}`);
                    this.invoices = await res.json();
                } catch (e) {
                    console.error("Failed to load invoices", e);
                    alert('Gagal memuat data invoice');
                }
            },

            async loadKontraBons() {
                try {
                    const res = await fetch(`<?= base_url('finance/payments/getKontraBons') ?>?customer_id=${this.selectedCustomer}`);
                    this.kontraBons = await res.json();
                } catch (e) {
                    console.error("Failed to load kontra bons", e);
                    alert('Gagal memuat data kontra bon');
                }
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }
        }
    }
</script>

<?= $this->endSection() ?>
