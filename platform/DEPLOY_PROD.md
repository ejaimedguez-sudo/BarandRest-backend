# Deploy Produccion - BarAndRest Platform

Este documento define un despliegue productivo para:
- Backend Node/Express (`platform/backend-node`)
- Frontend web React (`platform/web-react`)
- Mobile React Native (build separado por store/distribucion)

## 1. Requisitos servidor

- Ubuntu 22.04+ recomendado
- Node.js 20 LTS
- MySQL/MariaDB 10.4+
- Nginx
- PM2 o systemd para proceso Node
- Certificado TLS (Let's Encrypt o equivalente)

## 2. Variables y secretos

Usa `platform/backend-node/.env.production.example` como plantilla.

Variables obligatorias:
- `DB_*`
- `JWT_SECRET`
- `CORS_ORIGINS`
- `PAYMENT_PROVIDER` + credenciales reales
- `CFDI_PROVIDER` + credenciales reales

No subir `.env` a git.

## 3. Base de datos

### 3.1 Crear DB y usuario minimo privilegio

```sql
CREATE DATABASE IF NOT EXISTS barandrest_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'barandrest_app'@'%' IDENTIFIED BY 'REPLACE_STRONG_PASSWORD';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX ON barandrest_platform.* TO 'barandrest_app'@'%';
FLUSH PRIVILEGES;
```

## 4. Deploy backend

```bash
cd /var/www/barandrest/platform/backend-node
npm ci --omit=dev
cp .env.production.example .env
# Editar .env con valores reales
node src/server.js
```

### 4.1 Ejecutar como servicio (systemd)

Archivo: `/etc/systemd/system/barandrest-backend.service`

```ini
[Unit]
Description=BarAndRest Backend Node API
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/barandrest/platform/backend-node
ExecStart=/usr/bin/node src/server.js
Restart=always
RestartSec=5
Environment=NODE_ENV=production

[Install]
WantedBy=multi-user.target
```

Comandos:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now barandrest-backend
sudo systemctl status barandrest-backend
```

## 5. Build y deploy web

```bash
cd /var/www/barandrest/platform/web-react
npm ci
npm run build
```

Publicar `platform/web-react/dist` en Nginx.

## 6. Nginx ejemplo

```nginx
server {
    listen 80;
    server_name admin.tu-dominio.com;

    root /var/www/barandrest/platform/web-react/dist;
    index index.html;

    location / {
        try_files $uri /index.html;
    }

    location /api/ {
        proxy_pass http://127.0.0.1:4100/api/;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /health {
        proxy_pass http://127.0.0.1:4100/health;
    }
}
```

## 7. Validacion post-deploy

```bash
curl https://admin.tu-dominio.com/health
curl -X POST https://admin.tu-dominio.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@barandrest.local","password":"Demo12345"}'
```

Si es staging, ejecutar:

```bash
cd /var/www/barandrest/platform/backend-node
npm run seed:demo
npm run smoke:e2e
```

## 8. Backups

### Linux cron diario

```bash
0 2 * * * cd /var/www/barandrest/platform/backend-node && DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=barandrest_platform DB_USER=barandrest_app DB_PASSWORD='REPLACE' ./scripts/db-backup.sh /var/backups/barandrest
```

### Windows Task Scheduler

Ejecutar:

```powershell
cd C:\xampp\htdocs\apps\BarandRest\platform\backend-node
.\scripts\db-backup.ps1 -Host localhost -Port 3306 -Database barandrest_platform -User barandrest_app -Password "REPLACE" -OutputDir "C:\backups\barandrest"
```

## 9. GitHub Actions secrets recomendados

Para workflows CI/release y despliegue manual:
- `PROD_SSH_HOST`
- `PROD_SSH_USER`
- `PROD_SSH_KEY`
- `PROD_APP_PATH`
- `PAYMENT_API_KEY`
- `CFDI_API_KEY`
- `JWT_SECRET`

Para deploy automatico con `.github/workflows/platform-deploy.yml` (por `workflow_dispatch` y `environment`):
- `DEPLOY_SSH_HOST`
- `DEPLOY_SSH_PORT` (opcional, default `22`)
- `DEPLOY_SSH_USER`
- `DEPLOY_SSH_KEY`
- `DEPLOY_APP_PATH` (ejemplo `/var/www/barandrest`)
- `BACKEND_ENV_FILE` (contenido completo del `.env` productivo en un solo secret)

Flujo recomendado:
1. Crear `environment` en GitHub: `staging` y `production`.
2. Cargar secretos por environment.
3. Ejecutar workflow manual `Platform Deploy` y seleccionar environment.
4. Activar `runSmoke=true` para validar deploy en staging.

Automatizacion local para cargar secrets (PowerShell):

```powershell
cd C:\xampp\htdocs\apps\BarandRest\platform\scripts
.\setup-github-env-secrets.ps1 \
    -Environment staging \
    -DeployHost "TU_HOST" \
    -DeployUser "TU_USUARIO" \
    -DeployAppPath "/var/www/barandrest" \
    -SshKeyPath "C:\keys\barandrest_deploy" \
    -BackendEnvPath "C:\xampp\htdocs\apps\BarandRest\platform\backend-node\.env.production.example"
```

Automatizacion local para cargar secrets (bash):

```bash
cd /path/to/BarandRest/platform/scripts
./setup-github-env-secrets.sh staging TU_HOST TU_USUARIO /var/www/barandrest ~/.ssh/barandrest_deploy ../backend-node/.env.production.example
```

Primer despliegue recomendado:
1. Ejecutar `Platform Deploy` con `environment=staging` y `runSmoke=true`.
2. Verificar log del job y endpoint `health`.
3. Repetir en `production` con `runSmoke=false`.

## 10. Endurecimiento final recomendado

- Restringir `CORS_ORIGINS` a dominios exactos (sin `*`).
- Ajustar `RATE_LIMIT_MAX` por perfil de trafico real.
- Rotar secretos cada 90 dias.
- Monitorear `GET /api/dashboard/audit/recent` para trazabilidad.
- Habilitar alertas de disponibilidad y errores 5xx.
