<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h3 class="page-title"><?= $title ?></h3>
            <p class="text-muted"><?= $subtitle ?? '' ?></p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('/finance/expenses') ?>" class="btn btn-outline-secondary">
                <i data-lucide="arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Edit Biaya</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/finance/expenses/update/' . $expense->id) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="expense_number" class="form-label">Nomor Biaya</label>
                        <input type="text" class="form-control" id="expense_number" value="<?= $expense->expense_number ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="expense_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date"
                               value="<?= old('expense_date', date('Y-m-d', strtotime($expense->expense_date))) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $key => $label): ?>
                                <option value="<?= $key ?>" <?= old('category', $expense->category) == $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Pilih Metode</option>
                            <option value="CASH" <?= old('payment_method', $expense->payment_method) == 'CASH' ? 'selected' : '' ?>>Tunai</option>
                            <option value="TRANSFER" <?= old('payment_method', $expense->payment_method) == 'TRANSFER' ? 'selected' : '' ?>>Transfer</option>
                            <option value="CHECK" <?= old('payment_method', $expense->payment_method) == 'CHECK' ? 'selected' : '' ?>>Cek/Giro</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="description" name="description"
                               value="<?= old('description', $expense->description) ?>" placeholder="Deskripsi biaya" required maxlength="255">
                    </div>

                    <div class="col-md-6">
                        <label for="amount" class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="amount" name="amount"
                               value="<?= old('amount', $expense->amount) ?>" placeholder="0" required min="1" step="1">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Catatan tambahan (opsional)"><?= old('notes', $expense->notes) ?></textarea>
                    </div>
                </div>

                <hr>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save"></i> Update
                    </button>
                    <a href="<?= base_url('/finance/expenses') ?>" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
