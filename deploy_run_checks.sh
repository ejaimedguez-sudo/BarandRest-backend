#!/usr/bin/env bash
set -euo pipefail

echo '--- backup restore test: locating latest backup ---'
LATEST=$(ls -1t /var/backups/barandrest/*.sql.gz 2>/dev/null | head -n1 || true)
if [ -z "$LATEST" ]; then
  echo 'No backup files found in /var/backups/barandrest'
else
  echo 'Latest backup:' "$LATEST"
  echo '--- creating temporary DB barandrest_restore_test ---'
  sudo mysql -e "DROP DATABASE IF EXISTS barandrest_restore_test; CREATE DATABASE barandrest_restore_test;"
  echo '--- restoring into barandrest_restore_test (this may take a moment) ---'
  gunzip -c "$LATEST" | sudo mysql barandrest_restore_test
  echo '--- show tables in restored DB ---'
  sudo mysql -e "USE barandrest_restore_test; SHOW TABLES LIMIT 10;"
  echo '--- dropping temporary DB ---'
  sudo mysql -e "DROP DATABASE barandrest_restore_test;"
fi

echo '--- Install & configure Nginx (site without TLS) ---'
if ! command -v nginx >/dev/null 2>&1; then
  sudo apt-get update -y >/dev/null
  sudo DEBIAN_FRONTEND=noninteractive apt-get install -y nginx
fi
sudo tee /etc/nginx/sites-available/barandrest > /dev/null <<'NG'
server {
    listen 80;
    server_name _;
    root /var/www/barandrest/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.ht {
        deny all;
    }
}
NG
sudo ln -sf /etc/nginx/sites-available/barandrest /etc/nginx/sites-enabled/barandrest
sudo nginx -t || true
sudo systemctl restart nginx || true

echo '--- rclone list backups (if configured) ---'
if command -v rclone >/dev/null 2>&1; then
  rclone lsf s3:barandrest/backups/ --max-depth 1 || echo 'rclone listed failed or remote missing'
else
  echo 'rclone not installed'
fi

echo '--- summary ---'
echo 'Nginx site created at /etc/nginx/sites-available/barandrest (no TLS yet)'
echo 'CI workflow added at .github/workflows/ci.yml'
