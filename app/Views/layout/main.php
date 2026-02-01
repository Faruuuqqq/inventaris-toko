<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'TokoManager' ?></title>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        [x-cloak] { display: none !important; }

        /* CSS Variables for Shadcn UI Design System */
        :root {
            --background: 210 20% 98%;
            --foreground: 222 47% 11%;

            --card: 0 0% 100%;
            --card-foreground: 222 47% 11%;

            --popover: 0 0% 100%;
            --popover-foreground: 222 47% 11%;

            --primary: 217 91% 50%;
            --primary-foreground: 0 0% 100%;

            --secondary: 215 20% 94%;
            --secondary-foreground: 222 47% 11%;

            --muted: 215 20% 94%;
            --muted-foreground: 215 16% 47%;

            --accent: 217 91% 95%;
            --accent-foreground: 217 91% 40%;

            --destructive: 0 84% 60%;
            --destructive-foreground: 0 0% 100%;

            --success: 142 76% 36%;
            --success-foreground: 0 0% 100%;

            --warning: 38 92% 50%;
            --warning-foreground: 0 0% 100%;

            --border: 214 32% 91%;
            --input: 214 32% 91%;
            --ring: 217 91% 50%;

            --radius: 0.5rem;

            --sidebar-background: 222 47% 11%;
            --sidebar-foreground: 210 20% 90%;
            --sidebar-primary: 217 91% 60%;
            --sidebar-primary-foreground: 0 0% 100%;
            --sidebar-accent: 222 40% 18%;
            --sidebar-accent-foreground: 210 20% 98%;
            --sidebar-border: 222 40% 20%;
            --sidebar-ring: 217 91% 60%;
            --sidebar-muted: 222 30% 25%;
        }

        /* Tailwind Color System Mapping */
        .bg-background { background-color: hsl(var(--background)); }
        .text-foreground { color: hsl(var(--foreground)); }
        
        .bg-card { background-color: hsl(var(--card)); }
        .text-card-foreground { color: hsl(var(--card-foreground)); }
        
        .bg-primary { background-color: hsl(var(--primary)); }
        .text-primary { color: hsl(var(--primary)); }
        .text-primary-foreground { color: hsl(var(--primary-foreground)); }
        
        .bg-secondary { background-color: hsl(var(--secondary)); }
        .text-secondary { color: hsl(var(--secondary)); }
        .text-secondary-foreground { color: hsl(var(--secondary-foreground)); }
        
        .bg-muted { background-color: hsl(var(--muted)); }
        .text-muted { color: hsl(var(--muted)); }
        .text-muted-foreground { color: hsl(var(--muted-foreground)); }
        
        .bg-accent { background-color: hsl(var(--accent)); }
        .text-accent { color: hsl(var(--accent)); }
        .text-accent-foreground { color: hsl(var(--accent-foreground)); }
        
        .bg-destructive { background-color: hsl(var(--destructive)); }
        .text-destructive { color: hsl(var(--destructive)); }
        .text-destructive-foreground { color: hsl(var(--destructive-foreground)); }
        
        .bg-success { background-color: hsl(var(--success)); }
        .text-success { color: hsl(var(--success)); }
        
        .bg-warning { background-color: hsl(var(--warning)); }
        .text-warning { color: hsl(var(--warning)); }
        
        .border-border { border-color: hsl(var(--border)); }
        .bg-input { background-color: hsl(var(--input)); }
        .ring-offset-ring { --tw-ring-color: hsl(var(--ring)); }
        
        /* Opacity Variants */
        .bg-primary\/10 { background-color: hsl(var(--primary) / 0.1); }
        .bg-success\/10 { background-color: hsl(var(--success) / 0.1); }
        .bg-warning\/10 { background-color: hsl(var(--warning) / 0.1); }
        .bg-destructive\/10 { background-color: hsl(var(--destructive) / 0.1); }
        
        .bg-background\/80 { background-color: hsl(var(--background) / 0.8); }

        /* Sidebar Colors */
        .bg-sidebar { background-color: hsl(var(--sidebar-background)); }
        .text-sidebar-foreground { color: hsl(var(--sidebar-foreground)); }
        .bg-sidebar-primary { background-color: hsl(var(--sidebar-primary)); }
        .text-sidebar-primary-foreground { color: hsl(var(--sidebar-primary-foreground)); }
        .bg-sidebar-accent { background-color: hsl(var(--sidebar-accent)); }
        .text-sidebar-accent-foreground { color: hsl(var(--sidebar-accent-foreground)); }
        .border-sidebar-border { border-color: hsl(var(--sidebar-border)); }

        /* Rounded Radius */
        .rounded-lg { border-radius: var(--radius); }
        .rounded-md { border-radius: calc(var(--radius) - 2px); }
        .rounded-sm { border-radius: calc(var(--radius) - 4px); }
    </style>
