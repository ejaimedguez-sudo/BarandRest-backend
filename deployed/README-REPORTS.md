Reporte: configuración y despliegue
=================================

Pasos para tener reportes programados y envío por email funcionando:

1) Variables de entorno (editar `backend/.env`):

   - `MAIL_MAILER=smtp`
   - `MAIL_HOST=smtp.tu-servidor.com`
   - `MAIL_PORT=587`
   - `MAIL_USERNAME=usuario`
   - `MAIL_PASSWORD=secreto`
   - `MAIL_ENCRYPTION=tls`
   - `MAIL_FROM_ADDRESS=no-reply@tu-dominio.com`
   - `MAIL_FROM_NAME="Ordena Facil"`
   - `MAIL_REPORT_RECIPIENT=admin@tu-dominio.com` (destinatarios separados por coma)
   - `QUEUE_CONNECTION=database` (recomendado para producción)

2) Preparar la cola de base de datos (si usas `QUEUE_CONNECTION=database`):

   ```bash
   php artisan queue:table
   php artisan migrate
   ```

   Si la tabla `jobs` ya existe, solo ejecutar `php artisan migrate`.

3) Iniciar worker de colas (producción):

   - Usar `supervisor` o systemd para mantener `php artisan queue:work` en ejecución.
   - Local rápido:

     ```bash
     php artisan queue:work --sleep=3 --tries=3
     ```

4) Probar generación manualmente:

   ```bash
   php artisan reports:daily
   php artisan queue:work --once
   ```

7) Notas de pulido automático:

- Si colocas `public/images/logo.png`, el logo se inserta en el Excel y se muestra en el PDF.
- Los totales numéricos se calculan automáticamente para columnas con nombres como `price`, `cost`, `total`, `amount`, `qty`.
- Archivos generados en `storage/app/reports/` con nombres `report_YYYYMMDD_HHMMSS.xlsx` y `.pdf`.

5) Scheduler: el Kernel ya registra `reports:daily` para ejecutarse cada día.

6) Notas sobre formatos:

   - Los archivos Excel se guardan en `storage/app/reports/` con nombre `report_YYYYMMDD_HHMMSS.xlsx`.
   - El PDF se genera con la plantilla Blade `resources/views/reports/pdf.blade.php`. Puedes personalizar logo en `public/images/logo.png`.
