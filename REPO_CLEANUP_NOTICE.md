REPO CLEANUP & ACTIONS REQUIRED
=================================

Resumen de acciones automáticas realizadas:
- Eliminé backups temporales y scripts locales no funcionales del repositorio.
- Corregí el workflow root (`.github/workflows/ci.yml`) y lo puse a ejecutar en `backend`.
- Hice idempotente `DatabaseSeeder` para evitar errores en seed repetidos.
- Instalé Composer localmente en `backend`, ejecuté migraciones, seeders y tests.
- Inicié localmente en background `php artisan serve` (puerto 8000) y `php artisan queue:work`.

Acciones manuales URGENTES (debes hacerlas ahora):
1) Revocar el token comprometido (PAT)
   - Ir a: https://github.com/settings/tokens y revocar cualquier token expuesto.

2) Rotar Secrets de GitHub Actions
   - En el repo en GitHub: Settings → Secrets and variables → Actions
   - Actualizar `DEPLOY_SSH_PRIVATE_KEY` y cualquier secreto sensible.

3) Notificar colaboradores si reescribiste el historial
   - Si forzaste pushes que reescriben la historia, indicar a los colaboradores que vuelvan a clonar:

     git fetch origin
     git reset --hard origin/main

Verificaciones y comandos útiles (ejecutar localmente en PowerShell):
```
cd C:\xampp\htdocs\apps\BarandRest\backend
php -v
php composer.phar --version
php composer.phar install --no-interaction
php artisan migrate --force
php artisan db:seed --force
php artisan test --colors=never
php artisan serve --host=127.0.0.1 --port=8000    # si no está corriendo
```

Cómo inspeccionar los runs de GitHub Actions (web o CLI `gh`):
- Web: pestaña Actions en el repositorio.
- CLI (si tienes `gh` autenticado):
  gh run list --repo ejaimedguez-sudo/BarandRest-backend
  gh run view <run-id> --log --repo ejaimedguez-sudo/BarandRest-backend

Si quieres que limpie historial (remover tokens en commits) automáticamente:
- Proceso seguro que seguiré solo con tu confirmación explícita:
  1. Crear backup bundle local.
  2. Ejecutar `git filter-repo` para reemplazar/eliminar secretos (necesito permiso para push force o tú lo haces).
  3. Forzar push a `origin` y avisar a colaboradores.

Contacto/soporte:
- Si quieres que haga la limpieza histórica ahora, responde: "LIMPIAR HISTORIAL" y yo procedo (haré backup antes y te avisaré los pasos que los colaboradores deberán seguir).
