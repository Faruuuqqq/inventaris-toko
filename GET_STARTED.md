# 🚀 GET STARTED - INVENTARIS TOKO

**Welcome!** Ini panduan cepat untuk mulai dengan Inventaris Toko.

---

## ⚡ 3 MENIT SETUP

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Database
```bash
# Copy template
cp env-example .env

# Edit .env (set database credentials)
# DATABASE_HOSTNAME=localhost
# DATABASE_DATABASE=toko_distributor
# DATABASE_USERNAME=root
# DATABASE_PASSWORD=(empty)
```

### 3. Create Database & Seed Data
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS toko_distributor CHARACTER SET utf8mb4;"

# Run migrations
php spark migrate

# Seed test data (OPTIONAL - recommended for testing)
php spark db:seed DatabaseSeeder
```

---

## ▶️ RUN APPLICATION

```bash
php spark serve
```

Open: http://localhost:8080

---

## 🔑 LOGIN

```
Username: owner
Password: password
```

*Other test users: admin, sales, gudang (all with password: password)*

---

## 📚 NEXT STEPS

### 👈 **Read These Files**

1. **README.md** (in root) - Project overview
2. **docs/INDEX.md** - Documentation guide
3. **docs/SEEDING_QUICK_REFERENCE.md** - Seeding commands

### 🧪 **Testing**

- Manual: Login & explore dashboard
- API: Import `docs/api/Inventaris_Toko_API.postman_collection.json` to Postman

### 📖 **Learning**

- **Development**: `docs/DEVELOPER_ONBOARDING_GUIDE.md`
- **API**: `docs/COMPREHENSIVE_API_DOCUMENTATION.md`
- **Seeding**: `docs/SEEDING_GUIDE.md`

---

## 🎯 COMMON COMMANDS

```bash
# Database
php spark migrate                           # Run migrations
php spark migrate:fresh --seed              # Reset + seed
php spark db:seed DatabaseSeeder            # Seed data

# Development
php spark serve                             # Start server
./vendor/bin/phpunit                        # Run tests
php spark make:model YourModel              # Generate model
php spark make:controller YourController    # Generate controller

# Generator
php spark make:migration CreateTableName    # Generate migration
php spark make:seeder CustomSeeder          # Generate seeder
```

---

## 📂 KEY FOLDERS

```
app/              - Core code (Controllers, Models, Views)
public/           - Web files (CSS, JS, Images)
database/         - Migrations & Seeds
docs/             - Documentation (65 files!)
vendor/           - PHP libraries
writable/         - Logs & Cache
```

---

## ❓ TROUBLESHOOTING

### Database Error?
```bash
# Check database exists
mysql -u root -p -e "SHOW DATABASES;"

# Run migrations
php spark migrate
```

### Port 8080 Already in Use?
```bash
# Run on different port
php spark serve --port 8081
```

### Permission Error?
```bash
# Fix writable folder
chmod 755 writable/
```

### Need More Help?
→ See `docs/INDEX.md` for full documentation index

---

## 🌟 WHAT'S INCLUDED

✅ 222 verified routes  
✅ 95+ API endpoints  
✅ 4 database seeders (100+ test records)  
✅ 65 documentation files  
✅ Postman collection  
✅ Complete user roles  
✅ Production ready  

---

**Ready to go?** Start with `php spark serve` and login as `owner/password`! 🎉
