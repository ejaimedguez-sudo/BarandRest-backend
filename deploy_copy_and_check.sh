#!/bin/bash
set -e

SRC="/mnt/c/xampp/htdocs/apps/BarandRest/app/Console/Kernel.php"
DST="/var/www/barandrest/app/Console/Kernel.php"

sudo mkdir -p "$(dirname "$DST")"
sudo cp "$SRC" "$DST"
sudo chown -R www-data:www-data /var/www/barandrest/app

cd /var/www/barandrest

composer dump-autoload --optimize
php artisan config:clear || true
php artisan cache:clear || true
php artisan package:discover --ansi || true
php artisan list || true
