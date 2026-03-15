<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catalogo de Tipos de Producto - Ordena Facil</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/ui-action-buttons.css?v={{ $assetVersion }}">
    <link rel="stylesheet" href="/assets/ui-frames-pro.css?v={{ $assetVersion }}">
    <style>
        :root,
        :root[data-theme="clasico"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
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
            --form-field-row-gap: 10px;
            --form-field-col-gap: 12px;
            --form-label-min-height: 26px;
            --form-input-min-height: 35px;
        }

        :root[data-theme="premium"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
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
            --form-field-row-gap: 11px;
            --form-field-col-gap: 13px;
            --form-label-min-height: 26px;
            --form-input-min-height: 35px;
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
            padding: 12px;
            display: grid;
            gap: 10px;
        }

        .hero {
            background: linear-gradient(155deg, var(--panel), var(--panel-soft));
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            flex-wrap: wrap;
        }

        .hero h1 { margin: 0; font-size: clamp(19px, 2.1vw, 26px); line-height: 1.14; letter-spacing: .22px; }
        .hero p { margin: 5px 0 0; color: var(--muted); font-size: 12px; line-height: 1.45; }

        .badge {
            border-radius: 999px;
            border: 1px solid var(--border);
            padding: 5px 10px;
            font-size: 11px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.06);
        }

        .panel {
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(160deg, var(--panel), var(--panel-soft));
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.13);
            min-width: 0;
        }

        .panel .head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 10px 10px 0;
        }

        .panel .head h2 { margin: 0; font-size: 15px; letter-spacing: .2px; }
        .panel .body { padding: 10px; }

        .toolbar {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .toolbar input {
            flex: 1;
            min-width: 220px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 6px 8px;
            font: inherit;
            font-size: 12px;
            min-height: 32px;
        }

        .table-wrap {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: scroll;
            height: min(58vh, 520px);
            background: rgba(0, 0, 0, 0.06);
        }

        table { width: 100%; border-collapse: collapse; min-width: 760px; }

        th, td {
            padding: 8px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 12px;
            vertical-align: top;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: color-mix(in srgb, var(--panel) 88%, #000 12%);
            font-size: 11px;
            color: var(--muted);
            letter-spacing: .2px;
        }

        tbody tr { cursor: pointer; transition: background-color .16s ease; }
        tbody tr:hover { background: rgba(255, 255, 255, 0.05); }
        tbody tr.selected { background: rgba(242, 145, 27, 0.22); }

        .frame-footer {
            margin-top: 8px;
            display: flex;
            gap: 6px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .panel .head .btn,
        .frame-footer .btn,
        .editor-head .btn,
        .form-actions .btn {
            min-height: 28px;
            padding: 3px 8px;
            border-radius: 7px;
            font-size: 11px;
            letter-spacing: .1px;
        }

        .status {
            margin-top: 8px;
            border-radius: 9px;
            border: 1px solid var(--border);
            padding: 8px;
            font-size: 11.5px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.04);
            min-height: 34px;
            white-space: pre-wrap;
            line-height: 1.4;
        }

        .status.ok { background: var(--ok-bg); border-color: var(--ok-border); color: var(--ok-text); }
        .status.error { background: var(--warn-bg); border-color: var(--warn-border); color: var(--warn-text); }

        .empty { padding: 14px; text-align: center; color: var(--muted); font-size: 12px; }

        .editor-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.58);
            z-index: 3090;
            display: none;
        }

        .editor-overlay.active { display: block; }

        .editor-frame {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(.98);
            width: min(980px, calc(100vw - 32px));
            max-height: calc(100vh - 32px);
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(155deg, var(--panel), var(--panel-soft));
            box-shadow: 0 28px 50px rgba(0, 0, 0, 0.35);
            z-index: 3100;
            padding: 12px;
            display: none;
            overflow: auto;
        }

        .editor-frame.active { display: grid; gap: 8px; }

        .editor-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 6px;
        }

        .editor-head h2 { margin: 0; font-size: 16px; letter-spacing: .2px; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: var(--form-field-row-gap) var(--form-field-col-gap);
            align-items: start;
        }

        .form-grid.cols-1 { grid-template-columns: 1fr; }
        .form-grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .form-grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }

        .form-grid > * { min-width: 0; }

        .field { display: grid; gap: 4px; min-width: 0; }
        .field-wide, .form-actions { grid-column: 1 / -1; }

        .field label {
            font-size: 11.5px;
            color: var(--muted);
            line-height: 1.2;
            min-height: var(--form-label-min-height);
            display: flex;
            align-items: flex-end;
        }

        .field label.required::after {
            content: ' *';
            color: var(--c3);
            font-weight: 700;
        }

        .field input,
        .field textarea {
            width: 100%;
            max-width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 6px 8px;
            font: inherit;
            font-size: 12.5px;
            min-height: var(--form-input-min-height);
        }

        .field textarea { min-height: 92px; resize: vertical; }

        .form-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        @media (max-width: 1200px) {
            .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 760px) {
            .form-grid { grid-template-columns: 1fr; }
            .field-wide, .form-actions { grid-column: auto; }
            .frame-footer { justify-content: stretch; }
        }
    </style>
