<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="warehouseManager()">
    <!-- Page Header with Inline Summary Cards -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
        <!-- Title & Description -->
        <div class="flex-shrink-0">
            <h2 class="text-2xl font-bold text-foreground">Manajemen Gudang</h2>
            <p class="mt-1 text-muted-foreground">Kelola lokasi penyimpanan dan inventaris gudang Anda</p>
        </div>

        <!-- Summary Cards - Horizontal Inline -->
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto lg:flex-shrink-0">
            <!-- Total Warehouses -->
            <div class="rounded-lg border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-3 hover:border-warning/30 transition-colors">
                <div class="flex items-center gap-3 min-w-max">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-warning/10 flex-shrink-0">
                        <svg class="h-4 w-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                            <line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Total</p>
                        <p class="text-lg font-bold text-foreground" x-text="warehouses.length"></p>
                    </div>
                </div>
            </div>

            <!-- Active Warehouses -->
            <div class="rounded-lg border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-3 hover:border-success/30 transition-colors">
                <div class="flex items-center gap-3 min-w-max">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-success/10 flex-shrink-0">
                        <svg class="h-4 w-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Aktif</p>
                        <p class="text-lg font-bold text-foreground" x-text="warehouses.filter(w => parseInt(w.is_active) === 1).length"></p>
                    </div>
                </div>
            </div>

            <!-- Total Storage Value -->
            <div class="rounded-lg border border-border/50 bg-gradient-to-br from-blue/5 to-transparent p-3 hover:border-blue/30 transition-colors">
                <div class="flex items-center gap-3 min-w-max">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue/10 flex-shrink-0">
                        <svg class="h-4 w-4 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Nilai Stok</p>
                        <p class="text-lg font-bold text-foreground">Rp 0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Edit Warehouse Modal -->
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
                 <h2 class="text-xl font-bold text-foreground">Edit Gudang</h2>
                 <button 
                     @click="isEditDialogOpen = false"
                     class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                 >
                     <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                     </svg>
                 </button>
             </div>
             
             <!-- Modal Body -->
             <form @submit.prevent="submitEditForm" :action="`<?= base_url('master/warehouses') ?>/${editingWarehouse.id}`" method="POST" class="p-6 space-y-5">
                 <?= csrf_field() ?>
                 
                 <!-- Row 1: Name & Code -->
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                     <div class="space-y-2">
                         <label class="text-sm font-semibold text-foreground" for="edit_name">Nama Gudang *</label>
                         <input 
                             type="text" 
                             name="name" 
                             id="edit_name" 
                             required 
                             x-model="editingWarehouse.name"
                             :class="{'border-destructive': editErrors.name}"
                             class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all"
                         >
                         <span x-show="editErrors.name" class="text-destructive text-xs mt-1" x-text="editErrors.name"></span>
                     </div>
                     <div class="space-y-2">
                         <label class="text-sm font-semibold text-foreground" for="edit_code">Kode Gudang *</label>
                         <input 
                             type="text" 
                             name="code" 
                             id="edit_code" 
                             required 
                             x-model="editingWarehouse.code"
                             :class="{'border-destructive': editErrors.code}"
                             class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all"
                         >
                         <span x-show="editErrors.code" class="text-destructive text-xs mt-1" x-text="editErrors.code"></span>
                     </div>
                 </div>

                 <!-- Row 2: Address -->
                 <div class="space-y-2">
                     <label class="text-sm font-semibold text-foreground" for="edit_address">Alamat Lengkap</label>
                     <textarea 
                         name="address" 
                         id="edit_address" 
                         rows="3"
                         x-model="editingWarehouse.address"
                         :class="{'border-destructive': editErrors.address}"
                         class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all resize-none"
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
                         class="inline-flex items-center justify-center rounded-lg bg-warning text-white hover:bg-warning-light transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                     >
                         <svg x-show="!isEditSubmitting" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                         </svg>
                         <span x-show="isEditSubmitting" class="inline-flex items-center gap-2 mr-2">
                             <span class="animate-spin">⚙️</span>
                         </span>
                         <span x-text="isEditSubmitting ? 'Menyimpan...' : 'Update Gudang'"></span>
                     </button>
                 </div>
             </form>
         </div>
     </div>
 </div>

    <!-- Control Bar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search -->
        <div class="flex gap-3 flex-wrap items-center flex-1">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-64">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari nama atau kode gudang..." 
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 pl-10 transition-all"
                >
            </div>
        </div>

        <!-- Right Side: Add Button -->
        <button 
            @click="isDialogOpen = true"
            class="inline-flex items-center justify-center rounded-lg bg-warning text-white hover:bg-warning-light transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md whitespace-nowrap"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Tambah Gudang</span>
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
                        <th class="px-6 py-3 text-left font-semibold text-foreground">Kode</th>
                        <th class="px-6 py-3 text-left font-semibold text-foreground">Alamat</th>
                        <th class="px-6 py-3 text-center font-semibold text-foreground">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-foreground">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="warehouse in filteredWarehouses" :key="warehouse.id">
                        <tr class="border-b border-border/50 hover:bg-muted/20 transition">
                            <td class="px-6 py-4 font-semibold text-foreground" x-text="warehouse.name"></td>
                            <td class="px-6 py-4 text-muted-foreground" x-text="warehouse.code || '-'"></td>
                            <td class="px-6 py-4 text-muted-foreground max-w-xs truncate" :title="warehouse.address" x-text="warehouse.address || '-'"></td>
                            <td class="px-6 py-4 text-center">
                                <span 
                                    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold"
                                    :class="parseInt(warehouse.is_active) === 1 
                                        ? 'border-success/30 bg-success/15 text-success' 
                                        : 'border-muted-foreground/30 bg-muted/15 text-muted-foreground'">
                                    <span class="inline-block h-2 w-2 rounded-full" :class="parseInt(warehouse.is_active) === 1 ? 'bg-success' : 'bg-muted-foreground'"></span>
                                    <span x-text="parseInt(warehouse.is_active) === 1 ? 'Aktif' : 'Nonaktif'"></span>
                                </span>
                            </td>
                             <td class="px-6 py-4 text-right">
                                 <div class="flex gap-2 justify-end">
                                     <!-- Edit Button -->
                                     <button 
                                         @click="openEditModal(warehouse)"
                                         class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                         title="Edit gudang"
                                     >
                                         <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                         </svg>
                                     </button>
                                    <!-- Delete Button -->
                                    <button 
                                        @click="deleteWarehouse(warehouse.id)"
                                        class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                        title="Hapus gudang"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div x-show="filteredWarehouses.length === 0" class="p-12 text-center">
            <svg class="h-16 w-16 mx-auto text-muted-foreground opacity-30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
            </svg>
            <p class="text-lg font-semibold text-foreground mt-2">Tidak ada gudang ditemukan</p>
            <p class="text-sm text-muted-foreground mt-1">Coba ubah filter atau cari dengan kata kunci lain</p>
            <button 
                @click="isDialogOpen = true"
                class="mt-6 inline-flex items-center justify-center rounded-lg bg-warning text-white hover:bg-warning-light transition h-10 px-6 gap-2 text-sm font-semibold">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Gudang Pertama
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
                <h2 class="text-xl font-bold text-foreground">Tambah Gudang Baru</h2>
                <button 
                    @click="isDialogOpen = false"
                    class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form @submit.prevent="submitForm" action="<?= base_url('master/warehouses/store') ?>" method="POST" class="p-6 space-y-5">
                <?= csrf_field() ?>
                
                <!-- Row 1: Name & Code -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="name">Nama Gudang *</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            placeholder="Contoh: Gudang Pusat Jakarta"
                            :class="{'border-destructive': errors.name}"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all"
                        >
                        <span x-show="errors.name" class="text-destructive text-xs mt-1" x-text="errors.name"></span>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="code">Kode Gudang *</label>
                        <input 
                            type="text" 
                            name="code" 
                            id="code" 
                            required 
                            placeholder="Contoh: GDG-001"
                            :class="{'border-destructive': errors.code}"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all"
                        >
                        <span x-show="errors.code" class="text-destructive text-xs mt-1" x-text="errors.code"></span>
                    </div>
                </div>

                <!-- Row 2: Address -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="address">Alamat Lengkap</label>
                    <textarea 
                        name="address" 
                        id="address" 
                        placeholder="Contoh: Jl. Merpati No. 456, Jakarta Utara 14450"
                        rows="3"
                        :class="{'border-destructive': errors.address}"
                        class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all resize-none"
                    ></textarea>
                    <span x-show="errors.address" class="text-destructive text-xs mt-1" x-text="errors.address"></span>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-border/50">
                    <button 
                        type="button" 
                        @click="isDialogOpen = false" 
                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-6 text-sm font-semibold"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        class="inline-flex items-center justify-center rounded-lg bg-warning text-white hover:bg-warning-light transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isSubmitting" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span x-show="isSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Gudang'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function warehouseManager() {
     return {
         warehouses: <?= json_encode($warehouses ?? []) ?>,
         search: '',
         isDialogOpen: false,
         isEditDialogOpen: false,
         isSubmitting: false,
         isEditSubmitting: false,
         errors: {},
         editErrors: {},
         editingWarehouse: {},

         get filteredWarehouses() {
             return this.warehouses.filter(w => {
                 const searchLower = this.search.toLowerCase();
                 return (w.name && w.name.toLowerCase().includes(searchLower)) ||
                        (w.code && w.code.toLowerCase().includes(searchLower));
             });
         },

         openEditModal(warehouse) {
             this.editingWarehouse = JSON.parse(JSON.stringify(warehouse));
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
                    ModalManager.success('Data gudang berhasil ditambahkan', () => {
                        this.isDialogOpen = false;
                        form.reset();
                        this.errors = {};
                        // Reload page to refresh warehouse list
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
                     ModalManager.success('Data gudang berhasil diperbarui', () => {
                         this.isEditDialogOpen = false;
                         this.editErrors = {};
                         this.editingWarehouse = {};
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
            const warehouse = this.warehouses.find(w => w.id === warehouseId);
            const warehouseName = warehouse ? warehouse.name : 'gudang ini';
            ModalManager.submitDelete(
                `<?= base_url('master/warehouses/delete') ?>/${warehouseId}`,
                warehouseName,
                () => {
                    this.warehouses = this.warehouses.filter(w => w.id !== warehouseId);
                }
            );
        }
    }
}
</script>

<?= $this->endSection() ?>
