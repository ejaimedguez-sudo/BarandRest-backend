#!/bin/bash
set -euo pipefail

sudo tee /etc/systemd/system/laravel-worker.service > /dev/null <<'SERVICE'
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/barandrest/artisan queue:work --sleep=3 --tries=3 --timeout=90
RestartSec=5

[Install]
WantedBy=multi-user.target
SERVICE

sudo tee /etc/systemd/system/laravel-scheduler.service > /dev/null <<'SERVICE'
[Unit]
Description=Laravel Scheduler Service

[Service]
User=www-data
Group=www-data
Type=oneshot
ExecStart=/usr/bin/php /var/www/barandrest/artisan schedule:run >> /var/www/barandrest/storage/logs/schedule.log 2>&1

[Install]
WantedBy=multi-user.target
SERVICE

sudo tee /etc/systemd/system/laravel-scheduler.timer > /dev/null <<'TIMER'
[Unit]
Description=Run Laravel scheduler every minute

[Timer]
OnCalendar=*-*-* *:*:00
Persistent=true

[Install]
WantedBy=timers.target
TIMER

sudo systemctl daemon-reload
sudo systemctl enable --now laravel-worker.service || true
sudo systemctl enable --now laravel-scheduler.timer || true
sudo systemctl status laravel-worker.service --no-pager || true
sudo systemctl status laravel-scheduler.timer --no-pager || true
