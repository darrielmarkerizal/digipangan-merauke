#!/bin/bash

echo "🚀 Memulai Deployment Digipangan..."

# Ambil direktori user saat ini secara otomatis (misal: sikz2191)
USER_HOME=$(eval echo ~$(whoami))
DIR="$USER_HOME/digipangan_app"
DOC_ROOT="$USER_HOME/public_html/digipangan.id"
PHP_BIN="/opt/cpanel/ea-php83/root/usr/bin/php"

# Pastikan kita berada di dalam folder project
cd $DIR

echo "📦 Menarik pembaruan terbaru dari GitHub..."
git pull origin main

echo "🎨 Menyalin folder build frontend ke Etalase..."
# Hapus folder build lama di public_html agar bersih, lalu salin yang baru
rm -rf $DOC_ROOT/build
cp -R public/build $DOC_ROOT/build

echo "🔄 Menjalankan migrasi database (jika ada perubahan tabel)..."
$PHP_BIN artisan migrate --force

echo "🧹 Membersihkan cache konfigurasi Laravel..."
$PHP_BIN artisan optimize:clear

echo "✅ Deployment selesai dengan sukses! Website Anda sudah terbarui."
