#!/bin/bash
set -euo pipefail

# Copiar wrapper al servidor
sudo mkdir -p /opt/barandrest
sudo cp /mnt/c/xampp/htdocs/apps/BarandRest/db_backup_wrapper.sh /opt/barandrest/db_backup_wrapper.sh
sudo chmod +x /opt/barandrest/db_backup_wrapper.sh

# Cron job
sudo tee /etc/cron.d/barandrest_backup > /dev/null <<'CRON'
# Daily DB backup at 03:00
0 3 * * * root /opt/barandrest/db_backup_wrapper.sh >> /var/log/barandrest/db_backup.log 2>&1
CRON
sudo chmod 644 /etc/cron.d/barandrest_backup
sudo mkdir -p /var/log/barandrest
sudo touch /var/log/barandrest/db_backup.log
sudo chown root:root /var/log/barandrest/db_backup.log || true

# Install UFW and fail2ban
sudo apt-get update -y
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y ufw fail2ban
sudo ufw allow 80/tcp || true
sudo ufw allow 443/tcp || true
sudo ufw allow 3000/tcp || true
sudo ufw --force enable || true
sudo systemctl enable --now fail2ban || true

# Logrotate
sudo tee /etc/logrotate.d/barandrest > /dev/null <<'LR'
/var/www/barandrest/storage/logs/*.log {
    weekly
    rotate 4
    compress
    missingok
    notifempty
    copytruncate
    create 0640 www-data www-data
}
LR

# Rotate DB password for barandrest_user
NEWPASS=$(openssl rand -base64 24)
sudo mysql -e "ALTER USER 'barandrest_user'@'localhost' IDENTIFIED BY '${NEWPASS}'; FLUSH PRIVILEGES;"
sudo mkdir -p /root/creds
sudo tee /root/creds/barandrest_db_creds.txt > /dev/null <<CRED
DB_USERNAME=barandrest_user
DB_PASSWORD=${NEWPASS}
CRED
sudo chmod 600 /root/creds/barandrest_db_creds.txt

# Update .env
sudo sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${NEWPASS}/" /var/www/barandrest/.env || true

# Restart worker
sudo systemctl restart laravel-worker.service || true

# Test DB connection
if mysql -u"barandrest_user" -p"${NEWPASS}" -e "SELECT 1" barandrest >/dev/null 2>&1; then
  echo 'DB_OK'
else
  echo 'DB_FAIL' >&2
fi
