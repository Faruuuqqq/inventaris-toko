-- =====================================================================
-- INVENTARIS TOKO - FINANCIAL SEED DATA (ADD-ON)
-- Generated: February 14, 2026
-- Description: Additional financial data for testing finance pages
-- Import this AFTER database_full_seed.sql
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- =====================================================================
-- PART 1: ADDITIONAL PAYMENTS (More Receivables & Payables)
-- =====================================================================

-- Additional Receivable Payments (Customer Payments)
INSERT INTO `payments` (`payment_number`, `payment_date`, `type`, `reference_id`, `amount`, `method`, `notes`, `user_id`, `created_at`) VALUES
-- Payments for Contra Bons
('PAY-2024-02-008', '2024-02-14', 'RECEIVABLE', 14, 15000000.00, 'TRANSFER', 'Pembayaran termin 2 untuk INV-2024-02-0011 (CB-2024-004)', 4, '2024-02-14 09:00:00'),
('PAY-2024-02-009', '2024-02-14', 'RECEIVABLE', 24, 10000000.00, 'TRANSFER', 'Pembayaran parsial untuk INV-2024-02-0016 (CB-2024-001)', 4, '2024-02-14 10:00:00'),
('PAY-2024-02-010', '2024-02-14', 'RECEIVABLE', 11, 6500000.00, 'CASH', 'Pelunasan sisa INV-2024-02-0001 (CB-2024-001)', 2, '2024-02-14 11:00:00'),

-- New payments for unpaid invoices
('PAY-2024-02-011', '2024-02-14', 'RECEIVABLE', 14, 12500000.00, 'TRANSFER', 'Pelunasan sisa INV-2024-02-0011 (CB-2024-004)', 4, '2024-02-14 12:00:00'),
('PAY-2024-02-012', '2024-02-14', 'RECEIVABLE', 22, 5000000.00, 'TRANSFER', 'Pembayaran parsial untuk INV-2024-02-0014 (CB-2024-005)', 4, '2024-02-14 13:00:00'),
('PAY-2024-02-013', '2024-02-14', 'RECEIVABLE', 27, 9300000.00, 'CASH', 'Pelunasan sisa INV-2024-02-0019', 5, '2024-02-14 14:00:00'),

-- Historical payments from January
('PAY-2024-01-005', '2024-01-20', 'RECEIVABLE', 5, 12300000.00, 'TRANSFER', 'Pelunasan INV-2024-01-0005', 5, '2024-01-20 10:00:00'),
('PAY-2024-01-006', '2024-01-25', 'RECEIVABLE', 8, 15600000.00, 'TRANSFER', 'Pelunasan INV-2024-01-0008', 4, '2024-01-25 14:00:00'),
('PAY-2024-01-007', '2024-01-31', 'RECEIVABLE', 2, 8900000.00, 'CASH', 'Pelunasan INV-2024-01-0002', 2, '2024-01-31 15:00:00'),

-- Additional Payable Payments (Supplier Payments)
('PAY-2024-02-014', '2024-02-14', 'PAYABLE', 3, 18500000.00, 'TRANSFER', 'Pelunasan PO-2024-01-003', 2, '2024-02-14 15:00:00'),
('PAY-2024-02-015', '2024-02-14', 'PAYABLE', 4, 12500000.00, 'TRANSFER', 'Pelunasan PO-2024-01-004', 4, '2024-02-14 16:00:00'),
('PAY-2024-02-016', '2024-02-14', 'PAYABLE', 5, 7000000.00, 'CASH', 'Pembayaran parsial PO-2024-02-001', 2, '2024-02-14 17:00:00'),
('PAY-2024-02-017', '2024-02-14', 'PAYABLE', 8, 7500000.00, 'TRANSFER', 'Pembayaran parsial PO-2024-02-004', 4, '2024-02-14 18:00:00');

