Installation and quick start
===========================

1) Prerrequisitos (Windows local / XAMPP):

- PHP (usando XAMPP recommended) with extensions `pdo`, `pdo_mysql`, `gd`.
- Composer (or use included `composer.phar`).
- MySQL (XAMPP).

2) Setup rápido (desde `backend/`):

PowerShell (recommended):

```powershell
.
cd backend\
scripts\setup.ps1
```

Windows cmd:

```bat
cd backend
scripts\setup.bat
```

Esto instalará dependencias, generará `APP_KEY`, ejecutará migraciones y creará la tabla `jobs`.

3) Variables importantes en `.env`:

- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` — configurar con tu SMTP.
- `MAIL_REPORT_RECIPIENT` — email(s) que recibirán los reportes.
- `QUEUE_CONNECTION=database` para producción y `php artisan queue:work` en background.

4) Ejecutar reporte manual y procesar cola:

```bat
php artisan reports:daily
php artisan queue:work --once
```

5) CI (GitHub Actions): el workflow incluido ejecuta tests y migraciones usando sqlite.
