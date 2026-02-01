<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="purchaseReturnManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Retur Pembelian</h2>
            <p class="mt-1 text-muted-foreground">Kelola return barang dari supplier dan persetujuan refund</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-3">
        <!-- Total Returns -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-primary/5 to-transparent p-5 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Retur</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="purchaseReturns.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">transaksi</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7-4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Menunggu Persetujuan</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="pendingApproval"></p>
                    <p class="mt-1 text-xs text-muted-foreground">retur</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Refund -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Refund</p>
                    <p class="mt-2 text-2xl font-bold text-foreground">Rp 0</p>
                    <p class="mt-1 text-xs text-muted-foreground">disetujui</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                    <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Bar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search & Filter -->
        <div class="flex gap-3 flex-1 flex-wrap">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-64">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari No. Retur atau supplier..." 
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
                    <option value="<?= $supplier['id_supplier'] ?>"><?= $supplier['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Right Side: Add Button -->
        <a 
            href="<?= base_url('transactions/purchase-returns/create') ?>"
            class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Buat Retur</span>
            <span class="sm:hidden">Buat</span>
        </a>
    </div>

    <!-- Purchase Returns Table -->
    <div class="rounded-xl border border-border/50 bg-surface shadow-sm overflow-hidden">
        <!-- Table Header -->
        <div class="border-b border-border/50 bg-muted/30 px-6 py-3">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                <span x-text="`${filteredReturns.length} retur ditemukan`"></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50 bg-background/50">
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">No. Retur</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Supplier</th>
                        <th class="h-12 px-6 py-3 text-center font-semibold text-foreground uppercase text-xs tracking-wide">Status</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Jumlah Refund</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Tanggal</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="retur in filteredReturns" :key="retur.id_retur_pembelian">
                        <tr class="border-b border-border/30 hover:bg-primary/3 transition-colors duration-150">
                            <!-- Return Number -->
                            <td class="px-6 py-4">
                                <a href="<?= base_url('transactions/purchase-returns/detail') ?>/:retur.id_retur_pembelian" class="font-semibold text-primary hover:text-primary-light transition" x-text="retur.nomor_retur"></a>
                            </td>

                            <!-- Supplier -->
                            <td class="px-6 py-4 font-medium text-foreground" x-text="retur.name"></td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 text-center">
                                <span 
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"
                                    :class="{
                                        'border-warning/30 bg-warning/10 text-warning': retur.status === 'Menunggu Persetujuan',
                                        'border-success/30 bg-success/10 text-success': retur.status === 'Disetujui',
                                        'border-destructive/30 bg-destructive/10 text-destructive': retur.status === 'Ditolak'
                                    }"
                                    x-text="retur.status">
                                </span>
                            </td>

                            <!-- Refund Amount -->
                            <td class="px-6 py-4 text-right font-bold text-foreground" x-text="'Rp ' + formatNumber(retur.total_refund)"></td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-right text-muted-foreground" x-text="formatDate(retur.tanggal_retur)"></td>

                            <!-- Action Buttons -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <a 
                                        :href="`<?= base_url('transactions/purchase-returns/detail') ?>/${retur.id_retur_pembelian}`"
                                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                        title="Lihat detail"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <template x-if="retur.status === 'Menunggu Persetujuan'">
                                        <a 
                                            :href="`<?= base_url('transactions/purchase-returns/approve') ?>/${retur.id_retur_pembelian}`"
                                            class="inline-flex items-center justify-center rounded-lg border border-success/30 bg-success/5 hover:bg-success/15 transition h-9 w-9 text-success"
                                            title="Setujui retur"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                    </template>
                                    <template x-if="retur.status === 'Menunggu Persetujuan'">
                                        <a 
                                            :href="`<?= base_url('transactions/purchase-returns/edit') ?>/${retur.id_retur_pembelian}`"
                                            class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                            title="Edit retur"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    </template>
                                    <template x-if="retur.status === 'Menunggu Persetujuan'">
                                        <button 
                                            @click="deleteReturn(retur.id_retur_pembelian)"
                                            class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                            title="Hapus retur"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredReturns.length === 0">
                        <td colspan="6" class="py-12 px-6 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="h-12 w-12 text-muted-foreground opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m7-4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-medium text-foreground">Tidak ada retur ditemukan</p>
                                <p class="text-xs text-muted-foreground">Coba ubah filter atau cari dengan kata kunci lain</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="border-t border-border/50 bg-muted/20 px-6 py-3 flex items-center justify-between text-xs text-muted-foreground">
            <span x-text="`Menampilkan ${filteredReturns.length} dari ${purchaseReturns.length} retur`"></span>
            <a href="<?= base_url('transactions/purchase-returns') ?>" class="text-primary hover:text-primary-light font-semibold transition">
                Refresh
            </a>
        </div>
    </div>
</div>

<script>
function purchaseReturnManager() {
    return {
        purchaseReturns: <?= json_encode($purchaseReturns ?? []) ?>,
        search: '',
        supplierFilter: 'all',

        get filteredReturns() {
            return this.purchaseReturns.filter(retur => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = (retur.nomor_retur && retur.nomor_retur.toLowerCase().includes(searchLower)) ||
                                    (retur.name && retur.name.toLowerCase().includes(searchLower));
                
                const matchesSupplier = this.supplierFilter === 'all' || 
                                       retur.id_supplier == this.supplierFilter;
                                      
                return matchesSearch && matchesSupplier;
            });
        },

        get pendingApproval() {
            return this.purchaseReturns.filter(r => r.status === 'Menunggu Persetujuan').length;
        },

        deleteReturn(returId) {
            if (confirm('Apakah Anda yakin ingin menghapus retur ini?')) {
                window.location.href = `<?= base_url('transactions/purchase-returns/delete') ?>/${returId}`;
            }
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