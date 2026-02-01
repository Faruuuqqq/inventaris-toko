<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="customerManager()">
    <!-- Summary Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Total Customer</p>
                <p class="text-2xl font-bold" x-text="customers.length"></p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Customer dengan Piutang</p>
                <p class="text-2xl font-bold text-warning" x-text="customersWithPiutang"></p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Total Piutang</p>
                <p class="text-2xl font-bold text-destructive" x-text="formatRupiah(totalPiutang)"></p>
            </div>
        </div>
    </div>

    <!-- Toolbar: Search & Tabs & Add Button -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col sm:flex-row gap-4 flex-1">
            <!-- Search -->
            <div class="relative w-full sm:w-72">
                <span class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground">
                    <?= icon('Search', 'h-4 w-4') ?>
                </span>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari customer..." 
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-9"
                >
            </div>

            <!-- Tabs -->
            <div class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground">
                <button 
                    @click="activeTab = 'all'"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                    :class="activeTab === 'all' ? 'bg-background text-foreground shadow-sm' : ''">
                    Semua
                </button>
                <button 
                    @click="activeTab = 'piutang'"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                    :class="activeTab === 'piutang' ? 'bg-background text-foreground shadow-sm' : ''">
                    Piutang
                </button>
            </div>
        </div>

        <!-- Add Button -->
        <button 
            @click="isDialogOpen = true"
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
        >
            <?= icon('Plus', 'mr-2 h-4 w-4') ?>
            Tambah Customer
        </button>
    </div>

    <!-- Customer Cards Grid -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <template x-for="customer in filteredCustomers" :key="customer.id">
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm transition-shadow hover:shadow-md">
                <div class="flex flex-col space-y-1.5 p-6 pb-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-xs text-muted-foreground" x-text="customer.code || customer.id"></p>
                                <template x-if="parseFloat(customer.credit_limit || 0) > 0">
                                    <!-- Note: In real app, check actual debt/piutang if available, otherwise using credit limit as placeholder or 0 -->
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80">
                                        <?= icon('AlertCircle', 'mr-1 h-3 w-3') ?>
                                        Piutang
                                    </div>
                                </template>
                            </div>
                            <h3 class="mt-1 text-lg font-semibold leading-none tracking-tight" x-text="customer.name"></h3>
                        </div>
                        <div class="flex gap-1">
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                <?= icon('Pencil', 'h-4 w-4') ?>
                            </button>
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8 text-destructive">
                                <?= icon('Trash2', 'h-4 w-4') ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-6 pt-0 space-y-3">
                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                        <?= icon('Phone', 'h-4 w-4') ?>
                        <span x-text="customer.phone || '-'"></span>
                    </div>
                    <div class="flex items-start gap-2 text-sm text-muted-foreground">
                        <?= icon('MapPin', 'mt-0.5 h-4 w-4 shrink-0') ?>
                        <span x-text="customer.address || '-'"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-t pt-3">
                        <div>
                            <p class="text-xs text-muted-foreground">Credit Limit</p>
                            <p class="font-semibold text-primary" x-text="formatRupiah(customer.credit_limit || 0)"></p>
                        </div>
                        <!-- Piutang Placeholder (Needs Backend Data) -->
                        <template x-if="false"> <!-- Placehoder for real debt logic -->
                            <div>
                                <p class="text-xs text-muted-foreground">Piutang</p>
                                <p class="font-semibold text-destructive">Rp 0</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Modal (Dialog) -->
    <div 
        x-show="isDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-lg rounded-lg border bg-background p-6 shadow-lg sm:rounded-lg"
            @click.away="isDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex flex-col space-y-1.5 text-center sm:text-left mb-4">
                <h2 class="text-lg font-semibold leading-none tracking-tight">Tambah Customer Baru</h2>
            </div>
            
            <form action="<?= base_url('master/customers/store') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="name">Nama Customer</label>
                    <input type="text" name="name" id="name" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="phone">No. Telepon</label>
                    <input type="text" name="phone" id="phone" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="address">Alamat</label>
                    <input type="text" name="address" id="address" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="credit_limit">Limit Kredit</label>
                    <input type="number" name="credit_limit" id="credit_limit" value="0" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="isDialogOpen = false" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Simpan
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

        get filteredCustomers() {
            return this.customers.filter(cust => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = (cust.name && cust.name.toLowerCase().includes(searchLower)) ||
                                    (cust.code && cust.code.toLowerCase().includes(searchLower));
                
                // Note: Logic for 'piutang' tab depends on backend data.
                // Assuming 'credit_limit' or similar field for now if 'piutang' field missing.
                const matchesTab = this.activeTab === 'all' || 
                                 (this.activeTab === 'piutang' && (false)); // Placeholder logic

                return matchesSearch && matchesTab;
            });
        },

        get customersWithPiutang() {
            // Placeholder logic using filter, replace 'false' with actual debt check
            return this.customers.filter(c => false).length; 
        },

        get totalPiutang() {
             // Placeholder logic
            return 0;
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }
    }
}
</script>

<?= $this->endSection() ?>
