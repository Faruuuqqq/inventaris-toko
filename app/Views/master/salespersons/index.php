<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('partials/page-header', ['title' => $title, 'subtitle' => $subtitle ?? '']) ?>

    <!-- Salesperson Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <?= view('partials/data-table-header', [
                'title' => 'Data Salesperson',
                'subtitle' => 'Kelola data tenaga penjual',
                'addButton' => 'Tambah Salesperson',
                'modalId' => 'salespersonModal'
            ]) ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($salespersons)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted-foreground">Belum ada data salesperson</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($salespersons as $salesperson): ?>
                    <tr>
                        <td><?= esc($salesperson->name) ?></td>
                        <td><?= esc($salesperson->phone ?? '-') ?></td>
                        <td>
                            <span class="badge <?= $salesperson->is_active ? 'badge-success' : 'badge-destructive' ?>">
                                <?= $salesperson->is_active ? 'Aktif' : 'Non-Aktif' ?>
                            </span>
                        </td>
                        <td>
                            <?= view('partials/action-buttons', [
                                'id' => $salesperson->id,
                                'editModal' => 'editSalespersonModal',
                                'deleteUrl' => '/master/salespersons/delete/' . $salesperson->id,
                                'data' => (array)$salesperson
                            ]) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Salesperson Modal -->
<div id="salespersonModal" class="modal hidden">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm w-full max-w-md">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-xl font-semibold">Tambah Salesperson Baru</h3>
        </div>
        <div class="p-6 pt-0">
            <form action="/master/salespersons" method="post" class="space-y-4">
                <?= csrf_field() ?>
                <div class="space-y-2">
                    <label for="name">Nama Salesperson</label>
                    <input type="text" name="name" id="name" class="form-input" placeholder="Masukkan nama" required>
                </div>
                <div class="space-y-2">
                    <label for="phone">Telepon</label>
                    <input type="text" name="phone" id="phone" class="form-input" placeholder="Nomor telepon">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('salespersonModal').classList.add('hidden')">
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

<!-- Edit Salesperson Modal -->
<div id="editSalespersonModal" class="modal hidden">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm w-full max-w-md">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-xl font-semibold">Edit Salesperson</h3>
        </div>
        <div class="p-6 pt-0">
            <form id="editSalespersonForm" action="" method="post" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <div class="space-y-2">
                    <label for="edit_name">Nama Salesperson</label>
                    <input type="text" name="name" id="edit_name" class="form-input" placeholder="Masukkan nama" required>
                </div>
                <div class="space-y-2">
                    <label for="edit_phone">Telepon</label>
                    <input type="text" name="phone" id="edit_phone" class="form-input" placeholder="Nomor telepon">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('editSalespersonModal').classList.add('hidden')">
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

<script>
function openEditModal(modalId, id, data) {
    if (modalId === 'editSalespersonModal' && data) {
        document.getElementById('editSalespersonForm').action = '/master/salespersons/update/' + id;
        document.getElementById('edit_name').value = data.name || '';
        document.getElementById('edit_phone').value = data.phone || '';
    }
    document.getElementById(modalId).classList.remove('hidden');
}
</script>
<?= $this->endSection() ?>
