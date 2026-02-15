<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<script>
function customerManager() {
     return {
         customers: <?= json_encode($customers ?? []) ?>,
         search: '',
         isDialogOpen: false,
         isEditDialogOpen: false,
         isSubmitting: false,
         isEditSubmitting: false,
         errors: {},
         editErrors: {},
         editingCustomer: {},

        get filteredCustomers() {
            return this.customers.filter(cust => {
                const searchLower = this.search.toLowerCase();
                return (cust.name && cust.name.toLowerCase().includes(searchLower)) ||
                       (cust.code && cust.code.toLowerCase().includes(searchLower));
            });
        },

        get customersWithPiutang() {
            return this.customers.filter(c => parseFloat(c.receivable_balance || 0) > 0).length;
        },

         get totalPiutang() {
             return this.customers.reduce((sum, c) => sum + (parseFloat(c.receivable_balance) || 0), 0);
         },

         openEditModal(customer) {
             this.editingCustomer = JSON.parse(JSON.stringify(customer));
             this.editErrors = {};
             this.isEditDialogOpen = true;
         },

        async submitForm(event) {
            event.preventDefault();
            const form = event.target;
            
            this.errors = {};
            this.isSubmitting = true;

            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok || response.status === 201) {
                    ModalManager.success('Pelanggan berhasil ditambahkan', () => {
                        this.isDialogOpen = false;
                        form.reset();
                        this.errors = {};
                        window.location.reload();
                    });
                } else if (response.status === 422) {
                    const data = await response.json();
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    ModalManager.error(data.message || 'Terjadi kesalahan validasi.');
                } else {
                    const data = await response.json();
                    ModalManager.error(data.message || 'Gagal menyimpan data.');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                ModalManager.error('Terjadi kesalahan: ' + error.message);
            } finally {
                this.isSubmitting = false;
            }
         },

         async submitEditForm(event) {
             event.preventDefault();
             const form = event.target;
             
             this.editErrors = {};
             this.isEditSubmitting = true;

             try {
                 const formData = new FormData(form);
                 
                 const response = await fetch(form.action, {
                     method: 'POST',
                     body: formData,
                     headers: {
                         'X-Requested-With': 'XMLHttpRequest'
                     }
                 });

                 if (response.ok || response.status === 200) {
                     ModalManager.success('Pelanggan berhasil diperbarui', () => {
                         this.isEditDialogOpen = false;
                         this.editErrors = {};
                         this.editingCustomer = {};
                         window.location.reload();
                     });
                 } else if (response.status === 422) {
                     const data = await response.json();
                     if (data.errors) {
                         this.editErrors = data.errors;
                     }
                     ModalManager.error(data.message || 'Terjadi kesalahan validasi.');
                 } else {
                     const data = await response.json();
                     ModalManager.error(data.message || 'Gagal memperbarui data.');
                 }
            } catch (error) {
                console.error('Form submission error:', error);
                ModalManager.error('Terjadi kesalahan: ' + error.message);
            } finally {
                this.isEditSubmitting = false;
            }
         },

         deleteCustomer(customerId) {
             const customer = this.customers.find(c => c.id === customerId);
             const customerName = customer ? customer.name : 'pelanggan ini';
             ModalManager.submitDelete(
                 `<?= base_url('master/customers') ?>/${customerId}`,
                 customerName,
                 () => {
                     this.customers = this.customers.filter(c => c.id !== customerId);
                 }
             );
         },

        exportData() {
            try {
                window.location.href = `<?= base_url('master/customers/export-pdf') ?>`;
            } catch (error) {
                console.error('Export failed:', error);
                alert('Gagal mengekspor data.');
            }
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number || 0);
        }
    }
}
</script>

