document.addEventListener('DOMContentLoaded', function () {
    const formRegistro = document.getElementById('formRegistroEntrada');
    const clienteSelectedText = document.getElementById('cliente_selected_text');
    const clienteCedulaInput = document.getElementById('cliente_cedula');
    const clienteIdHidden = document.getElementById('selected_cliente_id');
    const horaInput = document.getElementById('hora');

    const tablaEntradasBody = document.getElementById('tablaEntradasBody');
    const searchInput = document.getElementById('searchInput');
    const btnBuscar = document.getElementById('btnBuscar');

    const searchClientInput = document.getElementById('searchClient');
    const clientesTablaBody = document.querySelector('#clientesTabla tbody');

    const clienteModalEl = document.getElementById('clienteModal');
    const editarModalEl = document.getElementById('editarModal');
    const eliminarModalEl = document.getElementById('eliminarModal');

    let currentSearchTerm = '';
    let timeoutSearch = null;

    const getEndpoint = (queryString) => {
        const basePath = window.location.pathname;
        return `${basePath}?${queryString}`;
    };

    function getModal(el) {
        if (!el || typeof bootstrap === 'undefined') return null;
        return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    }

    function showMessage(msg, type = 'success') {
        const toast = document.getElementById('toastMessage');
        if (!toast) { alert(msg); return; }
        toast.textContent = msg;
        toast.className = type;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3500);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m] || m));
    }

    async function parseJsonSafe(res) {
        const text = await res.text();
        if (text.trim().startsWith('<')) {
            console.error('El servidor devolvió HTML en vez de JSON:\n', text);
            throw new Error('El servidor devolvió HTML. Revisa la consola.');
        }
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('JSON inválido:\n', text.slice(0, 800));
            throw new Error('El servidor devolvió una respuesta no válida.');
        }
    }

    // ==================== MODAL CLIENTES ====================
    function cargarClientesModal(termino = '') {
        if (!clientesTablaBody) return;

        clientesTablaBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted p-3"><i class="fas fa-spinner fa-spin"></i> Cargando clientes...</td></tr>';

        const url = getEndpoint(`page=asistencia&action=buscar_clientes_ajax&termino=${encodeURIComponent(termino)}`);

        fetch(url)
            .then(async res => {
                if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
                return parseJsonSafe(res);
            })
            .then(data => {
                const lista = Array.isArray(data) ? data : (data.data || []);

                if (lista.length === 0) {
                    clientesTablaBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted p-3">No se encontraron clientes.</td></tr>';
                    return;
                }

                let html = '';
                lista.forEach(c => {
                    const cedula = c.cedula || c.cedula_persona || '';
                    const nombre = c.nombre || `${c.nombre_cliente || ''} ${c.apellido || ''}`.trim();
                    const correo = c.correo || '—';
                    const telefono = c.telefono || '—';

                    html += `
                        <tr>
                            <td>${escapeHtml(cedula)}</td>
                            <td>${escapeHtml(nombre)}</td>
                            <td>${escapeHtml(correo)}</td>
                            <td>${escapeHtml(telefono)}</td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-secondary btn-select-client"
                                        data-cedula="${escapeHtml(cedula)}"
                                        data-nombre="${escapeHtml(nombre)}">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    `;
                });
                clientesTablaBody.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                clientesTablaBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger p-3">⚠️ ${err.message}</td></tr>`;
            });
    }

    const selectorBtn = document.querySelector('.modal-select-btn');
    if (selectorBtn) {
        selectorBtn.addEventListener('click', () => {
            if (searchClientInput) searchClientInput.value = '';
            cargarClientesModal('');
        });
    }

    if (searchClientInput) {
        searchClientInput.addEventListener('input', function () {
            clearTimeout(timeoutSearch);
            timeoutSearch = setTimeout(() => cargarClientesModal(this.value.trim()), 300);
        });
    }

    if (clientesTablaBody) {
        clientesTablaBody.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-select-client');
            if (!btn) return;

            const cedula = btn.getAttribute('data-cedula');
            const nombre = btn.getAttribute('data-nombre');

            if (clienteIdHidden) clienteIdHidden.value = cedula;
            if (clienteCedulaInput) clienteCedulaInput.value = cedula;
            if (clienteSelectedText) clienteSelectedText.textContent = nombre;

            const modal = getModal(clienteModalEl);
            if (modal) modal.hide();
        });
    }

    // ==================== FORM REGISTRO ====================
    if (formRegistro) {
        formRegistro.addEventListener('submit', async function (e) {
            e.preventDefault();

            const cedula = clienteIdHidden ? clienteIdHidden.value : '';
            if (!cedula) {
                showMessage('⚠️ Debe seleccionar un cliente primero.', 'error');
                return;
            }

            const btnSubmit = this.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.dataset.originalText = btnSubmit.innerHTML;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';
            }

            try {
                const checkUrl = getEndpoint(
                    `page=asistencia&action=verificar_entrada_reciente&cedula=${encodeURIComponent(cedula)}`
                );
                const checkRes = await fetch(checkUrl);
                if (!checkRes.ok) throw new Error(`Error HTTP ${checkRes.status}`);
                const checkData = await parseJsonSafe(checkRes);

                if (checkData.existe) {
                    const hora = (checkData.ultima_hora || '').split(' ')[1] || '';
                    showMessage(`⚠️ Este cliente ya tiene una entrada a las ${hora}.`, 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('cedula', cedula);
                if (horaInput && horaInput.value) formData.append('hora', horaInput.value);

                const res = await fetch(getEndpoint('page=asistencia&action=registrar'), {
                    method: 'POST',
                    body: formData
                });
                if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
                const data = await parseJsonSafe(res);

                if (data.success) {
                    showMessage('✅ Entrada registrada correctamente', 'success');
                    if (clienteIdHidden) clienteIdHidden.value = '';
                    if (clienteCedulaInput) clienteCedulaInput.value = '';
                    if (clienteSelectedText) clienteSelectedText.textContent = 'Seleccione cliente';
                    if (horaInput) horaInput.value = '';
                    cargarEntradas(currentSearchTerm);
                } else {
                    showMessage('❌ ' + (data.message || 'No se pudo registrar'), 'error');
                }
            } catch (err) {
                console.error(err);
                showMessage('❌ ' + err.message, 'error');
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = btnSubmit.dataset.originalText || 'Registrar Entrada';
                }
            }
        });
    }

    // ==================== TABLA ENTRADAS ====================
    function cargarEntradas(termino = '') {
        currentSearchTerm = termino;
        const url = getEndpoint(`page=asistencia&action=buscar_entradas_ajax&termino=${encodeURIComponent(termino)}`);

        fetch(url)
            .then(async res => {
                if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
                return parseJsonSafe(res);
            })
            .then(data => {
                const lista = Array.isArray(data) ? data : (data.data || []);
                actualizarTablaEntradas(lista);
            })
            .catch(console.error);
    }

    function actualizarTablaEntradas(entradas) {
        if (!tablaEntradasBody) return;

        if (!Array.isArray(entradas) || entradas.length === 0) {
            tablaEntradasBody.innerHTML = '<tr><td colspan="4" class="text-center">No hay entradas registradas hoy.</td></tr>';
            return;
        }

        let html = '';
        entradas.forEach(e => {
            let hora = '—';
            if (e.fecha) {
                const parts = e.fecha.split(' ');
                hora = parts[1] || e.fecha;
            }

            const id = e.id_asistencia || '';
            const cedula = e.cedula_cliente || e.cedula || '';
            const nombre = e.nombre_cliente || e.nombre || '';

            html += `
                <tr data-id="${id}">
                    <td>${escapeHtml(hora)}</td>
                    <td>${escapeHtml(cedula)}</td>
                    <td>${escapeHtml(nombre)}</td>
                    <td>
                        <div class="acciones-botones">
                            <button type="button" class="btn btn-sm btn-warning editar-btn" data-id="${id}" data-hora="${escapeHtml(hora)}" data-cedula="${escapeHtml(cedula)}" data-nombre="${escapeHtml(nombre)}"><i class="fas fa-edit"></i> Editar</button>
                            <button type="button" class="btn btn-sm btn-danger eliminar-btn" data-id="${id}" data-hora="${escapeHtml(hora)}" data-cedula="${escapeHtml(cedula)}" data-nombre="${escapeHtml(nombre)}"><i class="fas fa-trash-alt"></i> Eliminar</button>
                        </div>
                    </td>
                </tr>
            `;
        });
        tablaEntradasBody.innerHTML = html;
    }

    // Delegación de eventos para Editar/Eliminar
    if (tablaEntradasBody) {
        tablaEntradasBody.addEventListener('click', (e) => {
            const btnEditar = e.target.closest('.editar-btn');
            if (btnEditar) {
                abrirEditarModal(btnEditar);
                return;
            }

            const btnEliminar = e.target.closest('.eliminar-btn');
            if (btnEliminar) {
                abrirEliminarModal(btnEliminar);
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', () => {
            clearTimeout(timeoutSearch);
            timeoutSearch = setTimeout(() => cargarEntradas(searchInput.value.trim()), 300);
        });
    }

    if (btnBuscar) {
        btnBuscar.addEventListener('click', () => cargarEntradas(searchInput ? searchInput.value.trim() : ''));
    }

    // ==================== EDITAR ====================
    function abrirEditarModal(btn) {
        document.getElementById('edit_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_cedula').value = btn.getAttribute('data-cedula');
        document.getElementById('edit_nombre').value = btn.getAttribute('data-nombre');
        document.getElementById('edit_hora').value = btn.getAttribute('data-hora');

        const modal = getModal(editarModalEl);
        if (modal) modal.show();
    }

    const guardarEdicionBtn = document.getElementById('guardarEdicion');
    if (guardarEdicionBtn) {
        guardarEdicionBtn.addEventListener('click', async () => {
            const id = document.getElementById('edit_id').value;
            const hora = document.getElementById('edit_hora').value;

            if (!hora) {
                showMessage('⚠️ Debe indicar una hora.', 'error');
                return;
            }

            guardarEdicionBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('hora', hora);

                const res = await fetch(getEndpoint('page=asistencia&action=editar'), {
                    method: 'POST',
                    body: formData
                });
                if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
                const data = await parseJsonSafe(res);

                if (data.success) {
                    showMessage('✅ Hora actualizada', 'success');
                    const modal = getModal(editarModalEl);
                    if (modal) modal.hide();
                    cargarEntradas(currentSearchTerm);
                } else {
                    showMessage('❌ ' + (data.message || 'Error al actualizar'), 'error');
                }
            } catch (err) {
                console.error(err);
                showMessage('❌ ' + err.message, 'error');
            } finally {
                guardarEdicionBtn.disabled = false;
            }
        });
    }

    // ==================== ELIMINAR ====================
    function abrirEliminarModal(btn) {
        document.getElementById('delete_id').value = btn.getAttribute('data-id');
        document.getElementById('delete_hora').textContent = btn.getAttribute('data-hora');
        document.getElementById('delete_cedula').textContent = btn.getAttribute('data-cedula');
        document.getElementById('delete_nombre').textContent = btn.getAttribute('data-nombre');

        const modal = getModal(eliminarModalEl);
        if (modal) modal.show();
    }

    const confirmarEliminarBtn = document.getElementById('confirmarEliminar');
    if (confirmarEliminarBtn) {
        confirmarEliminarBtn.addEventListener('click', async () => {
            const id = document.getElementById('delete_id').value;
            confirmarEliminarBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('id', id);

                const res = await fetch(getEndpoint('page=asistencia&action=eliminar'), {
                    method: 'POST',
                    body: formData
                });
                if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
                const data = await parseJsonSafe(res);

                if (data.success) {
                    showMessage('🗑️ Entrada eliminada', 'success');
                    const modal = getModal(eliminarModalEl);
                    if (modal) modal.hide();
                    cargarEntradas(currentSearchTerm);
                } else {
                    showMessage('❌ ' + (data.message || 'Error al eliminar'), 'error');
                }
            } catch (err) {
                console.error(err);
                showMessage('❌ ' + err.message, 'error');
            } finally {
                confirmarEliminarBtn.disabled = false;
            }
        });
    }

    // ==================== HISTORIAL DE ASISTENCIAS ====================
    // Abre el modal SIN requerir cliente. Muestra TODAS las asistencias.
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('#btnHistorial');
        if (!btn) return;

        const modalEl = document.getElementById('historialModal');
        if (!modalEl) {
            console.error('🔴 [Historial] No existe #historialModal');
            return;
        }

        const modal = getModal(modalEl);
        if (modal) modal.show();

        const histTermino = document.getElementById('hist_termino');
        const histInicio  = document.getElementById('hist_inicio');
        const histFin     = document.getElementById('hist_fin');

        if (histTermino) histTermino.value = '';
        if (histInicio)  histInicio.value = '';
        if (histFin)     histFin.value = '';

        await cargarHistorialGlobal();
    });

    async function cargarHistorialGlobal() {
        const historialBody = document.getElementById('historialBody');
        const histTermino   = document.getElementById('hist_termino');
        const histInicio    = document.getElementById('hist_inicio');
        const histFin       = document.getElementById('hist_fin');

        if (historialBody) {
            historialBody.innerHTML = '<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando asistencias...</td></tr>';
        }

        const termino     = histTermino ? histTermino.value.trim() : '';
        const fechaInicio = histInicio  ? histInicio.value : '';
        const fechaFin    = histFin     ? histFin.value    : '';

        let url = `page=asistencia&action=historial_asistencias`;
        const qs = [];
        if (termino)     qs.push(`termino=${encodeURIComponent(termino)}`);
        if (fechaInicio) qs.push(`inicio=${encodeURIComponent(fechaInicio)}`);
        if (fechaFin)    qs.push(`fin=${encodeURIComponent(fechaFin)}`);
        if (qs.length)   url += '&' + qs.join('&');

        try {
            const res = await fetch(getEndpoint(url));
            if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
            const data = await parseJsonSafe(res);

            if (!data.success) {
                if (historialBody) {
                    historialBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${escapeHtml(data.message || 'Error')}</td></tr>`;
                }
                return;
            }

            // Resumen
            const r = data.resumen || {};
            const histHoy    = document.getElementById('hist_hoy');
            const histSemana = document.getElementById('hist_semana');
            const histMes    = document.getElementById('hist_mes');
            const histTotal  = document.getElementById('hist_total');
            const histUltima = document.getElementById('hist_ultima');

            if (histHoy)    histHoy.textContent    = r.hoy ?? 0;
            if (histSemana) histSemana.textContent = r.semana ?? 0;
            if (histMes)    histMes.textContent    = r.mes ?? 0;
            if (histTotal)  histTotal.textContent  = r.total ?? 0;
            if (histUltima) histUltima.textContent = r.ultima_visita || '—';

            // Tabla
            const lista = data.asistencias || [];
            if (!historialBody) return;

            if (lista.length === 0) {
                historialBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay asistencias con esos criterios.</td></tr>';
                return;
            }

            let html = '';
            lista.forEach((a, i) => {
                const partes = (a.fecha || '').split(' ');
                html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${escapeHtml(a.cedula_cliente || '')}</td>
                        <td>${escapeHtml(a.nombre_cliente || '')}</td>
                        <td>${escapeHtml(partes[0] || '')}</td>
                        <td>${escapeHtml(partes[1] || '')}</td>
                    </tr>
                `;
            });
            historialBody.innerHTML = html;
        } catch (err) {
            console.error('🔴 [Historial] Error:', err);
            if (historialBody) {
                historialBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">⚠️ ${escapeHtml(err.message)}</td></tr>`;
            }
        }
    }

    // Botón filtrar
    document.addEventListener('click', async function (e) {
        if (!e.target.closest('#hist_btnFiltrar')) return;
        await cargarHistorialGlobal();
    });

    // Botón ver todo
    document.addEventListener('click', async function (e) {
        if (!e.target.closest('#hist_btnTodo')) return;

        const histTermino = document.getElementById('hist_termino');
        const histInicio  = document.getElementById('hist_inicio');
        const histFin     = document.getElementById('hist_fin');

        if (histTermino) histTermino.value = '';
        if (histInicio)  histInicio.value = '';
        if (histFin)     histFin.value = '';

        await cargarHistorialGlobal();
    });

    // Búsqueda en vivo por cédula/nombre (debounce)
    document.addEventListener('input', function (e) {
        if (!e.target.matches('#hist_termino')) return;
        clearTimeout(timeoutSearch);
        timeoutSearch = setTimeout(() => cargarHistorialGlobal(), 350);
    });
});