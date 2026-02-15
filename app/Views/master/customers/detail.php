<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="{ isEditDialogOpen: false, isEditSubmitting: false, editErrors: {}, editingCustomer: <?= json_encode($customer) ?> }">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
         <div>
             <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
                 <?= icon('Users', 'h-8 w-8 text-primary') ?>
                 Detail Pelanggan
             </h1>
             <p class="text-sm text-muted-foreground mt-1">Informasi lengkap dan riwayat pelanggan</p>
         </div>
         <div class="flex gap-3">
             <a href="<?= base_url('master/customers') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
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
    <!-- Left Column: Customer Details (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Customer Information Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('User', 'h-5 w-5 text-primary') ?>
                    Informasi Pelanggan
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Customer Name and Type -->
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Pelanggan</p>
                        <p class="text-2xl font-bold text-foreground mt-2"><?= $customer->name ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Tipe Pelanggan</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                <?= $customer->type === 'B2B' ? 'Bisnis' : 'Konsumen' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nomor Telepon</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $customer->phone ?? '-' ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Email</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $customer->email ?? '-' ?></p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Alamat</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $customer->address ?? '-' ?></p>
                    </div>
                </div>

                <!-- Credit Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Limit Kredit</p>
                        <p class="text-lg font-bold text-foreground mt-1"><?= format_currency($customer['credit_limit']) ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Piutang Saat Ini</p>
                        <p class="text-lg font-bold" :class="<?= $customer['receivable_balance'] > $customer['credit_limit'] ? "'text-destructive'" : "'text-foreground'" ?>">
                            <?= format_currency($customer['receivable_balance']) ?>
                        </p>
                    </div>
                </div>

                <!-- Credit Status -->
                <?php 
                $used_percent = ($customer['receivable_balance'] / $customer['credit_limit']) * 100;
                $status_class = $used_percent > 80 ? 'destructive' : ($used_percent > 50 ? 'warning' : 'success');
                ?>
                <div class="pt-4 border-t border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide mb-2">Status Kredit</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-foreground">Penggunaan Kredit</span>
                            <span class="font-semibold"><?= round($used_percent, 1) ?>%</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-<?= $status_class ?> h-2 rounded-full" style="width: <?= min($used_percent, 100) ?>%"></div>
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Sisa Kredit: <span class="font-semibold text-foreground"><?= format_currency($customer['credit_limit'] - $customer['receivable_balance']) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('History', 'h-5 w-5 text-primary') ?>
                    Transaksi Terbaru
                </h2>
            </div>

            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">No. Invoice</th>
                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Tanggal</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground w-28">Total</th>
                            <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php if (empty($recent_sales)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                Belum ada transaksi
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recent_sales as $sale): ?>
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-6 py-4 font-mono font-semibold text-foreground"><?= $sale['invoice_number'] ?></td>
                                <td class="px-6 py-4"><?= format_date($sale['created_at']) ?></td>
                                <td class="px-6 py-4 text-right font-semibold"><?= format_currency($sale['total_amount']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="'<?= match($sale['payment_status']) {
                                        'PAID' => 'bg-success/10 text-success',
                                        'PARTIAL' => 'bg-warning/10 text-warning',
                                        'UNPAID' => 'bg-destructive/10 text-destructive',
                                        default => 'bg-muted/10 text-muted'
                                    } ?>'">
                                        <?= match($sale['payment_status']) {
                                            'PAID' => 'Lunas',
                                            'PARTIAL' => 'Sebagian',
                                            'UNPAID' => 'Belum',
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
                <a href="<?= base_url('transactions/sales/credit?customer_id=' . $customer->id) ?>" class="w-full h-10 rounded-lg bg-primary text-white font-medium flex items-center justify-center hover:bg-primary/90 transition">
                    <?= icon('Plus', 'h-5 w-5 mr-2') ?>
                    Penjualan Kredit
                </a>

                <a href="<?= base_url('finance/payments/receivable?customer_id=' . $customer->id) ?>" class="w-full h-10 rounded-lg border border-primary/50 text-primary font-medium flex items-center justify-center hover:bg-primary/5 transition">
                    <?= icon('CreditCard', 'h-5 w-5 mr-2') ?>
                    Terima Pembayaran
                </a>

                <a href="<?= base_url('info/history/sales?customer_id=' . $customer->id) ?>" class="w-full h-10 rounded-lg border border-border/50 text-foreground font-medium flex items-center justify-center hover:bg-muted transition">
                    <?= icon('History', 'h-5 w-5 mr-2') ?>
                    Lihat Riwayat
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('BarChart3', 'h-5 w-5 text-primary') ?>
                    Statistik
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Total Penjualan</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= format_currency($customer['total_sales'] ?? 0) ?></p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Jumlah Transaksi</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $customer['transaction_count'] ?? 0 ?></p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Rata-rata Transaksi</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= format_currency($customer['average_transaction'] ?? 0) ?></p>
                </div>
            </div>
         </div>
     </div>
 </div>

    <!-- Edit Customer Modal -->
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
                <h2 class="text-xl font-bold text-foreground">Edit Pelanggan</h2>
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
                        ModalManager.success('Pelanggan berhasil diperbarui', () => {
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
            }" :action="`<?= base_url('master/customers') ?>/${editingCustomer.id}`" method="POST" class="p-6 space-y-4">
                <?= csrf_field() ?>
                
                <!-- Nama Pelanggan -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_name">Nama Pelanggan *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="edit_name" 
                        required 
                        x-model="editingCustomer.name"
                        :class="{'border-destructive': editErrors.name}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
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
                        x-model="editingCustomer.phone"
                        :class="{'border-destructive': editErrors.phone}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="editErrors.phone" class="text-destructive text-xs mt-1" x-text="editErrors.phone"></span>
                </div>

                <!-- Batas Kredit -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_credit_limit">Batas Kredit (Rp)</label>
                    <input 
                        type="number" 
                        name="credit_limit" 
                        id="edit_credit_limit" 
                        step="1"
                        min="0"
                        x-model.number="editingCustomer.credit_limit"
                        :class="{'border-destructive': editErrors.credit_limit}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="editErrors.credit_limit" class="text-destructive text-xs mt-1" x-text="editErrors.credit_limit"></span>
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
                        class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <?= icon('Edit', 'h-5 w-5 mr-2') ?>
                        <span x-show="isEditSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isEditSubmitting ? 'Menyimpan...' : 'Update Pelanggan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
 </div>

 <?= $this->endSection() ?>
