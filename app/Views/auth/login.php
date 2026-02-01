<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Content Security Policy Removed for Development -->
    <title>Sign In - TokoManager</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Plus Jakarta Sans + Inter Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.js"></script>
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        h1, h2, h3, .font-display {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        [x-cloak] { display: none !important; }

        /* ===== MODERN ENTERPRISE COLOR SYSTEM ===== */
        :root {
            /* Primary: Emerald Green */
            --primary: 16 92% 35%;
            --primary-light: 16 86% 48%;
            --primary-lighter: 16 100% 96%;
            --primary-foreground: 0 0% 100%;

            /* Secondary: Indigo */
            --secondary: 217 91% 50%;
            --secondary-light: 217 91% 60%;
            --secondary-foreground: 0 0% 100%;

            /* Neutrals */
            --background: 210 16% 98%;
            --surface: 0 0% 100%;
            --foreground: 222 47% 11%;
            --muted: 214 32% 91%;
            --muted-foreground: 215 16% 47%;
            --border: 214 32% 91%;

            /* Status */
            --success: 142 76% 36%;
            --warning: 38 92% 50%;
            --destructive: 0 84% 60%;

            /* Sidebar */
            --sidebar-bg: 222 47% 11%;
            --sidebar-fg: 210 20% 90%;
            --sidebar-accent: 222 40% 18%;
        }

        /* Color utilities */
        .bg-primary { background-color: hsl(var(--primary)); }
        .bg-primary-light { background-color: hsl(var(--primary-light)); }
        .bg-primary-lighter { background-color: hsl(var(--primary-lighter)); }
        .text-primary { color: hsl(var(--primary)); }
        .text-primary-foreground { color: hsl(var(--primary-foreground)); }
        
        .bg-secondary { background-color: hsl(var(--secondary)); }
        .text-secondary { color: hsl(var(--secondary)); }
        
        .bg-background { background-color: hsl(var(--background)); }
        .bg-surface { background-color: hsl(var(--surface)); }
        .text-foreground { color: hsl(var(--foreground)); }
        .text-muted-foreground { color: hsl(var(--muted-foreground)); }
        .border-border { border-color: hsl(var(--border)); }
        
        .bg-destructive { background-color: hsl(var(--destructive)); }
        .text-destructive { color: hsl(var(--destructive)); }

        /* Opacity variants */
        .bg-primary\/10 { background-color: hsl(var(--primary) / 0.10); }
        .bg-primary\/20 { background-color: hsl(var(--primary) / 0.20); }

        /* Gradient background for left side */
        .gradient-brand {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #312E81 100%);
        }

        /* Smooth transitions */
        input, button {
            transition: all 150ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px hsl(var(--primary) / 0.1), 0 0 0 1.5px hsl(var(--primary));
        }

        button:active {
            transform: scale(0.98);
        }

        /* Password visibility toggle */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            padding: 4px;
        }

        /* Loading spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Floating label effect */
        .input-group {
            position: relative;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .brand-section {
                display: none !important;
            }
        }
    </style>
