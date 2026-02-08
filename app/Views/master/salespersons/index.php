<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="salespersonManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Salesperson</h2>
            <p class="mt-1 text-muted-foreground">Kelola data tim penjual dan performa mereka</p>
        </div>
    </div>

    <!-- Summary Cards - Gradient Theme -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-3">
        <!-- Total Salespersons -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-purple/5 to-transparent p-5 hover:border-purple/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Salesperson</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="salespersons.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">aktif</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple/10">
                    <svg class="h-5 w-5 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Salespersons -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-green/5 to-transparent p-5 hover:border-green/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Salesperson Aktif</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="salespersons.filter(s => s.is_active).length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">status</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green/10">
                    <svg class="h-5 w-5 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-blue/5 to-transparent p-5 hover:border-blue/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Penjualan</p>
                    <p class="mt-2 text-2xl font-bold text-foreground">Rp 0</p>
                    <p class="mt-1 text-xs text-muted-foreground">bulan ini</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue/10">
                    <svg class="h-5 w-5 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
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
                    placeholder="Cari nama atau nomor telepon salesperson..." 
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 pl-10 transition-all"
                >
            </div>
        </div>

        <!-- Right Side: Add Button -->
        <button 
            @click="isDialogOpen = true"
            class="inline-flex items-center justify-center rounded-lg bg-purple text-white hover:bg-purple-light transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md whitespace-nowrap"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
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
                                    <a 
                                        :href="`<?= base_url('master/salespersons/edit') ?>/${salesperson.id}`"
                                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                        title="Edit salesperson"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <!-- Delete Button -->
                                    <button 
                                        @click="deleteSalesperson(salesperson.id)"
                                        class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                        title="Hapus salesperson"
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
        <div x-show="filteredSalespersons.length === 0" class="p-12 text-center">
            <svg class="h-16 w-16 mx-auto text-muted-foreground opacity-30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <p class="text-lg font-semibold text-foreground mt-2">Tidak ada salesperson ditemukan</p>
            <p class="text-sm text-muted-foreground mt-1">Coba ubah filter atau cari dengan kata kunci lain</p>
            <button 
                @click="isDialogOpen = true"
                class="mt-6 inline-flex items-center justify-center rounded-lg bg-purple text-white hover:bg-purple-light transition h-10 px-6 gap-2 text-sm font-semibold">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
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
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
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
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
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
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
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
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
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
                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-6 text-sm font-semibold"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        class="inline-flex items-center justify-center rounded-lg bg-purple text-white hover:bg-purple-light transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isSubmitting" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
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

<script>
function salespersonManager() {
    return {
        salespersons: <?= json_encode($salespersons ?? []) ?>,
        search: '',
        isDialogOpen: false,
        isSubmitting: false,
        errors: {},

        get filteredSalespersons() {
            return this.salespersons.filter(s => {
                const searchLower = this.search.toLowerCase();
                return (s.name && s.name.toLowerCase().includes(searchLower)) ||
                       (s.phone && s.phone.toLowerCase().includes(searchLower));
            });
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

        deleteSalesperson(salespersonId) {
            const salesperson = this.salespersons.find(s => s.id === salespersonId);
            const salespersonName = salesperson ? salesperson.name : 'salesperson ini';
            ModalManager.submitDelete(
                `<?= base_url('master/salespersons/delete') ?>/${salespersonId}`,
                salespersonName,
                () => {
                    this.salespersons = this.salespersons.filter(s => s.id !== salespersonId);
                }
            );
        }
    }
}
</script>

<?= $this->endSection() ?>
