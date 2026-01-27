<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TokoManager</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/mobile.css">
</head>
<body>
<div class="flex min-h-screen items-center justify-center bg-background p-4">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-primary">
                <?= icon('Package', 'h-7 w-7 text-primary-foreground') ?>
            </div>
            <h1 class="text-2xl font-bold text-foreground">TokoManager</h1>
            <p class="mt-1 text-muted-foreground">Sistem Manajemen Toko</p>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 text-center">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Masuk ke Akun</h3>
                <p class="text-sm text-muted-foreground">Masukkan kredensial Anda</p>
            </div>
            <div class="p-6 pt-0">
                <?php if (session()->has('error')): ?>
                    <div class="mb-4 rounded bg-destructive/10 p-3 text-destructive">
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>

                <form action="/login" method="post" class="space-y-4">
                    <div class="space-y-2">
                        <label for="username" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Username</label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            placeholder="Masukkan username"
                            required
                        >
                    </div>
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Masuk
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-sm text-muted-foreground">
            Demo: Username: owner, Admin: password
        </p>
    </div>
</div>
</body>
</html>
