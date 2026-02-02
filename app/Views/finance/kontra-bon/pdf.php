<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontra Bon - <?= esc($kontraBon['document_number']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }
        
        .header h1 {
            font-size: 28px;
            color: #1e40af;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .header p {
            font-size: 11px;
            color: #666;
        }
        
        /* Document Info */
        .doc-info {
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
        }
        
        .doc-info table {
            width: 100%;
        }
        
        .doc-info td {
            padding: 5px 0;
        }
        
        .doc-info td:first-child {
            width: 40%;
            font-weight: bold;
            color: #475569;
        }
        
        .doc-info td:last-child {
            width: 60%;
        }
        
        .doc-number {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
        }
        
        /* Customer Info */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .customer-info {
            margin-bottom: 30px;
            background: #f1f5f9;
            padding: 15px;
            border-radius: 5px;
        }
        
        .customer-info p {
            margin-bottom: 8px;
        }
        
        .customer-info strong {
            display: inline-block;
            width: 120px;
            color: #475569;
        }
        
        /* Details Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .details-table th {
            background: #1e40af;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        .details-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .details-table tr:last-child td {
            border-bottom: 2px solid #1e40af;
        }
        
        /* Total Section */
        .total-section {
            text-align: right;
            margin-bottom: 40px;
        }
        
        .total-box {
            display: inline-block;
            background: #1e40af;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            min-width: 300px;
        }
        
        .total-box .label {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .total-box .amount {
            font-size: 24px;
            font-weight: bold;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        /* Notes */
        .notes-section {
            margin-bottom: 40px;
            padding: 15px;
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 3px;
        }
        
        .notes-section p {
            margin-top: 8px;
            color: #78350f;
            white-space: pre-wrap;
        }
        
        /* Signature */
        .signature-section {
            margin-top: 60px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            text-align: center;
            width: 33%;
            padding: 10px;
        }
        
        .signature-box p {
            margin-bottom: 80px;
            font-weight: bold;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin: 0 20px;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            
            .container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>KONTRA BON</h1>
            <p>Dokumen Pembayaran Tertunda</p>
        </div>
        
        <!-- Document Information -->
        <div class="doc-info">
            <table>
                <tr>
                    <td>No. Dokumen</td>
                    <td><span class="doc-number"><?= esc($kontraBon['document_number']) ?></span></td>
                </tr>
                <tr>
                    <td>Tanggal Dibuat</td>
                    <td><?= date('d F Y', strtotime($kontraBon['created_at'])) ?></td>
                </tr>
                <tr>
                    <td>Tanggal Jatuh Tempo</td>
                    <td><?= $kontraBon['due_date'] ? date('d F Y', strtotime($kontraBon['due_date'])) : '-' ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        <span class="status-badge status-<?= strtolower($kontraBon['status']) ?>">
                            <?= esc($kontraBon['status']) ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Customer Information -->
        <div class="customer-info">
            <div class="section-title">INFORMASI CUSTOMER</div>
            <p><strong>Nama:</strong> <?= esc($kontraBon['customer_name']) ?></p>
            <?php if (!empty($kontraBon['customer_email'])): ?>
            <p><strong>Email:</strong> <?= esc($kontraBon['customer_email']) ?></p>
            <?php endif; ?>
            <?php if (!empty($kontraBon['customer_phone'])): ?>
            <p><strong>Telepon:</strong> <?= esc($kontraBon['customer_phone']) ?></p>
            <?php endif; ?>
            <?php if (!empty($kontraBon['customer_address'])): ?>
            <p><strong>Alamat:</strong> <?= esc($kontraBon['customer_address']) ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Details Table -->
        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Keterangan</th>
                    <th style="width: 30%; text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Kontra Bon</strong><br>
                        <small style="color: #64748b;">
                            <?= $kontraBon['notes'] ? esc($kontraBon['notes']) : 'Pembayaran tertunda' ?>
                        </small>
                    </td>
                    <td style="text-align: right; font-weight: bold; font-size: 14px;">
                        Rp <?= number_format($kontraBon['total_amount'], 0, ',', '.') ?>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Total Section -->
        <div class="total-section">
            <div class="total-box">
                <div class="label">TOTAL KONTRA BON</div>
                <div class="amount">Rp <?= number_format($kontraBon['total_amount'], 0, ',', '.') ?></div>
            </div>
        </div>
        
        <!-- Notes Section -->
        <?php if (!empty($kontraBon['notes'])): ?>
        <div class="notes-section">
            <div class="section-title" style="border-color: #f59e0b;">CATATAN</div>
            <p><?= esc($kontraBon['notes']) ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Dibuat Oleh,</p>
                <div class="signature-line">
                    (........................)
                </div>
            </div>
            <div class="signature-box">
                <p>Disetujui Oleh,</p>
                <div class="signature-line">
                    (........................)
                </div>
            </div>
            <div class="signature-box">
                <p>Penerima,</p>
                <div class="signature-line">
                    (........................)
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dicetak secara otomatis oleh sistem TokoManager</p>
            <p>Dicetak pada: <?= date('d F Y H:i:s') ?> WIB</p>
        </div>
    </div>
</body>
</html>
