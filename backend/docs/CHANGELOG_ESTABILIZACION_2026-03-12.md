# Changelog de Estabilizacion - 2026-03-12

## Objetivo
Consolidar la migracion a Ordena Facil, estabilizar la portada operativa, reforzar seguridad por roles y habilitar optimizacion de produccion.

## Cambios principales

- Branding consolidado de BarandRest a Ordena Facil en vistas, correos, scripts y documentacion.
- Portada `welcome` rediseñada para operacion real:
  - menu lateral por secciones con acordeon y busqueda,
  - control por rol/capacidades,
  - tutorial guiado,
  - boton de refresco de interfaz,
  - boton `Acerca de` con datos de version/stack/estado.
- Soporte de instalacion multiplataforma:
  - `install` view,
  - `manifest.json` y `service-worker.js`,
  - scripts de instalacion/actualizacion,
  - workflow para build de instaladores.
- Seguridad y permisos:
  - middleware `EnsureRole`,
  - endpoint `api/system/capabilities`,
  - restriccion por rol en endpoints sensibles,
  - middleware `SecurityHeaders` registrado globalmente,
  - iframe permitido en mismo origen (`SAMEORIGIN` + `frame-ancestors 'self'`).
- Rutas refactorizadas para cache de rutas:
  - eliminadas closures en `routes/web.php` y `routes/api.php`.
  - nuevos controladores invocables: `AuthUserController`, `SystemCapabilitiesController`.
- Depuracion funcional:
  - correccion de plantilla de email duplicada,
  - ajuste de mensajes y paths legacy en scripts operativos,
  - fallback para mantener menu funcional si falla capacidades.

## Validaciones ejecutadas

- `php -l` en archivos modificados sin errores.
- `php artisan test`: 10 pruebas en verde (19 assertions).
- `php artisan route:cache`: exitoso.
- `php artisan config:cache`: exitoso.
- `php artisan event:cache`: exitoso.
- `php artisan view:cache`: exitoso.

## Estado final
Sistema estable para operacion diaria, con interfaz unificada, rutas cacheables y hardening base para produccion.
