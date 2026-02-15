<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Content Security Policy Removed for Development -->
    <title><?= $title ?? 'TokoManager' ?></title>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Plus Jakarta Sans + Inter Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Design System CSS (Extracted from inline styles) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/design-system.css') ?>">
    
    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">>
</head>
<body class="h-full min-h-screen bg-background text-foreground" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Backdrop with glass morphism effect -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 backdrop-blur-0"
         x-transition:enter-end="opacity-100 backdrop-blur-sm"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 backdrop-blur-sm"
         x-transition:leave-end="opacity-0 backdrop-blur-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-foreground/20 md:hidden">
    </div>

    <!-- Sidebar -->
    <?= view('layout/sidebar') ?>

    <!-- Main Content Wrapper -->
    <div class="flex min-h-screen flex-col transition-all duration-300 md:ml-64">
        
        <!-- Sticky Header - Modern with blur effect -->
        <header class="header-sticky sticky top-0 z-30">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                
                <!-- Left Section: Menu Toggle + Title -->
                <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                    <!-- Mobile Hamburger Toggle -->
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="inline-flex items-center justify-center rounded-lg p-2 text-foreground/60 hover:bg-primary-lighter hover:text-primary transition-all duration-200 md:hidden"
                            aria-label="Toggle Sidebar">
                        <?= icon('Menu', 'h-5 w-5') ?>
                    </button>

                    <!-- Page Title -->
                    <div class="flex flex-col gap-0.5 min-w-0">
                        <h1 class="text-lg font-bold text-foreground leading-tight truncate"><?= $title ?? 'Dashboard' ?></h1>
                        <?php if (isset($subtitle)): ?>
                            <p class="text-xs text-muted-foreground hidden sm:block truncate"><?= $subtitle ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Section: Header Actions -->
                <div class="flex items-center gap-2 sm:gap-3 md:gap-4 ml-4">
                    <!-- Global Search - Hidden on mobile -->
                    <div class="relative hidden lg:flex items-center">
                        <input type="text" 
                               placeholder="Cari..." 
                               class="h-9 rounded-lg border border-border bg-muted px-3 py-1.5 pl-9 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent transition-all duration-200 w-56"
                               aria-label="Global search">
                        <span class="absolute left-3 text-muted-foreground pointer-events-none">
                            <?= icon('Search', 'h-4 w-4') ?>
                        </span>
                    </div>

                    <!-- Divider -->
                    <div class="hidden md:block w-px h-6 bg-border/50"></div>

<!-- Notifications Dropdown -->
                     <div class="relative" x-data="{ notifOpen: false }">
                         <button @click="notifOpen = !notifOpen"
                                 class="relative inline-flex items-center justify-center h-9 w-9 rounded-lg text-foreground/60 hover:bg-primary-lighter hover:text-primary transition-all duration-200"
                                 aria-label="Notifications">
                             <?= icon('Bell', 'h-5 w-5') ?>
                             <span data-notification-badge class="hidden absolute -top-1 -right-1 h-5 w-5 rounded-full bg-destructive text-xs text-white flex items-center justify-center font-bold">0</span>
                         </button>
                         
                         <!-- Notification Panel -->
                         <div x-show="notifOpen"
                              x-transition:enter="transition ease-out duration-150"
                              x-transition:enter-start="opacity-0 scale-95"
                              x-transition:enter-end="opacity-100 scale-100"
                              x-transition:leave="transition ease-in duration-100"
                              x-transition:leave-start="opacity-100 scale-100"
                              x-transition:leave-end="opacity-0 scale-95"
                              @click.away="notifOpen = false"
                              class="absolute right-0 mt-3 w-80 rounded-xl border border-border bg-surface shadow-lg overflow-hidden z-50">
                             <div class="bg-muted px-4 py-3 border-b border-border">
                                 <h3 class="font-bold text-foreground text-sm">Notifikasi</h3>
                             </div>
                             <div data-notification-container class="max-h-96 overflow-y-auto">
                                 <div class="px-4 py-3 hover:bg-primary-lighter/50 cursor-pointer transition-colors border-b border-border last:border-0">
                                     <p class="text-sm font-medium text-foreground">Belum ada notifikasi</p>
                                     <p class="text-xs text-muted-foreground mt-1">Anda akan menerima pemberitahuan di sini</p>
                                 </div>
                             </div>
                         </div>
                     </div>

                    <!-- User Menu Dropdown -->
                    <div class="relative md:ml-2 md:pl-2 md:border-l md:border-border" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-foreground/60 hover:bg-primary-lighter hover:text-primary transition-all duration-200"
                                aria-label="User menu">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground text-sm font-bold">
                                <?= substr(session()->get('fullname') ?? 'U', 0, 1) ?>
                            </div>
                            <span class="hidden sm:inline-block text-sm font-medium leading-none truncate max-w-[100px]">
                                <?= session()->get('fullname') ?? 'User' ?>
                            </span>
                        </button>
                        
                        <!-- User Menu Panel -->
                        <div x-show="userOpen"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             @click.away="userOpen = false"
                             class="absolute right-0 mt-3 w-48 rounded-xl border border-border bg-surface shadow-lg overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-border bg-muted">
                                <p class="text-sm font-bold text-foreground"><?= session()->get('fullname') ?? 'User' ?></p>
                                <p class="text-xs text-muted-foreground capitalize mt-0.5"><?= session()->get('role') ?? 'Role' ?></p>
                            </div>
                            <div class="py-1">
                                <a href="<?= base_url('settings') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-foreground/70 hover:bg-primary-lighter hover:text-primary transition-colors">
                                    <?= icon('Settings', 'h-4 w-4') ?>
                                    <span>Pengaturan</span>
                                </a>
                                <a href="<?= base_url('logout') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-destructive/70 hover:bg-destructive-light/20 hover:text-destructive transition-colors">
                                    <?= icon('LogOut', 'h-4 w-4') ?>
                                    <span>Keluar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-background">
            <div class="mx-auto max-w-7xl">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

<!-- Modal Management JavaScript -->
    <script src="<?= base_url('assets/js/modal.js') ?>"></script>
    
    <!-- Notification System JavaScript -->
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
    <script>
        // Make base_url available to the notification system
        const base_url = '<?= base_url() ?>';
    </script>

    <!-- Global Modal Instances -->
    <?= view('partials/delete-confirm-modal') ?>
    <?= view('partials/success-modal') ?>
    <?= view('partials/error-modal') ?>
    <?= view('partials/warning-modal') ?>

    <!-- Global Loading Overlay -->
    <?= view('components/loading-overlay') ?>

</body>
</html>
