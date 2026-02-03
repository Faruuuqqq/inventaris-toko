# 🚀 PHASE 4 PREPARATION - MANUAL BROWSER TESTING GUIDE

**Status**: Ready to start Phase 4  
**Previous Phase**: Phase 3 ✅ Complete  
**Next Task**: Open application in browser and test all features

---

## 📋 PHASE 4 OVERVIEW

Phase 4 will involve:
1. Opening the application in a web browser
2. Logging in with test credentials
3. Testing every feature systematically
4. Monitoring for errors in Network tab and console
5. Documenting all findings

**Duration**: 4-6 hours  
**Scope**: 100+ manual test cases  
**Deliverable**: PHASE4_MANUAL_TEST_RESULTS.md

---

## ✅ PRE-TEST CHECKLIST

Before starting Phase 4:

- [ ] Application is running (check URL: likely http://localhost/inventaris-toko)
- [ ] Database has test data
- [ ] Browser DevTools are open (F12)
- [ ] Network tab is visible and monitoring
- [ ] Console is visible for error monitoring
- [ ] PHASE3_CONTROLLER_VERIFICATION_REPORT.md is accessible for reference

---

## 🔍 WHAT TO TEST IN PHASE 4

### Category 1: Authentication & Setup
- [ ] Login page loads
- [ ] Login with valid credentials works
- [ ] Login with invalid credentials shows error
- [ ] Logout works
- [ ] Dashboard displays after login
- [ ] Settings page is accessible

### Category 2: Master Data (Products, Customers, Suppliers, Warehouses, Salespersons)
For each master data type:
- [ ] List page loads
- [ ] Create form loads
- [ ] Create new record (fill form, submit)
- [ ] New record appears in list
- [ ] Detail page loads
- [ ] Edit form loads
- [ ] Edit existing record
- [ ] Changes saved correctly
- [ ] Dropdown (/getList) works in other forms
- [ ] Delete record
- [ ] Record removed from list

**Specific test**: Supplier dropdown will verify Suppliers::getList() fix works

### Category 3: Sales Transactions
- [ ] Sales list loads
- [ ] Create sales form loads
- [ ] Cash sales form displays
- [ ] Cash sales form submits (check endpoint: /storeCash)
- [ ] Credit sales form displays
- [ ] Credit sales form submits (check endpoint: /storeCredit)
- [ ] New sales appear in list
- [ ] Sales detail page loads
- [ ] Sales can be edited
- [ ] Delivery notes can be generated
- [ ] Stock movements recorded

### Category 4: Purchase Transactions
- [ ] Purchases list loads
- [ ] Create purchase form works
- [ ] Form submits to /store endpoint
- [ ] New purchase in list
- [ ] Purchase receive form works
- [ ] Stock received properly
- [ ] Payable balance updates

### Category 5: Returns Processing
- [ ] Sales returns list loads
- [ ] Create sales return form works
- [ ] Submit sales return (check endpoint)
- [ ] Approval form accessible
- [ ] Approve/reject return
- [ ] Inventory updated
- [ ] Purchase returns work similarly

### Category 6: Finance & Payments
- [ ] Expenses list loads (/info/history/expenses-data AJAX)
- [ ] Create expense form works (/finance/expenses/store)
- [ ] Edit expense form works (/finance/expenses/update/{id})
- [ ] Delete expense works (/finance/expenses/delete/{id})
- [ ] Payments payable list loads (/finance/payments/payable)
- [ ] Payment recording works (/finance/payments/storePayable)
- [ ] Payments receivable list loads (/finance/payments/receivable)
- [ ] Payment receivable recording works (/finance/payments/storeReceivable)
- [ ] Kontra-bon creation works
- [ ] Kontra-bon approval/deletion works

### Category 7: Reporting & History
- [ ] History pages load (/info/history/*)
- [ ] Sales history AJAX loads (/info/history/sales-data) ✅
- [ ] Purchases history AJAX loads (/info/history/purchases-data) ✅
- [ ] Returns history AJAX loads (/info/history/sales-returns-data) ✅
- [ ] Payments history loads ✅
- [ ] Expenses history loads (/info/history/expenses-data) ✅
- [ ] Stock movements history loads (/info/history/stock-movements-data) ✅
- [ ] Toggle hide on sale works (/info/history/toggleSaleHide/{id})
- [ ] Saldo page loads
- [ ] **Stock data endpoint works** (/info/saldo/stock-data) ← This verifies our fix! ✅
- [ ] Stock mutations load (/info/stock/getMutations) ✅
- [ ] Inventory reports generate
- [ ] Analytics display correctly

### Category 8: File Management
- [ ] File upload works
- [ ] Bulk upload works
- [ ] File list displays
- [ ] File download works
- [ ] File view works
- [ ] File delete works

### Category 9: System & Settings
- [ ] Settings page loads
- [ ] Profile update works
- [ ] Store settings update works
- [ ] Password change works
- [ ] Data audit/history accessible
- [ ] System analytics display

---

## 🎯 KEY ENDPOINTS TO VERIFY IN BROWSER

### Critical AJAX Endpoints (will show in Network tab as XHR)

These should all return JSON (status 200):

✅ = Already fixed/verified in Phase 3

1. ✅ GET `/info/history/sales-data` - Sales history table
2. ✅ GET `/info/history/purchases-data` - Purchases history table
3. ✅ GET `/info/history/sales-returns-data` - Returns table
4. ✅ GET `/info/history/purchase-returns-data` - Purchase returns table
5. ✅ GET `/info/history/payments-receivable-data` - Receivable payments table
6. ✅ GET `/info/history/payments-payable-data` - Payable payments table
7. ✅ GET `/info/history/expenses-data` - Expenses table
8. ✅ GET `/info/history/stock-movements-data` - Stock movements table
9. ✅ GET `/master/customers/getList` - Customer dropdown
10. ✅ GET `/master/suppliers/getList` - **Supplier dropdown (NEWLY FIXED!)**
11. ✅ GET `/master/warehouses/getList` - Warehouse dropdown
12. ✅ GET `/master/salespersons/getList` - Salesperson dropdown
13. ✅ GET `/transactions/sales/getProducts` - Product dropdown
14. ✅ GET `/info/saldo/stock-data` - **Stock data (NAMING FIXED!)**
15. ✅ GET `/info/stock/getMutations` - Stock mutations

### Form Submission Endpoints (POST)

Should all redirect with success message (status 302 or 200 with success JSON):

1. POST `/master/customers/store` - Create customer
2. POST `/master/suppliers/store` - Create supplier
3. POST `/master/products/store` - Create product
4. POST `/master/warehouses/store` - Create warehouse
5. POST `/master/salespersons` - Create salesperson
6. POST `/transactions/sales/storeCash` - Create cash sale
7. POST `/transactions/sales/storeCredit` - Create credit sale
8. POST `/transactions/purchases/store` - Create purchase
9. POST `/transactions/sales-returns/store` - Create sales return
10. POST `/transactions/purchase-returns/store` - Create purchase return
11. POST `/finance/expenses/store` - Create expense
12. POST `/finance/kontra-bon/store` - Create kontra-bon
13. POST `/finance/payments/storePayable` - Record payment payable
14. POST `/finance/payments/storeReceivable` - Record payment receivable

---

## 🔧 HOW TO RUN TESTS

### For Each Feature Test:

1. **Open DevTools** (F12)
2. **Go to Network tab**
3. **Perform the action** (click button, fill form, etc.)
4. **Look for any red 404/500 errors** in Network tab
5. **Check console** for JavaScript errors (red in Console tab)
6. **Record result**: ✅ (pass) or ❌ (fail) with error details

### Example Test: Create New Customer

1. Navigate to /master/customers
2. Click "Add Customer" button
3. Fill in form (Name, Phone, Address, Credit Limit)
4. Click "Save"
5. **Check Network tab**: Should see POST `/master/customers/store` with status 200-302
6. **Check result**: New customer should appear in list
7. **Record**: ✅ Passed or ❌ Failed with error message

---

## 📊 EXPECTED RESULTS

### Should See These Success Indicators

✅ All list pages load with data  
✅ All forms submit successfully  
✅ All AJAX calls return JSON  
✅ No 404 errors for defined endpoints  
✅ No 500 errors (server errors)  
✅ No console errors in browser  
✅ Data persists correctly  
✅ Related data updates (stock, balances, etc.)  

### Might See These (Usually OK)

🟡 404 on undefined endpoints (acceptable - they're undefined)  
🟡 Loading spinners/delays (acceptable - data loading)  
🟡 Validation error messages (acceptable - invalid data)  

### Should NOT See

🔴 500 Internal Server Error  
🔴 PHP/Exception errors in console  
🔴 Missing method/undefined function errors  
🔴 Database connection errors  
🔴 Data not saving when form succeeds  

---

## 📝 TESTING PROCESS

### Test Session Setup

```
Time: [when starting]
Tester: [your name]
Application URL: http://localhost/inventaris-toko
Test User: [username]/[password]
Environment: Development/Test
```

### For Each Test

Record:
- **Test Name**: e.g., "Create New Customer"
- **Steps**: What you did
- **Expected Result**: What should happen
- **Actual Result**: What actually happened
- **Status**: ✅ Pass / ⚠️ Warning / ❌ Fail
- **Notes**: Any issues or observations

### Example Log Entry

```
Test: Create New Customer
Steps: 
  1. Navigate to /master/customers
  2. Click "Add Customer"
  3. Fill Name: "PT Maju Jaya", Phone: "081234567890", Credit Limit: "5000000"
  4. Click "Save"
Expected: Customer saved, appears in list
Actual: ✅ Customer saved, appears in list with correct data
Status: ✅ PASS
Notes: Form validation works (tested with empty name - shows error)
```

---

## 🎯 CRITICAL TESTS (Verify Phase 3 Fixes)

### Test 1: Supplier Dropdown Works
**What to test**: Suppliers::getList() method fix

**Steps**:
1. Go to any form that has supplier dropdown (e.g., Create Purchase)
2. Click/expand supplier dropdown
3. Check Network tab for request to `/master/suppliers/getList`
4. Verify response is JSON array of suppliers
5. Select a supplier - should work without error

**Expected**: ✅ Dropdown loads suppliers successfully  
**What we fixed**: Added the missing getList() method to Suppliers controller

---

### Test 2: Saldo Stock Data Loads
**What to test**: Saldo endpoint naming fix

**Steps**:
1. Navigate to /info/saldo (or wherever saldo page is)
2. Check Network tab
3. Look for request to `/info/saldo/stock-data` (kebab-case)
4. Should see successful JSON response (not 404)
5. Stock data should display on page

**Expected**: ✅ Stock data loads without 404 error  
**What we fixed**: Changed endpoint from /stockData (camelCase) to /stock-data (kebab-case)

---

## 📋 ISSUES TO WATCH FOR

### Known Issues to Monitor

1. **404 errors** - If any endpoint returns 404, it's not defined in Routes.php
2. **500 errors** - If any endpoint returns 500, there's a code error in controller
3. **Missing data** - If forms submit but data doesn't appear, check database
4. **Validation issues** - If forms reject valid data, check validation rules
5. **Permission errors** - If some features blocked, check role/permission system

### Phase 2 & 3 Findings to Verify

- ✅ Saldo endpoint naming fixed - verify stock-data loads
- ✅ Suppliers getList() added - verify supplier dropdown works
- ✅ Sales uses type-specific endpoints - verify cash/credit sales both work

---

## 🚀 STARTING PHASE 4

When ready to begin:

1. **Open browser**: Navigate to application URL
2. **Login**: Use test credentials
3. **Open DevTools**: F12 key (open Network + Console tabs)
4. **Create test file**: Copy the testing checklist above
5. **Start testing**: Go through each category systematically
6. **Document findings**: Record all results
7. **When done**: Create PHASE4_MANUAL_TEST_RESULTS.md

---

## 📞 TROUBLESHOOTING

### If You Get 404 Error

1. **Check spelling** of endpoint in URL
2. **Check Routes.php** to verify route is defined
3. **Run `php spark routes`** to see all defined routes
4. **Check controller method** exists
5. **Check that method is public** (not protected/private)

### If You Get 500 Error

1. **Check error logs**: Look in `var/log/` directory
2. **Check browser console**: Right-click → Inspect → Console tab
3. **Check for PHP errors**: Page should show PHP error message
4. **Run application in DEBUG mode** if available
5. **Check database connection**: Verify database is accessible

### If Form Doesn't Submit

1. **Check Network tab**: Did POST request go out?
2. **Check response code**: Was it 200, 302, or error?
3. **Check console**: Any JavaScript errors preventing submission?
4. **Check validation**: Form might be validating client-side first
5. **Check browser console** for AJAX error handling

---

## 🎓 FINAL NOTES

### Why We Do Manual Testing

- Automated tests can't catch everything
- User experience verification
- Real-world data flow testing
- Edge case discovery
- Integration issues between features

### What This Verifies

✅ All code written in Phase 3 actually works  
✅ All endpoints defined in Phase 2 are reachable  
✅ All business logic functions correctly  
✅ All data persists correctly  
✅ All integrations work properly  

### Success Criteria for Phase 4

✅ 100+ test cases executed  
✅ 95%+ tests passing  
✅ No critical errors (500s)  
✅ No missing endpoints (404s on defined routes)  
✅ All features functioning as designed  
✅ No JavaScript console errors  

---

**Ready to start Phase 4? Open your browser and let's test! 🚀**

Next: `PHASE4_MANUAL_TEST_RESULTS.md` (to be created during Phase 4)