</head>
<body class="catalog-standard">
<main class="wrap">
    <section class="panel">
        <div class="head">
            <span class="badge">Vista operativa</span>
        </div>
        <div class="body">
            <div class="toolbar">
                <input id="tableFilter" type="search" placeholder="Buscar por codigo, nombre o descripcion...">
                <button id="btnRefresh" class="btn btn-compact" type="button">Actualizar</button>
            </div>
            <div id="tableContainer" class="table-wrap"></div>
            <div class="frame-footer">
                <button id="btnAdd" class="btn btn-compact btn-add" type="button">Agregar</button>
                <button id="btnEdit" class="btn btn-compact btn-edit" type="button">Editar</button>
                <button id="btnDelete" class="btn btn-compact btn-delete" type="button">Eliminar</button>
            </div>
            <div id="status" class="status">Selecciona un registro y usa Agregar, Editar o Eliminar.</div>
        </div>
    </section>
</main>

<div id="editorOverlay" class="editor-overlay" aria-hidden="true"></div>
<section id="editorFrame" class="editor-frame" aria-hidden="true">
    <div class="editor-head">
        <h2 id="formTitle">Nuevo tipo de producto</h2>
        <button id="btnCloseEditor" class="btn btn-compact" type="button">Cerrar</button>
    </div>
    <div class="editor-body">
        <form id="typeForm" class="form-grid cols-2">
            <input id="typeId" type="hidden">

            <div class="field">
                <label for="code">Codigo</label>
                <input id="code" name="code" type="text" maxlength="80" pattern="[A-Za-z0-9\-_.]+" title="Solo letras, numeros, guion, guion bajo y punto" placeholder="TIPO-BAR">
            </div>

            <div class="field">
                <label for="name" class="required">Nombre</label>
                <input id="name" name="name" type="text" maxlength="120" required placeholder="Bar, Cocina, Materia Prima...">
            </div>

            <div class="field field-wide">
                <label for="description">Descripcion</label>
                <textarea id="description" name="description" maxlength="1000" placeholder="Uso o alcance del tipo"></textarea>
            </div>

            <div class="form-actions">
                <button id="btnSubmit" class="btn btn-compact btn-add" type="submit">Guardar</button>
                <button id="btnCancelEdit" class="btn btn-compact" type="button">Cancelar</button>
            </div>
        </form>
    </div>
</section>

