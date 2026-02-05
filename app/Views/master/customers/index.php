<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="customerManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Pelanggan</h2>
            <p class="mt-1 text-muted-foreground">Kelola daftar pelanggan dan kredit mereka</p>
        </div>
    </div>

    <!-- Summary Cards - Gradient Theme -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-3">
        <!-- Total Customers -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-primary/5 to-transparent p-5 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Customer</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="customers.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">aktif</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 00-6-6 6 6 0 00-6 6z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Customers with Debt -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Customer dengan Piutang</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="customersWithPiutang"></p>
                    <p class="mt-1 text-xs text-muted-foreground">menunggak</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Debt -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-destructive/5 to-transparent p-5 hover:border-destructive/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Piutang</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="formatRupiah(totalPiutang)"></p>
                    <p class="mt-1 text-xs text-muted-foreground">yang harus diterima</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-destructive/10">
                    <svg class="h-5 w-5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 5v2M7.08 6.24A9 9 0 0120 4.5M3 4.5c0 4.98 3.582 9.128 8.25 9.97M12 21c-4.67 0-8.25-4.15-8.25-9.25"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Bar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search & Tabs -->
        <div class="flex gap-3 flex-wrap items-center flex-1">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-64">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari nama atau kode pelanggan..." 
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 pl-10 transition-all"
                >
            </div>

            <!-- Filter Tabs -->
            <div class="inline-flex h-10 items-center rounded-lg border border-border bg-muted/30 p-1">
                <button 
                    @click="activeTab = 'all'"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-all whitespace-nowrap"
                    :class="activeTab === 'all' ? 'bg-surface text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'">
                    Semua
                </button>
                <button 
                    @click="activeTab = 'piutang'"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-all whitespace-nowrap"
                    :class="activeTab === 'piutang' ? 'bg-surface text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'">
                    Piutang
                </button>
            </div>
        </div>

        <!-- Right Side: Add Button -->
        <div class="flex gap-2">
            <!-- Export Button -->
            <button 
                @click="exportData()"
                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-3 gap-2 text-sm font-medium"
                title="Export data ke PDF"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span class="hidden sm:inline">Export</span>
            </button>

            <button 
                @click="isDialogOpen = true"
                class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md whitespace-nowrap"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Tambah Customer</span>
                <span class="sm:hidden">Tambah</span>
            </button>
        </div>
    </div>

    <!-- Customer Cards Grid - Enhanced -->
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <template x-for="customer in filteredCustomers" :key="customer.id">
            <!-- Customer Card -->
            <div class="rounded-xl border border-border/50 bg-surface shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden group">
                <!-- Card Header with Badge -->
                <div class="border-b border-border/50 bg-gradient-to-r from-primary/3 to-transparent px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider" x-text="`Customer #${customer.code || customer.id}`"></p>
                            <h3 class="mt-2 text-lg font-bold text-foreground truncate group-hover:text-primary transition" x-text="customer.name"></h3>
                        </div>
                        <div class="flex gap-1 flex-shrink-0 ml-3">
                            <!-- Edit Button -->
                            <button 
                                @click="editCustomer(customer.id)"
                                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                title="Edit pelanggan"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <!-- Delete Button -->
                            <button 
                                @click="deleteCustomer(customer.id)"
                                class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                title="Hapus pelanggan"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Debt Badge -->
                    <div class="mt-3">
                        <template x-if="parseFloat(customer.credit_limit || 0) > 0">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-warning/30 bg-warning/15 px-2.5 py-1 text-xs font-semibold text-warning">
                                <span class="inline-block h-2 w-2 rounded-full bg-warning"></span>
                                Piutang
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 space-y-4">
                    <!-- Contact Info -->
                    <div class="space-y-2.5">
                        <!-- Phone -->
                        <div class="flex items-center gap-3 text-sm">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-muted/50">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 4.493a1 1 0 00.502.756l2.048 1.029a2.42 2.42 0 101.622 2.3l-2.048-1.029a1 1 0 00-.756-.502l-4.493-1.498a1 1 0 00-.684-.948V5z"/>
                                </svg>
                            </div>
                            <span class="text-foreground font-medium" x-text="customer.phone || '(tidak ada)'"></span>
                        </div>

                        <!-- Address -->
                        <div class="flex items-start gap-3 text-sm">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-muted/50 flex-shrink-0 mt-0.5">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span class="text-foreground font-medium line-clamp-2" x-text="customer.address || '(tidak ada)'"></span>
                        </div>
                    </div>

                    <!-- Financial Info -->
                    <div class="border-t border-border/50 pt-4 grid grid-cols-2 gap-3">
                        <!-- Credit Limit -->
                        <div class="rounded-lg bg-primary/5 p-3">
                            <p class="text-xs text-muted-foreground font-semibold uppercase">Credit Limit</p>
                            <p class="mt-1 font-bold text-primary text-sm" x-text="formatRupiah(customer.credit_limit || 0)"></p>
                        </div>
                        <!-- Status -->
                        <div class="rounded-lg bg-success/5 p-3">
                            <p class="text-xs text-muted-foreground font-semibold uppercase">Status</p>
                            <p class="mt-1 font-bold text-success text-sm">Aktif</p>
                        </div>
                    </div>
                </div>

                <!-- Card Footer with Link -->
                <div class="border-t border-border/50 bg-muted/20 px-6 py-3">
                    <a :href="`<?= base_url('master/customers/') ?>${customer.id}`" class="text-sm font-semibold text-primary hover:text-primary-light transition flex items-center gap-1">
                        Lihat detail
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="filteredCustomers.length === 0" class="col-span-full">
            <div class="rounded-xl border-2 border-dashed border-border/50 bg-muted/20 p-12 text-center">
                <svg class="h-16 w-16 mx-auto text-muted-foreground opacity-30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 00-6-6 6 6 0 00-6 6z"/>
                </svg>
                <p class="text-lg font-semibold text-foreground mt-2">Tidak ada pelanggan ditemukan</p>
                <p class="text-sm text-muted-foreground mt-1">Coba ubah filter atau cari dengan kata kunci lain</p>
                <button 
                    @click="isDialogOpen = true"
                    class="mt-6 inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-6 gap-2 text-sm font-semibold">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Customer Pertama
                </button>
            </div>
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
                <h2 class="text-xl font-bold text-foreground">Tambah Customer Baru</h2>
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
            <form @submit.prevent="submitForm" action="<?= base_url('master/customers/store') ?>" method="POST" class="p-6 space-y-5">
                <?= csrf_field() ?>
                
                <!-- Row 1: Name & Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="name">Nama Pelanggan *</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            placeholder="Contoh: PT Mitra Sejahtera"
                            :class="{'border-destructive': errors.name}"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
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
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                        >
                        <span x-show="errors.phone" class="text-destructive text-xs mt-1" x-text="errors.phone"></span>
                    </div>
                </div>

                <!-- Row 2: Address -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="address">Alamat Lengkap</label>
                    <textarea 
                        name="address" 
                        id="address" 
                        placeholder="Contoh: Jl. Sudirman No. 123, Jakarta Pusat 12190"
                        rows="3"
                        :class="{'border-destructive': errors.address}"
                        class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all resize-none"
                    ></textarea>
                    <span x-show="errors.address" class="text-destructive text-xs mt-1" x-text="errors.address"></span>
                </div>

                <!-- Row 3: Credit Limit -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="credit_limit">Limit Kredit (Rp) *</label>
                    <input 
                        type="number" 
                        name="credit_limit" 
                        id="credit_limit" 
                        value="0" 
                        required 
                        placeholder="0"
                        :class="{'border-destructive': errors.credit_limit}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <p class="text-xs text-muted-foreground mt-1">Tentukan batas maksimal kredit untuk pelanggan ini</p>
                    <span x-show="errors.credit_limit" class="text-destructive text-xs mt-1" x-text="errors.credit_limit"></span>
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
                        class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isSubmitting" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span x-show="isSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Customer'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function customerManager() {
    return {
        customers: <?= json_encode($customers ?? []) ?>,
        search: '',
        activeTab: 'all',
        isDialogOpen: false,
        isSubmitting: false,
        errors: {},

        get filteredCustomers() {
            return this.customers.filter(cust => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = (cust.name && cust.name.toLowerCase().includes(searchLower)) ||
                                    (cust.code && cust.code.toLowerCase().includes(searchLower));
                
                const matchesTab = this.activeTab === 'all' || 
                                 (this.activeTab === 'piutang' && parseFloat(cust.credit_limit || 0) > 0);

                return matchesSearch && matchesTab;
            });
        },

        get customersWithPiutang() {
            return this.customers.filter(c => parseFloat(c.credit_limit || 0) > 0).length; 
        },

        get totalPiutang() {
            return this.customers.reduce((sum, c) => sum + (parseFloat(c.credit_limit || 0)), 0);
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
                    ModalManager.success('Data pelanggan berhasil ditambahkan', () => {
                        this.isDialogOpen = false;
                        form.reset();
                        this.errors = {};
                        // Reload page to refresh customer list
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

        editCustomer(customerId) {
            // TODO: Implement edit functionality
            window.location.href = `<?= base_url('master/customers/edit') ?>/${customerId}`;
        },

        deleteCustomer(customerId) {
            const customer = this.customers.find(c => c.id === customerId);
            const customerName = customer ? customer.name : 'customer ini';
            ModalManager.submitDelete(
                `<?= base_url('master/customers/delete') ?>/${customerId}`,
                customerName,
                () => {
                    this.customers = this.customers.filter(c => c.id !== customerId);
                }
            );
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        },

        exportData() {
            try {
                window.location.href = `<?= base_url('master/customers/export-pdf') ?>`;
            } catch (error) {
                console.error('Export failed:', error);
                alert('Gagal mengekspor data. Silakan coba lagi.');
            }
        }
    }
}
</script>

<?= $this->endSection() ?>
