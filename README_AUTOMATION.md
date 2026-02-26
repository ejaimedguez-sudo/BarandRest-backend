# Automatización local

Estos scripts automatizan la puesta en marcha del entorno de desarrollo local y las comprobaciones básicas.

Windows (PowerShell):

- `.\scripts\setup_local.ps1` — instala dependencias, copia `.env`, crea `database/database.sqlite`, ejecuta migraciones y seeders y cachea configuraciones.
- `.\scripts\start_local.ps1` — inicia `php artisan serve` y `php artisan queue:work` en segundo plano.

Linux/macOS:

- `./scripts/setup_local.sh` — equivalente POSIX al script de PowerShell.
- `./scripts/start_local.sh` — inicia servidor y worker en background.
- `./scripts/health_check.sh` — ejecuta chequeos básicos de migraciones, rutas y smoke HTTP (`/up`).

Windows (PowerShell):

- `./scripts/health_check.ps1` — equivalente para Windows de los chequeos de salud.

Recomendación:

- Asegúrate de tener PHP y Composer en el PATH.
- Para ejecutar los workflows de GitHub Actions localmente necesitas Docker y la herramienta `act`.
- Antes de merge/deploy, ejecuta health checks para confirmar estado operativo del backend.
