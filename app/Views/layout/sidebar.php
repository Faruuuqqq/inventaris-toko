<aside class="fixed left-0 top-0 z-40 flex h-screen w-64 flex-col bg-sidebar">
    <!-- Logo -->
    <div class="flex h-16 items-center gap-3 border-b border-sidebar-border px-6">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sidebar-primary">
            <?= icon('Package', 'h-4 w-4 text-sidebar-primary-foreground') ?>
        </div>
        <div>
            <h1 class="text-lg font-bold text-sidebar-foreground">TokoManager</h1>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto p-4">
        <ul class="space-y-1">
            <li>
                <a href="/dashboard" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground <?= current_url(true)->getPath() === '/dashboard' ? 'bg-sidebar-primary text-sidebar-primary-foreground' : '' ?>">
                    <?= icon('LayoutDashboard', 'h-4 w-4') ?>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Data Utama -->
            <li>
                <div x-data="{ open: false }" class="mb-1">
                    <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                        <div class="flex items-center gap-3">
                            <?= icon('Users', 'h-4 w-4') ?>
                            <span>Data Utama</span>
                        </div>
                        <template x-if="open">
                            <?= icon('ChevronDown', 'h-4 w-4') ?>
                        </template>
                        <template x-if="!open">
                            <?= icon('ChevronRight', 'h-4 w-4') ?>
                        </template>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1 border-l border-sidebar-border pl-3">
                        <a href="/master/products" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground <?= current_url(true)->getPath() === '/master/products' ? 'bg-sidebar-accent text-sidebar-accent-foreground' : '' ?>">
                            <?= icon('Package', 'h-4 w-4') ?>
                            <span>Produk</span>
                        </a>
                        <a href="/master/customers" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                            <?= icon('UserCheck', 'h-4 w-4') ?>
                            <span>Customer</span>
                        </a>
                        <a href="/master/suppliers" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                            <?= icon('Truck', 'h-4 w-4') ?>
                            <span>Supplier</span>
                        </a>
                        <a href="/master/warehouses" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                            <?= icon('Warehouse', 'h-4 w-4') ?>
                            <span>Gudang</span>
                        </a>
                        <a href="/master/salespersons" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                            <?= icon('BadgePercent', 'h-4 w-4') ?>
                            <span>Sales</span>
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </nav>

    <!-- User Profile -->
    <div class="border-t border-sidebar-border p-4">
        <div class="mb-3 flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sidebar-accent">
                <span class="text-sm font-medium text-sidebar-accent-foreground">
                    <?= strtoupper(substr(session()->get('fullname') ?? 'U', 0, 1)) ?>
                </span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-sidebar-foreground"><?= session()->get('fullname') ?></p>
                <p class="text-xs text-sidebar-muted capitalize"><?= session()->get('role') ?></p>
            </div>
        </div>
        <a href="/logout" class="flex w-full items-center justify-start gap-2 rounded-md px-3 py-2 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
            <?= icon('LogOut', 'h-4 w-4') ?>
            Keluar
        </a>
    </div>

<script>
    // Load helper function
    <?php include_once APPPATH . 'Helpers/ui_helper.php'; ?>
</script>
</aside>