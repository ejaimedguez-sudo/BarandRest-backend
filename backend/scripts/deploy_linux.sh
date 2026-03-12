#!/usr/bin/env bash
set -euo pipefail

# Simple deployment helper for Linux servers (run on the target server)
# Usage: sudo ./deploy_linux.sh /path/to/backend_release_clean.zip /var/www/ordena-facil

ZIP_PATH="$1"
TARGET_DIR="$2"

echo "Deploying $ZIP_PATH to $TARGET_DIR"

mkdir -p "$TARGET_DIR"
unzip -o "$ZIP_PATH" -d "$TARGET_DIR"
cd "$TARGET_DIR"

if [ -f composer.phar ]; then
  php composer.phar install --no-dev --optimize-autoloader
else
  composer install --no-dev --optimize-autoloader
fi

if [ ! -f .env ]; then
  cp .env.example .env
fi

php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link || true

# Set permissions - adjust user/group as needed
chown -R www-data:www-data "$TARGET_DIR"
find "$TARGET_DIR" -type f -exec chmod 644 {} \;
find "$TARGET_DIR" -type d -exec chmod 755 {} \;

echo "Deployed. To run worker use systemd/supervisor or: php artisan queue:work --sleep=3 --tries=3"