<script>
    const role = localStorage.getItem('ordena-facil-role') || localStorage.getItem('barandrest-role') || 'guest';
    const params = new URLSearchParams(window.location.search);
    const theme = (params.get('theme') === 'premium' || localStorage.getItem('ordena-facil-theme') === 'premium') ? 'premium' : 'clasico';

    document.documentElement.setAttribute('data-theme', theme);

    const roleBadge = document.getElementById('roleBadge');
    const typeForm = document.getElementById('typeForm');
    const formTitle = document.getElementById('formTitle');
    const statusBox = document.getElementById('status');
    const tableContainer = document.getElementById('tableContainer');
    const tableFilter = document.getElementById('tableFilter');
    const editorFrame = document.getElementById('editorFrame');
    const editorOverlay = document.getElementById('editorOverlay');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const btnAdd = document.getElementById('btnAdd');
    const btnEdit = document.getElementById('btnEdit');
    const btnDelete = document.getElementById('btnDelete');
    const btnCloseEditor = document.getElementById('btnCloseEditor');

    const fields = {
        id: document.getElementById('typeId'),
        code: document.getElementById('code'),
        name: document.getElementById('name'),
        description: document.getElementById('description'),
    };

    let types = [];
    let canManageCatalog = false;
    let selectedTypeId = null;
    const UI_TEXT = {
        noManagePermission: 'No tienes permisos para administrar el catalogo.',
        noManageRole: 'Tu rol actual no tiene permisos para administrar el catalogo.',
        noEditSelection: 'Selecciona un registro para editar.',
        noDeleteSelection: 'Selecciona un registro para eliminar.',
        noCreateEditPermission: 'No tienes permisos para crear o editar registros del catalogo.',
        ready: 'Selecciona un registro y usa Agregar, Editar o Eliminar.',
        deleted: 'Registro eliminado correctamente.',
        updated: 'Registro actualizado correctamente.',
        created: 'Registro creado correctamente.',
        canceled: 'Edicion cancelada.',
        refreshed: 'Listado actualizado.',
    };

    if (roleBadge) {
        roleBadge.textContent = `Rol: ${role}`;
    }

    function setStatus(message, type) {
        statusBox.textContent = message;
        statusBox.classList.remove('ok', 'error');
        if (type) statusBox.classList.add(type);
    }

    function requestJson(url, options = {}) {
        const headers = {
            Accept: 'application/json',
            'X-USER-ROLE': role,
            ...options.headers,
        };

        return fetch(url, { ...options, headers }).then(async (response) => {
            if (!response.ok) {
                let message = `HTTP ${response.status}`;

                try {
                    const payload = await response.json();
                    const errors = payload?.errors && typeof payload.errors === 'object' ? payload.errors : null;
                    if (errors) {
                        const first = Object.values(errors).flat().find(Boolean);
                        if (first) message = String(first);
                    } else if (payload?.message) {
                        message = String(payload.message);
                    }
                } catch (_error) {
                    const text = await response.text();
                    if (text) message = `${message} - ${text}`;
                }

                throw new Error(message);
            }

            if (response.status === 204) return null;
            return response.json();
        });
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getSelectedType() {
        return types.find((item) => Number(item.id) === Number(selectedTypeId)) || null;
    }

    function updateActionButtons() {
        const hasSelection = !!getSelectedType();
        btnAdd.disabled = !canManageCatalog;
        btnEdit.disabled = !canManageCatalog || !hasSelection;
        btnDelete.disabled = !canManageCatalog || !hasSelection;
    }

    function setFormEditable(enabled) {
        Object.values(fields).forEach((field) => {
            if (field.id === 'typeId') return;
            field.disabled = !enabled;
        });

        btnSubmit.disabled = !enabled;
        btnCancelEdit.disabled = !enabled;
        btnCloseEditor.disabled = !enabled;
        updateActionButtons();
    }

    function clearForm() {
        fields.id.value = '';
        fields.code.value = '';
        fields.name.value = '';
        fields.description.value = '';
        formTitle.textContent = 'Nuevo tipo de producto';
        btnSubmit.textContent = 'Guardar';
    }

    function collectPayload() {
        return {
            code: fields.code.value.trim().toUpperCase() || null,
            name: fields.name.value.trim(),
            description: fields.description.value.trim() || null,
        };
    }

    function closeEditor() {
        editorOverlay.classList.remove('active');
        editorFrame.classList.remove('active');
        editorOverlay.setAttribute('aria-hidden', 'true');
        editorFrame.setAttribute('aria-hidden', 'true');
        clearForm();
    }

    function openEditor(mode) {
        if (!canManageCatalog) {
            setStatus(UI_TEXT.noManagePermission, 'error');
            return;
        }

        clearForm();

        if (mode === 'edit') {
            const type = getSelectedType();
            if (!type) {
                setStatus(UI_TEXT.noEditSelection, 'error');
                return;
            }

            fields.id.value = String(type.id);
            fields.code.value = type.code || '';
            fields.name.value = type.name || '';
            fields.description.value = type.description || '';
            const typeName = String(type.name || '').trim();
            formTitle.textContent = typeName
                ? `Editar tipo de producto: ${typeName}`
                : `Editar tipo de producto #${type.id}`;
            btnSubmit.textContent = 'Guardar cambios';
        } else {
            formTitle.textContent = 'Nuevo tipo de producto';
            btnSubmit.textContent = 'Guardar';
        }

        editorOverlay.classList.add('active');
        editorFrame.classList.add('active');
        editorOverlay.setAttribute('aria-hidden', 'false');
        editorFrame.setAttribute('aria-hidden', 'false');
        fields.name.focus();
    }

    function renderTable(rows) {
        if (!rows.length) {
            tableContainer.innerHTML = '<div class="empty">No hay tipos de producto registrados.</div>';
            selectedTypeId = null;
            updateActionButtons();
            return;
        }

        const body = rows.map((type) => {
            const selected = Number(selectedTypeId) === Number(type.id) ? ' class="selected"' : '';
            return `
                <tr data-type-id="${type.id}"${selected}>
                    <td>${escapeHtml(type.code || '-')}</td>
                    <td>${escapeHtml(type.name || '-')}</td>
                    <td>${escapeHtml(type.description || '-')}</td>
                    <td>${type.created_at ? new Date(type.created_at).toLocaleDateString() : '-'}</td>
                </tr>
            `;
        }).join('');

        tableContainer.innerHTML = `
            <table>
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Creacion</th>
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
            renderTable(types);
            return;
        }

        const filtered = types.filter((type) => {
            const line = `${type.code || ''} ${type.name || ''} ${type.description || ''}`.toLowerCase();
            return line.includes(term);
        });

        renderTable(filtered);
    }

    async function loadTypes() {
        try {
            const data = await requestJson('/api/product-types');
            types = Array.isArray(data) ? data : [];

            if (!getSelectedType()) {
                selectedTypeId = null;
            }

            applyFilter();
        } catch (error) {
            tableContainer.innerHTML = '<div class="empty">No fue posible cargar tipos de producto.</div>';
            setStatus(`Error cargando tipos: ${String(error.message || error)}`, 'error');
        }
    }

    async function loadCapabilities() {
        try {
            const payload = await requestJson('/api/system/capabilities');
            const capabilities = Array.isArray(payload?.capabilities) ? payload.capabilities : [];
            canManageCatalog = capabilities.includes('manage_catalog');

            if (!canManageCatalog) {
                setFormEditable(false);
                setStatus(UI_TEXT.noManageRole, 'error');
            } else {
                setFormEditable(true);
                setStatus(UI_TEXT.ready, null);
            }
        } catch (error) {
            canManageCatalog = false;
            setFormEditable(false);
            setStatus(`No se pudieron cargar permisos: ${String(error.message || error)}`, 'error');
        }
    }

    async function removeSelectedType() {
        if (!canManageCatalog) return;

        const type = getSelectedType();
        if (!type) {
            setStatus(UI_TEXT.noDeleteSelection, 'error');
            return;
        }

        const label = type?.name ? `"${type.name}"` : `#${type.id}`;
        if (!window.confirm(`¿Deseas eliminar el tipo de producto ${label}? Esta accion no se puede deshacer.`)) return;

        try {
            await requestJson(`/api/product-types/${type.id}`, { method: 'DELETE' });
            selectedTypeId = null;
            setStatus(UI_TEXT.deleted, 'ok');
            await loadTypes();
        } catch (error) {
            setStatus(`No se pudo eliminar: ${String(error.message || error)}`, 'error');
        }
    }

    typeForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!canManageCatalog) {
            setStatus(UI_TEXT.noCreateEditPermission, 'error');
            return;
        }

        const payload = collectPayload();
        const editingId = fields.id.value ? Number(fields.id.value) : null;

        try {
            if (editingId) {
                await requestJson(`/api/product-types/${editingId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                selectedTypeId = editingId;
                setStatus(UI_TEXT.updated, 'ok');
            } else {
                await requestJson('/api/product-types', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                setStatus(UI_TEXT.created, 'ok');
            }

            closeEditor();
            await loadTypes();
        } catch (error) {
            setStatus(`No se pudo guardar: ${String(error.message || error)}`, 'error');
        }
    });

    btnCancelEdit.addEventListener('click', () => {
        closeEditor();
        setStatus(UI_TEXT.canceled, null);
    });

    btnCloseEditor.addEventListener('click', () => {
        closeEditor();
    });

    editorOverlay.addEventListener('click', () => {
        closeEditor();
    });

    document.getElementById('btnRefresh').addEventListener('click', async () => {
        await loadTypes();
        setStatus(UI_TEXT.refreshed, null);
    });

    btnAdd.addEventListener('click', () => {
        openEditor('add');
    });

    btnEdit.addEventListener('click', () => {
        openEditor('edit');
    });

    btnDelete.addEventListener('click', async () => {
        await removeSelectedType();
    });

    tableFilter.addEventListener('input', applyFilter);

    tableContainer.addEventListener('click', (event) => {
        const row = event.target.closest('tr[data-type-id]');
        if (!row) return;

        selectedTypeId = Number(row.dataset.typeId);
        applyFilter();
        const selected = getSelectedType();
        if (selected) {
            setStatus(`Registro seleccionado: ${selected.name}`, null);
        }
    });

    fields.code.addEventListener('blur', () => {
        fields.code.value = fields.code.value.trim().toUpperCase();
    });

    async function init() {
        clearForm();
        await loadCapabilities();
        await loadTypes();
        updateActionButtons();
    }

    init();
</script>
</body>
</html>
