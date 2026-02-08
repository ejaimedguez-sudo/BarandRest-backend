BarAndRest — Setup rápido (local / producción)
===============================================

Instrucciones para poner en marcha la aplicación localmente (Windows/XAMPP) y recomendaciones de producción.

Local (Windows, usando XAMPP):

1. Copia/edita `.env` desde `.env.example` y ajusta variables DB y MAIL.

2. Ejecuta desde la carpeta `backend` (PowerShell recomendado):

```powershell
.\setup.ps1
```

o usando el batch:

```cmd
setup.bat
```

Esto instalará dependencias (composer), generará `APP_KEY`, aplicará migraciones, creará la tabla de colas y poblará seeders de prueba si existen.

3. Generar y enviar reportes (prueba):

```powershell
php artisan reports:daily
php artisan queue:work --once
```

Producción — puntos clave:

- Asegura `QUEUE_CONNECTION=database` y configura `MAIL_*` con credenciales reales.
- Ejecuta `php artisan migrate --force` en el servidor.
- Ejecuta un worker persistente con `supervisor`, `systemd` o un servicio Windows:

  - systemd example:

    ```ini
    [Unit]
    Description=Laravel Queue Worker
    After=network.target

    [Service]
    User=www-data
    Group=www-data
    Restart=always
    ExecStart=/usr/bin/php /path/to/backend/artisan queue:work --sleep=3 --tries=3

    [Install]
    WantedBy=multi-user.target
    ```

- Configura backups y rotación para `storage/app/reports` si generas muchos archivos.

- Seguridad y buenas prácticas (recomendado):

- Nunca subir el archivo `.env` al repositorio. Usa `.env.example` como plantilla.
- En producción: `APP_ENV=production` y `APP_DEBUG=false`.
- Asegura `APP_KEY` y usa HTTPS. Configura `APP_URL` con `https://`.
- Activa `SESSION_SECURE_COOKIE=true` y `SESSION_HTTP_ONLY=true` en `.env`.
- Establece permisos restrictivos para `storage/` y `bootstrap/cache` (ej. 755/775 según sistema).
- Ejecuta `composer audit` periódicamente y actualiza dependencias.
- Usa un servicio SMTP confiable (o Mailtrap para pruebas) y no dejes credenciales en el repo.
- Habilita un firewall y restringe accesos DB por IP cuando sea posible.
- Revisa el workflow CI para no exponer secretos en logs; usa `actions/checkout` y secretos de GitHub para credenciales.

CI (GitHub Actions): se incluye un workflow básico que instala deps y ejecuta `phpunit`.