</head>
<body class="h-full min-h-screen bg-background text-foreground">
    
    <!-- Main Container: Split Screen Layout -->
    <div class="flex min-h-screen login-container" x-data="{ 
        username: '', 
        password: '', 
        showPassword: false,
        rememberMe: false,
        isLoading: false,
        selectedRole: 'admin'
    }">
        
        <!-- Left Side: Brand Section (Hidden on Mobile) -->
        <div class="hidden md:flex md:w-1/2 gradient-brand relative overflow-hidden items-center justify-center brand-section">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-20 -right-40 w-80 h-80 bg-primary/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 -left-40 w-80 h-80 bg-secondary/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 px-12 text-center text-white max-w-md">
                <!-- Logo -->
                <div class="mb-8 flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 3h18v2H3V3zm0 3h18v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6zm2 2v10h2V8H5zm4 0v10h2V8H9zm4 0v10h2V8h-2zm4 0v10h2V8h-2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Heading -->
                <h2 class="text-4xl font-bold mb-4 leading-tight">
                    Manajemen Toko Modern
                </h2>

                <!-- Description -->
                <p class="text-white/80 text-lg mb-8 leading-relaxed">
                    Kelola inventaris, transaksi, dan laporan dengan mudah. Platform terpadu untuk bisnis retail Anda.
                </p>

                <!-- Testimonial/Features -->
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-white/90 text-sm">Pantau stok real-time dengan analitik mendalam</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-white/90 text-sm">Kelola pelanggan dan supplier dengan fitur lengkap</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-white/90 text-sm">Laporan finansial lengkap dan terdepan</span>
                    </div>
                </div>

                <!-- Footer Quote -->
                <div class="mt-12 pt-8 border-t border-white/20">
                    <p class="text-white/70 text-sm italic">
                        "Solusi manajemen yang kami butuhkan untuk mengelola bisnis retail dengan efisien."
                    </p>
                    <p class="text-white/90 text-xs mt-3 font-medium">— Ribuan Toko di Indonesia</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 bg-surface flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
            <div class="w-full max-w-md">
                
                <!-- Mobile Logo (Only visible on mobile) -->
                <div class="md:hidden mb-8 flex justify-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10">
                        <svg class="h-7 w-7 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 3h18v2H3V3zm0 3h18v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6zm2 2v10h2V8H5zm4 0v10h2V8H9zm4 0v10h2V8h-2zm4 0v10h2V8h-2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-foreground mb-2">Masuk ke Akun</h1>
                    <p class="text-muted-foreground">Akses dashboard manajemen toko Anda</p>
                </div>

                <!-- Error Message -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-6 p-4 rounded-lg bg-destructive/10 border border-destructive/30">
                        <div class="flex gap-3">
                            <svg class="h-5 w-5 text-destructive flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-destructive font-medium"><?= session()->getFlashdata('error') ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="<?= base_url('login') ?>" method="post" class="space-y-5" @submit="isLoading = true">
                    <?= csrf_field() ?>

                    <!-- Username/Email Field -->
                    <div class="space-y-2">
                        <label for="username" class="text-sm font-semibold text-foreground block">
                            Username atau Email
                        </label>
                        <div class="input-group">
                            <input
                                id="username"
                                name="username"
                                type="text"
                                placeholder="Masukkan username atau email"
                                required
                                x-model="username"
                                class="w-full px-4 py-3 bg-background border border-border rounded-lg text-foreground placeholder:text-muted-foreground focus:border-primary"
                            />
                        </div>
                    </div>

                    <!-- Password Field with Toggle -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-sm font-semibold text-foreground block">
                                Password
                            </label>
                            <a href="#" class="text-xs text-primary hover:text-primary-light transition">
                                Lupa password?
                            </a>
                        </div>
                        <div class="input-group relative">
                            <input
                                id="password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="••••••••••••"
                                required
                                x-model="password"
                                class="w-full px-4 py-3 pr-12 bg-background border border-border rounded-lg text-foreground placeholder:text-muted-foreground focus:border-primary"
                            />
                            <!-- Password Toggle Button -->
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="password-toggle text-muted-foreground hover:text-foreground"
                                aria-label="Toggle password visibility"
                            >
                                <template x-if="!showPassword">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </template>
                                <template x-if="showPassword">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.604-1.753A10.048 10.048 0 0112 5c4.478 0 8.268 2.943 9.543 7a9.97 9.97 0 01-1.563 4.803m-5.604 1.753A10.047 10.047 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.972 9.972 0 011.563-4.803m5.604-1.753A10.049 10.049 0 0112 5"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 5.656"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.111 6.111A7 7 0 0112 5v0a7 7 0 110 14v0 0v0a7 7 0 01-5.889-5.889"/>
                                    </svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Security Note -->
                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                x-model="rememberMe"
                                class="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                            />
                            <span class="text-sm text-muted-foreground">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Sign In Button -->
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="w-full mt-6 px-4 py-3 bg-primary text-primary-foreground font-semibold rounded-lg hover:bg-primary-light disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <template x-if="!isLoading">
                            <span>Masuk ke Dashboard</span>
                        </template>
                        <template x-if="isLoading">
                            <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </template>
                    </button>

                    <!-- Alternative Login Note -->
                    <div class="text-center text-xs text-muted-foreground pt-4">
                        <p>Demo: Username: <strong>admin</strong> | Password: <strong>admin123</strong></p>
                    </div>
                </form>

                <!-- Footer Links -->
                <div class="mt-8 pt-6 border-t border-border">
                    <div class="flex items-center justify-center gap-1 text-sm">
                        <span class="text-muted-foreground">Belum punya akun?</span>
                        <a href="#" class="text-primary font-semibold hover:text-primary-light transition">
                            Hubungi admin
                        </a>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="mt-6 p-4 bg-background rounded-lg border border-border/50">
                    <div class="flex gap-3">
                        <svg class="h-4 w-4 text-muted-foreground flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-muted-foreground">
                            Data Anda dilindungi dengan enkripsi tingkat enterprise. Kami tidak akan pernah membagikan informasi pribadi Anda.
                        </p>
                    </div>
                </div>

                <!-- Copyright -->
                <p class="text-center text-xs text-muted-foreground mt-8">
                    &copy; 2024 TokoManager. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>

</body>
</html>
