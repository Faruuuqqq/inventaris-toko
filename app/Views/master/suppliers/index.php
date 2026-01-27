<!-- Supplier Table -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-semibold">Data Supplier</h3>
                <p class="text-sm text-muted-foreground">Kelola data supplier</p>
            </div>
            <button 
                class="btn btn-primary"
                onclick="document.getElementById('supplierModal').classList.remove('hidden')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
                Tambah Supplier
            </button>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Saldo Utang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?= $supplier['code'] ?? 'AUTO' ?></td>
                    <td><?= $supplier['name'] ?></td>
                    <td><?= $supplier['phone'] ?? '-' ?></td>
                    <td class="text-right"><?= format_currency($supplier['debt_balance']) ?></td>
                    <td>
                        <div class="flex gap-1">
                            <button class="btn btn-ghost" style="height: 32px; width: 32px; padding: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button class="btn btn-ghost text-destructive" style="height: 32px; width: 32px; padding: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Supplier Modal -->
<div id="supplierModal" class="modal hidden">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm w-full max-w-md">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-xl font-semibold">Tambah Supplier Baru</h3>
        </div>
        <div class="p-6 pt-0">
            <form action="/master/suppliers" method="post" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="code">Kode</label>
                        <input type="text" name="code" id="code" class="form-input" placeholder="Kode Supplier">
                    </div>
                    <div class="space-y-2">
                        <label for="name">Nama Supplier</label>
                        <input type="text" name="name" id="name" class="form-input" placeholder="Masukkan nama" required>
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="phone">Telepon</label>
                    <input type="text" name="phone" id="phone" class="form-input" placeholder="Nomor telepon">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('supplierModal').classList.add('hidden')">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>