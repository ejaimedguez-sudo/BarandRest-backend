#!/bin/bash
set -e
services=(nginx php8.3-fpm mariadb laravel-worker laravel-scheduler)
for s in "${services[@]}"; do
  printf "%s: " "$s"
  systemctl is-active --quiet "$s" && echo "active" || echo "inactive"
done
sudo -u www-data php /var/www/barandrest/artisan schedule:run --verbose || true
curl -I -sS http://127.0.0.1 | sed -n '1p' || true
