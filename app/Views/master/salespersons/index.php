<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>


<script>
function salespersonManager() {
     return {
         salespersons: <?= json_encode($salespersons ?? []) ?>,
         search: '',
         isDialogOpen: false,
         isEditDialogOpen: false,
         isSubmitting: false,
         isEditSubmitting: false,
         errors: {},
         editErrors: {},
         editingSalesperson: {},

          get filteredSalespersons() {
              return this.salespersons.filter(s => {
                  const searchLower = this.search.toLowerCase();
                  return (s.name && s.name.toLowerCase().includes(searchLower)) ||
                         (s.phone && s.phone.toLowerCase().includes(searchLower));
              });
          },

          get activeCount() {
              return this.salespersons.filter(s => s.is_active).length;
          },

          get totalSales() {
              return 0; // Placeholder - actual total sales would come from separate data/calculation
          },

         openEditModal(salesperson) {
             this.editingSalesperson = JSON.parse(JSON.stringify(salesperson));
             this.editErrors = {};
             this.isEditDialogOpen = true;
         },

        async submitForm(event) {
            event.preventDefault();
            const form = event.target;
            
            // Clear previous errors
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
                    // Success
                    ModalManager.success('Data salesperson berhasil ditambahkan', () => {
                        this.isDialogOpen = false;
                        form.reset();
                        this.errors = {};
                        // Reload page to refresh salesperson list
                        window.location.reload();
                    });
                } else if (response.status === 422) {
                    // Validation error
                    const data = await response.json();
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    ModalManager.error(data.message || 'Terjadi kesalahan validasi. Silakan periksa kembali data Anda.');
                } else {
                    // Other error
                    const data = await response.json();
                    ModalManager.error(data.message || 'Gagal menyimpan data. Silakan coba lagi.');
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
                     ModalManager.success('Data salesperson berhasil diperbarui', () => {
                         this.isEditDialogOpen = false;
                         this.editErrors = {};
                         this.editingSalesperson = {};
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

         deleteSalesperson(salespersonId) {
             const salesperson = this.salespersons.find(s => s.id === salespersonId);
             const salespersonName = salesperson ? salesperson.name : 'salesperson ini';
             ModalManager.submitDelete(
                 `<?= base_url('master/salespersons') ?>/${salespersonId}`,
                 salespersonName,
                 () => {
                     this.salespersons = this.salespersons.filter(s => s.id !== salespersonId);
                 }
             );
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

<div x-data="salespersonManager()">
    <!-- Page Header with Expanded Summary Cards -->
    <div class="mb-8 flex flex-col gap-6">
        <!-- Title & Description -->
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Salesperson</h2>
            <p class="mt-1 text-muted-foreground">Kelola data tim penjual dan performa mereka</p>
        </div>

        <!-- Summary Cards - Expanded Horizontal Layout -->
        <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
             <!-- Total Salespersons -->
              <div class="rounded-xl border border-border/50 bg-gradient-to-br from-purple/5 to-transparent p-6 hover:border-purple/30 transition-colors">
                 <div class="flex items-start justify-between">
                     <div>
                         <p class="text-sm font-medium text-muted-foreground">Total Salesperson</p>
                         <p class="mt-2 text-2xl font-bold text-foreground" x-text="salespersons.length"></p>
                         <p class="mt-1 text-xs text-muted-foreground">salesperson terdaftar</p>
                     </div>
                     <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple/10 flex-shrink-0">
                         <?= icon('Zap', 'h-5 w-5 text-purple') ?>
                     </div>
                 </div>
             </div>

             <!-- Active Salespersons -->
              <div class="rounded-xl border border-border/50 bg-gradient-to-br from-green/5 to-transparent p-6 hover:border-green/30 transition-colors">
                 <div class="flex items-start justify-between">
                     <div>
                         <p class="text-sm font-medium text-muted-foreground">Status Aktif</p>
                          <p class="mt-2 text-2xl font-bold text-foreground" x-text="activeCount"></p>
                         <p class="mt-1 text-xs text-muted-foreground">salesperson aktif</p>
                     </div>
                      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green/10 flex-shrink-0">
                          <?= icon('CheckCircle', 'h-5 w-5 text-green') ?>
                      </div>
                 </div>
             </div>

             <!-- Total Sales -->
              <div class="rounded-xl border border-border/50 bg-gradient-to-br from-blue/5 to-transparent p-6 hover:border-blue/30 transition-colors">
                 <div class="flex items-start justify-between">
                     <div>
                         <p class="text-sm font-medium text-muted-foreground">Total Penjualan</p>
                          <p class="mt-2 text-2xl font-bold text-foreground" x-text="formatRupiah(totalSales)"></p>
                         <p class="mt-1 text-xs text-muted-foreground">total penjualan</p>
                     </div>
                      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue/10 flex-shrink-0">
                          <?= icon('DollarSign', 'h-5 w-5 text-blue') ?>
                      </div>
                 </div>
             </div>
        </div>
    </div>

     <!-- Edit Salesperson Modal -->
     <div 
         x-show="isEditDialogOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition.opacity
         style="display: none;"
     >
         <div 
             class="w-full max-w-2xl rounded-xl border border-border/50 bg-surface shadow-xl"
             @click.away="isEditDialogOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
         >
             <!-- Modal Header -->
             <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                 <h2 class="text-xl font-bold text-foreground">Edit Salesperson</h2>
                 <button 
                     @click="isEditDialogOpen = false"
                     class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                 >
                      <?= icon('X', 'h-5 w-5') ?>
                 </button>
             </div>
             
             <!-- Modal Body -->
             <form @submit.prevent="submitEditForm" :action="`<?= base_url('master/salespersons') ?>/${editingSalesperson.id}`" method="POST" class="p-6 space-y-5">
                 <?= csrf_field() ?>
                 
                 <!-- Row 1: Name & Phone -->
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                     <div class="space-y-2">
                         <label class="text-sm font-semibold text-foreground" for="edit_name">Nama Salesperson *</label>
                         <input 
                             type="text" 
                             name="name" 
                             id="edit_name" 
                             required 
                             x-model="editingSalesperson.name"
                             :class="{'border-destructive': editErrors.name}"
                             class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                         >
                         <span x-show="editErrors.name" class="text-destructive text-xs mt-1" x-text="editErrors.name"></span>
                     </div>
                     <div class="space-y-2">
                         <label class="text-sm font-semibold text-foreground" for="edit_phone">No. Telepon</label>
                         <input 
                             type="text" 
                             name="phone" 
                             id="edit_phone" 
                             x-model="editingSalesperson.phone"
                             :class="{'border-destructive': editErrors.phone}"
                             class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                         >
                         <span x-show="editErrors.phone" class="text-destructive text-xs mt-1" x-text="editErrors.phone"></span>
                     </div>
                 </div>

                 <!-- Row 2: Email -->
                 <div class="space-y-2">
                     <label class="text-sm font-semibold text-foreground" for="edit_email">Email</label>
                     <input 
                         type="email" 
                         name="email" 
                         id="edit_email" 
                         x-model="editingSalesperson.email"
                         :class="{'border-destructive': editErrors.email}"
                         class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                     >
                     <span x-show="editErrors.email" class="text-destructive text-xs mt-1" x-text="editErrors.email"></span>
                 </div>

                 <!-- Row 3: Address -->
                 <div class="space-y-2">
                     <label class="text-sm font-semibold text-foreground" for="edit_address">Alamat</label>
                     <textarea 
                         name="address" 
                         id="edit_address" 
                         rows="3"
                         x-model="editingSalesperson.address"
                         :class="{'border-destructive': editErrors.address}"
                         class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all resize-none"
                     ></textarea>
                     <span x-show="editErrors.address" class="text-destructive text-xs mt-1" x-text="editErrors.address"></span>
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
                         class="inline-flex items-center justify-center rounded-lg bg-purple text-white hover:bg-purple-light transition h-11 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                     >
                         <?= icon('Edit', 'h-5 w-5 mr-2') ?>
                         <span x-show="isEditSubmitting" class="inline-flex items-center gap-2 mr-2">
                             <span class="animate-spin">⚙️</span>
                         </span>
                         <span x-text="isEditSubmitting ? 'Menyimpan...' : 'Update Salesperson'"></span>
                     </button>
                 </div>
             </form>
         </div>
     </div>

    <!-- Control Bar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search -->
        <div class="flex gap-3 flex-wrap items-center flex-1">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-64">
                 <?= icon('Search', 'absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground') ?>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari nama atau nomor telepon salesperson..." 
                    class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 pl-10 transition-all"
                >
            </div>
        </div>

        <!-- Right Side: Add Button -->
        <button 
            @click="isDialogOpen = true"
            class="inline-flex items-center justify-center rounded-lg bg-purple text-white hover:bg-purple-light transition h-11 px-6 gap-2 text-sm font-semibold shadow-sm hover:shadow-md whitespace-nowrap"
        >
             <?= icon('Plus', 'h-5 w-5') ?>
            <span class="hidden sm:inline">Tambah Salesperson</span>
            <span class="sm:hidden">Tambah</span>
        </button>
    </div>

    <!-- Data Table -->
    <div class="rounded-xl border border-border/50 bg-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border/50 bg-muted/30">
                        <th class="px-6 py-3 text-left font-semibold text-foreground">Nama</th>
                        <th class="px-6 py-3 text-left font-semibold text-foreground">Telepon</th>
                        <th class="px-6 py-3 text-left font-semibold text-foreground">Email</th>
                        <th class="px-6 py-3 text-center font-semibold text-foreground">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-foreground">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="salesperson in filteredSalespersons" :key="salesperson.id">
                        <tr class="border-b border-border/50 hover:bg-muted/20 transition">
                            <td class="px-6 py-4 font-semibold text-foreground" x-text="salesperson.name"></td>
                            <td class="px-6 py-4 text-muted-foreground" x-text="salesperson.phone || '-'"></td>
                            <td class="px-6 py-4 text-muted-foreground" x-text="salesperson.email || '-'"></td>
                            <td class="px-6 py-4 text-center">
                                <span 
                                    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold"
                                    :class="salesperson.is_active 
                                        ? 'border-green/30 bg-green/15 text-green' 
                                        : 'border-muted-foreground/30 bg-muted/15 text-muted-foreground'">
                                    <span class="inline-block h-2 w-2 rounded-full" :class="salesperson.is_active ? 'bg-green' : 'bg-muted-foreground'"></span>
                                    <span x-text="salesperson.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                 <div class="flex gap-2 justify-end">
                                     <!-- Edit Button -->
                                     <button 
                                         @click="openEditModal(salesperson)"
                                         class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                         title="Edit salesperson"
                                     >
                                         <?= icon('Edit', 'h-4 w-4') ?>
                                     </button>
                                    <!-- Delete Button -->
                                    <button 
                                        @click="deleteSalesperson(salesperson.id)"
                                        class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                        title="Hapus salesperson"
                                    >
                                         <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div x-show="filteredSalespersons.length === 0" class="p-12 text-center">
             <?= icon('Zap', 'h-16 w-16 mx-auto text-muted-foreground opacity-30 mb-4') ?>
            <p class="text-lg font-semibold text-foreground mt-2">Tidak ada salesperson ditemukan</p>
            <p class="text-sm text-muted-foreground mt-1">Coba ubah filter atau cari dengan kata kunci lain</p>
                <button 
                @click="isDialogOpen = true"
                class="mt-6 inline-flex items-center justify-center rounded-lg bg-purple text-white hover:bg-purple-light transition h-11 px-6 gap-2 text-sm font-semibold">
                <?= icon('Plus', 'h-5 w-5') ?>
                Tambah Salesperson Pertama
            </button>
        </div>
    </div>

    <!-- Modal (Dialog) - Enhanced -->
    <div 
        x-show="isDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-2xl rounded-xl border border-border/50 bg-surface shadow-xl"
            @click.away="isDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <!-- Modal Header -->
            <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-foreground">Tambah Salesperson Baru</h2>
                <button 
                    @click="isDialogOpen = false"
                    class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                >
                    <?= icon('X', 'h-5 w-5') ?>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form @submit.prevent="submitForm" action="<?= base_url('master/salespersons') ?>" method="POST" class="p-6 space-y-5">
                <?= csrf_field() ?>
                
                <!-- Row 1: Name & Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="name">Nama Salesperson *</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            placeholder="Contoh: Budi Santoso"
                            :class="{'border-destructive': errors.name}"
                            class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                        >
                        <span x-show="errors.name" class="text-destructive text-xs mt-1" x-text="errors.name"></span>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="phone">No. Telepon</label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            placeholder="Contoh: 081234567890"
                            :class="{'border-destructive': errors.phone}"
                            class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                        >
                        <span x-show="errors.phone" class="text-destructive text-xs mt-1" x-text="errors.phone"></span>
                    </div>
                </div>

                <!-- Row 2: Email -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="email">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        placeholder="Contoh: budi@company.com"
                        :class="{'border-destructive': errors.email}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                    >
                    <span x-show="errors.email" class="text-destructive text-xs mt-1" x-text="errors.email"></span>
                </div>

                <!-- Row 3: Address -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="address">Alamat</label>
                    <textarea 
                        name="address" 
                        id="address" 
                        placeholder="Contoh: Jl. Jendral Sudirman No. 456, Jakarta Pusat"
                        rows="3"
                        :class="{'border-destructive': errors.address}"
                        class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all resize-none"
                    ></textarea>
                    <span x-show="errors.address" class="text-destructive text-xs mt-1" x-text="errors.address"></span>
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
                        class="inline-flex items-center justify-center rounded-lg bg-purple text-white hover:bg-purple-light transition h-11 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!isSubmitting" class="mr-2"><?= icon('Plus', 'h-5 w-5') ?></span>
                        <span x-show="isSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Salesperson'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection() ?>
