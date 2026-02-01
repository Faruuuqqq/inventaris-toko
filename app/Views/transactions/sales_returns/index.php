<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header with Action Button -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('RotateCcw', 'h-8 w-8 text-destructive') ?>
            <?= $title ?? 'Retur Penjualan' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Kelola retur produk dari customer</p>
    </div>
    <a href="<?= base_url('transactions/sales-returns/create') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition whitespace-nowrap">
        <?= icon('Plus', 'h-5 w-5') ?>
        Tambah Retur
    </a>
</div>

<!-- Summary Cards -->
<div class="grid gap-4 md:grid-cols-3 mb-6">
    <div class="rounded-lg border bg-surface p-4 flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-primary/15 flex items-center justify-center">
            <?= icon('RotateCcw', 'h-6 w-6 text-primary') ?>
        </div>
        <div>
            <p class="text-xs text-muted-foreground">Total Retur</p>
            <p class="text-2xl font-bold text-foreground" x-text="returns.length">0</p>
        </div>
    </div>
    
    <div class="rounded-lg border bg-surface p-4 flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-warning/15 flex items-center justify-center">
            <?= icon('Clock', 'h-6 w-6 text-warning') ?>
        </div>
        <div>
            <p class="text-xs text-muted-foreground">Menunggu Persetujuan</p>
            <p class="text-2xl font-bold text-warning" x-text="returns.filter(r => r.status === 'Menunggu Persetujuan').length">0</p>
        </div>
    </div>

    <div class="rounded-lg border bg-surface p-4 flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-destructive/15 flex items-center justify-center">
            <?= icon('TrendingDown', 'h-6 w-6 text-destructive') ?>
        </div>
        <div>
            <p class="text-xs text-muted-foreground">Total Refund</p>
            <p class="text-2xl font-bold text-destructive" x-text="'Rp ' + formatNumber(getTotalRefund())">Rp 0</p>
        </div>
    </div>
</div>

<!-- Control Bar with Search and Filters -->
<div class="rounded-lg border bg-surface p-4 mb-6" x-data="returnsList()">
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <div class="flex-1 relative">
            <input type="text" 
                   x-model="search" 
                   placeholder="Cari nomor retur, customer..." 
                   class="w-full h-10 rounded-lg border border-border bg-background px-4 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            <?= icon('Search', 'h-5 w-5 text-muted-foreground absolute left-3 top-2.5') ?>
        </div>

        <select x-model="filterStatus" class="h-10 px-4 rounded-lg border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 whitespace-nowrap">
            <option value="">Semua Status</option>
            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
            <option value="Disetujui">Disetujui</option>
            <option value="Ditolak">Ditolak</option>
        </select>

        <select x-model="filterCustomer" class="h-10 px-4 rounded-lg border border-border bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 min-w-[200px]">
            <option value="">Semua Customer</option>
            <template x-for="customer in customers" :key="customer.id">
                <option :value="customer.id" x-text="customer.name"></option>
            </template>
        </select>

        <button @click="resetFilters()" class="h-10 px-4 rounded-lg border border-border/50 bg-background text-sm font-medium text-foreground hover:bg-muted transition">
            <?= icon('RotateCcw', 'h-4 w-4 mr-2 inline') ?>
            Reset
        </button>
    </div>
</div>

