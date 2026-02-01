# 🧪 UI Enhancement Testing Guide

## 📋 **Prerequisites**

✅ Server sudah running di `http://localhost:8080`
✅ Browser modern (Chrome, Firefox, Edge, Safari)
✅ Developer Tools (F12)

---

## 🚀 **Quick Start Testing**

### **1. Buka Aplikasi**

1. Buka browser Anda
2. Navigate ke: `http://localhost:8080`
3. Login jika diperlukan
4. Anda akan melihat Dashboard

---

## 🌙 **Test 1: Dark Mode**

### **Steps:**
1. ✅ Lihat di header kanan atas
2. ✅ Cari tombol toggle dengan icon bulan 🌙
3. ✅ Klik tombol tersebut
4. ✅ **Expected Result:**
   - Background berubah dari terang ke gelap
   - Text berubah warna
   - Transisi smooth (0.3s)
   - Toggle slider bergeser ke kanan
   - Preference tersimpan (refresh page tetap dark)

### **Verify:**
```javascript
// Buka Console (F12 > Console)
// Check current theme:
document.documentElement.getAttribute('data-theme')
// Should return: "dark" or null (light)

// Check saved preference:
localStorage.getItem('theme')
// Should return: "dark" or "light"
```

### **Manual Toggle:**
```javascript
// Di Console:
DarkMode.toggle();  // Toggle mode
DarkMode.enable();  // Force dark
DarkMode.disable(); // Force light
```

---

## 📱 **Test 2: Mobile Responsive**

### **Steps:**
1. ✅ Buka Developer Tools (F12)
2. ✅ Klik icon device toolbar (Ctrl+Shift+M)
3. ✅ Pilih device: iPhone 12 Pro atau resize ke 375px
4. ✅ **Expected Result:**
   - Sidebar tersembunyi
   - Hamburger menu (☰) muncul di header kiri
   - Search bar di header tersembunyi
   - Stats cards jadi 1 kolom

### **Test Hamburger Menu:**
1. ✅ Klik hamburger menu (☰)
2. ✅ **Expected Result:**
   - Sidebar slide in dari kiri
   - Dark overlay muncul
   - Sidebar bisa di-scroll
3. ✅ Klik overlay (area gelap)
4. ✅ **Expected Result:**
   - Sidebar slide out
   - Overlay hilang

### **Test Touch Targets:**
1. ✅ Inspect semua button
2. ✅ **Expected Result:**
   - Minimum size: 44x44px
   - Easy to tap dengan jari

---

## 🔔 **Test 3: Toast Notifications**

### **Method 1: Via Console**
```javascript
// Buka Console (F12)

// Success toast
Toast.success('Data berhasil disimpan!');

// Error toast
Toast.error('Terjadi kesalahan saat menyimpan');

// Warning toast
Toast.warning('Stok hampir habis!');

// Info toast
Toast.info('Ada pembaruan sistem tersedia');

// Custom duration (10 seconds)
Toast.success('Pesan ini 10 detik', 'Title', 10000);
```

### **Method 2: Via Flash Message**
1. ✅ Trigger action yang set flash message (e.g., save data)
2. ✅ **Expected Result:**
   - Toast muncul dari kanan atas
   - Slide in animation
   - Auto-dismiss setelah 5 detik
   - Bisa di-close manual dengan X

### **Verify:**
- ✅ Toast position: top-right
- ✅ Animation: slide-in dari kanan
- ✅ Icon sesuai type (✓ success, ✗ error, ⚠ warning, ℹ info)
- ✅ Color sesuai type
- ✅ Close button berfungsi
- ✅ Auto-dismiss setelah 5 detik

---

## ✅ **Test 4: Form Validation**

### **Create Test Form in Console:**
```javascript
// Paste this in Console (F12)
document.body.innerHTML += `
<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 9999;">
    <h3>Test Form Validation</h3>
    <form data-validate>
        <div class="form-group">
            <label class="form-label required">Email</label>
            <input type="email" class="form-input" required>
            <div class="invalid-feedback">Email tidak valid</div>
        </div>
        
        <div class="form-group">
            <label class="form-label required">Password</label>
            <input type="password" class="form-input" required minlength="8">
            <div class="invalid-feedback">Password minimal 8 karakter</div>
        </div>
        
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-secondary" onclick="this.closest('div').remove()">Close</button>
    </form>
</div>
`;
```

### **Test Validation:**
1. ✅ Leave email empty, click submit
   - **Expected:** Red border, error icon, error message
2. ✅ Type invalid email: "test"
   - **Expected:** Red border on blur
3. ✅ Type valid email: "test@example.com"
   - **Expected:** Green border, checkmark icon
4. ✅ Type short password: "123"
   - **Expected:** Red border, error message
5. ✅ Type valid password: "12345678"
   - **Expected:** Green border, checkmark