-- Update Sales Paid Amounts based on payments
UPDATE `sales` SET `paid_amount` = 18500000.00, `payment_status` = 'PAID' WHERE `id` = 11;
UPDATE `sales` SET `paid_amount` = 27500000.00, `payment_status` = 'PAID' WHERE `id` = 19;
UPDATE `sales` SET `paid_amount` = 11000000.00, `payment_status` = 'PAID' WHERE `id` = 24;
UPDATE `sales` SET `paid_amount` = 28500000.00, `payment_status` = 'PAID' WHERE `id` = 27;
UPDATE `sales` SET `paid_amount` = 5000000.00, `payment_status` = 'PARTIAL' WHERE `id` = 22;

-- Update Purchase Orders Paid Amounts based on payments
UPDATE `purchase_orders` SET `paid_amount` = 22000000.00, `payment_status` = 'PAID' WHERE `id_po` = 5;
UPDATE `purchase_orders` SET `paid_amount` = 22500000.00, `payment_status` = 'PARTIAL' WHERE `id_po` = 8;

-- Update Contra Bons Status based on payments
UPDATE `contra_bons` SET `status` = 'PAID' WHERE `id` = 1;
UPDATE `contra_bons` SET `status` = 'PAID' WHERE `id` = 4;

-- =====================================================================
-- PART 2: ADDITIONAL EXPENSES (More Financial Expenses)
-- =====================================================================

INSERT INTO `expenses` (`expense_number`, `expense_date`, `category`, `description`, `amount`, `payment_method`, `user_id`, `created_at`) VALUES
-- January Expenses
('EXP-2024-01-005', '2024-01-10', 'Sewa Tempat', 'Sewa gudang bulan Januari', 15000000.00, 'TRANSFER', 2, '2024-01-10 10:00:00'),
('EXP-2024-01-006', '2024-01-15', 'Marketing', 'Iklan sosial media', 2500000.00, 'TRANSFER', 2, '2024-01-15 14:00:00'),
('EXP-2024-01-007', '2024-01-20', 'Lain-lain', 'Biaya bank admin', 150000.00, 'TRANSFER', 4, '2024-01-20 11:00:00'),
('EXP-2024-01-008', '2024-01-25', 'Transportasi', 'Bensin untuk delivery', 500000.00, 'CASH', 5, '2024-01-25 15:00:00'),
('EXP-2024-01-009', '2024-01-28', 'Pemeliharaan', 'Service kendaraan operasional', 1200000.00, 'TRANSFER', 4, '2024-01-28 10:00:00'),
('EXP-2024-01-010', '2024-01-30', 'Lain-lain', 'Biaya fotokopi dokumen', 85000.00, 'CASH', 5, '2024-01-30 14:00:00'),
-- February Expenses
('EXP-2024-02-007', '2024-02-01', 'Sewa Tempat', 'Sewa gudang bulan Februari', 15000000.00, 'TRANSFER', 2, '2024-02-01 10:00:00'),
('EXP-2024-02-008', '2024-02-03', 'Marketing', 'Iklan Google Ads', 3500000.00, 'TRANSFER', 2, '2024-02-03 14:00:00'),
('EXP-2024-02-009', '2024-02-05', 'Lain-lain', 'Biaya notaris', 750000.00, 'TRANSFER', 4, '2024-02-05 11:00:00'),
('EXP-2024-02-010', '2024-02-07', 'Transportasi', 'Tol dan parkir bulanan', 800000.00, 'CASH', 5, '2024-02-07 15:00:00'),
('EXP-2024-02-011', '2024-02-09', 'Komisi', 'Komisi sales bulan Januari', 4500000.00, 'TRANSFER', 2, '2024-02-09 10:00:00'),
('EXP-2024-02-012', '2024-02-11', 'Pemeliharaan', 'Perbaikan pintu gudang', 850000.00, 'CASH', 4, '2024-02-11 14:00:00'),
('EXP-2024-02-013', '2024-02-13', 'Lain-lain', 'Biaya internet bulanan', 450000.00, 'TRANSFER', 5, '2024-02-13 16:00:00'),
('EXP-2024-02-014', '2024-02-14', 'Pajak', 'Pajak PPN bulan Januari', 12000000.00, 'TRANSFER', 2, '2024-02-14 10:00:00'),
('EXP-2024-02-015', '2024-02-14', 'Lain-lain', 'Biaya rapat supplier', 350000.00, 'CASH', 4, '2024-02-14 14:00:00');

