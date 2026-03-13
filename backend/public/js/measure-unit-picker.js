(function () {
    function normalizeText(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function createMeasureUnitPicker(config) {
        const unitInput = config.unitInput;
        const toggleButton = config.toggleButton;
        const pickerPanel = config.pickerPanel;
        const searchInput = config.searchInput;
        const optionsContainer = config.optionsContainer;
        const suggestionContainer = config.suggestionContainer;
        const emptyHtml = config.emptyHtml || '<div class="empty">Sin coincidencias en catalogo de medidas.</div>';

        let measures = [];
        let enabled = true;

        function renderOptions(term) {
            const query = normalizeText(term);
            const filtered = measures.filter((measure) => {
                const line = normalizeText(`${measure.name || ''} ${measure.abbreviation || ''} ${measure.description || ''}`);
                return !query || line.includes(query);
            });

            if (!filtered.length) {
                optionsContainer.innerHTML = emptyHtml;
                return;
            }

            optionsContainer.innerHTML = filtered.map((measure) => {
                const code = measure.abbreviation || measure.name;
                return `<button type="button" class="measure-option" data-code="${code}" data-name="${measure.name || ''}"><code>${code}</code><span>${measure.name || ''}</span></button>`;
            }).join('');
        }

        function showSuggestion() {
            const typed = normalizeText(unitInput.value);

            if (!typed) {
                suggestionContainer.textContent = '';
                renderOptions(searchInput.value || '');
                return;
            }

            const exact = measures.find((measure) => {
                const code = normalizeText(measure.abbreviation || '');
                const name = normalizeText(measure.name || '');
                return code === typed || name === typed;
            });

            if (exact) {
                const code = exact.abbreviation || exact.name;
                suggestionContainer.innerHTML = `Codigo detectado: <strong>${code}</strong> (${exact.name})`;
                renderOptions(unitInput.value);
                return;
            }

            const suggested = measures.find((measure) => {
                const code = normalizeText(measure.abbreviation || '');
                const name = normalizeText(measure.name || '');
                return code.includes(typed) || name.includes(typed);
            });

            if (suggested) {
                const code = suggested.abbreviation || suggested.name;
                suggestionContainer.innerHTML = `Sugerencia: <strong>${code}</strong> (${suggested.name})`;
            } else {
                suggestionContainer.textContent = 'No hay coincidencias en catalogo para esta captura.';
            }

            renderOptions(unitInput.value);
        }

        function open() {
            pickerPanel.classList.remove('collapsed');
            pickerPanel.setAttribute('aria-hidden', 'false');
            toggleButton.setAttribute('aria-expanded', 'true');
            renderOptions(searchInput.value || unitInput.value || '');
        }

        function close() {
            pickerPanel.classList.add('collapsed');
            pickerPanel.setAttribute('aria-hidden', 'true');
            toggleButton.setAttribute('aria-expanded', 'false');
        }

        function toggle() {
            if (pickerPanel.classList.contains('collapsed')) {
                open();
                searchInput.focus();
                searchInput.select();
                return;
            }
            close();
        }

        function reset() {
            searchInput.value = '';
            suggestionContainer.textContent = '';
            renderOptions('');
            close();
        }

        function setEnabled(value) {
            enabled = !!value;
            toggleButton.disabled = !enabled;
            searchInput.disabled = !enabled;
        }

        toggleButton.addEventListener('click', () => {
            if (!enabled) return;
            toggle();
        });

        searchInput.addEventListener('input', () => {
            renderOptions(searchInput.value);
        });

        optionsContainer.addEventListener('click', (event) => {
            const option = event.target.closest('.measure-option');
            if (!option) return;

            unitInput.value = option.dataset.code || '';
            showSuggestion();
            close();

            if (typeof config.onSelect === 'function') {
                config.onSelect(option.dataset.code || '', option.dataset.name || '');
            }
        });

        unitInput.addEventListener('input', () => {
            if (pickerPanel.classList.contains('collapsed')) {
                open();
            }
            showSuggestion();
        });

        return {
            setMeasures(newMeasures) {
                measures = Array.isArray(newMeasures) ? newMeasures : [];
                renderOptions('');
            },
            showSuggestion,
            open,
            close,
            reset,
            setEnabled,
            setErrorMessage(message) {
                optionsContainer.innerHTML = `<div class="empty">${message}</div>`;
            },
        };
    }

    window.createMeasureUnitPicker = createMeasureUnitPicker;
})();
