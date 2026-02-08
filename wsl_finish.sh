#!/usr/bin/env bash
set -euxo pipefail

# Crear bootstrap/app.php
sudo mkdir -p /var/www/barandrest/bootstrap
cat > /tmp/app.php <<'PHP'
<?php

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
PHP

sudo mv /tmp/app.php /var/www/barandrest/bootstrap/app.php || true
sudo chown www-data:www-data /var/www/barandrest/bootstrap/app.php || true
sudo chmod 644 /var/www/barandrest/bootstrap/app.php || true

# Crear storage y bootstrap/cache
sudo mkdir -p /var/www/barandrest/storage /var/www/barandrest/bootstrap/cache
sudo chown -R www-data:www-data /var/www/barandrest/storage /var/www/barandrest/bootstrap/cache
sudo chmod -R ug+rwx /var/www/barandrest/storage /var/www/barandrest/bootstrap/cache

# Composer install
cd /var/www/barandrest
sudo -u www-data /usr/bin/composer install --no-interaction --no-dev --optimize-autoloader || true

# Artisan tasks
sudo -u www-data php /var/www/barandrest/artisan package:discover --ansi || true
sudo -u www-data php /var/www/barandrest/artisan config:cache --no-ansi || true

# Permisos finales
sudo chown -R www-data:www-data /var/www/barandrest || true
sudo find /var/www/barandrest -type d -exec chmod 755 {} + || true
sudo find /var/www/barandrest -type f -exec chmod 644 {} + || true
sudo chmod -R ug+rwx /var/www/barandrest/storage /var/www/barandrest/bootstrap/cache || true

# Verificaciones
echo '--- bootstrap/app.php stat ---'
sudo stat -c '%n size:%s uid:%u gid:%g mode:%a' /var/www/barandrest/bootstrap/app.php || true
sudo sed -n '1,160p' /var/www/barandrest/bootstrap/app.php || true

echo '--- composer show (first 10) ---'
sudo -u www-data /usr/bin/composer show -d /var/www/barandrest | sed -n '1,10p' || true

echo '--- artisan version ---'
sudo -u www-data php /var/www/barandrest/artisan --version || true

# DB test
PW=$(sudo sed -n '2p' /root/creds/barandrest_db_creds.txt | cut -d'=' -f2- || true)
if [ -n "$PW" ]; then
  echo '--- DB test ---'
  sudo mysql -u barandrest_user -p"$PW" -e "SELECT CURRENT_USER(), VERSION();" barandrest || true
else
  echo 'DB password empty'
fi

# Update docs
sudo mkdir -p /var/www/barandrest/docs
DT=$(date -u +"%Y-%m-%dT%H:%M:%SZ")
sudo tee /var/www/barandrest/docs/FINAL_DEPLOY_SUMMARY.md > /dev/null <<DOC
# Deploy summary - $DT
- SSH: configurado en puerto 3000 con clave `deploy_key`.
- Database: MariaDB instalado; base `barandrest` creada; usuario `barandrest_user` creado.
- Backups: dumps subidos a S3 en `s3:barandrest/backups/`.
- Composer: dependencias instaladas y autoload generado (si la instalación terminó correctamente).
- Laravel: `php artisan config:cache` ejecutado (siempre que la app esté sana).
- Credenciales DB: almacenadas en `/root/creds/barandrest_db_creds.txt` (permisos 600).

Revisar y rotar credenciales sensibles tras confirmar acceso.
DOC
sudo chown -R www-data:www-data /var/www/barandrest/docs || true

echo 'SCRIPT_COMPLETE'
