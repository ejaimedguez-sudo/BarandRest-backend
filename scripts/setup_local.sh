#!/usr/bin/env bash
set -euo pipefail
echo "== Setup local development environment (Linux/macOS) =="
if [ -f composer.phar ]; then
  COMPOSER="php composer.phar"
else
  COMPOSER="composer"
fi
if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
  echo "Created .env from .env.example"
fi
echo "Running composer install..."
$COMPOSER install --no-interaction --prefer-dist
echo "Generating app key..."
php artisan key:generate || true
mkdir -p database
DB_FILE="database/database.sqlite"
if [ ! -f "$DB_FILE" ]; then
  touch "$DB_FILE"
  echo "Created $DB_FILE"
fi
echo "Running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --force
echo "Clearing and caching configs..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "Setup complete. Start app with ./scripts/start_local.sh"
