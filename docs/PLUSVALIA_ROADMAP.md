# Roadmap de Plusvalia del Sistema

## Objetivo
Incrementar utilidad, control operativo y velocidad de decision con mejoras medibles en costos, margen, merma, servicio y confiabilidad.

## KPIs base recomendados
- Margen real por item (%)
- Desviacion costo manual vs costo calculado
- Costo de merma mensual
- Tiempo promedio de atencion por orden
- Rotacion de inventario por categoria
- Tasa de quiebre de stock
- Ticket promedio y mix de venta
- Items con margen negativo

## Fase 1 (alto impacto, baja complejidad)
### 1. Alertas de rentabilidad
- Semaforo por item: verde/amarillo/rojo segun margen.
- Alertas en dashboard para items con margen negativo o bajo umbral.
- Beneficio: accion inmediata sobre precios y recetas.

### 2. Filtro de desviacion en catalogo
- Vista directa de items con desviacion >= X%.
- Boton desde dashboard para abrir catalogo filtrado.
- Beneficio: auditoria diaria de costos sin friccion.

### 3. Historial de costo por receta
- Snapshot por cambio de ingredientes (quien, cuando, antes/despues).
- Beneficio: trazabilidad y control de variaciones.

### 4. Politicas de precio minimo
- Bloquear guardar precio si rompe margen minimo por categoria.
- Excepcion con permiso de gerente.
- Beneficio: proteccion de utilidad.

## Fase 2 (alto impacto, complejidad media)
### 5. Simulador de precio y margen
- Simulacion por item: costo esperado, margen objetivo, precio sugerido.
- Escenarios: +5% costo insumos, promo, happy hour.
- Beneficio: decisiones comerciales con datos.

### 6. Ingenieria de menu
- Clasificar items por popularidad y margen (tipo estrella, puzzle, etc.).
- Recomendaciones de promocion/ajuste por cuadrante.
- Beneficio: mayor contribucion por mix de venta.

### 7. Costeo por unidad estandar
- Convertidor de unidades (ml, oz, g, pza).
- Costo unificado por unidad base para evitar errores.
- Beneficio: precision de costeo y consistencia.

### 8. Merma y desperdicio
- Registro de merma por turno y causa.
- KPI de merma por familia y por responsable.
- Beneficio: reduccion de fuga no visible.

## Fase 3 (estrategica)
### 9. Forecast de demanda
- Proyeccion semanal por item/categoria.
- Reorden sugerido segun consumo real.
- Beneficio: menos quiebres y sobreinventario.

### 10. Abastecimiento inteligente
- Recomendacion de compras segun rotacion y margen.
- Simulacion de impacto de precio de proveedor.
- Beneficio: mejor flujo de caja y costo promedio.

### 11. Tablero ejecutivo
- Vista gerente: utilidad neta operativa diaria, desviaciones, riesgo de stock.
- Beneficio: gobierno de negocio en tiempo real.

### 12. Integracion contable/fiscal
- Export de costos, ventas y comisiones para conciliacion.
- Beneficio: cierre financiero mas rapido y confiable.

## Seguridad y confiabilidad (transversal)
- Matriz de permisos por accion sensible (precio, costo, receta).
- Bitacora de auditoria para cambios criticos.
- Pruebas automaticas de regresion para costeo y margen.
- Backups verificados y restauracion probada.

## Quick wins tecnicos recomendados (siguiente sprint)
1. Boton de dashboard que abra catalogo con filtro de desviacion >= 10%.
2. Semaforo de margen en listado de menu items.
3. Regla de margen minimo configurable por categoria.
4. Registro de historial de costos de receta.

## Quick wins tecnicos implementados (14-mar-2026)
1. Capa de cache HTTP para catalogos (`ETag` + `Cache-Control`) en endpoints de:
	 - `/api/products`
	 - `/api/menu-items`
	 - `/api/measures`
	 - `/api/product-types`
	 - `/api/menu-categories`
2. Paginacion incremental opt-in sin romper compatibilidad:
	 - Parametros: `paginate=1`, `per_page=50`, `page=1`.
	 - Comportamiento legacy preservado: sin `paginate=1` se mantiene respuesta completa (array) para no afectar UI actual.
3. Filtro backend opt-in por termino (`q`) para reducir payload transferido cuando se requiera.

### Ejemplos de uso
- Listado tradicional (compatibilidad actual):
	- `GET /api/products`
- Paginado:
	- `GET /api/products?paginate=1&per_page=50&page=1`
- Paginado + filtro:
	- `GET /api/menu-items?paginate=1&per_page=40&page=1&q=mojito`

Beneficio: menor latencia percibida, menos transferencia de datos y mejor escalabilidad de catalogos sin modificar el diseño conceptual de la aplicacion.

## Orden de ejecucion sugerido
1. Filtro + semaforo + alertas (visibilidad inmediata).
2. Reglas de precio minimo y permisos (proteccion de utilidad).
3. Historial y trazabilidad (control de gestion).
4. Simulador y menu engineering (optimizacion comercial).

## Criterio de priorizacion
- Impacto en utilidad (40%)
- Riesgo operativo reducido (25%)
- Esfuerzo tecnico (20%)
- Tiempo de adopcion por usuarios (15%)