<!-- Returns Table -->
<div class="rounded-lg border bg-surface overflow-hidden">
    <div class="relative w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/50 border-b border-border/50">
                <tr>
                    <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">No. Retur</th>
                    <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Tanggal</th>
                    <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Customer</th>
                    <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground">Status</th>
                    <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground">Jumlah Refund</th>
                    <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                <template x-if="filteredReturns().length === 0">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                            <div class="flex flex-col items-center gap-2">
                                <?= icon('Package', 'h-8 w-8 opacity-50') ?>
                                <p>Tidak ada retur ditemukan</p>
                            </div>
                        </td>
                    </tr>
                </template>

                <template x-for="retur in filteredReturns()" :key="retur.id">
                    <tr class="hover:bg-muted/50 transition">
                        <!-- Return Number -->
                        <td class="px-6 py-4">
                            <a :href="'<?= base_url('transactions/sales-returns/detail/') ?>' + retur.id" 
                               class="font-semibold text-primary hover:underline">
                                <span x-text="retur.number"></span>
                            </a>
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            <span x-text="formatDate(retur.date)"></span>
                        </td>

                        <!-- Customer -->
                        <td class="px-6 py-4">
                            <span class="font-medium text-foreground" x-text="retur.customer_name"></span>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center">
                            <span :class="getStatusClass(retur.status)" 
                                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                  x-text="retur.status"></span>
                        </td>

                        <!-- Refund Amount -->
                        <td class="px-6 py-4 text-right font-semibold text-destructive">
                            <span x-text="'Rp ' + formatNumber(retur.refund_amount)"></span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a :href="'<?= base_url('transactions/sales-returns/detail/') ?>' + retur.id" 
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border/50 text-muted-foreground hover:text-primary hover:border-primary/50 transition"
                                   title="Lihat Detail">
                                    <?= icon('Eye', 'h-4 w-4') ?>
                                </a>

                                <template x-if="retur.status === 'Menunggu Persetujuan'">
                                    <a :href="'<?= base_url('transactions/sales-returns/approve/') ?>' + retur.id" 
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-success/50 text-success hover:bg-success/10 transition"
                                       title="Setujui">
                                        <?= icon('Check', 'h-4 w-4') ?>
                                    </a>

                                    <a :href="'<?= base_url('transactions/sales-returns/edit/') ?>' + retur.id" 
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-warning/50 text-warning hover:bg-warning/10 transition"
                                       title="Edit">
                                        <?= icon('Edit', 'h-4 w-4') ?>
                                    </a>

                                    <button @click="deleteReturn(retur.id, retur.number)" 
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-destructive/50 text-destructive hover:bg-destructive/10 transition"
                                            title="Hapus">
                                        <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div x-show="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-transition>
    <div class="bg-surface rounded-lg border border-border p-6 max-w-sm w-full mx-4 shadow-lg">
        <h3 class="text-lg font-bold text-foreground mb-2">Hapus Retur?</h3>
        <p class="text-sm text-muted-foreground mb-6">
            Apakah Anda yakin ingin menghapus retur <strong x-text="deleteReturNumber"></strong>? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex gap-3 justify-end">
            <button @click="showDeleteModal = false" class="h-10 px-4 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition">
                Batal
            </button>
            <form :action="'<?= base_url('transactions/sales-returns/delete/') ?>' + deleteReturId" method="POST" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="h-10 px-4 rounded-lg bg-destructive text-white font-medium hover:bg-destructive/90 transition">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function returnsList() {
        return {
            search: '',
            filterStatus: '',
            filterCustomer: '',
            showDeleteModal: false,
            deleteReturId: null,
            deleteReturNumber: '',
            returns: <?= json_encode($salesReturns ?? []) ?>,
            customers: <?= json_encode($customers ?? []) ?>,

            filteredReturns() {
                return this.returns.filter(retur => {
                    const matchSearch = this.search === '' || 
                        retur.number.toLowerCase().includes(this.search.toLowerCase()) ||
                        retur.customer_name.toLowerCase().includes(this.search.toLowerCase());
                    
                    const matchStatus = this.filterStatus === '' || retur.status === this.filterStatus;
                    const matchCustomer = this.filterCustomer === '' || retur.customer_id == this.filterCustomer;
                    
                    return matchSearch && matchStatus && matchCustomer;
                });
            },

            getTotalRefund() {
                return this.returns.reduce((sum, retur) => sum + (parseFloat(retur.refund_amount) || 0), 0);
            },

            getStatusClass(status) {
                const classes = {
                    'Menunggu Persetujuan': 'bg-warning/15 text-warning',
                    'Disetujui': 'bg-success/15 text-success',
                    'Ditolak': 'bg-destructive/15 text-destructive'
                };
                return classes[status] || 'bg-muted/50 text-muted-foreground';
            },

            resetFilters() {
                this.search = '';
                this.filterStatus = '';
                this.filterCustomer = '';
            },

            deleteReturn(id, number) {
                this.deleteReturId = id;
                this.deleteReturNumber = number;
                this.showDeleteModal = true;
            },

            formatDate(date) {
                return new Date(date).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }
        }
    }
</script>

<?= $this->endSection() ?>