---

## 📊 **Test 5: Table Sorting**

### **Create Test Table in Console:**
```javascript
// Paste this in Console
document.body.innerHTML += `
<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 9999; max-width: 800px; width: 90%;">
    <h3>Test Sortable Table</h3>
    <table class="w-full" data-sortable style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f3f4f6;">
                <th data-sortable style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #e5e7eb;">Nama</th>
                <th data-sortable style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #e5e7eb;">Umur</th>
                <th data-sortable style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #e5e7eb;">Gaji</th>
            </tr>
        </thead>
        <tbody>
            <tr><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">Alice</td><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">25</td><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">5000000</td></tr>
            <tr><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">Bob</td><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">30</td><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">7000000</td></tr>
            <tr><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">Charlie</td><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">22</td><td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">4500000</td></tr>
        </tbody>
    </table>
    <button type="button" class="btn btn-secondary" style="margin-top: 1rem;" onclick="this.closest('div').remove()">Close</button>
</div>
`;
const table = document.querySelector('table[data-sortable]');
Advanced.TableEnhancer.makeSortable(table);
```

### **Test:**
1. ✅ Klik header "Nama" → Sort A-Z
2. ✅ Klik lagi → Sort Z-A
3. ✅ Klik "Umur" → Sort by number

---

## 💀 **Test 6: Skeleton Loader**

### **Test in Console:**
```javascript
// Create test
document.body.innerHTML += `
<div id="skeleton-test" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 9999; width: 600px;">
    <h3>Loading...</h3>
    <div id="content"><p>Original content</p></div>
    <button onclick="document.getElementById('skeleton-test').remove()" class="btn btn-secondary" style="margin-top: 1rem;">Close</button>
</div>
`;

// Show skeleton
const content = document.getElementById('content');
Advanced.SkeletonLoader.show(content, 'table', { rows: 5, columns: 4 });

// Hide after 3 seconds
setTimeout(() => Advanced.SkeletonLoader.hide(content), 3000);
```

---

## ✨ **Test 7: Animations**

### **Test in Console:**
```javascript
// Hover effects
document.body.innerHTML += `
<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 9999;">
    <h3>Hover Effects</h3>
    <div style="display: grid; gap: 1rem; margin-top: 1rem;">
        <div class="card hover-lift" style="padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;">Hover Lift</div>
        <div class="card hover-grow" style="padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;">Hover Grow</div>
        <button class="btn btn-primary ripple">Ripple Effect</button>
        <button class="btn btn-primary animate-pulse">Pulsing Button</button>
    </div>
    <button onclick="this.closest('div').remove()" class="btn btn-secondary" style="margin-top: 1rem;">Close</button>
</div>
`;
```

---

## 🎯 **Test 8: Confirm Dialog**

### **Test in Console:**
```javascript
(async () => {
    const confirmed = await ConfirmDialog.show({
        title: 'Test Dialog',
        message: 'Apakah Anda yakin?',
        type: 'warning'
    });
    
    Toast[confirmed ? 'success' : 'info'](confirmed ? 'Confirmed!' : 'Cancelled');
})();
```

---

## ✅ **Quick Test Checklist**

Copy-paste semua code di bawah ke Console untuk test semua fitur sekaligus:

```javascript
// Test All Features
console.log('🧪 Testing UI Enhancements...\n');

// 1. Dark Mode
console.log('1. Dark Mode:', typeof DarkMode !== 'undefined' ? '✅' : '❌');

// 2. Toast
console.log('2. Toast:', typeof Toast !== 'undefined' ? '✅' : '❌');
Toast.success('Toast test berhasil!');

// 3. Advanced Features
console.log('3. Advanced:', typeof Advanced !== 'undefined' ? '✅' : '❌');

// 4. Form Validation
console.log('4. Validation:', typeof FormValidation !== 'undefined' ? '✅' : '❌');

// 5. UI Components
console.log('5. UI Components:', typeof UI !== 'undefined' ? '✅' : '❌');

// 6. HTMX
console.log('6. HTMX:', typeof htmx !== 'undefined' ? '✅' : '❌');

console.log('\n✅ All core features loaded!');
```

---

## 📝 **Manual Browser Testing**

### **Step-by-Step:**

1. **Buka** `http://localhost:8080` di browser
2. **Buka Console** (F12 > Console)
3. **Copy-paste** test code di atas
4. **Lihat hasil** - Semua harus ✅
5. **Test dark mode** - Klik toggle di header
6. **Test mobile** - Resize browser ke 375px
7. **Test toast** - Run: `Toast.success('Test')`
8. **Test validation** - Use test form code
9. **Test table** - Use test table code
10. **Done!** ✨

---

## 🎉 **Success!**

Jika semua test passed, UI enhancement berhasil! 🚀

**Server running at:** http://localhost:8080