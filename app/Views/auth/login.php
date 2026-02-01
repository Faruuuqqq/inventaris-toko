<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TokoManager</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-background font-sans antialiased">
    <!-- Transpiled from referensi-ui/src/pages/Login.tsx -->
    <div class="flex min-h-screen items-center justify-center bg-background p-4" x-data="{ 
        email: '', 
        password: '', 
        showPassword: false, 
        selectedRole: 'admin',
        isLoading: false
    }">
      <div class="w-full max-w-md">
        <div class="mb-8 text-center">
          <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-primary">
            <!-- Package Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7 text-primary-foreground"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
          </div>
          <h1 class="text-2xl font-bold text-foreground">TokoManager</h1>
          <p class="mt-1 text-muted-foreground">Sistem Manajemen Toko</p>
        </div>

        <!-- Card Component -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
          <div class="flex flex-col space-y-1.5 p-6 text-center">
            <h3 class="text-2xl font-semibold leading-none tracking-tight">Masuk ke Akun</h3>
            <p class="text-sm text-muted-foreground">Pilih role dan masukkan kredensial Anda</p>
          </div>
          <div class="p-6 pt-0">
            <!-- Tabs -->
            <div class="mb-6">
              <div class="grid w-full grid-cols-2 h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground">
                 <button type="button" @click="selectedRole = 'owner'" :class="selectedRole === 'owner' ? 'bg-background text-foreground shadow-sm' : ''" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">Owner</button>
                 <button type="button" @click="selectedRole = 'admin'" :class="selectedRole === 'admin' ? 'bg-background text-foreground shadow-sm' : ''" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">Admin</button>
              </div>
              
              <div x-show="selectedRole === 'owner'" class="mt-4">
                <p class="text-sm text-muted-foreground">
                  Login sebagai pemilik dengan akses penuh ke semua fitur dan laporan.
                </p>
              </div>
              <div x-show="selectedRole === 'admin'" class="mt-4">
                <p class="text-sm text-muted-foreground">
                  Login sebagai admin untuk mengelola transaksi harian.
                </p>
              </div>
            </div>

            <form action="<?= base_url('login') ?>" method="post" class="space-y-4">
                <?= csrf_field() ?>
                
                <!-- Helper for error/flash messages -->
                <?php if (session()->getFlashdata('error')): ?>
                    <p class="text-sm text-destructive font-medium"><?= session()->getFlashdata('error') ?></p>
                <?php endif; ?>

              <div class="space-y-2">
                <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="email">Email / Username</label>
                <input
                  id="email"
                  name="username" 
                  type="text"
                  placeholder="Username"
                  class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                  x-model="email"
                />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="password">Password</label>
                <div class="relative">
                  <input
                    id="password"
                    name="password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="••••••••"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-background"
                    x-model="password"
                  />
                  <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                    @click="showPassword = !showPassword"
                  >
                    <template x-if="showPassword">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                    </template>
                    <template x-if="!showPassword">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </template>
                  </button>
                </div>
              </div>

              <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full">
                Masuk sebagai <span x-text="selectedRole === 'owner' ? 'Owner' : 'Admin'"></span>
              </button>
            </form>
          </div>
        </div>

        <p class="mt-6 text-center text-sm text-muted-foreground">
          Demo: Masukkan email dan password apapun untuk login
        </p>
      </div>
    </div>
</body>
</html>
