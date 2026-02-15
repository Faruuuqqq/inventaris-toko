<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="purchaseManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Pembelian</h2>
            <p class="mt-1 text-muted-foreground">Kelola purchase order dan penerimaan barang dari supplier</p>
        </div>
    </div>

    <!-- Summary Cards - Compact Grid -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Purchase Orders -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-primary/5 to-transparent p-5 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total PO</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="purchaseOrders.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">aktif</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('Info', 'h-5 w-5 text-primary') ?>
                </div>
            </div>
        </div>

        <!-- Pending Received -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Menunggu Penerimaan</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="pendingReceived"></p>
                    <p class="mt-1 text-xs text-muted-foreground">PO</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <?= icon('Clock', 'h-5 w-5 text-warning') ?>
                </div>
            </div>
        </div>

        <!-- Received -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Sudah Diterima</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="fullyReceived"></p>
                    <p class="mt-1 text-xs text-muted-foreground">PO</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                    <?= icon('CheckCircle', 'h-5 w-5 text-success') ?>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-blue/5 to-transparent p-5 hover:border-blue/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Nilai PO</p>
                    <p class="mt-2 text-2xl font-bold text-foreground">Rp 0</p>
                    <p class="mt-1 text-xs text-muted-foreground">semua pembelian</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue/10">
                    <?= icon('DollarSign', 'h-5 w-5 text-blue') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Bar - Professional Toolbar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search & Filter -->
        <div class="flex gap-3 flex-1 flex-wrap">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-64">
                <?= icon('Search', 'absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground') ?>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari No. PO atau supplier..." 
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 pl-10 transition-all"
                >
            </div>
            
            <!-- Supplier Filter -->
            <select 
                x-model="supplierFilter"
                class="flex h-10 items-center rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
            >
                <option value="all">Semua Supplier</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier->id ?>"><?= esc($supplier->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Right Side: Action Buttons -->
        <div class="flex gap-2">
            <!-- Export Button -->
            <button 
                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-3 gap-2 text-sm font-medium"
                title="Export data"
            >
                <?= icon('ArrowDown', 'h-4 w-4') ?>
                <span class="hidden sm:inline">Export</span>
            </button>

            <!-- Create PO Button -->
            <a 
                href="<?= base_url('transactions/purchases/create') ?>"
                class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md"
                title="Buat PO baru"
            >
                <?= icon('Plus', 'h-5 w-5') ?>
                <span class="hidden sm:inline">Buat PO</span>
                <span class="sm:hidden">Buat</span>
            </a>
        </div>
    </div>

    <!-- Purchase Orders Table - Professional Data Grid -->
    <div class="rounded-xl border border-border/50 bg-surface shadow-sm overflow-hidden">
        <!-- Table Header with Column Info -->
        <div class="border-b border-border/50 bg-muted/30 px-6 py-3">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                <span x-text="`${filteredPurchaseOrders.length} PO ditemukan`"></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50 bg-background/50">
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">No. PO</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Supplier</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Gudang</th>
                        <th class="h-12 px-6 py-3 text-center font-semibold text-foreground uppercase text-xs tracking-wide">Status</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Total</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Tanggal</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="po in filteredPurchaseOrders" :key="po.id_po">
                        <tr class="border-b border-border/30 hover:bg-primary/3 transition-colors duration-150">
                            <!-- PO Number -->
                            <td class="px-6 py-4">
                                <a href="<?= base_url('transactions/purchases/detail') ?>/:po.id_po" class="font-semibold text-primary hover:text-primary-light transition" x-text="po.nomor_po"></a>
                            </td>

                            <!-- Supplier -->
                            <td class="px-6 py-4 font-medium text-foreground" x-text="po.name"></td>

                            <!-- Warehouse -->
                            <td class="px-6 py-4 text-foreground" x-text="po.warehouse_name || '-'"></td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 text-center">
                                <span 
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"
                                    :class="{
                                        'border-warning/30 bg-warning/10 text-warning': po.status === 'Dipesan',
                                        'border-blue/30 bg-blue/10 text-blue': po.status === 'Sebagian Diterima',
                                        'border-success/30 bg-success/10 text-success': po.status === 'Diterima Semua',
                                        'border-destructive/30 bg-destructive/10 text-destructive': po.status === 'Dibatalkan'
                                    }"
                                    x-text="po.status">
                                </span>
                            </td>

                            <!-- Total Value -->
                            <td class="px-6 py-4 text-right font-bold text-foreground" x-text="'Rp ' + formatNumber(po.total_bayar)"></td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-right text-muted-foreground" x-text="formatDate(po.tanggal_po)"></td>

                            <!-- Action Buttons -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <a 
                                        :href="`<?= base_url('transactions/purchases/detail') ?>/${po.id_po}`"
                                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                        title="Lihat detail"
                                    >
                                        <?= icon('Eye', 'h-4 w-4') ?>
                                    </a>
                                    <template x-if="po.status !== 'Diterima Semua' && po.status !== 'Dibatalkan'">
                                        <a 
                                            :href="`<?= base_url('transactions/purchases/receive') ?>/${po.id_po}`"
                                            class="inline-flex items-center justify-center rounded-lg border border-success/30 bg-success/5 hover:bg-success/15 transition h-9 w-9 text-success"
                                            title="Terima barang"
                                        >
                                            <?= icon('CheckCircle', 'h-4 w-4') ?>
                                        </a>
                                    </template>
                                    <template x-if="po.status === 'Dipesan'">
                                        <a 
                                            :href="`<?= base_url('transactions/purchases/edit') ?>/${po.id_po}`"
                                            class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                            title="Edit PO"
                                        >
                                            <?= icon('Edit', 'h-4 w-4') ?>
                                        </a>
                                    </template>
                                    <template x-if="po.status === 'Dipesan'">
                                        <button 
                                            @click="deletePO(po.id_po)"
                                            class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                            title="Hapus PO"
                                        >
                                            <?= icon('Trash2', 'h-4 w-4') ?>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredPurchaseOrders.length === 0">
                        <td colspan="7" class="py-12 px-6 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <?= icon('Info', 'h-12 w-12 text-muted-foreground opacity-30') ?>
                                <p class="text-sm font-medium text-foreground">Tidak ada PO ditemukan</p>
                                <p class="text-xs text-muted-foreground">Coba ubah filter atau cari dengan kata kunci lain</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="border-t border-border/50 bg-muted/20 px-6 py-3 flex items-center justify-between text-xs text-muted-foreground">
            <span x-text="`Menampilkan ${filteredPurchaseOrders.length} dari ${purchaseOrders.length} PO`"></span>
            <a href="<?= base_url('transactions/purchases') ?>" class="text-primary hover:text-primary-light font-semibold transition">
                Refresh
            </a>
        </div>
    </div>
