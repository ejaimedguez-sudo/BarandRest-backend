# BarAndRest — Deployment Guide

This document collects step-by-step deployment and hardening instructions to run BarAndRest in production.

1) Prerequisites
- PHP 8.2 with required extensions: pdo_mysql, mbstring, openssl, xml, gd, zip, bcmath
- MySQL (or MariaDB) server
- Composer
- Supervisor or systemd for queue worker
- Web server (Nginx or Apache) with HTTPS (Let's Encrypt or commercial cert)

2) Prepare repository on server

```bash
cd /var/www
git clone <your-repo-url> barandrest
cd barandrest/backend
composer install --no-dev --optimize-autoloader
cp .env.example .env
```

3) Environment variables
- Edit `.env` and set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`
- Set DB credentials (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- Configure mail (`MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`)
- Set `MAIL_REPORT_RECIPIENT` to a real address (comma separated)
- Ensure `QUEUE_CONNECTION=database`
- Set `SESSION_SECURE_COOKIE=true` and `SESSION_HTTP_ONLY=true`

Example SMTP settings to add to `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="BarAndRest"
MAIL_REPORT_RECIPIENT=reports@example.com
```

Process manager examples and detailed worker instructions are available in `docs/process_manager.md`.

4) App key, storage and migrations

```bash
php artisan key:generate --force
php artisan migrate --force
php artisan queue:table || true
php artisan migrate --force
php artisan storage:link || true
```

5) Queue worker (systemd example)

Create `/etc/systemd/system/laravel-queue.service`:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/barandrest/backend/artisan queue:work --sleep=3 --tries=3 --timeout=120
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=barandrest-queue

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
```

6) Web server (Nginx) minimal config

Use a server block pointing to `backend/public` and ensure `try_files $uri $uri/ /index.php?$query_string;` is set. Configure HTTPS.

7) Security checklist
- Do not commit `.env`; use deployment secrets or environment store.
- Use HTTPS and HSTS.
- Limit DB access by IP.
- Use least-privileged DB user.
- Run `composer audit` regularly and apply security updates.
- Rotate backups and purge `storage/app/reports` periodically.

8) Verify report system

```bash
php artisan reports:daily
php artisan queue:work --once
ls storage/app/reports
```

9) Troubleshooting
- If mail fails, use `MAIL_MAILER=log` to inspect generated mails locally.
- Check logs in `storage/logs/laravel.log` and system journal for queue worker service.

10) CI/CD notes
- The included GitHub Actions workflow runs tests and migrations. Configure secrets in GitHub for DB and MAIL when enabling integration tests.

11) Rollback
- Use your deployment tool (capistrano, forge, ansible) to roll back; ensure DB migrations are reversible or backup DB prior to deploy.