-- =====================================================================
-- PART 3: CUSTOMER & SUPPLIER BALANCE UPDATES
-- =====================================================================

-- Update Customer Receivable Balances based on payments and unpaid sales
UPDATE `customers` SET `receivable_balance` = 0.00 WHERE `id` IN (1, 3, 5, 6, 7, 9, 10, 11, 13, 15, 17, 18, 20);
UPDATE `customers` SET `receivable_balance` = 5000000.00 WHERE `id` = 16; -- PT. Distribusi Mandiri
UPDATE `customers` SET `receivable_balance` = 18500000.00 WHERE `id` = 20; -- CV. Logistik Cepat (unpaid CB)

-- Update Supplier Debt Balances based on payments and unpaid POs
UPDATE `suppliers` SET `debt_balance` = 0.00 WHERE `id` IN (1, 2, 3, 4, 6, 7);
UPDATE `suppliers` SET `debt_balance` = 2500000.00 WHERE `id` = 5; -- CV. Kesehatan Utama (partial PO)
UPDATE `suppliers` SET `debt_balance` = 2500000.00 WHERE `id` = 8; -- CV. Sparepart Mobil (partial PO)
UPDATE `suppliers` SET `debt_balance` = 18000000.00 WHERE `id` = 9; -- PT. Mainan Anak (unpaid PO)
UPDATE `suppliers` SET `debt_balance` = 15000000.00 WHERE `id` = 10; -- Distributor Aksesoris (unpaid PO)

-- =====================================================================
-- PART 4: FINANCIAL AUDIT LOGS
-- =====================================================================