</div>

<script>
function purchaseManager() {
    return {
        purchaseOrders: <?= json_encode($purchaseOrders ?? []) ?>,
        search: '',
        supplierFilter: 'all',

        get filteredPurchaseOrders() {
            return this.purchaseOrders.filter(po => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = (po.nomor_po && po.nomor_po.toLowerCase().includes(searchLower)) ||
                                    (po.name && po.name.toLowerCase().includes(searchLower));
                
                const matchesSupplier = this.supplierFilter === 'all' || 
                                       po.id_supplier == this.supplierFilter;
                                      
                return matchesSearch && matchesSupplier;
            });
        },

        get pendingReceived() {
            return this.purchaseOrders.filter(po => po.status === 'Dipesan' || po.status === 'Sebagian Diterima').length;
        },

        get fullyReceived() {
            return this.purchaseOrders.filter(po => po.status === 'Diterima Semua').length;
        },

        deletePO(poId) {
            const po = this.purchaseOrders.find(p => p.id === poId);
            const poNumber = po ? po.po_number : 'PO ini';
            ModalManager.submitDelete(
                `<?= base_url('transactions/purchases/delete') ?>/${poId}`,
                poNumber,
                () => {
                    this.purchaseOrders = this.purchaseOrders.filter(p => p.id !== poId);
                }
            );
        },

        formatNumber(value) {
            return parseFloat(value || 0).toLocaleString('id-ID');
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
        }
    }
}
</script>

<?= $this->endSection() ?>