<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <!-- Profile Settings -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('User', 'h-5 w-5') ?>
                    Profil Pengguna
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary text-2xl font-bold text-primary-foreground">
                        <?= strtoupper(substr(session()->get('fullname') ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-lg font-semibold"><?= esc(session()->get('fullname')) ?></p>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground capitalize">
                                <?= esc(session()->get('role')) ?>
                            </span>
                            <span class="text-sm text-muted-foreground"><?= esc(session()->get('email') ?? '-') ?></span>
                        </div>
                    </div>
                </div>

                <hr class="border-border">

                <form action="<?= base_url('settings/updateProfile') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Nama Lengkap</label>
                            <input type="text" name="fullname" value="<?= esc(session()->get('fullname')) ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                         </div>
                         <div class="space-y-2">
                             <label class="text-sm font-medium">Email</label>
                             <input type="email" name="email" value="<?= esc($user->email ?? $user['email'] ?? '') ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary/90 transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md mt-4">Simpan Perubahan</button>
                </form>
             </div>
         </div>
 
         <!-- Store Settings -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Store', 'h-5 w-5') ?>
                    Informasi Toko
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <form action="<?= base_url('settings/updateStore') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                             <label class="text-sm font-medium">Nama Toko</label>
                             <input type="text" name="store_name" value="<?= esc($config['company_name'] ?? 'Toko Sejahtera') ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                         </div>
                         <div class="space-y-2">
                             <label class="text-sm font-medium">No. Telepon</label>
                             <input type="text" name="store_phone" value="<?= esc($config['company_phone'] ?? '') ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                         </div>
                     </div>
                     <div class="space-y-2 mt-4">
                         <label class="text-sm font-medium">Alamat</label>
                         <input type="text" name="store_address" value="<?= esc($config['company_address'] ?? '') ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                     </div>
                     <div class="grid gap-4 md:grid-cols-2 mt-4">
                         <div class="space-y-2">
                             <label class="text-sm font-medium">NPWP</label>
                             <input type="text" name="store_npwp" value="<?= esc($config['tax_number'] ?? '') ?>" placeholder="Masukkan NPWP" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                        </div>
                     </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary/90 transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md mt-4">Simpan Perubahan</button>
                </form>
             </div>
         </div>
 
         <!-- Security Settings -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Lock', 'h-5 w-5') ?>
                    Keamanan
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <form action="<?= base_url('settings/changePassword') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="space-y-2">
                         <label class="text-sm font-medium">Password Saat Ini</label>
                         <input type="password" name="current_password" placeholder="********" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                     </div>
                     <div class="grid gap-4 md:grid-cols-2 mt-4">
                         <div class="space-y-2">
                             <label class="text-sm font-medium">Password Baru</label>
                             <input type="password" name="new_password" placeholder="********" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                         </div>
                         <div class="space-y-2">
                             <label class="text-sm font-medium">Konfirmasi Password</label>
                             <input type="password" name="confirm_password" placeholder="********" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                         </div>
                     </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary/90 transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md mt-4">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Notification Settings -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Bell', 'h-5 w-5') ?>
                    Notifikasi
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium">Stok Menipis</p>
                        <p class="text-sm text-muted-foreground">Peringatan saat stok rendah</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
                <hr class="border-border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium">Piutang Jatuh Tempo</p>
                        <p class="text-sm text-muted-foreground">Pengingat piutang</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
                <hr class="border-border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium">Laporan Harian</p>
                        <p class="text-sm text-muted-foreground">Kirim laporan via email</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-muted rounded-full peer peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Access Control -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Shield', 'h-5 w-5') ?>
                    Kontrol Akses
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-3">
                <div class="rounded-lg border p-3">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">Owner</span>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-primary text-primary-foreground">
                            Akses Penuh
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Dapat mengakses semua fitur termasuk laporan dan pengaturan
                    </p>
                </div>
                <div class="rounded-lg border p-3">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">Admin</span>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">
                            Terbatas
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Dapat mengelola transaksi harian dan data master
                    </p>
                </div>
                <div class="rounded-lg border p-3">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">Gudang</span>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">
                            Terbatas
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Hanya dapat mengelola stok dan penerimaan barang
                    </p>
                </div>
                <div class="rounded-lg border p-3">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">Sales</span>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground">
                            Terbatas
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Hanya dapat membuat transaksi penjualan
                    </p>
                </div>
            </div>
        </div>

        <!-- App Info -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Settings', 'h-5 w-5') ?>
                    Tentang Aplikasi
                </h3>
            </div>
            <div class="p-6 pt-0 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Versi</span>
                    <span>1.0.0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Build</span>
                    <span><?= date('Y.m.d') ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Framework</span>
                    <span>CodeIgniter 4</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Lisensi</span>
                    <span>Enterprise</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>