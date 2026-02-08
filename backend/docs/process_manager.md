# Process manager examples (worker)

This file shows example configurations to run a persistent Laravel queue worker using `supervisor` (Linux) or `sc.exe`/`nssm` on Windows. Adjust paths and user/service names to your environment.

## Supervisor (systemd) + SupervisorD example (Linux)

1. Install supervisor (Debian/Ubuntu):

```bash
sudo apt update
sudo apt install supervisor
```

2. Create a program file `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --timeout=0
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
stopwaitsecs=3600
```

3. Reload supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Systemd unit example

Create `/etc/systemd/system/laravel-worker.service`:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --timeout=0

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-worker
sudo systemctl start laravel-worker
```

## Windows (nssm) example

1. Download `nssm` (Non-Sucking Service Manager) and place it in PATH.

2. Create a service:

```powershell
nssm install LaraveQueueWorker "C:\\xampp\\php\\php.exe" "C:\\xampp\\htdocs\\apps\\BarandRest\\backend\\artisan" "queue:work --sleep=3 --tries=3 --timeout=0"
nssm set LaraveQueueWorker AppDirectory C:\xampp\htdocs\apps\BarandRest\backend
nssm start LaraveQueueWorker
```

## Notes
- Ensure the `QUEUE_CONNECTION` and `MAIL_*` settings in `.env` are correctly set for production.
- Rotate logs and monitor worker status.
