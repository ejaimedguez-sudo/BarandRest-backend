<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catalogo de Productos - Ordena Facil</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <style>
        :root,
        :root[data-theme="clasico"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
            --c5: #730C02;
            --bg: #fdf2e8;
            --panel: #fffaf7;
            --panel-soft: #fff4ea;
            --text: #3a1a0e;
            --muted: #8f5f45;
            --border: #f3d9c8;
            --ok-bg: rgba(16, 185, 129, 0.1);
            --ok-border: rgba(16, 185, 129, 0.35);
            --ok-text: #065f46;
            --warn-bg: rgba(185, 28, 28, 0.08);
            --warn-border: rgba(185, 28, 28, 0.28);
            --warn-text: #991b1b;
        }

        :root[data-theme="premium"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
            --c5: #730C02;
            --bg: #1b0f0b;
            --panel: #26140f;
            --panel-soft: #1f110d;
            --text: #f8e9d6;
            --muted: #d8bca4;
            --border: rgba(242, 194, 48, 0.2);
            --ok-bg: rgba(16, 185, 129, 0.14);
            --ok-border: rgba(16, 185, 129, 0.4);
            --ok-text: #a7f3d0;
            --warn-bg: rgba(239, 68, 68, 0.12);
            --warn-border: rgba(248, 113, 113, 0.32);
            --warn-text: #fecaca;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Figtree", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(900px 440px at -10% -40%, rgba(242, 145, 27, 0.2), transparent 56%),
                radial-gradient(900px 440px at 110% -40%, rgba(242, 70, 7, 0.2), transparent 56%),
                var(--bg);
        }

        .wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 14px;
            display: grid;
            gap: 12px;
        }

        .hero {
            background: linear-gradient(155deg, var(--panel), var(--panel-soft));
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            flex-wrap: wrap;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(18px, 2.6vw, 28px);
        }

        .hero p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .badge {
            border-radius: 999px;
            border: 1px solid var(--border);
            padding: 7px 11px;
            font-size: 12px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.06);
        }

        .grid {
            display: grid;
            gap: 12px;
        }

        .panel {
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(160deg, var(--panel), var(--panel-soft));
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.14);
            min-width: 0;
        }

        .panel .head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 12px 12px 0;
        }

        .panel .head h2 {
            margin: 0;
            font-size: 16px;
        }

        .panel .body {
            padding: 12px;
        }

        .form-grid {
            display: grid;
            gap: 10px;
        }

        .field {
            display: grid;
            gap: 4px;
        }

        .field label {
            font-size: 12px;
            color: var(--muted);
        }

        .field input {
            border: 1px solid var(--border);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 9px 10px;
            font: inherit;
            min-height: 40px;
        }

        .field input:focus-visible {
            outline: 2px solid color-mix(in srgb, var(--c1) 76%, #fff 24%);
            outline-offset: 1px;
        }

        .field-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .field-row .btn {
            min-height: 30px;
            padding: 4px 8px;
            font-size: 11px;
        }

        .measure-suggestion {
            min-height: 18px;
            font-size: 11px;
            color: var(--muted);
        }

        .measure-suggestion strong {
            color: var(--c1);
            letter-spacing: .2px;
        }

        .measure-picker {
            display: grid;
            gap: 8px;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.04);
        }

        .measure-picker.collapsed {
            display: none;
        }

        .measure-picker input {
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 7px 9px;
            font: inherit;
            min-height: 34px;
        }

        .measure-options {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: auto;
            max-height: 160px;
            display: grid;
            align-content: start;
            background: rgba(0, 0, 0, 0.1);
        }

        .measure-option {
            border: 0;
            border-bottom: 1px solid var(--border);
            background: transparent;
            color: var(--text);
            text-align: left;
            padding: 8px;
            font: inherit;
            cursor: pointer;
            display: grid;
            gap: 2px;
        }

        .measure-option:last-child {
            border-bottom: 0;
        }

        .measure-option:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .measure-option code {
            font-size: 11px;
            color: var(--c1);
            font-weight: 700;
        }

        .measure-option span {
            font-size: 11px;
            color: var(--muted);
        }

        .form-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            border: 1px solid var(--border);
            border-radius: 10px;
            min-height: 38px;
            padding: 7px 12px;
            font: inherit;
            cursor: pointer;
            color: var(--text);
            background: rgba(255, 255, 255, 0.05);
        }

        .btn.primary {
            border-color: color-mix(in srgb, var(--c2) 72%, #fff 28%);
            background: linear-gradient(120deg, rgba(242, 145, 27, 0.34), rgba(242, 70, 7, 0.24));
        }

        .btn:hover {
            filter: brightness(1.06);
        }

        .btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-add {
            border-color: color-mix(in srgb, var(--c2) 72%, #fff 28%);
            background: linear-gradient(120deg, rgba(242, 145, 27, 0.34), rgba(242, 70, 7, 0.24));
            color: #fff7eb;
        }

        .btn-edit {
            border-color: color-mix(in srgb, var(--c1) 68%, #fff 32%);
            background: linear-gradient(120deg, rgba(242, 194, 48, 0.3), rgba(242, 145, 27, 0.2));
            color: #3d1e0e;
        }

        .btn-delete {
            border-color: rgba(191, 19, 4, 0.9);
            background: linear-gradient(120deg, rgba(191, 19, 4, 0.92), rgba(115, 12, 2, 0.92));
            color: #ffffff;
        }

        .status {
            margin-top: 10px;
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 10px;
            font-size: 12px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.04);
            min-height: 40px;
            white-space: pre-wrap;
        }

        .status.ok {
            background: var(--ok-bg);
            border-color: var(--ok-border);
            color: var(--ok-text);
        }

        .status.error {
            background: var(--warn-bg);
            border-color: var(--warn-border);
            color: var(--warn-text);
        }

        .toolbar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .toolbar input {
            flex: 1;
            min-width: 180px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 8px 10px;
            font: inherit;
            min-height: 38px;
        }

        .table-wrap {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: scroll;
            height: min(58vh, 540px);
            background: rgba(0, 0, 0, 0.06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 730px;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 13px;
            vertical-align: middle;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: color-mix(in srgb, var(--panel) 88%, #000 12%);
            font-size: 12px;
            color: var(--muted);
            letter-spacing: .2px;
        }

        tbody tr {
            cursor: pointer;
            transition: background-color .16s ease;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        tbody tr.selected {
            background: rgba(242, 145, 27, 0.22);
        }

        .frame-footer {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .editor-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0, 0, 0, 0.48);
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease;
        }

        .editor-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .editor-frame {
            position: fixed;
            z-index: 101;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(.98);
            width: min(760px, calc(100vw - 32px));
            max-height: calc(100vh - 32px);
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(160deg, var(--panel), var(--panel-soft));
            box-shadow: 0 28px 50px rgba(0, 0, 0, 0.35);
            padding: 14px;
            display: grid;
            gap: 10px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease, transform .18s ease;
        }

        .editor-frame.active {
            opacity: 1;
            pointer-events: auto;
            transform: translate(-50%, -50%) scale(1);
        }

        .editor-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .editor-head h2 {
            margin: 0;
            font-size: 18px;
        }

        .editor-body {
            overflow: auto;
            max-height: calc(100vh - 170px);
            padding-right: 2px;
        }

        .empty {
            padding: 16px;
            color: var(--muted);
            font-size: 13px;
        }

        @media (max-width: 980px) {
            .table-wrap { height: min(46vh, 420px); }

            .frame-footer {
                justify-content: stretch;
            }

            .frame-footer .btn {
                flex: 1;
            }
        }
    </style>
</head>
<body>
<main class="wrap">
    <section class="hero">
        <div>
            <h1>Catalogo de Productos</h1>
            <p>Administra el alta, edicion y eliminacion de productos segun permisos del usuario activo.</p>
        </div>
        <div class="badge" id="roleBadge">Rol: guest</div>
    </section>

    <section class="grid">
        <article class="panel">
            <div class="head">
                <h2>Productos registrados</h2>
                <button id="btnRefresh" class="btn" type="button">Actualizar</button>
            </div>
            <div class="body">
                <div class="toolbar">
                    <input id="tableFilter" type="search" placeholder="Buscar por nombre, SKU o unidad">
                </div>
                <div id="tableContainer" class="table-wrap"></div>
                <div class="frame-footer">
                    <button id="btnAdd" class="btn btn-add" type="button">Agregar</button>
                    <button id="btnEdit" class="btn btn-edit" type="button">Editar</button>
                    <button id="btnDelete" class="btn btn-delete" type="button">Eliminar</button>
                </div>
                <div id="status" class="status">Selecciona un producto y usa los botones de accion.</div>
            </div>
        </article>
    </section>
</main>

<div id="editorOverlay" class="editor-overlay" aria-hidden="true"></div>
<section id="editorFrame" class="editor-frame" aria-hidden="true">
    <div class="editor-head">
        <h2 id="formTitle">Nuevo producto</h2>
        <button id="btnCloseEditor" class="btn" type="button">Cerrar</button>
    </div>
    <div class="editor-body">
        <form id="productForm" class="form-grid">
            <input id="productId" type="hidden">

            <div class="field">
                <label for="sku">SKU</label>
                <input id="sku" name="sku" type="text" maxlength="120" placeholder="Opcional">
            </div>

            <div class="field">
                <label for="name">Nombre *</label>
                <input id="name" name="name" type="text" maxlength="255" required>
            </div>

            <div class="field">
                <div class="field-row">
                    <label for="unit">Unidad *</label>
                    <button id="btnToggleMeasurePicker" class="btn" type="button" aria-expanded="false">Catalogo de medidas</button>
                </div>
                <input id="unit" name="unit" type="text" maxlength="50" placeholder="kg, lt, pieza" required>
                <div id="unitSuggestion" class="measure-suggestion"></div>
                <div id="measurePicker" class="measure-picker collapsed" aria-hidden="true">
                    <input id="measureSearch" type="search" placeholder="Buscar medida por nombre o codigo...">
                    <div id="measureOptions" class="measure-options"></div>
                </div>
            </div>

            <div class="field">
                <label for="cost">Costo</label>
                <input id="cost" name="cost" type="number" min="0" step="0.01" placeholder="0.00">
            </div>

            <div class="field">
                <label for="stock">Stock</label>
                <input id="stock" name="stock" type="number" step="0.001" placeholder="0">
            </div>

            <div class="field">
                <label for="reorder_level">Nivel de reposicion</label>
                <input id="reorder_level" name="reorder_level" type="number" min="0" step="1" placeholder="0">
            </div>

            <div class="form-actions">
                <button id="btnSubmit" class="btn btn-add" type="submit">Guardar producto</button>
                <button id="btnCancelEdit" class="btn" type="button">Cancelar</button>
            </div>
        </form>
    </div>
</section>

<script src="/js/measure-unit-picker.js"></script>
<script>
    const role = localStorage.getItem('ordena-facil-role') || localStorage.getItem('barandrest-role') || 'guest';
    const params = new URLSearchParams(window.location.search);
    const theme = (params.get('theme') === 'premium' || localStorage.getItem('ordena-facil-theme') === 'premium') ? 'premium' : 'clasico';

    const roleBadge = document.getElementById('roleBadge');
    const productForm = document.getElementById('productForm');
    const formTitle = document.getElementById('formTitle');
    const statusBox = document.getElementById('status');
    const tableContainer = document.getElementById('tableContainer');
    const tableFilter = document.getElementById('tableFilter');
    const editorFrame = document.getElementById('editorFrame');
    const editorOverlay = document.getElementById('editorOverlay');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const btnToggleMeasurePicker = document.getElementById('btnToggleMeasurePicker');
    const measurePicker = document.getElementById('measurePicker');
    const measureSearch = document.getElementById('measureSearch');
    const measureOptions = document.getElementById('measureOptions');
    const unitSuggestion = document.getElementById('unitSuggestion');
    const btnAdd = document.getElementById('btnAdd');
    const btnEdit = document.getElementById('btnEdit');
    const btnDelete = document.getElementById('btnDelete');
    const btnCloseEditor = document.getElementById('btnCloseEditor');

    const fields = {
        id: document.getElementById('productId'),
        sku: document.getElementById('sku'),
        name: document.getElementById('name'),
        unit: document.getElementById('unit'),
        cost: document.getElementById('cost'),
        stock: document.getElementById('stock'),
        reorder_level: document.getElementById('reorder_level'),
    };

    let products = [];
    let measuresCatalog = [];
    let canManageCatalog = false;
    let selectedProductId = null;
    const measurePickerApi = window.createMeasureUnitPicker({
        unitInput: fields.unit,
        toggleButton: btnToggleMeasurePicker,
        pickerPanel: measurePicker,
        searchInput: measureSearch,
        optionsContainer: measureOptions,
        suggestionContainer: unitSuggestion,
    });

    document.documentElement.setAttribute('data-theme', theme);
    roleBadge.textContent = `Rol: ${role}`;

    function setStatus(message, type) {
        statusBox.textContent = message;
        statusBox.classList.remove('ok', 'error');
        if (type) {
            statusBox.classList.add(type);
        }
    }

    function normalizeNumber(value) {
        if (value === '' || value === null || value === undefined) return null;
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : null;
    }

    function collectPayload() {
        return {
            sku: fields.sku.value.trim() || null,
            name: fields.name.value.trim(),
            unit: fields.unit.value.trim(),
            cost: normalizeNumber(fields.cost.value),
            stock: normalizeNumber(fields.stock.value),
            reorder_level: normalizeNumber(fields.reorder_level.value),
        };
    }

    function clearForm() {
        fields.id.value = '';
        fields.sku.value = '';
        fields.name.value = '';
        fields.unit.value = '';
        fields.cost.value = '';
        fields.stock.value = '';
        fields.reorder_level.value = '';
        formTitle.textContent = 'Nuevo producto';
        btnSubmit.textContent = 'Guardar producto';
        measurePickerApi.reset();
    }

    async function loadMeasuresCatalog() {
        try {
            const data = await requestJson('/api/measures');
            measuresCatalog = Array.isArray(data) ? data : [];
            measurePickerApi.setMeasures(measuresCatalog);
        } catch (_error) {
            measuresCatalog = [];
            measurePickerApi.setMeasures([]);
            measurePickerApi.setErrorMessage('No fue posible cargar medidas.');
        }
    }

    function getSelectedProduct() {
        return products.find((item) => Number(item.id) === Number(selectedProductId)) || null;
    }

    function updateActionButtons() {
        const hasSelection = !!getSelectedProduct();
        btnAdd.disabled = !canManageCatalog;
        btnEdit.disabled = !canManageCatalog || !hasSelection;
        btnDelete.disabled = !canManageCatalog || !hasSelection;
    }

    function openEditor(mode) {
        if (!canManageCatalog) {
            setStatus('No tienes permisos para administrar el catalogo.', 'error');
            return;
        }

        if (mode === 'edit') {
            const product = getSelectedProduct();
            if (!product) {
                setStatus('Selecciona un producto para editar.', 'error');
                return;
            }

            fields.id.value = String(product.id);
            fields.sku.value = product.sku || '';
            fields.name.value = product.name || '';
            fields.unit.value = product.unit || '';
            fields.cost.value = product.cost ?? '';
            fields.stock.value = product.stock ?? '';
            fields.reorder_level.value = product.reorder_level ?? '';
            formTitle.textContent = `Editar producto #${product.id}`;
            btnSubmit.textContent = 'Guardar cambios';
            measurePickerApi.showSuggestion();
        } else {
            clearForm();
            formTitle.textContent = 'Agregar producto';
            btnSubmit.textContent = 'Guardar producto';
        }

        editorOverlay.classList.add('active');
        editorFrame.classList.add('active');
        editorOverlay.setAttribute('aria-hidden', 'false');
        editorFrame.setAttribute('aria-hidden', 'false');
        fields.name.focus();
    }

    function closeEditor() {
        editorOverlay.classList.remove('active');
        editorFrame.classList.remove('active');
        editorOverlay.setAttribute('aria-hidden', 'true');
        editorFrame.setAttribute('aria-hidden', 'true');
        clearForm();
    }

    function setFormEditable(enabled) {
        Object.values(fields).forEach((field) => {
            if (field.id === 'productId') return;
            field.disabled = !enabled;
        });

        btnSubmit.disabled = !enabled;
        btnCancelEdit.disabled = !enabled;
        btnCloseEditor.disabled = !enabled;
        measurePickerApi.setEnabled(enabled);
        updateActionButtons();
    }

    async function requestJson(url, options = {}) {
        const headers = {
            'Accept': 'application/json',
            'X-USER-ROLE': role,
            ...options.headers,
        };

        const response = await fetch(url, { ...options, headers });

        if (!response.ok) {
            const text = await response.text();
            throw new Error(`HTTP ${response.status} - ${text}`);
        }

        if (response.status === 204) return null;
        return response.json();
    }

    async function loadCapabilities() {
        try {
            const payload = await requestJson('/api/system/capabilities');
            const capabilities = Array.isArray(payload?.capabilities) ? payload.capabilities : [];
            canManageCatalog = capabilities.includes('manage_catalog');

            if (!canManageCatalog) {
                setFormEditable(false);
                setStatus('Tu rol actual no tiene permisos para administrar el catalogo.', 'error');
            } else {
                setFormEditable(true);
                setStatus('Selecciona un producto y usa Agregar, Editar o Eliminar.', null);
            }
        } catch (error) {
            canManageCatalog = false;
            setFormEditable(false);
            setStatus(`No se pudieron cargar permisos: ${String(error.message || error)}`, 'error');
        }
    }

    function formatNumber(value, decimals = 2) {
        if (value === null || value === undefined || value === '') return '-';
        const numeric = Number(value);
        if (!Number.isFinite(numeric)) return String(value);
        return numeric.toFixed(decimals);
    }

    function renderTable(rows) {
        if (!rows.length) {
            tableContainer.innerHTML = '<div class="empty">No hay productos registrados.</div>';
            selectedProductId = null;
            updateActionButtons();
            return;
        }

        const body = rows.map((product) => {
            const selected = Number(selectedProductId) === Number(product.id) ? ' class="selected"' : '';
            return `
                <tr data-product-id="${product.id}"${selected}>
                    <td>${product.sku || '-'}</td>
                    <td>${product.name || '-'}</td>
                    <td>${product.unit || '-'}</td>
                    <td>${formatNumber(product.cost, 2)}</td>
                    <td>${formatNumber(product.stock, 3)}</td>
                    <td>${product.reorder_level ?? '-'}</td>
                </tr>
            `;
        }).join('');

        tableContainer.innerHTML = `
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Unidad</th>
                        <th>Costo</th>
                        <th>Stock</th>
                        <th>Reposicion</th>
                    </tr>
                </thead>
                <tbody>${body}</tbody>
            </table>
        `;

        updateActionButtons();
    }

    function applyFilter() {
        const term = (tableFilter.value || '').trim().toLowerCase();
        if (!term) {
            renderTable(products);
            return;
        }

        const filtered = products.filter((product) => {
            const line = `${product.sku || ''} ${product.name || ''} ${product.unit || ''}`.toLowerCase();
            return line.includes(term);
        });

        renderTable(filtered);
    }

    async function loadProducts() {
        try {
            const data = await requestJson('/api/products');
            products = Array.isArray(data) ? data : [];

            if (!getSelectedProduct()) {
                selectedProductId = null;
            }

            applyFilter();
        } catch (error) {
            tableContainer.innerHTML = '<div class="empty">No fue posible cargar productos.</div>';
            setStatus(`Error cargando productos: ${String(error.message || error)}`, 'error');
        }
    }

    async function removeSelectedProduct() {
        if (!canManageCatalog) return;

        const product = getSelectedProduct();
        if (!product) {
            setStatus('Selecciona un producto para eliminar.', 'error');
            return;
        }

        const productId = Number(product.id);
        const label = product?.name ? `"${product.name}"` : `#${productId}`;
        if (!window.confirm(`¿Deseas eliminar el producto ${label}?`)) return;

        try {
            await requestJson(`/api/products/${productId}`, { method: 'DELETE' });
            setStatus('Producto eliminado correctamente.', 'ok');
            selectedProductId = null;
            await loadProducts();
        } catch (error) {
            setStatus(`No se pudo eliminar: ${String(error.message || error)}`, 'error');
        }
    }

    productForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!canManageCatalog) {
            setStatus('No tienes permisos para crear o editar productos.', 'error');
            return;
        }

        const payload = collectPayload();
        const editingId = fields.id.value ? Number(fields.id.value) : null;

        try {
            if (editingId) {
                await requestJson(`/api/products/${editingId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                setStatus('Producto actualizado correctamente.', 'ok');
                selectedProductId = editingId;
            } else {
                await requestJson('/api/products', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                setStatus('Producto creado correctamente.', 'ok');
            }

            closeEditor();
            await loadProducts();
        } catch (error) {
            setStatus(`No se pudo guardar: ${String(error.message || error)}`, 'error');
        }
    });

    btnCancelEdit.addEventListener('click', () => {
        closeEditor();
        measurePickerApi.close();
        setStatus('Edicion cancelada.', null);
    });

    btnCloseEditor.addEventListener('click', () => {
        closeEditor();
        measurePickerApi.close();
    });

    editorOverlay.addEventListener('click', () => {
        closeEditor();
        measurePickerApi.close();
    });

    document.getElementById('btnRefresh').addEventListener('click', async () => {
        await loadProducts();
        setStatus('Listado actualizado.', null);
    });

    btnAdd.addEventListener('click', () => {
        openEditor('add');
    });

    btnEdit.addEventListener('click', () => {
        openEditor('edit');
    });

    btnDelete.addEventListener('click', async () => {
        await removeSelectedProduct();
    });

    tableFilter.addEventListener('input', applyFilter);

    tableContainer.addEventListener('click', (event) => {
        const row = event.target.closest('tr[data-product-id]');
        if (!row) return;

        selectedProductId = Number(row.dataset.productId);
        applyFilter();
        const selected = getSelectedProduct();
        if (selected) {
            setStatus(`Producto seleccionado: ${selected.name}`, null);
        }
    });

    async function init() {
        clearForm();
        await loadCapabilities();
        await loadMeasuresCatalog();
        await loadProducts();
        updateActionButtons();
    }

    init();
</script>
</body>
</html>
