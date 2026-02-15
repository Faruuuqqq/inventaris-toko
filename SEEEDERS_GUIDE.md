# Cara Menjalankan Seeders Baru

## Seeders Baru yang Ditambahkan:

1. **PurchaseOrdersSeeder.php** - Data Purchase Orders (PO) dan Purchase Order Items
2. **DeliveryNotesSeeder.php** - Data Delivery Notes (Surat Jalan) dan Delivery Note Items
3. **AuditLogsSeeder.php** - Data Audit Logs untuk tracking aktivitas
4. **FinanceReportingSeeder.php** - Data Contra Bons dan Financial Views untuk reporting

---

## Cara Menjalankan:

### Opsi 1: Run Semua Seeders (Rekomendasi)
```bash
php spark db:seed
```
Ini akan menjalankan SEMUA seeder secara berurutan:
1. InitialDataSeeder
2. Phase4TestDataSeeder
3. SalesDataSeeder
4. StockMutationsSeeder
5. SalesReturnsSeeder
6. PurchaseReturnsSeeder
7. PaymentsSeeder
8. ExpensesSeeder
9. **PurchaseOrdersSeeder** (BARU)
10. **DeliveryNotesSeeder** (BARU)
11. **AuditLogsSeeder** (BARU)
12. **FinanceReportingSeeder** (BARU)

### Opsi 2: Run Seeder Spesifik
```bash
# Hanya Purchase Orders
php spark db:seed PurchaseOrdersSeeder

# Hanya Delivery Notes
php spark db:seed DeliveryNotesSeeder

# Hanya Audit Logs
php spark db:seed AuditLogsSeeder

# Hanya Finance Reporting (Contra Bons & Views)
php spark db:seed FinanceReportingSeeder
```

---

## Data yang Ditambahkan:

### PurchaseOrdersSeeder
- **10 Purchase Orders** dengan berbagai status:
  - 4 PO Fully Received (Diterima Semua)
  - 3 PO Partially Received (Sebagian)
  - 3 PO Pending (Dipesan)
- **30 Purchase Order Items**
- **Stock Mutations IN** otomatis dari penerimaan barang
- Update **Supplier Debt Balance** untuk PO yang belum lunas

### DeliveryNotesSeeder
- **15-20 Delivery Notes** (Surat Jalan) untuk sales
- **Delivery Note Items** sesuai dengan item di sales
- Berbagai status:
  - Delivered (Diterima) - 15 DN
  - In Transit (Dikirim) - 5 DN
  - Pending (Menunggu) - 5 DN
- Data driver dan kendaraan

### AuditLogsSeeder
- **100+ Audit Logs** untuk tracking aktivitas:
  - 25% CREATE logs (pembuatan data baru)
  - 35% UPDATE logs (update data)
  - 25% LOGIN/LOGOUT logs (aktivitas auth)
  - 15% VIEW logs (view data)
- Data IP address dan User Agent

### FinanceReportingSeeder
- **3-5 Contra Bons** (Credit Notes) untuk customer dengan tagihan
- **7 Financial Views** untuk reporting:
  1. `v_monthly_sales_summary` - Summary sales bulanan
  2. `v_monthly_purchases_summary` - Summary pembelian bulanan
  3. `v_monthly_expenses_summary` - Summary expenses bulanan
  4. `v_cash_flow_summary` - Summary cash flow
  5. `v_customer_aging` - Aging report piutang customer
  6. `v_supplier_aging` - Aging report hutang supplier
  7. `v_financial_summary` - Summary financial lengkap

---

## Cara Menggunakan Financial Views:

### Query ke Views:

```sql
-- Monthly Sales Summary
SELECT * FROM v_monthly_sales_summary;

-- Monthly Purchases Summary
SELECT * FROM v_monthly_purchases_summary;

-- Monthly Expenses Summary
SELECT * FROM v_monthly_expenses_summary;

-- Cash Flow Summary
SELECT * FROM v_cash_flow_summary;

-- Customer Aging Report
SELECT * FROM v_customer_aging;

-- Supplier Aging Report
SELECT * FROM v_supplier_aging;

-- Financial Summary
SELECT * FROM v_financial_summary;
```

### Di Controller:

```php
// Model untuk Financial Views
class FinanceModel extends Model {
    protected $table = 'v_financial_summary';
    
    public function getMonthlySalesSummary() {
        return $this->db->table('v_monthly_sales_summary')
            ->orderBy('month', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    public function getCustomerAging() {
        return $this->db->table('v_customer_aging')
            ->where('total_outstanding >', 0)
            ->orderBy('total_outstanding', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    // ... methods lain
}
```

---

## Troubleshooting:

### Error: "No suppliers found"
**Solusi**: Jalankan `InitialDataSeeder` dulu sebelum `PurchaseOrdersSeeder`

### Error: "No sales found"
**Solusi**: Jalankan `SalesDataSeeder` dulu sebelum `DeliveryNotesSeeder`

### Error: "View already exists"
**Solusi**: Drop view manual atau jalankan seeder lagi (view akan di-drop otomatis)

### Error: Foreign key constraint
**Solusi**: Jalankan seeder dalam urutan yang benar (lihat opsi 1 di atas)

---

## Rekomendasi:

### Untuk Development:
```bash
# Hapus dan ulangi semua data
php spark db:seed
```

### Untuk Testing Specific Module:
```bash
# Test modul purchases saja
php spark db:seed PurchaseOrdersSeeder

# Test modul logistics saja
php spark db:seed DeliveryNotesSeeder

# Test modul finance saja
php spark db:seed FinanceReportingSeeder
```

### Untuk Production:
Gunakan **SQL files** (database_full_seed.sql dan database_finance_seed.sql) bukan PHP seeders

---

## Summary Setelah Menjalankan Semua Seeders:

- **Users**: 5
- **Categories**: 10
- **Warehouses**: 3
- **Products**: 50
- **Product Stocks**: 150
- **Customers**: 20
- **Suppliers**: 10
- **Salespersons**: 5
- **Sales**: 30
- **Sale Items**: 80
- **Purchase Orders**: 10
- **Purchase Order Items**: 30
- **Stock Mutations**: 100+
- **Sales Returns**: 3
- **Purchase Returns**: 2
- **Payments**: 20+
- **Expenses**: 25+
- **Delivery Notes**: 15-20
- **Delivery Note Items**: 50-60
- **Contra Bons**: 3-5
- **Audit Logs**: 100+
- **Financial Views**: 7

Total: **~700+ records**

---

## Notes:

1. **Password**: Semua user menggunakan password: `test123`
2. **Tanggal**: Data di-generate untuk 90 hari terakhir
3. **Status**: Berbagai status untuk realism
4. **Views**: Views akan di-update setiap kali ada perubahan data
5. **Audit Logs**: Mencatat CREATE, UPDATE, DELETE, LOGIN, LOGOUT, dan VIEW

---

**Created**: February 14, 2026
**Seeders Added**: 4 new seeders
**Total Seeders**: 12 seeders
