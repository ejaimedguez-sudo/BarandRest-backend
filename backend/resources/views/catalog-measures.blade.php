<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catalogo de Medidas - Ordena Facil</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <style>
        :root,
        :root[data-theme="clasico"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
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
            min-width: 760px;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 13px;
            vertical-align: top;
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

        .btn:hover { filter: brightness(1.06); }
        .btn:disabled { opacity: .5; cursor: not-allowed; }

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

        .empty {
            padding: 16px;
            color: var(--muted);
            font-size: 13px;
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

        .form-grid { display: grid; gap: 10px; }
        .field { display: grid; gap: 4px; }

        .field label {
            font-size: 12px;
            color: var(--muted);
        }

        .field input,
        .field textarea {
            border: 1px solid var(--border);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 9px 10px;
            font: inherit;
            min-height: 40px;
        }

        .field textarea {
            min-height: 110px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
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
            <h1>Catalogo de Medidas</h1>
            <p>Administra unidades y medidas antes de registrar productos del inventario.</p>
        </div>
        <div class="badge" id="roleBadge">Rol: guest</div>
    </section>

    <section class="panel">
        <div class="head">
            <h2>Medidas registradas</h2>
            <button id="btnRefresh" class="btn" type="button">Actualizar</button>
        </div>
        <div class="body">
            <div class="toolbar">
                <input id="tableFilter" type="search" placeholder="Buscar por nombre o abreviatura">
            </div>
            <div id="tableContainer" class="table-wrap"></div>
            <div class="frame-footer">
                <button id="btnAdd" class="btn btn-add" type="button">Agregar</button>
                <button id="btnEdit" class="btn btn-edit" type="button">Editar</button>
                <button id="btnDelete" class="btn btn-delete" type="button">Eliminar</button>
            </div>
            <div id="status" class="status">Selecciona una medida y usa los botones de accion.</div>
        </div>
    </section>
</main>

<div id="editorOverlay" class="editor-overlay" aria-hidden="true"></div>
<section id="editorFrame" class="editor-frame" aria-hidden="true">
    <div class="editor-head">
        <h2 id="formTitle">Nueva medida</h2>
        <button id="btnCloseEditor" class="btn" type="button">Cerrar</button>
    </div>
    <div class="editor-body">
        <form id="measureForm" class="form-grid">
            <input id="measureId" type="hidden">

            <div class="field">
                <label for="name">Nombre *</label>
                <input id="name" name="name" type="text" maxlength="120" required>
            </div>

            <div class="field">
                <label for="abbreviation">Abreviatura</label>
                <input id="abbreviation" name="abbreviation" type="text" maxlength="20" placeholder="kg, ml, pza">
            </div>

            <div class="field">
                <label for="description">Descripcion</label>
                <textarea id="description" name="description" maxlength="500" placeholder="Uso o detalle de la medida"></textarea>
            </div>

            <div class="form-actions">
                <button id="btnSubmit" class="btn btn-add" type="submit">Guardar medida</button>
                <button id="btnCancelEdit" class="btn" type="button">Cancelar</button>
            </div>
        </form>
    </div>
</section>

<script>
    const role = localStorage.getItem('ordena-facil-role') || localStorage.getItem('barandrest-role') || 'guest';
    const params = new URLSearchParams(window.location.search);
    const theme = (params.get('theme') === 'premium' || localStorage.getItem('ordena-facil-theme') === 'premium') ? 'premium' : 'clasico';

    const roleBadge = document.getElementById('roleBadge');
    const measureForm = document.getElementById('measureForm');
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
        id: document.getElementById('measureId'),
        name: document.getElementById('name'),
        abbreviation: document.getElementById('abbreviation'),
        description: document.getElementById('description'),
    };

    let measures = [];
    let canManageCatalog = false;
    let selectedMeasureId = null;

    document.documentElement.setAttribute('data-theme', theme);
    roleBadge.textContent = `Rol: ${role}`;

    function setStatus(message, type) {
        statusBox.textContent = message;
        statusBox.classList.remove('ok', 'error');
        if (type) statusBox.classList.add(type);
    }

    function clearForm() {
        fields.id.value = '';
        fields.name.value = '';
        fields.abbreviation.value = '';
        fields.description.value = '';
        formTitle.textContent = 'Nueva medida';
        btnSubmit.textContent = 'Guardar medida';
    }

    function getSelectedMeasure() {
        return measures.find((item) => Number(item.id) === Number(selectedMeasureId)) || null;
    }

    function updateActionButtons() {
        const hasSelection = !!getSelectedMeasure();
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
            const measure = getSelectedMeasure();
            if (!measure) {
                setStatus('Selecciona una medida para editar.', 'error');
                return;
            }

            fields.id.value = String(measure.id);
            fields.name.value = measure.name || '';
            fields.abbreviation.value = measure.abbreviation || '';
            fields.description.value = measure.description || '';
            formTitle.textContent = `Editar medida #${measure.id}`;
            btnSubmit.textContent = 'Guardar cambios';
        } else {
            clearForm();
            formTitle.textContent = 'Agregar medida';
            btnSubmit.textContent = 'Guardar medida';
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
            if (field.id === 'measureId') return;
            field.disabled = !enabled;
        });

        btnSubmit.disabled = !enabled;
        btnCancelEdit.disabled = !enabled;
        btnCloseEditor.disabled = !enabled;
        updateActionButtons();
    }

    function collectPayload() {
        return {
            name: fields.name.value.trim(),
            abbreviation: fields.abbreviation.value.trim() || null,
            description: fields.description.value.trim() || null,
        };
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
                setStatus('Selecciona una medida y usa Agregar, Editar o Eliminar.', null);
            }
        } catch (error) {
            canManageCatalog = false;
            setFormEditable(false);
            setStatus(`No se pudieron cargar permisos: ${String(error.message || error)}`, 'error');
        }
    }

    function renderTable(rows) {
        if (!rows.length) {
            tableContainer.innerHTML = '<div class="empty">No hay medidas registradas.</div>';
            selectedMeasureId = null;
            updateActionButtons();
            return;
        }

        const body = rows.map((measure) => {
            const selected = Number(selectedMeasureId) === Number(measure.id) ? ' class="selected"' : '';
            return `
                <tr data-measure-id="${measure.id}"${selected}>
                    <td>${measure.name || '-'}</td>
                    <td>${measure.abbreviation || '-'}</td>
                    <td>${measure.description || '-'}</td>
                    <td>${measure.created_at ? new Date(measure.created_at).toLocaleDateString() : '-'}</td>
                </tr>
            `;
        }).join('');

        tableContainer.innerHTML = `
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Abreviatura</th>
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
            renderTable(measures);
            return;
        }

        const filtered = measures.filter((measure) => {
            const line = `${measure.name || ''} ${measure.abbreviation || ''} ${measure.description || ''}`.toLowerCase();
            return line.includes(term);
        });

        renderTable(filtered);
    }

    async function loadMeasures() {
        try {
            const data = await requestJson('/api/measures');
            measures = Array.isArray(data) ? data : [];

            if (!getSelectedMeasure()) {
                selectedMeasureId = null;
            }

            applyFilter();
        } catch (error) {
            tableContainer.innerHTML = '<div class="empty">No fue posible cargar medidas.</div>';
            setStatus(`Error cargando medidas: ${String(error.message || error)}`, 'error');
        }
    }

    async function removeSelectedMeasure() {
        if (!canManageCatalog) return;

        const measure = getSelectedMeasure();
        if (!measure) {
            setStatus('Selecciona una medida para eliminar.', 'error');
            return;
        }

        const label = measure?.name ? `"${measure.name}"` : `#${measure.id}`;
        if (!window.confirm(`¿Deseas eliminar la medida ${label}?`)) return;

        try {
            await requestJson(`/api/measures/${measure.id}`, { method: 'DELETE' });
            selectedMeasureId = null;
            setStatus('Medida eliminada correctamente.', 'ok');
            await loadMeasures();
        } catch (error) {
            setStatus(`No se pudo eliminar: ${String(error.message || error)}`, 'error');
        }
    }

    measureForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!canManageCatalog) {
            setStatus('No tienes permisos para crear o editar medidas.', 'error');
            return;
        }

        const payload = collectPayload();
        const editingId = fields.id.value ? Number(fields.id.value) : null;

        try {
            if (editingId) {
                await requestJson(`/api/measures/${editingId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                selectedMeasureId = editingId;
                setStatus('Medida actualizada correctamente.', 'ok');
            } else {
                await requestJson('/api/measures', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                setStatus('Medida creada correctamente.', 'ok');
            }

            closeEditor();
            await loadMeasures();
        } catch (error) {
            setStatus(`No se pudo guardar: ${String(error.message || error)}`, 'error');
        }
    });

    btnCancelEdit.addEventListener('click', () => {
        closeEditor();
        setStatus('Edicion cancelada.', null);
    });

    btnCloseEditor.addEventListener('click', () => {
        closeEditor();
    });

    editorOverlay.addEventListener('click', () => {
        closeEditor();
    });

    document.getElementById('btnRefresh').addEventListener('click', async () => {
        await loadMeasures();
        setStatus('Listado actualizado.', null);
    });

    btnAdd.addEventListener('click', () => {
        openEditor('add');
    });

    btnEdit.addEventListener('click', () => {
        openEditor('edit');
    });

    btnDelete.addEventListener('click', async () => {
        await removeSelectedMeasure();
    });

    tableFilter.addEventListener('input', applyFilter);

    tableContainer.addEventListener('click', (event) => {
        const row = event.target.closest('tr[data-measure-id]');
        if (!row) return;

        selectedMeasureId = Number(row.dataset.measureId);
        applyFilter();
        const selected = getSelectedMeasure();
        if (selected) {
            setStatus(`Medida seleccionada: ${selected.name}`, null);
        }
    });

    async function init() {
        clearForm();
        await loadCapabilities();
        await loadMeasures();
        updateActionButtons();
    }

    init();
</script>
</body>
</html>