INSERT INTO `audit_logs` (`user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
-- Payment Logs
(2, 'CREATE', 'payments', 11, NULL, '{"payment_number":"PAY-2024-02-008","type":"RECEIVABLE","amount":15000000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 09:00:00'),
(4, 'CREATE', 'payments', 12, NULL, '{"payment_number":"PAY-2024-02-009","type":"RECEIVABLE","amount":10000000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 10:00:00'),
(2, 'CREATE', 'payments', 13, NULL, '{"payment_number":"PAY-2024-02-010","type":"RECEIVABLE","amount":6500000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 11:00:00'),
(4, 'CREATE', 'payments', 14, NULL, '{"payment_number":"PAY-2024-02-011","type":"RECEIVABLE","amount":12500000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 12:00:00'),
(4, 'CREATE', 'payments', 15, NULL, '{"payment_number":"PAY-2024-02-012","type":"RECEIVABLE","amount":5000000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 13:00:00'),
(5, 'CREATE', 'payments', 16, NULL, '{"payment_number":"PAY-2024-02-013","type":"RECEIVABLE","amount":9300000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 14:00:00'),
(2, 'CREATE', 'payments', 17, NULL, '{"payment_number":"PAY-2024-02-014","type":"PAYABLE","amount":18500000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 15:00:00'),
(4, 'CREATE', 'payments', 18, NULL, '{"payment_number":"PAY-2024-02-015","type":"PAYABLE","amount":12500000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 16:00:00'),
(2, 'CREATE', 'payments', 19, NULL, '{"payment_number":"PAY-2024-02-016","type":"PAYABLE","amount":7000000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 17:00:00'),
(4, 'CREATE', 'payments', 20, NULL, '{"payment_number":"PAY-2024-02-017","type":"PAYABLE","amount":7500000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 18:00:00'),
-- Expense Logs
(2, 'CREATE', 'expenses', 11, NULL, '{"expense_number":"EXP-2024-01-005","category":"Sewa Tempat"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-10 10:00:00'),
(2, 'CREATE', 'expenses', 12, NULL, '{"expense_number":"EXP-2024-01-006","category":"Marketing"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-15 14:00:00'),
(4, 'CREATE', 'expenses', 13, NULL, '{"expense_number":"EXP-2024-01-007","category":"Lain-lain"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-20 11:00:00'),
(5, 'CREATE', 'expenses', 14, NULL, '{"expense_number":"EXP-2024-01-008","category":"Transportasi"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-25 15:00:00'),
(4, 'CREATE', 'expenses', 15, NULL, '{"expense_number":"EXP-2024-01-009","category":"Pemeliharaan"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-28 10:00:00'),
(5, 'CREATE', 'expenses', 16, NULL, '{"expense_number":"EXP-2024-01-010","category":"Lain-lain"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-30 14:00:00'),
-- Status Update Logs
(2, 'UPDATE', 'sales', 11, '{"payment_status":"PARTIAL"}', '{"payment_status":"PAID"}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 11:00:00'),
(4, 'UPDATE', 'sales', 19, '{"payment_status":"PARTIAL"}', '{"payment_status":"PAID"}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 14:00:00'),
(2, 'UPDATE', 'contra_bons', 1, '{"status":"PARTIAL"}', '{"status":"PAID"}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 12:00:00'),
(4, 'UPDATE', 'contra_bons', 4, '{"status":"PARTIAL"}', '{"status":"PAID"}', '127.0.0.1', 'Mozilla/5.0', '2024-02-14 14:00:00');

-- =====================================================================
-- PART 5: FINANCIAL SUMMARY VIEWS (Optional - for reporting)
-- =====================================================================

-- View: Monthly Sales Summary
CREATE OR REPLACE VIEW `v_monthly_sales_summary` AS
SELECT 
    DATE_FORMAT(`created_at`, '%Y-%m') AS `month`,
    COUNT(*) AS `total_transactions`,
    SUM(CASE WHEN `payment_type` = 'CASH' THEN 1 ELSE 0 END) AS `cash_transactions`,
    SUM(CASE WHEN `payment_type` = 'CREDIT' THEN 1 ELSE 0 END) AS `credit_transactions`,
    SUM(`total_amount`) AS `total_sales`,
    SUM(`paid_amount`) AS `total_paid`,
    SUM(`total_amount` - `paid_amount`) AS `total_outstanding`
FROM `sales`
GROUP BY DATE_FORMAT(`created_at`, '%Y-%m')
ORDER BY `month` DESC;

-- View: Monthly Purchases Summary
CREATE OR REPLACE VIEW `v_monthly_purchases_summary` AS
SELECT 
    DATE_FORMAT(`tanggal_po`, '%Y-%m') AS `month`,
    COUNT(*) AS `total_po`,
    SUM(CASE WHEN `status` = 'Dipesan' THEN 1 ELSE 0 END) AS `pending_po`,
    SUM(CASE WHEN `status` = 'Sebagian' THEN 1 ELSE 0 END) AS `partial_po`,
    SUM(CASE WHEN `status` = 'Diterima Semua' THEN 1 ELSE 0 END) AS `received_po`,
    SUM(`total_amount`) AS `total_purchases`,
    SUM(`paid_amount`) AS `total_paid`,
    SUM(`total_amount` - `paid_amount`) AS `total_outstanding`
FROM `purchase_orders`
GROUP BY DATE_FORMAT(`tanggal_po`, '%Y-%m')
ORDER BY `month` DESC;

-- View: Monthly Expenses Summary
CREATE OR REPLACE VIEW `v_monthly_expenses_summary` AS
SELECT 
    DATE_FORMAT(`expense_date`, '%Y-%m') AS `month`,
    COUNT(*) AS `total_expenses`,
    `category`,
    SUM(`amount`) AS `total_amount`
FROM `expenses`
GROUP BY DATE_FORMAT(`expense_date`, '%Y-%m'), `category`
ORDER BY `month` DESC, `total_amount` DESC;

-- View: Cash Flow Summary
CREATE OR REPLACE VIEW `v_cash_flow_summary` AS
SELECT 
    DATE_FORMAT(`created_at`, '%Y-%m') AS `month`,
    'CASH IN' AS `cash_type`,
    SUM(`amount`) AS `total_amount`
FROM `payments`
WHERE `type` = 'RECEIVABLE' AND `method` = 'CASH'
GROUP BY DATE_FORMAT(`created_at`, '%Y-%m')

UNION ALL

SELECT 
    DATE_FORMAT(`created_at`, '%Y-%m') AS `month`,
    'CASH OUT' AS `cash_type`,
    SUM(`amount`) AS `total_amount`
FROM `payments`
WHERE `type` = 'PAYABLE' AND `method` = 'CASH'
GROUP BY DATE_FORMAT(`created_at`, '%Y-%m`)

