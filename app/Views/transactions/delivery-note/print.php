<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - <?= esc($sale['invoice_number']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #1f2937;
            padding: 40px 20px;
            background: #ffffff;
        }

        .delivery-note {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 40px;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .header p {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        /* Info Section */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
        }

        .info-box h3 {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-box p {
            margin-bottom: 6px;
            font-size: 13px;
            color: #4b5563;
        }

        .info-box strong {
            color: #1f2937;
            font-weight: 600;
        }

        /* Table */
        .table-container {
            margin: 30px 0;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table thead {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
        }

        table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            color: #111827;
            border: 1px solid #e5e7eb;
            font-size: 12px;
        }

        table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            color: #374151;
        }

        table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        table td.no {
            text-align: center;
            width: 40px;
            font-weight: 500;
        }

        table td.qty,
        table td.unit {
            text-align: center;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box p {
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #1f2937;
        }

        .signature-box .signature {
            height: 70px;
            border-top: 1.5px solid #1f2937;
            margin: 60px 0 20px 0;
        }

        .signature-box .name {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }

        /* Print Styles */
        .print-button {
            text-align: center;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 1px solid #e5e7eb;
            display: none;
        }

        .print-button button {
            padding: 12px 32px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .print-button button:hover {
            background: #1d4ed8;
        }

        @media screen {
            .print-button {
                display: block;
            }
        }

        @media print {
            .print-button {
                display: none;
            }

            body {
                padding: 0;
                background: white;
            }

            .delivery-note {
                border: none;
                border-radius: 0;
                padding: 0;
                box-shadow: none;
            }

            .header {
                page-break-after: avoid;
            }

            .info-section {
                page-break-inside: avoid;
            }

            .table-container {
                page-break-inside: avoid;
            }

            .footer {
                page-break-before: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="delivery-note">
        <!-- Header -->
        <div class="header">
            <h1>SURAT JALAN / DELIVERY NOTE</h1>
            <p><?= esc($sale['invoice_number']) ?></p>
        </div>

        <!-- Pengirim & Penerima -->
        <div class="info-section">
            <div class="info-box">
                <h3>📦 Pengirim (Sender)</h3>
                <p><strong><?= esc($warehouse['name']) ?></strong></p>
                <p><?= esc($warehouse['address'] ?? '') ?></p>
                <p>📞 <?= esc($warehouse['phone'] ?? '') ?></p>
            </div>
            <div class="info-box">
                <h3>👥 Penerima (Recipient)</h3>
                <p><strong><?= esc($customer['name']) ?></strong></p>
                <p><?= esc($customer['address'] ?? '') ?></p>
                <p>📞 <?= esc($customer['phone'] ?? '') ?></p>
            </div>
        </div>

        <!-- Info Pengiriman -->
        <div class="info-section">
            <div class="info-box">
                <h3>📋 Informasi Pengiriman</h3>
                <p><strong>Tanggal:</strong> <?= format_date($sale['created_at']) ?></p>
                <p><strong>Salesman:</strong> <?= esc($salesperson['name'] ?? 'N/A') ?></p>
                <p><strong>Gudang:</strong> <?= esc($warehouse['name']) ?></p>
            </div>
            <div class="info-box">
                <h3>🧾 Keterangan Dokumen</h3>
                <p><strong>No. Faktur:</strong> <?= esc($sale['invoice_number']) ?></p>
                <p><strong>Tipe Pembayaran:</strong> <?= $sale['payment_type'] === 'CASH' ? '💵 Tunai' : '📱 Kredit' ?></p>
                <p><strong>Status:</strong> <?= esc($sale['status'] ?? 'COMPLETED') ?></p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="no">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th class="unit">Satuan</th>
                        <th class="qty">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="no"><?= $no++ ?></td>
                            <td><?= esc($item['product_code']) ?></td>
                            <td><?= esc($item['product_name']) ?></td>
                            <td class="unit"><?= esc($item['unit'] ?? '-') ?></td>
                            <td class="qty"><?= number_format($item['quantity'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer dengan Tanda Tangan -->
        <div class="footer">
            <div class="signature-box">
                <p>Pengirim,</p>
                <p style="font-size: 12px; color: #6b7280; margin-bottom: 20px;">Warehouse Staff</p>
                <div class="signature"></div>
                <p class="name">( ............................ )</p>
                <p class="name">Tanggal: <?= date('d/m/Y') ?></p>
            </div>
            <div class="signature-box">
                <p>Penerima,</p>
                <p style="font-size: 12px; color: #6b7280; margin-bottom: 20px;">Customer / Recipient</p>
                <div class="signature"></div>
                <p class="name">( ............................ )</p>
                <p class="name">Tanggal: __________</p>
            </div>
        </div>
    </div>

    <!-- Print Button (hanya terlihat di browser) -->
    <div class="print-button">
        <button onclick="window.print()">🖨️ Cetak Surat Jalan</button>
    </div>
</body>
</html>
