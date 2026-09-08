// facturacion.js

document.addEventListener('DOMContentLoaded', function() {
    // ===== Auto-cerrar alertas existentes (de sesión) =====
    function autoCloseAlerts() {
        document.querySelectorAll('.alert:not(.alert-dynamic)').forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade-out');
                setTimeout(() => alert.remove(), 500);
            }, 4000);
        });
    }
    autoCloseAlerts();

    // ===== Mostrar alerta dinámica dentro del contenedor =====
    function showAlert(message, type = 'danger') {
        const container = document.getElementById('alertContainer');
        if (!container) return;

        container.querySelectorAll('.alert-dynamic').forEach(el => el.remove());

        const iconMap = {
            danger: 'exclamation-triangle',
            success: 'check-circle',
            warning: 'info-circle'
        };

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-dynamic`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            <i class="fas fa-${iconMap[type] || 'info-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        container.prepend(alertDiv);

        setTimeout(() => {
            alertDiv.classList.add('fade-out');
            setTimeout(() => alertDiv.remove(), 500);
        }, 4000);
    }

    // ===== Buscador AJAX =====
    const searchInput = document.getElementById('searchPagos');
    const tablaBody = document.getElementById('tablaPagosBody');
    let timeoutId = null;

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function primerNombreJS(nombreCompleto) {
        if (!nombreCompleto) return '';
        return nombreCompleto.split(' ')[0];
    }

    function buscarPagos() {
        const termino = searchInput.value.trim();
        fetch(`?page=facturacion&action=buscar_ajax&ajax=buscar_pagos&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    tablaBody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron pagos.</td></tr>';
                    return;
                }
                let html = '';
                data.forEach(p => {
                    let estadoPagoBadge = '';
                    if (p.estado_pago === 'Pagado') estadoPagoBadge = '<span class="badge-pagado">Pagado</span>';
                    else if (p.estado_pago === 'Atrasado') estadoPagoBadge = '<span class="badge-atrasado">Atrasado</span>';
                    else estadoPagoBadge = '<span class="badge-pendiente">Pendiente</span>';

                    let estadoClienteClass = '';
                    if (p.estado_cliente === 'Activo') estadoClienteClass = 'estado-activo';
                    else if (p.estado_cliente === 'Próximo a vencer') estadoClienteClass = 'estado-proximo';
                    else estadoClienteClass = 'estado-vencido';

                    let diasRestantesHtml = '';
                    const dias = parseInt(p.dias_restantes);
                    if (dias < 0) diasRestantesHtml = `Vencido hace ${Math.abs(dias)} días`;
                    else if (dias === 0) diasRestantesHtml = '<span class="aviso-vencimiento">⚠️ ¡Vence hoy!</span>';
                    else if (dias <= 5) diasRestantesHtml = `<span class="aviso-vencimiento">⚠️ ¡Te quedan ${dias} días!</span>`;
                    else diasRestantesHtml = `Faltan ${dias} días`;

                    const nombreCorto = primerNombreJS(p.nombre_cliente);

                    html += `
                        <tr data-id="${p.id_pago}">
                            <td>${p.id_pago}</td>
                            <td>${escapeHtml(p.cedula_cliente)}</td>
                            <td>${escapeHtml(nombreCorto)}</td>
                            <td>$${parseFloat(p.monto).toFixed(2)}</td>
                            <td>${escapeHtml(p.metodo_pago)}</td>
                            <td>${estadoPagoBadge}</td>
                            <td><span class="${estadoClienteClass}">${escapeHtml(p.estado_cliente)}</span></td>
                            <td>${diasRestantesHtml}</td>
                            <td>
                                <div class="acciones-botones">
                                    <button class="btn btn-sm btn-warning editar-btn" data-bs-toggle="modal" data-bs-target="#editarModal" 
                                        data-id="${p.id_pago}"
                                        data-cliente="${escapeHtml(p.cedula_cliente)}"
                                        data-nombre="${escapeHtml(p.nombre_cliente)}"
                                        data-monto="${p.monto}"
                                        data-metodo="${escapeHtml(p.metodo_pago)}"
                                        data-estado="${escapeHtml(p.estado_pago)}"
                                        data-fecha_pago="${p.fecha_pago}"
                                        data-fecha_vencimiento="${p.fecha_vencimiento}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-sm btn-danger eliminar-btn" data-bs-toggle="modal" data-bs-target="#eliminarModal" 
                                        data-id="${p.id_pago}">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                    <button class="btn btn-sm btn-info ver-btn" data-bs-toggle="modal" data-bs-target="#verModal" 
                                        data-id="${p.id_pago}"
                                        data-cliente="${escapeHtml(p.cedula_cliente)}"
                                        data-nombre="${escapeHtml(primerNombreJS(p.nombre_cliente))}"
                                        data-monto="${p.monto}"
                                        data-metodo="${escapeHtml(p.metodo_pago)}"
                                        data-estado="${escapeHtml(p.estado_pago)}"
                                        data-fecha_pago="${p.fecha_pago}"
                                        data-fecha_vencimiento="${p.fecha_vencimiento}">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tablaBody.innerHTML = html;
                asignarEventosBotones();
            })
            .catch(error => console.error('Error en búsqueda:', error));
    }

    function asignarEventosBotones() {
        document.querySelectorAll('.editar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('edit_cliente').value = this.dataset.cliente;
                document.getElementById('edit_nombre').value = this.dataset.nombre;
                document.getElementById('edit_monto').value = this.dataset.monto;
                document.getElementById('edit_metodo').value = this.dataset.metodo;
                document.getElementById('edit_estado').value = this.dataset.estado;
                document.getElementById('edit_fecha_pago').value = this.dataset.fecha_pago;
                document.getElementById('edit_fecha_vencimiento').value = this.dataset.fecha_vencimiento;
            });
        });
        document.querySelectorAll('.eliminar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('delete_id').value = this.dataset.id;
                document.getElementById('confirmDeleteBtn').href = `?page=facturacion&action=eliminar&eliminar_pago=${this.dataset.id}`;
            });
        });
        document.querySelectorAll('.ver-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('ver_id').innerText = this.dataset.id;
                document.getElementById('ver_cedula').innerText = this.dataset.cliente;
                document.getElementById('ver_nombre').innerText = this.dataset.nombre;
                document.getElementById('ver_monto').innerText = `$ ${parseFloat(this.dataset.monto).toFixed(2)}`;
                document.getElementById('ver_metodo').innerText = this.dataset.metodo;
                document.getElementById('ver_estado').innerText = this.dataset.estado;
                document.getElementById('ver_fecha_pago').innerText = this.dataset.fecha_pago;
                document.getElementById('ver_fecha_vencimiento').innerText = this.dataset.fecha_vencimiento;
            });
        });
    }

    asignarEventosBotones();

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            if (timeoutId) clearTimeout(timeoutId);
            timeoutId = setTimeout(buscarPagos, 400);
        });
    }
    document.getElementById('btnBuscar')?.addEventListener('click', buscarPagos);

    // ===== Modal Cliente =====
    const searchClient = document.getElementById('searchClient');
    const clienteModal = document.getElementById('clienteModal');
    function filterClientTable() {
        const filter = searchClient.value.toLowerCase();
        document.querySelectorAll('#clientesTabla tbody tr').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }
    if (searchClient) searchClient.addEventListener('keyup', filterClientTable);
    document.getElementById('clientesTabla')?.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-select-client');
        if (!btn) return;
        const row = btn.closest('tr');
        const id = row.getAttribute('data-id');
        const nombre = row.getAttribute('data-nombre');
        if (id && nombre) {
            document.getElementById('selected_cliente_id').value = id;
            document.getElementById('cliente_selected_text').innerText = nombre;
            document.getElementById('cliente_cedula').value = id;
            bootstrap.Modal.getInstance(clienteModal).hide();
        }
    });

    // ===== Método de pago =====
    document.querySelectorAll('.method-item').forEach(item => {
        item.addEventListener('click', () => {
            const metodo = item.getAttribute('data-metodo');
            document.getElementById('selected_metodo').value = metodo;
            document.getElementById('metodo_selected_text').innerText = metodo;
            bootstrap.Modal.getInstance(document.getElementById('metodoModal')).hide();
        });
    });

    // ===== Plan (SIN autocompletar monto) =====
    document.querySelectorAll('.plan-item').forEach(item => {
        item.addEventListener('click', () => {
            const plan = item.getAttribute('data-plan');
            const text = item.getAttribute('data-text');

            document.getElementById('selected_plan').value = plan;
            document.getElementById('plan_selected_text').innerText = text;

            bootstrap.Modal.getInstance(document.getElementById('planModal')).hide();
        });
    });

    // ===== VALIDACIÓN DEL MONTO EN TIEMPO REAL (CORREGIDA) =====
    function validarMonto(input) {
        const valor = input.value.trim();
        let esValido = false;

        // Si está vacío, eliminar ambas clases (borde normal)
        if (valor === '') {
            input.classList.remove('monto-valid', 'monto-invalid');
            esValido = false;
        } else {
            const numero = parseFloat(valor);
            // Validar: número > 0 y con formato de máximo 2 decimales
            if (!isNaN(numero) && numero > 0 && /^\d+(\.\d{1,2})?$/.test(valor)) {
                esValido = true;
                input.classList.remove('monto-invalid');
                input.classList.add('monto-valid');   // Azul
            } else {
                esValido = false;
                input.classList.remove('monto-valid');
                input.classList.add('monto-invalid'); // Rojo
            }
        }
        return esValido;
    }

    const montoInput = document.getElementById('monto_input');
    if (montoInput) {
        montoInput.addEventListener('input', function() {
            validarMonto(this);
        });
        montoInput.addEventListener('blur', function() {
            validarMonto(this);
        });
        // Si el campo tiene un valor al cargar, validarlo
        if (montoInput.value.trim() !== '') {
            validarMonto(montoInput);
        }
    }

    // ===== Formulario Registro (con alerta estilizada) =====
    const formRegistro = document.getElementById('formRegistroPago');
    if (formRegistro) {
        formRegistro.addEventListener('submit', (e) => {
            if (!document.getElementById('selected_cliente_id').value) {
                showAlert('⚠️ Debe seleccionar un cliente.', 'warning');
                e.preventDefault();
                return;
            }

            if (!validarMonto(montoInput)) {
                showAlert('⚠️ El monto ingresado no es válido. Debe ser un número positivo con máximo dos decimales (ej. 25.50).', 'danger');
                e.preventDefault();
                return;
            }

            const hiddenMonto = document.createElement('input');
            hiddenMonto.type = 'hidden';
            hiddenMonto.name = 'monto';
            hiddenMonto.value = montoInput.value;
            formRegistro.appendChild(hiddenMonto);
            montoInput.disabled = true;
        });
    }
});