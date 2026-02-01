<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('TrendingDown', 'h-8 w-8 text-destructive') ?>
            Pembayaran Utang
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Catat pembayaran utang ke supplier</p>
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
                Form Pembayaran Utang
            </h2>
        </div>

        <form action="<?= base_url('finance/payments/storePayable') ?>" method="post" class="p-6 space-y-6" x-data="payableForm()" x-init="loadSuppliers()">
            <?= csrf_field() ?>

            <!-- Supplier Selection -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Supplier *</label>
                <select name="supplier_id" 
                        x-model="selectedSupplier" 
                        @change="onSupplierChange()"
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Pilih supplier</option>
                    <template x-for="supplier in suppliers" :key="supplier.id">
                        <option :value="supplier.id">
                            <span x-text="supplier.name + ' (Utang: Rp ' + formatNumber(supplier.debt_balance) + ')'"></span>
                        </option>
                    </template>
                </select>
            </div>

            <!-- Supplier Info Box -->
            <div x-show="selectedSupplier" class="rounded-lg bg-destructive/5 border border-destructive/50 p-4 space-y-2">
                <h3 class="font-semibold text-sm text-destructive flex items-center gap-2">
                    <?= icon('Info', 'h-4 w-4') ?>
                    Informasi Supplier
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-muted-foreground">Sisa Utang</p>
                        <p class="font-bold text-lg text-destructive" x-text="'Rp ' + formatNumber(supplierDebt)"></p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Jatuh Tempo</p>
                        <p class="font-bold text-lg" :class="isOverdue ? 'text-destructive' : 'text-warning'" x-text="dueDate"></p>
                    </div>
                </div>
            </div>

            <!-- Purchase Orders Reference -->
            <div class="space-y-2" x-show="selectedSupplier">
                <label class="text-sm font-medium text-foreground">Referensi Pembelian *</label>
                <select name="purchase_id" 
                        x-model="selectedPurchase"
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                        @change="onPurchaseChange()">
                    <option value="">Pilih PO/Invoice Pembelian</option>
                    <template x-for="purchase in purchases" :key="purchase.id">
                        <option :value="purchase.id">
                            <span x-text="purchase.purchase_number + ' - Rp ' + formatNumber(purchase.remaining) + ' (sisa)'"></span>
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
                    <p class="text-xs text-muted-foreground" x-show="selectedPurchase && suggestedAmount > 0">
                        Saran: Rp <span x-text="formatNumber(suggestedAmount)"></span>
                    </p>
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
    function payableForm() {
        return {
            suppliers: [],
            purchases: [],
            selectedSupplier: '',
            selectedPurchase: '',
            amount: 0,
            paymentMethod: 'CASH',
            notes: '',
            supplierDebt: 0,
            dueDate: '',
            isOverdue: false,
            suggestedAmount: 0,

            async loadSuppliers() {
                try {
                    const res = await fetch('<?= base_url('master/suppliers/getList') ?>');
                    this.suppliers = await res.json();
                } catch (e) {
                    console.error("Failed to load suppliers", e);
                }
            },

            async onSupplierChange() {
                const supplier = this.suppliers.find(s => s.id == this.selectedSupplier);
                if (supplier) {
                    this.supplierDebt = parseFloat(supplier.debt_balance) || 0;
                    this.dueDate = supplier.oldest_due_date || 'N/A';
                    this.isOverdue = supplier.is_overdue || false;
                    this.selectedPurchase = '';
                    this.purchases = [];
                    this.suggestedAmount = 0;
                    await this.loadPurchases();
                } else {
                    this.supplierDebt = 0;
                    this.dueDate = '';
                    this.isOverdue = false;
                    this.purchases = [];
                }
            },

            async loadPurchases() {
                if (!this.selectedSupplier) return;

                try {
                    const res = await fetch(`<?= base_url('finance/payments/getSupplierPurchases') ?>?supplier_id=${this.selectedSupplier}`);
                    this.purchases = await res.json();
                } catch (e) {
                    console.error("Failed to load purchases", e);
                    alert('Gagal memuat data pembelian');
                }
            },

            onPurchaseChange() {
                if (this.selectedPurchase) {
                    const purchase = this.purchases.find(p => p.id == this.selectedPurchase);
                    if (purchase) {
                        this.suggestedAmount = parseFloat(purchase.remaining) || 0;
                    }
                } else {
                    this.suggestedAmount = 0;
                }
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }
        }
    }
</script>

<?= $this->endSection() ?>