UNION ALL

SELECT 
    DATE_FORMAT(`created_at`, '%Y-%m') AS `month`,
    'EXPENSES' AS `cash_type`,
    SUM(`amount`) AS `total_amount`
FROM `expenses`
WHERE `payment_method` = 'CASH'
GROUP BY DATE_FORMAT(`created_at`, '%Y-%m`)

ORDER BY `month` DESC, `cash_type`;

-- View: Customer Aging Report
CREATE OR REPLACE VIEW `v_customer_aging` AS
SELECT 
    c.id,
    c.code,
    c.name,
    c.phone,
    c.credit_limit,
    c.receivable_balance,
    SUM(CASE WHEN s.payment_status = 'UNPAID' AND s.due_date < CURDATE() THEN s.total_amount - s.paid_amount ELSE 0 END) AS `overdue_30`,
    SUM(CASE WHEN s.payment_status = 'UNPAID' AND s.due_date >= CURDATE() AND s.due_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN s.total_amount - s.paid_amount ELSE 0 END) AS `overdue_60`,
    SUM(CASE WHEN s.payment_status = 'UNPAID' AND s.due_date > DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN s.total_amount - s.paid_amount ELSE 0 END) AS `overdue_90`,
    SUM(CASE WHEN s.payment_status = 'UNPAID' THEN s.total_amount - s.paid_amount ELSE 0 END) AS `total_outstanding`
FROM `customers` c
LEFT JOIN `sales` s ON c.id = s.customer_id AND s.payment_status IN ('UNPAID', 'PARTIAL')
GROUP BY c.id, c.code, c.name, c.phone, c.credit_limit, c.receivable_balance
HAVING `total_outstanding` > 0
ORDER BY `total_outstanding` DESC;

-- View: Supplier Aging Report
CREATE OR REPLACE VIEW `v_supplier_aging` AS
SELECT 
    sup.id,
    sup.code,
    sup.name,
    sup.phone,
    sup.debt_balance,
    SUM(CASE WHEN po.payment_status = 'UNPAID' AND po.tanggal_po < DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN po.total_amount - po.paid_amount ELSE 0 END) AS `overdue_30`,
    SUM(CASE WHEN po.payment_status = 'UNPAID' AND po.tanggal_po >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND po.tanggal_po <= CURDATE() THEN po.total_amount - po.paid_amount ELSE 0 END) AS `overdue_60`,
    SUM(CASE WHEN po.payment_status = 'UNPAID' AND po.tanggal_po > CURDATE() THEN po.total_amount - po.paid_amount ELSE 0 END) AS `overdue_90`,
    SUM(CASE WHEN po.payment_status = 'UNPAID' THEN po.total_amount - po.paid_amount ELSE 0 END) AS `total_outstanding`
FROM `suppliers` sup
LEFT JOIN `purchase_orders` po ON sup.id = po.supplier_id AND po.payment_status IN ('UNPAID', 'PARTIAL')
GROUP BY sup.id, sup.code, sup.name, sup.phone, sup.debt_balance
HAVING `total_outstanding` > 0
ORDER BY `total_outstanding` DESC;

-- =====================================================================
-- END OF FINANCIAL SEED DATA
-- =====================================================================
