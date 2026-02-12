<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - TokoManager</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.js"></script>

    <!-- Design System -->
    <link rel="stylesheet" href="<?= base_url('assets/css/design-system.css') ?>">

    <style>
        [x-cloak] { display: none !important; }

        /* Override card hover - login card should not lift */
        .login-card.card:hover {
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px -1px rgba(0,0,0,0.1);
            transform: none;
            border-color: hsl(var(--border));
        }

        /* ShadCN-style Tabs */
        .tabs-list {
            display: inline-flex;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            background-color: hsl(var(--muted));
            padding: 0.25rem;
            color: hsl(var(--muted-foreground));
            width: 100%;
        }

        .tabs-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 150ms;
            cursor: pointer;
            border: none;
            background: transparent;
            color: hsl(var(--muted-foreground));
            flex: 1;
        }

        .tabs-trigger[data-state="active"] {
            background-color: hsl(var(--surface));
            color: hsl(var(--foreground));
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
        }

        .tabs-trigger:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px hsl(var(--primary) / 0.3);
        }

        /* Password toggle positioning */
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            padding: 0.25rem;
            background: none;
            border: none;
        }

        .password-toggle:active {
            transform: translateY(-50%);
        }
    </style>
</head>
<body class="h-full min-h-screen bg-background text-foreground">

    <div class="flex min-h-screen items-center justify-center p-4" x-data="{
        selectedRole: 'ADMIN',
        showPassword: false,
        isLoading: false
    }">
        <div class="w-full max-w-md">

            <!-- Brand -->
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-primary">
                    <svg class="h-7 w-7 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-foreground">TokoManager</h1>
                <p class="mt-1 text-muted-foreground">Sistem Manajemen Toko</p>
            </div>

            <!-- Card -->
            <div class="login-card card rounded-lg border border-border bg-surface shadow-sm">

                <!-- Card Header -->
                <div class="flex flex-col space-y-1.5 p-6 text-center">
                    <h2 class="text-2xl font-semibold leading-none tracking-tight text-foreground">Masuk ke Akun</h2>
                    <p class="text-sm text-muted-foreground">Pilih role dan masukkan kredensial Anda</p>
                </div>

                <!-- Card Content -->
                <div class="p-6 pt-0">

                    <!-- Role Tabs -->
                    <div class="mb-6">
                        <div class="tabs-list" role="tablist">
                            <button type="button"
                                role="tab"
                                :aria-selected="selectedRole === 'OWNER'"
                                :data-state="selectedRole === 'OWNER' ? 'active' : 'inactive'"
                                @click="selectedRole = 'OWNER'"
                                class="tabs-trigger">
                                Owner
                            </button>
                            <button type="button"
                                role="tab"
                                :aria-selected="selectedRole === 'ADMIN'"
                                :data-state="selectedRole === 'ADMIN' ? 'active' : 'inactive'"
                                @click="selectedRole = 'ADMIN'"
                                class="tabs-trigger">
                                Admin
                            </button>
                        </div>

                        <!-- Tab Descriptions -->
                        <div class="mt-4" role="tabpanel">
                            <p x-show="selectedRole === 'OWNER'" class="text-sm text-muted-foreground">
                                Login sebagai pemilik dengan akses penuh ke semua fitur dan laporan.
                            </p>
                            <p x-show="selectedRole === 'ADMIN'" class="text-sm text-muted-foreground">
                                Login sebagai admin untuk mengelola transaksi harian.
                            </p>
                        </div>
                    </div>

                    <!-- Flash: Error -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="mb-4 flex gap-3 rounded-md p-3 border" style="background:hsl(var(--destructive)/0.1);border-color:hsl(var(--destructive)/0.3)" role="alert">
                            <svg class="h-5 w-5 text-destructive flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-destructive font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="mb-4 flex gap-3 rounded-md p-3 border" style="background:hsl(var(--success)/0.1);border-color:hsl(var(--success)/0.3)" role="status">
                            <svg class="h-5 w-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-success font-medium"><?= esc(session()->getFlashdata('success')) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <form action="<?= base_url('login') ?>" method="post" class="space-y-4" @submit="isLoading = true" novalidate>
                        <?= csrf_field() ?>

                        <!-- Hidden role field -->
                        <input type="hidden" name="role" :value="selectedRole">

                        <!-- Username -->
                        <div class="space-y-2">
                            <label for="username" class="text-sm font-medium leading-none text-foreground">
                                Username
                            </label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                value="<?= esc(old('username') ?? '') ?>"
                                placeholder="Masukkan username"
                                required
                                autofocus
                                autocomplete="username"
                                class="flex h-10 w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary"
                            />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <label for="password" class="text-sm font-medium leading-none text-foreground">
                                Password
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                                    required
                                    autocomplete="current-password"
                                    class="flex h-10 w-full rounded-md border border-border bg-background px-3 py-2 pr-10 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="password-toggle text-muted-foreground hover:text-foreground"
                                    :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                                >
                                    <!-- Eye open -->
                                    <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <!-- Eye closed -->
                                    <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary-light disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <template x-if="!isLoading">
                                <span x-text="'Masuk sebagai ' + (selectedRole === 'OWNER' ? 'Owner' : 'Admin')"></span>
                            </template>
                            <template x-if="isLoading">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </template>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <p class="mt-6 text-center text-xs text-muted-foreground">
                &copy; <?= date('Y') ?> TokoManager. Semua hak dilindungi.
            </p>
        </div>
    </div>

</body>
</html>