</head>
<body class="min-h-screen bg-background text-foreground" x-data="{ sidebarOpen: false }" x-cloak>
    
    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-background/80 md:hidden">
    </div>

    <!-- Sidebar -->
    <?= view('layout/sidebar') ?>

    <!-- Main Content Wrapper -->
    <div class="flex min-h-screen flex-col transition-all duration-300 md:ml-64">
        
        <!-- Sticky Header -->
        <header class="sticky top-0 z-30 border-b border-border bg-card shadow-sm">
            <div class="flex h-16 items-center justify-between px-6">
                
                <!-- Left Section: Menu Toggle + Title -->
                <div class="flex items-center gap-4 flex-1">
                    <!-- Mobile Hamburger Toggle -->
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="inline-flex items-center justify-center rounded-lg p-2 text-foreground/70 hover:bg-accent hover:text-foreground transition-all duration-200 md:hidden"
                            aria-label="Toggle Sidebar">
                        <?= icon('Menu', 'h-5 w-5') ?>
                    </button>

                    <!-- Page Title -->
                    <div class="flex flex-col gap-0.5">
                        <h1 class="text-lg font-semibold text-foreground leading-tight"><?= $title ?? 'Dashboard' ?></h1>
                        <?php if (isset($subtitle)): ?>
                            <p class="text-xs text-muted-foreground hidden sm:block"><?= $subtitle ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Section: Header Actions -->
                <div class="flex items-center gap-3 md:gap-4">
                    <!-- Global Search -->
                    <div class="relative hidden sm:flex items-center">
                        <input type="text" 
                               placeholder="Cari..." 
                               class="h-9 rounded-lg border border-border bg-background px-3 py-1.5 pl-9 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-transparent transition-all duration-200 w-40 md:w-48 lg:w-56"
                               aria-label="Global search">
                        <span class="absolute left-3 text-muted-foreground pointer-events-none">
                            <?= icon('Search', 'h-4 w-4') ?>
                        </span>
                    </div>

                    <!-- Divider -->
                    <div class="hidden sm:block w-px h-6 bg-border"></div>

                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen"
                                class="relative inline-flex items-center justify-center h-9 w-9 rounded-lg text-foreground/70 hover:bg-accent hover:text-foreground transition-all duration-200"
                                aria-label="Notifications">
                            <?= icon('Bell', 'h-5 w-5') ?>
                            <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full bg-destructive animate-pulse"></span>
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
                             class="absolute right-0 mt-2 w-80 rounded-lg border border-border bg-card shadow-lg overflow-hidden z-50">
                            <div class="bg-background px-4 py-3 border-b border-border">
                                <h3 class="font-semibold text-foreground text-sm">Notifikasi</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <div class="px-4 py-3 hover:bg-accent/50 cursor-pointer transition-colors border-b border-border last:border-0">
                                    <p class="text-sm font-medium text-foreground">Belum ada notifikasi</p>
                                    <p class="text-xs text-muted-foreground mt-1">Anda akan menerima pemberitahuan di sini</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu Dropdown -->
                    <div class="relative ml-2 pl-2 border-l border-border" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-foreground/70 hover:bg-accent hover:text-foreground transition-all duration-200"
                                aria-label="User menu">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-accent text-accent-foreground text-sm font-semibold">
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
                             class="absolute right-0 mt-2 w-48 rounded-lg border border-border bg-card shadow-lg overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-border bg-background">
                                <p class="text-sm font-semibold text-foreground"><?= session()->get('fullname') ?? 'User' ?></p>
                                <p class="text-xs text-muted-foreground capitalize mt-0.5"><?= session()->get('role') ?? 'Role' ?></p>
                            </div>
                            <div class="py-1">
                                <a href="<?= base_url('settings') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-foreground/70 hover:bg-accent hover:text-foreground transition-colors">
                                    <?= icon('Settings', 'h-4 w-4') ?>
                                    <span>Pengaturan</span>
                                </a>
                                <a href="<?= base_url('logout') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-destructive/70 hover:bg-destructive/10 hover:text-destructive transition-colors">
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
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="mx-auto">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

</body>
</html>
