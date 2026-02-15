<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="{ isEditDialogOpen: false, isEditSubmitting: false, editErrors: {}, editingSupplier: <?= json_encode($supplier) ?> }">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
         <div>
             <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
                 <?= icon('Building2', 'h-8 w-8 text-primary') ?>
                 Detail Supplier
             </h1>
             <p class="text-sm text-muted-foreground mt-1">Informasi lengkap dan riwayat supplier</p>
         </div>
         <div class="flex gap-3">
             <a href="<?= base_url('master/suppliers') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
                 <?= icon('ArrowLeft', 'h-5 w-5') ?>
                 Kembali
             </a>
             <?php if (is_admin()): ?>
             <button @click="isEditDialogOpen = true" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                 <?= icon('Edit', 'h-5 w-5') ?>
                 Edit
             </button>
             <?php endif; ?>
         </div>
     </div>

<!-- Main Content Grid -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Supplier Details (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Supplier Information Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Building', 'h-5 w-5 text-primary') ?>
                    Informasi Supplier
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Supplier Name -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Supplier</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $supplier->name ?></p>
                </div>

                <!-- Contact Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nomor Telepon</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $supplier->phone ?? '-' ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Kode Supplier</p>
                        <p class="text-sm font-mono font-medium text-foreground mt-1"><?= $supplier->code ?? '-' ?></p>
                    </div>
                </div>

                <!-- Purchase Statistics -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Total Pembelian</p>
                        <p class="text-lg font-bold text-foreground mt-1"><?= format_currency($stats->total_purchases ?? 0) ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Rata-rata PO</p>
                        <p class="text-lg font-bold text-foreground mt-1"><?= format_currency($stats->avg_po ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Purchase Orders -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('ShoppingCart', 'h-5 w-5 text-primary') ?>
                    Purchase Order Terbaru
                </h2>
            </div>

            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">No. PO</th>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Tanggal</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground w-28">Total</th>
                            <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php if (empty($recentPOs)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                Belum ada purchase order
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recentPOs as $po): ?>
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-6 py-4 font-mono font-semibold text-foreground"><?= $po['nomor_po'] ?></td>
                                <td class="px-6 py-4"><?= format_date($po['tanggal_po']) ?></td>
                                <td class="px-6 py-4 text-right font-semibold"><?= format_currency($po['total_amount']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="'<?= match($po['status']) {
                                        'Diterima Semua' => 'bg-success/10 text-success',
                                        'Sebagian' => 'bg-warning/10 text-warning',
                                        'Dibatalkan' => 'bg-destructive/10 text-destructive',
                                        'Dipesan' => 'bg-info/10 text-info',
                                        default => 'bg-muted/10 text-muted'
                                    } ?>'">
                                        <?= match($po['status']) {
                                            'Diterima Semua' => 'Diterima',
                                            'Sebagian' => 'Sebagian',
                                            'Dibatalkan' => 'Batal',
                                            'Dipesan' => 'Dipesan',
                                            default => 'Unknown'
                                        } ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Quick Actions & Summary (1/3) -->
    <div class="space-y-6">
        
        <!-- Quick Actions -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Zap', 'h-5 w-5 text-primary') ?>
                    Aksi Cepat
                </h2>
            </div>

            <div class="p-6 space-y-3">
                <a href="<?= base_url('transactions/purchases/create?supplier_id=' . $supplier->id) ?>" class="w-full h-10 rounded-lg bg-primary text-white font-medium flex items-center justify-center hover:bg-primary/90 transition">
                    <?= icon('Plus', 'h-5 w-5 mr-2') ?>
                    Pembelian Baru
                </a>

                <a href="<?= base_url('finance/payments/payable?supplier_id=' . $supplier->id) ?>" class="w-full h-10 rounded-lg border border-primary/50 text-primary font-medium flex items-center justify-center hover:bg-primary/5 transition">
                    <?= icon('CreditCard', 'h-5 w-5 mr-2') ?>
                    Bayar Tagihan
                </a>

                <a href="<?= base_url('info/history/purchases?supplier_id=' . $supplier->id) ?>" class="w-full h-10 rounded-lg border border-border/50 text-foreground font-medium flex items-center justify-center hover:bg-muted transition">
                    <?= icon('History', 'h-5 w-5 mr-2') ?>
                    Lihat Riwayat
                </a>
            </div>
        </div>

        <!-- Debt Status -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('AlertCircle', 'h-5 w-5 text-primary') ?>
                    Status Hutang
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="p-4 rounded-lg bg-destructive/10 border border-destructive/20">
                    <p class="text-xs text-destructive font-semibold uppercase">Total Hutang</p>
                    <p class="text-2xl font-bold text-destructive mt-2"><?= format_currency($totalDebt) ?></p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Jumlah PO Belum Bayar</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $pendingCount ?? 0 ?></p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Total PO</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $stats->total_pos ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>
 </div>

    <!-- Edit Supplier Modal -->
    <div 
        x-show="isEditDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-md rounded-xl border border-border/50 bg-surface shadow-xl"
            @click.away="isEditDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <!-- Modal Header -->
            <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-foreground">Edit Supplier</h2>
                <button 
                    @click="isEditDialogOpen = false"
                    class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                >
                    <?= icon('X', 'h-5 w-5') ?>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form @submit.prevent="async (e) => {
                e.preventDefault();
                const form = e.target;
                editErrors = {};
                isEditSubmitting = true;
                
                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    if (response.ok || response.status === 200) {
                        ModalManager.success('Supplier berhasil diperbarui', () => {
                            isEditDialogOpen = false;
                            window.location.reload();
                        });
                    } else if (response.status === 422) {
                        const data = await response.json();
                        if (data.errors) editErrors = data.errors;
                        ModalManager.error(data.message || 'Terjadi kesalahan validasi.');
                    } else {
                        const data = await response.json();
                        ModalManager.error(data.message || 'Gagal memperbarui data.');
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    ModalManager.error('Terjadi kesalahan: ' + error.message);
                } finally {
                    isEditSubmitting = false;
                }
            }" :action="`<?= base_url('master/suppliers') ?>/${editingSupplier.id}`" method="POST" class="p-6 space-y-4">
                <?= csrf_field() ?>
                
                <!-- Nama Supplier -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_name">Nama Supplier *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="edit_name" 
                        required 
                        x-model="editingSupplier.name"
                        :class="{'border-destructive': editErrors.name}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all"
                    >
                    <span x-show="editErrors.name" class="text-destructive text-xs mt-1" x-text="editErrors.name"></span>
                </div>

                <!-- Telepon -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_phone">No. Telepon</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="edit_phone" 
                        x-model="editingSupplier.phone"
                        :class="{'border-destructive': editErrors.phone}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all"
                    >
                    <span x-show="editErrors.phone" class="text-destructive text-xs mt-1" x-text="editErrors.phone"></span>
                </div>

                <!-- Alamat -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_address">Alamat Lengkap</label>
                    <textarea 
                        name="address" 
                        id="edit_address" 
                        rows="2"
                        x-model="editingSupplier.address"
                        :class="{'border-destructive': editErrors.address}"
                        class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all resize-none"
                    ></textarea>
                    <span x-show="editErrors.address" class="text-destructive text-xs mt-1" x-text="editErrors.address"></span>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-border/50">
                    <button 
                        type="button" 
                        @click="isEditDialogOpen = false" 
                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-6 text-sm font-semibold"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isEditSubmitting"
                        class="inline-flex items-center justify-center rounded-lg bg-secondary text-white hover:bg-blue-600 transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <?= icon('Edit', 'h-5 w-5 mr-2') ?>
                        <span x-show="isEditSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isEditSubmitting ? 'Menyimpan...' : 'Update Supplier'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
 </div>

 <?= $this->endSection() ?>
