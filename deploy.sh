#!/bin/bash

echo "Memulai Deployment Digipangan..."

USER_HOME=$(eval echo ~$(whoami))
DIR="$USER_HOME/digipangan_app"
DOC_ROOT="$USER_HOME/public_html/digipangan.id"
PHP_BIN="/opt/cpanel/ea-php83/root/usr/bin/php"

cd $DIR

echo "Menarik pembaruan terbaru dari GitHub..."
git pull origin main

echo "Menyalin aset frontend (build & images)..."
rm -rf $DOC_ROOT/build
cp -R public/build $DOC_ROOT/build

if [ -d "public/images" ]; then
    rm -rf $DOC_ROOT/images
    cp -R public/images $DOC_ROOT/images
fi

echo "Menjalankan migrasi database..."
$PHP_BIN artisan migrate --force

echo "Membersihkan cache aplikasi..."
$PHP_BIN artisan optimize:clear

echo "Deployment selesai."
