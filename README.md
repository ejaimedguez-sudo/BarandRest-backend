# BarandRest — Gestión para Bares y Restaurantes

Proyecto inicial para una aplicación de administración de bares y restaurantes.

Stack recomendado:
- Backend: Laravel (PHP) + MySQL
- Frontend móvil: Ionic/Capacitor (recomendado) o React Native / Flutter

Siguientes pasos (local):
1. Instalar dependencias y crear proyecto Laravel:

```bash
cd c:/xampp/htdocs/apps/BarandRest
composer create-project laravel/laravel backend
cd backend
cp .env.example .env
php artisan key:generate
```

2. Crear base de datos MySQL (ej. `barandrest`) y configurar `.env` con credenciales.

3. Importar esquema inicial (si no usas migraciones aún):

```bash
mysql -u root -p barandrest < ../database/initial_schema.sql
```

4. Ejecutar servidor de desarrollo:

```bash
php artisan migrate
php artisan serve
```

Qué contiene este repo inicial:
- `database/initial_schema.sql`: esquema SQL inicial con tablas clave.
- `docs/er_diagram.mmd`: diagrama ER en Mermaid para visualizar relaciones.
- `.env.example`: plantilla de variables de entorno.
- `routes/api_sample.php`: ejemplar de rutas REST a implementar.

Si quieres, puedo:
- Generar migraciones de Laravel a partir del SQL.
- Scaffoldear controladores y modelos básicos (API REST).
- Crear plantilla básica de frontend Ionic.

Indica qué prefieres que haga a continuación.

---
Hecho: configuración inicial preparada; continúa con las acciones que indiques.

**API y comandos disponibles (resumen rápido)**

- Endpoints principales (Laravel API - `backend/routes/api.php`):
	- `GET /api/products` — CRUD productos (`ProductController`).
	- `GET /api/menu-items` — CRUD platos/recetas (`MenuItemController`).
	- `POST /api/orders` — Crear orden con `order_items` (controlador `OrderController`).
	- `GET /api/tables` — CRUD mesas.
	- `GET /api/customers` — CRUD clientes.
	- `GET /api/expenses` — CRUD gastos.
	- `GET /api/reports/daily` — Reporte diario (ventas totales por día).
	- `GET /api/reports/weekly` — Reporte por día en un rango semanal.
	- `GET /api/reports/monthly?year=2026` — Ventas por mes para un año.
	- `GET /api/reports/yearly` — Ventas por año.
	- `POST /api/commissions/compute` — Calcular comisiones para un rango (payload: `from`, `to`, `percent`).

- Flujo de creación de orden (`POST /api/orders`):
	- Request body debe incluir `order_items` array con objetos `{ menu_item_id, quantity, price? }`.
	- Al crear una orden el sistema:
		- calcula el `cost` por `menu_item` usando sus ingredientes (`MenuItem::calculateCostFromIngredients()`),
		- crea `OrderItem` con `price` y `cost`,
		- decrementa el `product.stock` proporcionalmente a los ingredientes usados,
		- registra cada cambio en `stock_movements`.

- Comando Artisan disponible:
	- `php artisan commissions:compute {from} {to} {percent=5}` — calcula comisiones por cada `order` en el rango y las guarda en la tabla `commissions`.
	- Está registrado y programado en `app/Console/Kernel.php` para ejecutarse automáticamente el primer día de cada mes (ejemplo), calculando la comisión del mes anterior.

- Comprobaciones y pruebas locales (sugeridas):
	- Validar sintaxis PHP de archivos modificados:
		```bash
		"C:\\xampp\\php\\php.exe" -l backend/app/Console/Kernel.php
		"C:\\xampp\\php\\php.exe" -l backend/app/Console/Commands/ComputeCommissions.php
		```
	- Ejecutar comando de ejemplo (ejecutar desde `backend/`):
		```bash
		php artisan commissions:compute 2026-02-01 2026-02-28 5
		```
	- Ejecutar tests (si tienes entorno Laravel listo):
		```bash
		cd backend
		composer install --dev
		vendor\\bin\\phpunit --testsuite Feature
		```

Si quieres que ejecute el comando ahora para generar comisiones de ejemplo en tu base de datos, confirma y lo ejecuto; o si prefieres, escribo tests de integración que arranquen Laravel y validen el flujo completo.