<div x-data="customerManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Pelanggan</h2>
            <p class="mt-1 text-muted-foreground">Kelola daftar pelanggan dan kredit mereka</p>
        </div>
    </div>

    <!-- Summary Cards - Compact Grid (Product Style) -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-3">
        <!-- Total Customers -->
         <div class="rounded-xl border border-border/50 bg-gradient-to-br from-primary/5 to-transparent p-6 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Pelanggan</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="customers.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">aktif</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 flex-shrink-0">
                    <?= icon('Users', 'h-5 w-5 text-primary') ?>
                </div>
            </div>
        </div>

        <!-- Customers with Piutang -->
         <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-6 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Dengan Piutang</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="customersWithPiutang"></p>
                    <p class="mt-1 text-xs text-muted-foreground">menunggak</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10 flex-shrink-0">
                    <?= icon('DollarSign', 'h-5 w-5 text-warning') ?>
                </div>
            </div>
        </div>

        <!-- Total Piutang -->
         <div class="rounded-xl border border-border/50 bg-gradient-to-br from-destructive/5 to-transparent p-6 hover:border-destructive/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Piutang</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="formatRupiah(totalPiutang)"></p>
                    <p class="mt-1 text-xs text-muted-foreground">belum dibayar</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-destructive/10 flex-shrink-0">
                    <?= icon('DollarSign', 'h-5 w-5 text-destructive') ?>
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
             <form @submit.prevent="submitEditForm" :action="`<?= base_url('master/customers') ?>/${editingCustomer.id}`" method="POST" class="p-6 space-y-4">
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
                          class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
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
                          class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
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
                          class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                      >
                     <span x-show="editErrors.credit_limit" class="text-destructive text-xs mt-1" x-text="editErrors.credit_limit"></span>
                 </div>

                 <!-- Modal Footer -->
                 <div class="flex justify-end gap-3 pt-4 border-t border-border/50">
                      <button 
                          type="button" 
                          @click="isEditDialogOpen = false" 
                          class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-11 px-6 text-sm font-semibold"
                      >
                          Batal
                      </button>
                      <button 
                          type="submit" 
                          :disabled="isEditSubmitting"
                          class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-11 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
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

    <!-- Control Bar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search -->
        <div class="flex-1 min-w-0">
            <div class="relative max-w-md">
                <?= icon('Search', 'absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground') ?>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari nama atau kode pelanggan..." 
                     class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 pl-10 transition-all"
                >
            </div>
        </div>

        <!-- Right Side: Action Buttons -->
        <div class="flex gap-2 flex-shrink-0">
            <!-- Export Button -->
            <button 
                @click="exportData()"
                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-11 px-6 gap-2 text-sm font-medium"
                title="Export data ke PDF"
            >
                <?= icon('Download', 'h-4 w-4') ?>
                <span class="hidden sm:inline">Export</span>
            </button>

            <!-- Add Button -->
            <button 
                @click="isDialogOpen = true"
                class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-11 px-6 gap-2 text-sm font-semibold shadow-sm hover:shadow-md whitespace-nowrap"
            >
                <?= icon('Plus', 'h-5 w-5') ?>
                <span class="hidden sm:inline">Tambah Pelanggan</span>
                <span class="sm:hidden">Tambah</span>
            </button>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="rounded-xl border border-border/50 bg-surface shadow-sm overflow-hidden">
        <!-- Table Header Info -->
        <div class="border-b border-border/50 bg-muted/30 px-6 py-3">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                <span x-text="`${filteredCustomers.length} pelanggan ditemukan`"></span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50 bg-background/50">
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Nama</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Kode</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Telepon</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Batas Kredit</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Piutang</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="customer in filteredCustomers" :key="customer.id">
                        <tr class="border-b border-border/30 hover:bg-primary/3 transition-colors duration-150">
                            <!-- Nama -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <?= icon('Users', 'h-5 w-5 text-muted-foreground') ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-foreground truncate" x-text="customer.name"></p>
                                        <p class="text-xs text-muted-foreground mt-0.5" x-text="`${customer.phone || '(tidak ada)'}`"></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Kode -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border border-border/30 bg-muted/30 px-2.5 py-1 text-xs font-semibold text-foreground font-mono" x-text="customer.code || '-'"></span>
                            </td>

                            <!-- Phone -->
                            <td class="px-6 py-4 text-foreground text-sm" x-text="customer.phone || '-'"></td>

                            <!-- Credit Limit -->
                            <td class="px-6 py-4 text-right font-semibold text-foreground" x-text="formatRupiah(customer.credit_limit)"></td>

                            <!-- Receivable Balance -->
                            <td class="px-6 py-4 text-right">
                                <span 
                                    class="font-semibold"
                                    :class="parseFloat(customer.receivable_balance) > 0 ? 'text-warning' : 'text-success'"
                                    x-text="formatRupiah(customer.receivable_balance)"
                                ></span>
                            </td>

                            <!-- Action -->
                             <td class="px-6 py-4 text-right">
                                 <div class="flex justify-end gap-1.5">
                                     <button 
                                         @click="openEditModal(customer)"
                                         class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                         title="Edit pelanggan"
                                     >
                                         <?= icon('Edit', 'h-4 w-4') ?>
                                     </button>
                                    <button 
                                        @click="deleteCustomer(customer.id)"
                                        class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                        title="Hapus pelanggan"
                                    >
                                         <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredCustomers.length === 0">
                        <td colspan="6" class="py-12 px-6 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <?= icon('Users', 'h-12 w-12 text-muted-foreground opacity-30') ?>
                                <p class="text-sm font-medium text-foreground">Tidak ada pelanggan ditemukan</p>
                                <p class="text-xs text-muted-foreground">Coba ubah pencarian atau tambahkan pelanggan baru</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div 
        x-show="isDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-md rounded-xl border border-border/50 bg-surface shadow-xl"
            @click.away="isDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <!-- Modal Header -->
            <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-foreground">Tambah Pelanggan Baru</h2>
                <button 
                    @click="isDialogOpen = false"
                    class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                >
                    <?= icon('X', 'h-5 w-5') ?>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form @submit.prevent="submitForm" action="<?= base_url('master/customers/store') ?>" method="POST" class="p-6 space-y-4">
                <?= csrf_field() ?>
                
                <!-- Nama Pelanggan -->
                <div class="space-y-2">
                     <label class="text-sm font-semibold text-foreground" for="name">Nama Pelanggan *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        placeholder="Contoh: PT Maju Jaya"
                        :class="{'border-destructive': errors.name}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.name" class="text-destructive text-xs mt-1" x-text="errors.name"></span>
                </div>

                <!-- Telepon -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="phone">No. Telepon</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        placeholder="Contoh: 081234567890"
                        :class="{'border-destructive': errors.phone}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.phone" class="text-destructive text-xs mt-1" x-text="errors.phone"></span>
                </div>

                <!-- Batas Kredit -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="credit_limit">Batas Kredit (Rp)</label>
                    <input 
                        type="number" 
                        name="credit_limit" 
                        id="credit_limit" 
                        step="1"
                        min="0"
                        placeholder="Contoh: 50000000"
                        :class="{'border-destructive': errors.credit_limit}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.credit_limit" class="text-destructive text-xs mt-1" x-text="errors.credit_limit"></span>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-border/50">
                    <button 
                        type="button" 
                        @click="isDialogOpen = false" 
                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-11 px-6 text-sm font-semibold"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-11 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <?= icon('Plus', 'h-5 w-5 mr-2') ?>
                        <span x-show="isSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Pelanggan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
