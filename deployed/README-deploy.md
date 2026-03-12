## Ordena Facil - Deploy checklist and server steps

This document lists recommended commands and steps to enable production services on the server after the code has been copied (via Ansible or manually).

1) Place release under `/var/www/barandrest` and set ownership:

   sudo chown -R www-data:www-data /var/www/barandrest

2) Copy your production `.env` (use `.env.production.example` as template) and set a secure `APP_KEY`:

   cp /var/www/barandrest/.env.production.example /var/www/barandrest/.env
   # Edit .env and set values (DB, MAIL credentials, APP_URL), then:
   php artisan key:generate --force

3) Install PHP dependencies and optimize:

   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan db:seed --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

4) Enable `systemd` units (templates located in `systemd/`):

   sudo cp systemd/barandrest-queue.service /etc/systemd/system/
   sudo cp systemd/barandrest-schedule.service /etc/systemd/system/
   sudo cp systemd/barandrest-schedule.timer /etc/systemd/system/
   sudo systemctl daemon-reload
   sudo systemctl enable --now barandrest-queue.service
   sudo systemctl enable --now barandrest-schedule.timer

5) Configure backups (example script in `scripts/db_backup.sh`). Add a cron job or systemd timer calling it daily.

6) HTTPS / Let's Encrypt (example using certbot):

   sudo apt update && sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d your-production-domain.example

7) Monitoring & healthchecks:

   - Use `scripts/check_health.sh` from a monitoring system or cron to verify HTTP and DB.
   - Integrate with uptime monitors (UptimeRobot, Prometheus + Alertmanager, etc.)

8) If you prefer Ansible-based deployment, run the playbook from a controller with Ansible installed. Ensure SSH access and place the deploy key at `deploy/keys/deploy_key`.

If you want, I can try to run these final remote steps once SSH is available on the host and port you provide.
