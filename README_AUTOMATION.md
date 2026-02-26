# Automatización local

Estos scripts automatizan la puesta en marcha del entorno de desarrollo local y las comprobaciones básicas.

Windows (PowerShell):

- `.\scripts\setup_local.ps1` — instala dependencias, copia `.env`, crea `database/database.sqlite`, ejecuta migraciones y seeders y cachea configuraciones.
- `.\scripts\start_local.ps1` — inicia `php artisan serve` y `php artisan queue:work` en segundo plano.

Linux/macOS:

- `./scripts/setup_local.sh` — equivalente POSIX al script de PowerShell.
- `./scripts/start_local.sh` — inicia servidor y worker en background.

Recomendación:

- Asegúrate de tener PHP y Composer en el PATH.
- Para ejecutar los workflows de GitHub Actions localmente necesitas Docker y la herramienta `act`.
