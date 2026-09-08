// asistencia.js
document.addEventListener('DOMContentLoaded', function() {
    // ==================== ELEMENTOS ====================
    const formRegistro = document.getElementById('formRegistroEntrada');
    const clienteSelectedText = document.getElementById('cliente_selected_text');
    const clienteCedula = document.getElementById('cliente_cedula');
    const clienteIdHidden = document.getElementById('selected_cliente_id');
    const horaInput = document.getElementById('hora');
    const tablaBody = document.getElementById('tablaEntradasBody');
    const searchInput = document.getElementById('searchInput');
    const btnBuscar = document.getElementById('btnBuscar');
    let currentSearchTerm = '';
    let timeoutId = null;

    // Modales
    const clienteModalEl = document.getElementById('clienteModal');
    const clienteModal = new bootstrap.Modal(clienteModalEl);
    const editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
    const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarModal'));

    // Toast
    function showMessage(message, type = 'success') {
        const toast = document.getElementById('toastMessage');
        toast.textContent = message;
        toast.className = type;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }

    // ==================== MODAL CLIENTES ====================
    const searchClient = document.getElementById('searchClient');
    const clientesTablaBody = document.querySelector('#clientesTabla tbody');

    function cargarClientes(termino) {
        fetch(`?page=asistencia&action=buscar_clientes_ajax&ajax=buscar_clientes&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                data.forEach(c => {
                    html += `
                        <tr data-id="${c.cedula_persona}" data-nombre="${escapeHtml(c.nombre)}" data-cedula="${c.cedula_persona}">
                            <td>${escapeHtml(c.cedula_persona)}</td>
                            <td>${escapeHtml(c.nombre)}</td>
                            <td>${escapeHtml(c.correo || '—')}</td>
                            <td>${escapeHtml(c.telefono || '—')}</td>
                            <td><button class="btn btn-select-client seleccionar-btn">Seleccionar</button></td>
                        </tr>
                    `;
                });
                clientesTablaBody.innerHTML = html;
            })
            .catch(console.error);
    }

    let searchTimeout;
    searchClient.addEventListener('keyup', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => cargarClientes(searchClient.value), 400);
    });
    clienteModalEl.addEventListener('show.bs.modal', () => cargarClientes(''));
    clientesTablaBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.seleccionar-btn');
        if (!btn) return;
        const row = btn.closest('tr');
        const id = row.getAttribute('data-id');
        const nombre = row.getAttribute('data-nombre');
        const cedula = row.getAttribute('data-cedula');
        clienteIdHidden.value = id;
        clienteSelectedText.innerText = nombre;
        clienteCedula.value = cedula;
        clienteModal.hide();
    });

    // ==================== REGISTRAR ENTRADA ====================
    formRegistro.addEventListener('submit', function(e) {
        e.preventDefault();
        const cedula = clienteIdHidden.value;
        if (!cedula) {
            showMessage('⚠️ Debe seleccionar un cliente.', 'error');
            return;
        }
        const hora = horaInput.value;
        const formData = new FormData();
        formData.append('cedula', cedula);
        if (hora) formData.append('hora', hora);

        fetch('?page=asistencia&action=registrar', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('✅ Entrada registrada correctamente', 'success');
                    clienteIdHidden.value = '';
                    clienteSelectedText.innerText = 'Seleccione cliente';
                    clienteCedula.value = '';
                    horaInput.value = '';
                    cargarEntradas(currentSearchTerm);
                } else {
                    showMessage('❌ Error: ' + data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión', 'error'));
    });

    // ==================== CARGAR / BUSCAR ENTRADAS ====================
    function cargarEntradas(termino) {
        currentSearchTerm = termino;
        fetch(`?page=asistencia&action=buscar_entradas_ajax&ajax=buscar_entradas&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => actualizarTabla(data))
            .catch(console.error);
    }

    function actualizarTabla(entradas) {
        if (!tablaBody) return;
        if (entradas.length === 0) {
            tablaBody.innerHTML = '<tr><td colspan="4" class="text-center">No hay entradas registradas hoy.</td></tr>';
            return;
        }
        let html = '';
        entradas.forEach(e => {
            const hora = new Date(e.fecha).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            html += `
                <tr data-id="${e.id_asistencia}">
                    <td>${hora}</td>
                    <td>${escapeHtml(e.cedula_cliente)}</td>
                    <td>${escapeHtml(e.nombre_cliente)}</td>
                    <td>
                        <div class="acciones-botones">
                            <button class="btn btn-sm btn-warning editar-btn" data-id="${e.id_asistencia}" data-hora="${hora}" data-cedula="${escapeHtml(e.cedula_cliente)}" data-nombre="${escapeHtml(e.nombre_cliente)}"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn btn-sm btn-danger eliminar-btn" data-id="${e.id_asistencia}" data-hora="${hora}" data-cedula="${escapeHtml(e.cedula_cliente)}" data-nombre="${escapeHtml(e.nombre_cliente)}"><i class="fas fa-trash-alt"></i> Eliminar</button>
                        </div>
                    </td>
                </tr>
            `;
        });
        tablaBody.innerHTML = html;
        document.querySelectorAll('.editar-btn').forEach(btn => btn.addEventListener('click', abrirEditarModal));
        document.querySelectorAll('.eliminar-btn').forEach(btn => btn.addEventListener('click', abrirEliminarModal));
    }

    // ===== BÚSQUEDA EN TIEMPO REAL + BOTÓN (IGUAL QUE FACTURACIÓN) =====
    function buscarEntradas() {
        const termino = searchInput.value.trim();
        cargarEntradas(termino);
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            if (timeoutId) clearTimeout(timeoutId);
            timeoutId = setTimeout(buscarEntradas, 400);
        });
    }

    if (btnBuscar) {
        btnBuscar.addEventListener('click', buscarEntradas);
    }

    // ==================== MODAL EDITAR ====================
    function abrirEditarModal(e) {
        const btn = e.currentTarget;
        const id = btn.getAttribute('data-id');
        const hora = btn.getAttribute('data-hora');
        const cedula = btn.getAttribute('data-cedula');
        const nombre = btn.getAttribute('data-nombre');
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_cedula').value = cedula;
        document.getElementById('edit_nombre').value = nombre;
        if (hora) {
            const parts = hora.split(':');
            document.getElementById('edit_hora').value = parts[0] + ':' + parts[1];
        } else {
            document.getElementById('edit_hora').value = '';
        }
        editarModal.show();
    }

    document.getElementById('guardarEdicion').addEventListener('click', () => {
        const id = document.getElementById('edit_id').value;
        const nuevaHora = document.getElementById('edit_hora').value;
        if (!nuevaHora) {
            showMessage('⚠️ La hora es obligatoria', 'error');
            return;
        }
        const formData = new FormData();
        formData.append('id', id);
        formData.append('hora', nuevaHora);
        fetch('?page=asistencia&action=editar', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('✅ Hora actualizada', 'success');
                    editarModal.hide();
                    cargarEntradas(currentSearchTerm);
                } else {
                    showMessage('❌ Error al actualizar', 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión', 'error'));
    });

    // ==================== MODAL ELIMINAR ====================
    function abrirEliminarModal(e) {
        const btn = e.currentTarget;
        const id = btn.getAttribute('data-id');
        const hora = btn.getAttribute('data-hora');
        const cedula = btn.getAttribute('data-cedula');
        const nombre = btn.getAttribute('data-nombre');
        document.getElementById('delete_id').value = id;
        document.getElementById('delete_hora').innerText = hora;
        document.getElementById('delete_cedula').innerText = cedula;
        document.getElementById('delete_nombre').innerText = nombre;
        eliminarModal.show();
    }

    document.getElementById('confirmarEliminar').addEventListener('click', () => {
        const id = document.getElementById('delete_id').value;
        const formData = new FormData();
        formData.append('id', id);
        fetch('?page=asistencia&action=eliminar', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('🗑️ Entrada eliminada', 'success');
                    eliminarModal.hide();
                    cargarEntradas(currentSearchTerm);
                } else {
                    showMessage('❌ Error al eliminar', 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión', 'error'));
    });

    // ==================== GRÁFICO ====================
    let chart = null;
    function loadChart() {
        if (typeof window.ocupacionData === 'undefined') return;
        const ctx = document.getElementById('ocupacionChart').getContext('2d');
        if (!ctx) return;
        if (chart) chart.destroy();
        const labels = window.ocupacionData.map(f => f.franja);
        const valores = window.ocupacionData.map(f => f.total);
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Número de entradas',
                    data: valores,
                    backgroundColor: '#C62828',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Cantidad' } }
                }
            }
        });
    }

    // ==================== PESTAÑAS ====================
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');
    function activateTab(tabId) {
        tabs.forEach(btn => btn.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));
        document.getElementById(tabId)?.classList.add('active');
        document.querySelector(`.tab-btn[data-tab="${tabId}"]`)?.classList.add('active');
        if (tabId === 'tab-metricas') {
            setTimeout(loadChart, 100);
        }
    }

    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-tab');
            activateTab(target);
        });
    });

    const activeTab = document.querySelector('.tab-content.active');
    if (!activeTab) {
        activateTab('tab-registrar');
    } else {
        if (activeTab.id === 'tab-metricas') {
            setTimeout(loadChart, 100);
        }
    }

    // ==================== AUXILIARES ====================
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m] || m));
    }

    // Carga inicial
    cargarEntradas('');
    document.querySelectorAll('.editar-btn').forEach(btn => btn.addEventListener('click', abrirEditarModal));
    document.querySelectorAll('.eliminar-btn').forEach(btn => btn.addEventListener('click', abrirEliminarModal));
});