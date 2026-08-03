# Panduan Deployment Digipangan ke cPanel

Panduan ini berisi langkah-langkah detail untuk melakukan *deployment* aplikasi Laravel (Digipangan) ke cPanel menggunakan arsitektur pemisahan direktori (Gudang vs Etalase). Tujuannya agar aplikasi lebih aman dan folder media tidak tercampur dengan *source code*.

## 1. Arsitektur Folder di cPanel
Kita menggunakan 3 direktori utama agar rapi dan aman:
- **`digipangan_app`** (Berada di Home/Root): Menyimpan *source code* utama Laravel, file `.env`, dan konfigurasi rahasia. Folder ini **TIDAK** dapat diakses publik.
- **`digipangan.id`** (Di dalam `public_html/`): Sebagai *Document Root* (Etalase) yang akan dibaca oleh Web Server (Apache/LiteSpeed). 
- **`digipangan_media`** (Berada di Home/Root): Menyimpan semua file media *upload* yang terpisah dari folder project, agar aman saat update versi atau *pull* dari Git.

---

## 2. Persiapan Server
1. Masuk ke cPanel.
2. Cari menu **MultiPHP Manager** (atau *Select PHP Version*).
3. Pastikan versi PHP untuk domain `digipangan.id` diatur minimal ke **PHP 8.3** (misal: `ea-php83`).
4. Buka **Terminal** di cPanel untuk melakukan perintah-perintah *command line*.

---

## 3. Langkah-Langkah Deployment Awal

### A. Clone Project (Mengunduh Gudang)
Di Terminal cPanel, jalankan perintah berikut untuk mengunduh kode ke dalam folder `digipangan_app`:
```bash
cd ~
git clone https://github.com/darrielmarkerizal/digipangan-merauke.git digipangan_app
```

### B. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env` lalu konfigurasikan:
```bash
cd ~/digipangan_app
cp .env.example .env
```
Edit file `.env` tersebut. Pastikan nilai-nilai ini diatur untuk *production*:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://digipangan.id

# Konfigurasi Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_db_anda
DB_USERNAME=user_db_anda
DB_PASSWORD="password_anda"

# Konfigurasi Custom Media Storage
FILESYSTEM_PUBLIC_ROOT=/home/username_cpanel/digipangan_media
FILESYSTEM_PUBLIC_URI=storage
```
*(Ganti `username_cpanel` dengan *username* asli cPanel Anda).*

### C. Install Dependencies & Database
Karena di *shared hosting* terminal default mungkin membaca versi PHP lama, selalu gunakan lokasi *binary* PHP 8.3 secara *full path*:
```bash
# Jalankan composer install (jika ada composer.phar)
/opt/cpanel/ea-php83/root/usr/bin/php composer.phar install --optimize-autoloader --no-dev

# Generate Key
/opt/cpanel/ea-php83/root/usr/bin/php artisan key:generate

# Jalankan Migrasi & Seeder Database
/opt/cpanel/ea-php83/root/usr/bin/php artisan migrate:fresh --seed --force
```

---

## 4. Setup Etalase (Document Root)

Domain Anda sudah diatur agar membaca direktori `public_html/digipangan.id` di menu *Domains* cPanel. Sekarang kita hubungkan ke aplikasi:

1. Buka **File Manager** > Masuk ke `digipangan_app/public`.
2. **Select All** lalu **Copy** semua isi (file `index.php`, `.htaccess`, folder `build`, dll).
3. Salin/Paste ke dalam folder `/public_html/digipangan.id`.
4. Jika ada file default seperti `index.html` atau `default.php` (Coming Soon) di sana, **hapus**.
5. Klik Kanan > **Edit** file `index.php` yang baru saja disalin ke `public_html/digipangan.id`.
6. Hapus isinya, lalu ganti dengan kode berikut agar menunjuk ke "Gudang":

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../../digipangan_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../../digipangan_app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../../digipangan_app/bootstrap/app.php';

// Memberitahu Laravel letak folder public yang baru
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
```

---

## 5. Konfigurasi Folder Media Eksternal
Agar file yang diupload (gambar, PDF, dll) tersimpan di luar project namun tetap bisa diakses publik:

1. Buat direktori media baru:
   ```bash
   mkdir ~/digipangan_media
   ```
2. Buat **Symlink** (Jalan Tol) dari folder media menuju *Document Root* agar bisa dibaca dari web `https://digipangan.id/storage/...`:
   ```bash
   ln -s /home/username_cpanel/digipangan_media /home/username_cpanel/public_html/digipangan.id/storage
   ```

---

## 6. Cara Update Saat Ada Perubahan Fitur

Jika Anda membuat fitur baru di komputer lokal, cara meng-update-nya di cPanel sangat mudah:

1. Dari terminal cPanel, tarik pembaruan Git:
   ```bash
   cd ~/digipangan_app
   git pull origin main
   ```
2. Salin folder `build` yang baru dari `/digipangan_app/public/build` ke dalam `/public_html/digipangan.id/build`. (Bisa dilakukan manual di File Manager atau lewat perintah).
3. Jika ada pembaruan database, jalankan migrasi:
   ```bash
   /opt/cpanel/ea-php83/root/usr/bin/php artisan migrate --force
   ```
4. Bersihkan *cache* aplikasi:
   ```bash
   /opt/cpanel/ea-php83/root/usr/bin/php artisan optimize:clear
   ```

*Selesai! Aplikasi Anda kini sudah menggunakan standar deployment yang sangat profesional, bersih, dan aman di shared hosting.*
