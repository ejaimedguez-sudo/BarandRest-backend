# Automatización local

Estos scripts automatizan la puesta en marcha del entorno de desarrollo local y las comprobaciones básicas.

Flujo recomendado (1 comando):

Windows (PowerShell):

- `powershell -ExecutionPolicy Bypass -File .\scripts\run_all.ps1` — setup + tests + health check.
- `powershell -ExecutionPolicy Bypass -File .\scripts\pre_release_check.ps1` — validación de configuración segura pre-release.
- `powershell -ExecutionPolicy Bypass -File .\scripts\run_all.ps1 -Start` — además inicia servidor y worker.
- `powershell -ExecutionPolicy Bypass -File .\scripts\run_all.ps1 -SkipTests` — omite tests.

Linux/macOS:

- `chmod +x ./scripts/*.sh` (solo la primera vez, si aplica).
- `./scripts/run_all.sh` — setup + tests + health check.
- `./scripts/pre_release_check.sh` — validación de configuración segura pre-release.
- `./scripts/run_all.sh --start` — además inicia servidor y worker.
- `./scripts/run_all.sh --skip-tests` — omite tests.

Windows (PowerShell):

- `.\scripts\setup_local.ps1` — instala dependencias, copia `.env`, crea `database/database.sqlite`, ejecuta migraciones y seeders y cachea configuraciones.
- `.\scripts\start_local.ps1` — inicia `php artisan serve` y `php artisan queue:work` en segundo plano.

Linux/macOS:

- `./scripts/setup_local.sh` — equivalente POSIX al script de PowerShell.
- `./scripts/start_local.sh` — inicia servidor y worker en background.
- `./scripts/health_check.sh` — ejecuta chequeos básicos de migraciones, rutas y smoke HTTP (`/up`).

Windows (PowerShell):

- `./scripts/health_check.ps1` — equivalente para Windows de los chequeos de salud.
- `./scripts/dev_runtime_check_test.ps1` — inicia runtime (`serve` + `queue` + `scheduler`), ejecuta health check y corre tests (`Feature,Unit`) en un solo comando.
- `./scripts/dev_runtime_check_test_background.ps1` — ejecuta el mismo flujo en segundo plano (PowerShell oculto), devolviendo PID y ruta de log.
- `./scripts/dev_runtime_check_test.ps1 -SkipTests` — ejecuta inicio + health check sin correr tests.
- `./scripts/dev_runtime_check_test.ps1 -StopOnFinish` — al terminar, detiene los procesos gestionados del runtime.
- `./scripts/register_health_check_task.ps1` — registra una tarea programada para ejecutar health checks periódicos.
- `./scripts/unregister_health_check_task.ps1` — elimina la tarea programada de health checks.

Recomendación:

- Asegúrate de tener PHP y Composer en el PATH.
- Para ejecutar los workflows de GitHub Actions localmente necesitas Docker y la herramienta `act`.
- Antes de merge/deploy, ejecuta health checks para confirmar estado operativo del backend.

Tarea programada (Windows):

- Registrar (cada 15 min):

	`powershell -ExecutionPolicy Bypass -File .\scripts\register_health_check_task.ps1 -IntervalMinutes 15`

- Registrar y además ejecutar al inicio del sistema:

	`powershell -ExecutionPolicy Bypass -File .\scripts\register_health_check_task.ps1 -IntervalMinutes 15 -RunAtStartup`

	Nota: en algunos equipos, `-RunAtStartup` puede requerir ejecutar PowerShell como administrador.

- Eliminar:

	`powershell -ExecutionPolicy Bypass -File .\scripts\unregister_health_check_task.ps1`
