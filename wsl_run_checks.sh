#!/bin/bash
set -e

echo '--- Checking services ---'
services=(nginx php8.3-fpm mariadb laravel-worker laravel-scheduler)
for s in "${services[@]}"; do
  echo "Service: $s"
  if systemctl is-active --quiet "$s"; then
    echo "  active"
  else
    echo "  inactive or failed"
    systemctl status "$s" --no-pager --full | sed -n '1,8p' || true
  fi
done

echo '--- Restarting PHP-FPM and Nginx ---'
sudo systemctl restart php8.3-fpm nginx || true

echo '--- Fixing ownership & permissions ---'
sudo chown -R www-data:www-data /var/www/barandrest || true
sudo chmod -R ug+rwX /var/www/barandrest/storage /var/www/barandrest/bootstrap/cache || true

echo '--- Composer install & Artisan tasks ---'
cd /var/www/barandrest || exit 0
sudo -u www-data composer install --no-interaction --no-dev --prefer-dist || true
sudo -u www-data php artisan migrate --force || true
sudo -u www-data php artisan config:cache || true
sudo -u www-data php artisan route:cache || true
sudo -u www-data php artisan view:cache || true
sudo -u www-data php artisan storage:link || true
sudo -u www-data php artisan queue:restart || true

echo '--- Running backup script (one run) ---'
sudo /opt/barandrest/db_backup_wrapper.sh || true

echo '--- Nginx config test & reload ---'
sudo nginx -t && sudo systemctl reload nginx || true

echo '--- Removing workspace copy of deploy_key_new ---'
rm -f /mnt/c/xampp/htdocs/apps/BarandRest/deploy_key_new || true

echo '--- HTTP check ---'
curl -I -sS http://127.0.0.1 | sed -n '1,5p' || true

echo '--- DONE ---'
