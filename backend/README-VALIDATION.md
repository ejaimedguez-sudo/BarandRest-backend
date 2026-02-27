# Validación rápida local

## Preflight automatizado (recomendado)

Desde `backend/`, ejecuta todo el procedimiento con un solo comando:

```powershell
./scripts/preflight.ps1
```

O usando batch:

```cmd
scripts\preflight.bat
```

Opciones útiles:

- Omitir npm install: `./scripts/preflight.ps1 -SkipNpm`
- Omitir auditorías: `./scripts/preflight.ps1 -SkipAudit`
- Omitir prueba HTTP: `./scripts/preflight.ps1 -SkipHttp`
- Cambiar puerto de prueba HTTP: `./scripts/preflight.ps1 -Port 8080`

Pasos para validar que la aplicación funciona en un entorno local (Windows XAMPP):

1. Instalar dependencias

```bash
php composer.phar install
```

2. Copiar `.env` y generar key (si no existe)

```bash
copy .env.example .env
php artisan key:generate
```

3. Ejecutar migraciones y seeders

```bash
php artisan migrate --force
php artisan db:seed
```

4. Crear enlace storage (si aún no existe)

```bash
php artisan storage:link
```

5. Iniciar servidor de desarrollo

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

6. Iniciar worker de colas (en otra terminal)

Windows (PowerShell):
```powershell
php artisan queue:work --sleep=3 --tries=3
```

Linux/macOS:
```bash
php artisan queue:work --sleep=3 --tries=3
```

7. Generar reportes manualmente (prueba)

```bash
php artisan reports:daily
```

8. Probar dashboard:
- URL: `http://127.0.0.1:8000/dashboard` (si montas la vista en una ruta web)
- Endpoint API (JSON): `GET /api/dashboard/metrics` con header `X-API-KEY` igual a `DASHBOARD_API_KEY` en tu `.env`.

9. Comprobar logs y reportes generados en `storage/app/reports/` y correos en `storage/logs/laravel.log` (si `MAIL_MAILER=log`).

Notas:
- Para producción configura `MAIL_*` reales y un worker persistente (systemd/supervisor).
- Ajusta `DASHBOARD_API_KEY` en `.env` con un valor seguro.
