<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'TokoManager' ?> - Sistem Manajemen Toko</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mobile.css">
    <script defer src="/assets/js/alpine.min.js"></script>
    <script src="/assets/js/validation.js" defer></script>
</head>
<body class="min-h-screen bg-background">
    <?= view('layout/sidebar') ?>

    <div class="ml-64 flex min-h-screen flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-card px-6">
            <div>
                <h1 class="text-xl font-semibold text-foreground"><?= $title ?? 'Dashboard' ?></h1>
                <?php if (isset($subtitle)): ?>
                    <p class="text-sm text-muted-foreground"><?= $subtitle ?></p>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <?= icon('Search', 'absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground') ?>
                    <input type="text" placeholder="Cari..." class="w-64 rounded-md border border-input bg-background pl-9 pr-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
                <button class="relative inline-flex items-center justify-center rounded-md p-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground">
                    <?= icon('Bell', 'h-5 w-5') ?>
                    <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-destructive"></span>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script>
        // Alpine.js global data
        function appData() {
            return {
                isLoggedIn: <?= session()->get('isLoggedIn') ? 'true' : 'false' ?>,
                user: {
                    name: '<?= session()->get('fullname') ?? '' ?>',
                    role: '<?= session()->get('role') ?? '' ?>'
                }
            }
        }
    </script>
</body>
</html>