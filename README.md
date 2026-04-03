# 📱 Mobile Shop — Screen & Customer Tracker

A Laravel 10 web app to manage your screen stock, customers, and repair jobs.

---

## ✅ Features

- **Categories** — iPhone, Samsung, Huawei, Xiaomi, OnePlus, etc.
- **Screen Stock** — Track Soft (OLED) & Hard (Original) screens per model
- **Filters** — Filter by brand, type, quality, stock status, search
- **Customers** — Save customer details and view their repair history
- **Repairs** — Log every repair, link screen used (auto-deducts from stock)
- **Dashboard** — Live stats, low stock alerts, recent repairs, revenue
- **Sample data** — Ready to use with 15 screens, 6 customers, 6 repairs

---

## 🚀 Quick Setup (5 Steps)

### Requirements
- PHP 8.1 or higher
- Composer
- No database needed (uses SQLite by default)

---

### Step 1 — Install PHP & Composer

**On Windows:**
1. Download PHP: https://windows.php.net/download/ (Thread Safe, x64)
2. Extract to `C:\php`, add to PATH
3. Download Composer: https://getcomposer.org/Composer-Setup.exe

**On Mac:**
```bash
brew install php composer
```

**On Ubuntu/Linux:**
```bash
sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-sqlite3 php8.2-zip
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

### Step 2 — Extract & Enter the Project

```bash
cd mobileshop
```

---

### Step 3 — Install Dependencies

```bash
composer install
```

---

### Step 4 — Configure Environment

```bash
# Copy the example env file
cp .env.example .env

# Generate app key
php artisan key:generate
```

---

### Step 5 — Set Up Database & Start

```bash
# Create the SQLite database file
touch database/database.sqlite

# Run migrations (creates all tables)
php artisan migrate

# (Optional) Load sample data
php artisan db:seed

# Start the server
php artisan serve
```

Then open your browser at: **http://localhost:8000**

---

## 🗄️ Using MySQL Instead of SQLite

Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mobileshop
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

Create the database first:
```sql
CREATE DATABASE mobileshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then run: `php artisan migrate --seed`

---

## 📁 Project Structure

```
mobileshop/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── CategoryController.php
│   │   ├── ScreenController.php
│   │   ├── CustomerController.php
│   │   └── RepairController.php
│   └── Models/
│       ├── Category.php
│       ├── Screen.php
│       ├── Customer.php
│       └── Repair.php
├── database/
│   ├── migrations/         ← Creates all tables
│   └── seeders/            ← Sample data
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── dashboard/
│   ├── categories/
│   ├── screens/
│   ├── customers/
│   └── repairs/
└── routes/web.php
```

---

## 🔧 How It Works

1. **Add Categories** — Create brands (iPhone, Samsung, etc.) with icons and colours
2. **Add Screens** — Add each screen model under its brand with stock count, type (Soft/Hard), price
3. **Add Customers** — Save customer name, phone, email
4. **Log a Repair** — Select customer + screen used → stock automatically reduces
5. **Dashboard** — See everything at a glance: active jobs, low stock, revenue

---

## 💡 Tips

- Run `php artisan db:seed` any time to reload sample data (wipes existing data first if you add `--fresh`: `php artisan migrate:fresh --seed`)
- To reset everything: `php artisan migrate:fresh --seed`
- Storage permissions fix (Linux/Mac): `chmod -R 775 storage bootstrap/cache`
