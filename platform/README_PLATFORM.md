# BarAndRest Platform (Node + React + React Native)

Esta carpeta agrega una arquitectura moderna y modular para cubrir la operacion integral de un restaurant-bar sin romper el backend Laravel actual.

## Componentes

- `backend-node/`: API REST en Node.js + Express + Sequelize + MySQL.
- `web-react/`: aplicacion web administrativa en React (Vite).
- `mobile-react-native/`: app movil en React Native (Expo).
- `sql/platform_schema.sql`: inicializacion de base de datos en MySQL de XAMPP.

## Modulos cubiertos

1. Menu digital QR
- Endpoint para obtener menu publico y generar QR por mesa.
- Orden de comensal desde QR o solicitud por mesero.

2. Gestion de mesas y meseros
- Asignacion de mesero a mesa.
- Registro de ordenes/ventas por mesa y mesero.
- KPIs por mesero y base para comisiones.

3. Modulo de ordenes
- Flujo para toma/envio de orden por mesero.
- Cajero puede crear pedidos adicionales.

4. Recetas y costos
- Registro de recetas por area (`barra`/`cocina`).
- Costeo por ingredientes y margen costo-beneficio.

5. Inventarios
- Entradas/salidas/ajustes de materia prima.
- Proveedores, ordenes de compra, cuentas por pagar.

6. Pagos y facturacion
- Emision de ticket por orden.
- Registro de pago.
- Factura ligada al ticket (stub de integracion con API fiscal).

7. Dashboard BI
- Ventas por periodo, comparativo con periodo anterior.
- KPIs clave y resumen por mesero.

## Roles y seguridad

Roles soportados:
- `administrador`
- `mesero`
- `cajero`
- `jefe_barra`
- `jefe_cocina`
- `gerente`

La API usa JWT y middleware RBAC por endpoint.

Hardening activo:
- `helmet` para headers de seguridad.
- `express-rate-limit` para throttling global.
- `requestId` por peticion.
- Auditoria persistente en tabla `AuditLogs`.

Usuarios demo (creados por `npm run seed:demo`):
- `admin@barandrest.local`
- `gerente@barandrest.local`
- `mesero@barandrest.local`
- `cajero@barandrest.local`
- `barra@barandrest.local`
- `cocina@barandrest.local`
- Password comun: `Demo12345`

## Arranque rapido (Windows/XAMPP)

1. Crear DB:
```bash
mysql -u root -p < platform/sql/platform_schema.sql
```

2. Backend API:
```bash
cd platform/backend-node
copy .env.example .env
npm install
npm run start
```

Nota MySQL/XAMPP en Windows:
- Si `root` solo funciona en modo local, usa `DB_HOST=localhost` (no `127.0.0.1`).
- Evita `#` sin comillas en `DB_PASSWORD` dentro de `.env`, porque `dotenv` lo interpreta como comentario.

Variables de integracion en `.env`:
- `PAYMENT_PROVIDER=mock|stripe-like`
- `PAYMENT_API_URL=...`
- `PAYMENT_API_KEY=...`
- `PAYMENT_CURRENCY=MXN`
- `CFDI_PROVIDER=mock|api`
- `CFDI_API_URL=...`
- `CFDI_API_KEY=...`
- `CORS_ORIGINS=http://localhost:5173,http://localhost:19006`
- `RATE_LIMIT_WINDOW_MS=60000`
- `RATE_LIMIT_MAX=120`

3. Seed de roles (opcional, ya se auto-crean al iniciar API):
```bash
npm run seed
```

Seed demo completo (usuarios, mesas, menu, recetas, inventario, proveedores, orden/ticket/pago):
```bash
npm run seed:demo
```

4. Frontend web:
```bash
cd platform/web-react
npm install
npm run dev
```

5. App movil:
```bash
cd platform/mobile-react-native
npm install
npm run start
```

## Validacion end-to-end

Con API arriba y seed demo cargado:

```bash
cd platform/backend-node
npm run smoke:e2e
```

Este smoke valida: auth, menu QR/publico, ordenes, ticket, pago, factura y dashboard BI.

## Endpoints clave

- Auth
  - `POST /api/auth/register`
  - `POST /api/auth/login`
- Menu QR
  - `GET /api/menu/public`
  - `GET /api/menu/public/table/:tableId/qr`
- Operacion
  - `POST /api/ops/tables`
  - `GET /api/ops/tables`
  - `PATCH /api/ops/tables/:id/assign-waiter`
  - `POST /api/ops/orders`
  - `POST /api/ops/orders/guest`
  - `POST /api/ops/orders/:id/add-items`
  - `PATCH /api/ops/orders/:id/status`
- Recetas/costos
  - `POST /api/recipes/ingredients`
  - `POST /api/recipes/recipes`
  - `GET /api/recipes/recipes/:id/costing`
- Inventarios
  - `POST /api/inventory/suppliers`
  - `POST /api/inventory/movements`
  - `POST /api/inventory/purchase-orders`
  - `POST /api/inventory/accounts-payable`
- Pagos/facturacion
  - `POST /api/billing/tickets/:orderId`
  - `POST /api/billing/payments/:ticketId`
  - `POST /api/billing/invoices/:ticketId`
  - Idempotencia de ticket por orden e invoice por ticket.
- BI
  - `GET /api/dashboard/sales?from=YYYY-MM-DD&to=YYYY-MM-DD`
  - `GET /api/dashboard/sales/timeseries?from=YYYY-MM-DD&to=YYYY-MM-DD&granularity=daily|weekly|monthly|yearly`
  - `GET /api/dashboard/waiters/commissions?from=YYYY-MM-DD&to=YYYY-MM-DD&commissionPct=5`
  - `GET /api/dashboard/audit/recent?limit=100`
- Realtime (SSE autenticado)
  - `GET /api/realtime/events`

## Backups

Windows PowerShell:
```powershell
cd platform/backend-node
./scripts/db-backup.ps1 -Host localhost -Port 3306 -Database barandrest_platform -User barandrest_app -Password "tu_password"
```

Bash:
```bash
cd platform/backend-node
DB_HOST=localhost DB_PORT=3306 DB_NAME=barandrest_platform DB_USER=barandrest_app DB_PASSWORD=tu_password ./scripts/db-backup.sh
```

## CI/CD

Workflow incluido:
- `.github/workflows/platform-ci.yml`
- `.github/workflows/platform-release.yml`

`platform-ci.yml` valida backend (syntax checks), build web y entorno mobile en pushes/PRs sobre `platform/**`.
`platform-release.yml` genera un bundle desplegable bajo demanda (`workflow_dispatch`).

## Integraciones externas pendientes

- Pasarela de pagos real: activar `PAYMENT_PROVIDER=stripe-like` y credenciales del proveedor.
- Facturacion CFDI real: activar `CFDI_PROVIDER=api` y credenciales de timbrado.
- Notificaciones push mobile (FCM/APNS) para complementar el canal realtime SSE actual.

## Notas de escalabilidad

- Separar en microservicios por dominio al crecer (ordenes, inventario, finanzas, BI).
- Agregar Redis para colas/eventos y cache de dashboard.
- Agregar pruebas E2E (Playwright/Detox) y CI por app.
