console.log('✅ facturacion.js cargado');

window.escapeHtmlG = function (str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function (m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m] || m;
    });
};

window.colorEstadoG = function (estado) {
    return ({
        'Activo':           'estado-activo',
        'Próximo a vencer': 'estado-proximo',
        'Vencido':          'estado-vencido',
        'Moroso':           'badge-atrasado',
        'Sin membresía':    'badge-pendiente'
    })[estado] || 'estado-vencido';
};

// ============================================================
// HELPER: Abrir modal anidado (por encima de otro modal)
// ============================================================
window._abrirModalAnidado = function (idModal) {
    var el = document.getElementById(idModal);
    if (!el) return null;

    document.body.classList.add('modal-anidado-abierto');

    var instance = bootstrap.Modal.getInstance(el);
    if (!instance) {
        instance = new bootstrap.Modal(el, {
            backdrop: 'static',
            keyboard: false,
            focus: false
        });
    }

    el.addEventListener('shown.bs.modal', function () {
        var backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length > 0) {
            var ultimo = backdrops[backdrops.length - 1];
            ultimo.classList.add('modal-anidado-backdrop');
            ultimo.style.zIndex = '1085';
        }
        el.style.zIndex = '1090';
    }, { once: true });

    el.addEventListener('hidden.bs.modal', function () {
        document.body.classList.remove('modal-anidado-abierto');
        document.querySelectorAll('.modal-backdrop.modal-anidado-backdrop').forEach(function (bd) {
            bd.remove();
        });
    }, { once: true });

    instance.show();
    return instance;
};

// ============================================================
// ESTADO DE CLIENTES
// ============================================================
window._estadoClientesCache = [];
window._filtroEstadoActivo = 'todos';
window._estadoTimeout = null;

window.marcarFiltroActivo = function (estado) {
    document.querySelectorAll('.filtro-pill').forEach(function (b) {
        b.classList.remove('active');
        var f = b.getAttribute('data-filtro');
        if ((estado === 'todos' && f === 'todos') ||
            (estado === 'Activo' && f === 'Activo') ||
            (estado === 'Próximo a vencer' && f === 'Proximo') ||
            (estado === 'Vencido' && f === 'Vencido') ||
            (estado === 'Moroso' && f === 'Moroso') ||
            (estado === 'Sin membresía' && f === 'Sin')) {
            b.classList.add('active');
        }
    });
    document.querySelectorAll('.estado-card').forEach(function (card) {
        card.classList.remove('active');
        if (card.getAttribute('data-estado') === estado) card.classList.add('active');
    });
};

window.cargarEstadoClientes = function () {
    var body = document.getElementById('tablaEstadoClientesBody');
    if (!body) return;

    body.innerHTML = '<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando estado de clientes...</td></tr>';

    var search = document.getElementById('searchEstadoClientes');
    var desde  = document.getElementById('estadoDesde');
    var hasta  = document.getElementById('estadoHasta');

    var qs = [];
    if (search && search.value.trim()) qs.push('termino=' + encodeURIComponent(search.value.trim()));
    if (desde && desde.value)          qs.push('desde='   + encodeURIComponent(desde.value));
    if (hasta && hasta.value)          qs.push('hasta='   + encodeURIComponent(hasta.value));

    var url = '?page=facturacion&action=estado_clientes';
    if (qs.length) url += '&' + qs.join('&');

    fetch(url)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.success) {
                body.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error al cargar.</td></tr>';
                return;
            }
            window._estadoClientesCache = data.clientes || [];

            var c = data.conteos || {};
            var el;
            el = document.getElementById('cnt_activo');  if (el) el.textContent = c['Activo']           || 0;
            el = document.getElementById('cnt_proximo'); if (el) el.textContent = c['Próximo a vencer'] || 0;
            el = document.getElementById('cnt_vencido'); if (el) el.textContent = c['Vencido']          || 0;
            el = document.getElementById('cnt_moroso');  if (el) el.textContent = c['Moroso']           || 0;
            el = document.getElementById('cnt_sin');     if (el) el.textContent = c['Sin membresía']    || 0;

            window.pintarEstadoClientes();
        })
        .catch(function (err) {
            console.error('Error estado clientes:', err);
            body.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error: ' + err.message + '</td></tr>';
        });
};

window.pintarEstadoClientes = function () {
    var body = document.getElementById('tablaEstadoClientesBody');
    if (!body) return;

    var lista = window._estadoClientesCache;

    if (window._filtroEstadoActivo !== 'todos') {
        lista = lista.filter(function (c) { return c.estado_cliente === window._filtroEstadoActivo; });
    }

    if (!lista.length) {
        body.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Sin resultados.</td></tr>';
        return;
    }

    function fmtFecha(f) {
        if (!f || f === '—') return '<span style="color:#cbd5e1;">—</span>';
        var partes = String(f).split(' ')[0].split('-');
        if (partes.length === 3) return partes[2] + '/' + partes[1] + '/' + partes[0];
        return f;
    }

    var html = '';
    lista.forEach(function (c) {
        var d = parseInt(c.dias_restantes, 10);
        var dHtml = '—';
        if (c.estado_cliente === 'Sin membresía' || isNaN(d)) dHtml = '<span style="color:#cbd5e1;">—</span>';
        else if (d < 0) dHtml = '<span style="color:#c62828;font-weight:700;">Vencido hace ' + Math.abs(d) + 'd</span>';
        else if (d === 0) dHtml = '<span class="aviso-vencimiento">⚠️ Hoy</span>';
        else if (d <= 7) dHtml = '<span class="aviso-vencimiento">⚠️ ' + d + 'd</span>';
        else dHtml = '<span style="color:#2e7d32;font-weight:600;">' + d + ' días</span>';

        var fechaPago = fmtFecha(c.ultima_fecha_pago);
        var fechaVenc = fmtFecha(c.fecha_fin);

        var nombreSafe = (c.nombre || '').replace(/'/g, '');
        var cedulaSafe = (c.cedula || '').replace(/'/g, '');

        var botonAccion = '';
        if (c.estado_cliente === 'Moroso') {
            botonAccion = '<button type="button" class="btn-reactivar" onclick="reactivarCliente(\'' + cedulaSafe + '\', \'' + nombreSafe + '\'); return false;"><i class="fas fa-check"></i> Reactivar</button>';
        } else if (c.estado_cliente !== 'Sin membresía') {
            botonAccion = '<button type="button" class="btn-moroso" onclick="marcarClienteMoroso(\'' + cedulaSafe + '\', \'' + nombreSafe + '\'); return false;"><i class="fas fa-user-slash"></i> Marcar moroso</button>';
        } else {
            botonAccion = '<span style="color:#cbd5e1;font-size:0.7rem;">—</span>';
        }

        html += '<tr>'
            + '<td>' + window.escapeHtmlG(c.cedula) + '</td>'
            + '<td>' + window.escapeHtmlG(c.nombre) + '</td>'
            + '<td>' + window.escapeHtmlG(c.tipo_membresia || '—') + '</td>'
            + '<td><span class="' + window.colorEstadoG(c.estado_cliente) + '">' + window.escapeHtmlG(c.estado_cliente) + '</span></td>'
            + '<td>' + fechaPago + '</td>'
            + '<td>' + fechaVenc + '</td>'
            + '<td>' + dHtml + '</td>'
            + '<td>' + window.escapeHtmlG(c.telefono || '—') + '</td>'
            + '<td>' + botonAccion + '</td>'
            + '</tr>';
    });
    body.innerHTML = html;
};

window.abrirModalEstadoClientes = function () {
    var modalEl = document.getElementById('estadoClientesModal');
    if (!modalEl) {
        alert('No se encontró el modal. Revisa la vista.');
        return false;
    }

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    var s = document.getElementById('searchEstadoClientes'); if (s) s.value = '';
    var d = document.getElementById('estadoDesde');          if (d) d.value = '';
    var h = document.getElementById('estadoHasta');          if (h) h.value = '';
    window._filtroEstadoActivo = 'todos';
    window.marcarFiltroActivo('todos');
    window.cargarEstadoClientes();
    return false;
};

window.filtrarEstadoClientes = function (estado) {
    window._filtroEstadoActivo = estado;
    window.marcarFiltroActivo(estado);
    window.pintarEstadoClientes();
    return false;
};

window.aplicarFiltrosEstado = function () {
    window.cargarEstadoClientes();
    return false;
};

window.refrescarEstadoClientes = function () {
    var s = document.getElementById('searchEstadoClientes'); if (s) s.value = '';
    var d = document.getElementById('estadoDesde');          if (d) d.value = '';
    var h = document.getElementById('estadoHasta');          if (h) h.value = '';
    window._filtroEstadoActivo = 'todos';
    window.marcarFiltroActivo('todos');
    window.cargarEstadoClientes();
    return false;
};

// ============ MARCAR MOROSO ============
window._cedulaMorosoPendiente = '';

window.marcarClienteMoroso = function (cedula, nombre) {
    if (!cedula) return false;

    window._cedulaMorosoPendiente = cedula;
    document.getElementById('moroso_cedula').textContent = cedula;
    document.getElementById('moroso_nombre').textContent = nombre || '—';

    window._abrirModalAnidado('marcarMorosoModal');
    return false;
};

// ============ REACTIVAR ============
window._cedulaReactivarPendiente = '';

window.reactivarCliente = function (cedula, nombre) {
    if (!cedula) return false;

    window._cedulaReactivarPendiente = cedula;
    document.getElementById('reactivar_cedula').textContent = cedula;
    document.getElementById('reactivar_nombre').textContent = nombre || '—';

    window._abrirModalAnidado('reactivarModal');
    return false;
};

// Búsqueda en vivo dentro del modal
document.addEventListener('input', function (e) {
    if (e.target && e.target.id === 'searchEstadoClientes') {
        clearTimeout(window._estadoTimeout);
        window._estadoTimeout = setTimeout(window.pintarEstadoClientes, 250);
    }
});

// ============================================================
// PAGOS — dentro de DOMContentLoaded
// ============================================================
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.alert:not(.alert-dynamic)').forEach(function (alert) {
        setTimeout(function () {
            alert.classList.add('fade-out');
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });

    function showAlert(message, type) {
        type = type || 'danger';
        var container = document.getElementById('alertContainer');
        if (!container) { alert(message); return; }

        container.querySelectorAll('.alert-dynamic').forEach(function (el) { el.remove(); });

        var iconMap = { danger: 'exclamation-triangle', success: 'check-circle', warning: 'info-circle' };

        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show alert-dynamic';
        alertDiv.role = 'alert';
        alertDiv.innerHTML = '<i class="fas fa-' + (iconMap[type] || 'info-circle') + '"></i> ' +
            message +
            ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

        container.prepend(alertDiv);

        setTimeout(function () {
            alertDiv.classList.add('fade-out');
            setTimeout(function () { alertDiv.remove(); }, 500);
        }, 4000);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m] || m;
        });
    }

    function primerNombreJS(nombreCompleto) {
        if (!nombreCompleto) return '';
        return String(nombreCompleto).split(' ')[0];
    }

    function hideModal(el) {
        if (!el || typeof bootstrap === 'undefined') return;
        var modal = bootstrap.Modal.getInstance(el);
        if (modal) modal.hide();
    }

    // ===== Buscador de pagos =====
    var searchInput = document.getElementById('searchPagos');
    var tablaBody   = document.getElementById('tablaPagosBody');
    var timeoutId   = null;

    function buscarPagos() {
        var termino = searchInput ? searchInput.value.trim() : '';

        fetch('?page=facturacion&action=buscar_ajax&termino=' + encodeURIComponent(termino))
            .then(function (response) { return response.json(); })
            .then(function (data) {
                var lista = Array.isArray(data) ? data : (data.data || []);

                if (lista.length === 0) {
                    tablaBody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron pagos.</td></tr>';
                    return;
                }

                var html = '';
                lista.forEach(function (p) {
                    var estadoPagoBadge = '';
                    if (p.estado_pago === 'Pagado') estadoPagoBadge = '<span class="badge-pagado">Pagado</span>';
                    else if (p.estado_pago === 'Atrasado') estadoPagoBadge = '<span class="badge-atrasado">Atrasado</span>';
                    else estadoPagoBadge = '<span class="badge-pendiente">Pendiente</span>';

                    var estadoClienteClass = 'estado-vencido';
                    if (p.estado_cliente === 'Activo') estadoClienteClass = 'estado-activo';
                    else if (p.estado_cliente === 'Próximo a vencer') estadoClienteClass = 'estado-proximo';

                    var diasRestantesHtml = '';
                    var dias = parseInt(p.dias_restantes, 10) || 0;
                    if (dias < 0) diasRestantesHtml = 'Vencido hace ' + Math.abs(dias) + ' días';
                    else if (dias === 0) diasRestantesHtml = '<span class="aviso-vencimiento">⚠️ ¡Vence hoy!</span>';
                    else if (dias <= 5) diasRestantesHtml = '<span class="aviso-vencimiento">⚠️ ¡Te quedan ' + dias + ' días!</span>';
                    else diasRestantesHtml = 'Faltan ' + dias + ' días';

                    var nombreCorto = primerNombreJS(p.nombre_cliente);
                    var monto = parseFloat(p.monto).toFixed(2);

                    html += '<tr data-id="' + p.id_pago + '">'
                        + '<td>' + p.id_pago + '</td>'
                        + '<td>' + escapeHtml(p.cedula_cliente) + '</td>'
                        + '<td>' + escapeHtml(nombreCorto) + '</td>'
                        + '<td>$' + monto + '</td>'
                        + '<td>' + escapeHtml(p.metodo_pago || '') + '</td>'
                        + '<td>' + estadoPagoBadge + '</td>'
                        + '<td><span class="' + estadoClienteClass + '">' + escapeHtml(p.estado_cliente || '') + '</span></td>'
                        + '<td>' + diasRestantesHtml + '</td>'
                        + '<td>'
                        +   '<div class="acciones-botones">'
                        +     '<button class="btn btn-sm btn-warning editar-btn" data-bs-toggle="modal" data-bs-target="#editarModal" '
                        +       'data-id="' + p.id_pago + '" '
                        +       'data-cliente="' + escapeHtml(p.cedula_cliente) + '" '
                        +       'data-nombre="' + escapeHtml(p.nombre_cliente || '') + '" '
                        +       'data-monto="' + p.monto + '" '
                        +       'data-metodo="' + escapeHtml(p.metodo_pago || '') + '" '
                        +       'data-estado="' + escapeHtml(p.estado_pago || '') + '" '
                        +       'data-fecha_pago="' + p.fecha_pago + '" '
                        +       'data-fecha_vencimiento="' + (p.fecha_vencimiento || '') + '">'
                        +       '<i class="fas fa-edit"></i> Editar'
                        +     '</button>'
                        +     '<button class="btn btn-sm btn-danger eliminar-btn" data-bs-toggle="modal" data-bs-target="#eliminarModal" '
                        +       'data-id="' + p.id_pago + '">'
                        +       '<i class="fas fa-trash-alt"></i> Eliminar'
                        +     '</button>'
                        +     '<button class="btn btn-sm btn-info ver-btn" data-bs-toggle="modal" data-bs-target="#verModal" '
                        +       'data-id="' + p.id_pago + '" '
                        +       'data-cliente="' + escapeHtml(p.cedula_cliente) + '" '
                        +       'data-nombre="' + escapeHtml(primerNombreJS(p.nombre_cliente)) + '" '
                        +       'data-monto="' + p.monto + '" '
                        +       'data-metodo="' + escapeHtml(p.metodo_pago || '') + '" '
                        +       'data-estado="' + escapeHtml(p.estado_pago || '') + '" '
                        +       'data-fecha_pago="' + p.fecha_pago + '" '
                        +       'data-fecha_vencimiento="' + (p.fecha_vencimiento || '') + '">'
                        +       '<i class="fas fa-eye"></i> Ver'
                        +     '</button>'
                        +   '</div>'
                        + '</td>'
                        + '</tr>';
                });
                tablaBody.innerHTML = html;
            })
            .catch(function (error) {
                console.error('Error en búsqueda:', error);
                showAlert('❌ Error al buscar pagos. Revisa la consola.', 'danger');
            });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            if (timeoutId) clearTimeout(timeoutId);
            timeoutId = setTimeout(buscarPagos, 400);
        });
    }
    var btnBuscar = document.getElementById('btnBuscar');
    if (btnBuscar) btnBuscar.addEventListener('click', buscarPagos);

    // Delegación para Editar / Eliminar / Ver
    if (tablaBody) {
        tablaBody.addEventListener('click', function (e) {
            var btnEditar = e.target.closest('.editar-btn');
            if (btnEditar) {
                document.getElementById('edit_id').value = btnEditar.dataset.id;
                document.getElementById('edit_cliente').value = btnEditar.dataset.cliente;
                document.getElementById('edit_nombre').value = btnEditar.dataset.nombre;
                document.getElementById('edit_monto').value = btnEditar.dataset.monto;
                document.getElementById('edit_metodo').value = btnEditar.dataset.metodo;
                document.getElementById('edit_estado').value = btnEditar.dataset.estado;
                document.getElementById('edit_fecha_pago').value = btnEditar.dataset.fecha_pago;
                document.getElementById('edit_fecha_vencimiento').value = btnEditar.dataset.fecha_vencimiento;
                return;
            }

            var btnEliminar = e.target.closest('.eliminar-btn');
            if (btnEliminar) {
                document.getElementById('delete_id').value = btnEliminar.dataset.id;
                document.getElementById('confirmDeleteBtn').href =
                    '?page=facturacion&action=eliminar&eliminar_pago=' + btnEliminar.dataset.id;
                return;
            }

            var btnVer = e.target.closest('.ver-btn');
            if (btnVer) {
                document.getElementById('ver_id').innerText = btnVer.dataset.id;
                document.getElementById('ver_cedula').innerText = btnVer.dataset.cliente;
                document.getElementById('ver_nombre').innerText = btnVer.dataset.nombre;
                document.getElementById('ver_monto').innerText = '$ ' + parseFloat(btnVer.dataset.monto).toFixed(2);
                document.getElementById('ver_metodo').innerText = btnVer.dataset.metodo;
                document.getElementById('ver_estado').innerText = btnVer.dataset.estado;
                document.getElementById('ver_fecha_pago').innerText = btnVer.dataset.fecha_pago;
                document.getElementById('ver_fecha_vencimiento').innerText = btnVer.dataset.fecha_vencimiento;
            }
        });
    }

    // ===== Validación de monto =====
    function validarMonto(input) {
        var valor = input.value.trim();
        var esValido = false;

        if (valor === '') {
            input.classList.remove('monto-valid', 'monto-invalid');
            esValido = false;
        } else {
            var numero = parseFloat(valor);
            if (!isNaN(numero) && numero > 0 && /^\d+(\.\d{1,2})?$/.test(valor)) {
                esValido = true;
                input.classList.remove('monto-invalid');
                input.classList.add('monto-valid');
            } else {
                esValido = false;
                input.classList.remove('monto-valid');
                input.classList.add('monto-invalid');
            }
        }
        return esValido;
    }

    var montoInput = document.getElementById('monto_input');
    if (montoInput) {
        montoInput.addEventListener('input', function () { validarMonto(this); });
        montoInput.addEventListener('blur', function () { validarMonto(this); });
        if (montoInput.value.trim() !== '') validarMonto(montoInput);
    }

    // ===== Plan =====
    window._precioPlanSeleccionado = 0;
    window._planTextoSeleccionado  = '';

    document.querySelectorAll('.plan-item').forEach(function (item) {
        item.addEventListener('click', function () {
            var plan   = item.getAttribute('data-plan');
            var text   = item.getAttribute('data-text');
            var precio = parseFloat(item.getAttribute('data-precio')) || 0;

            document.getElementById('selected_plan').value = plan;

            if (plan === '') {
                document.getElementById('plan_selected_text').innerText = text;
                window._precioPlanSeleccionado = 0;
                window._planTextoSeleccionado  = text;
            } else {
                document.getElementById('plan_selected_text').innerText =
                    text + ' — $' + precio.toFixed(2);
                window._precioPlanSeleccionado = precio;
                window._planTextoSeleccionado  = text;
            }

            if (montoInput && window._precioPlanSeleccionado > 0) {
                var ingresado = parseFloat(montoInput.value) || 0;
                if (ingresado > 0 && ingresado < window._precioPlanSeleccionado) {
                    showAlert('⚠️ El monto ingresado ($' + ingresado.toFixed(2) +
                        ') es menor al precio del plan ($' + window._precioPlanSeleccionado.toFixed(2) +
                        '). El cliente quedará como MOROSO.', 'warning');
                }
            }

            hideModal(document.getElementById('planModal'));
        });
    });

    // ===== Formulario Registro =====
    var formRegistro = document.getElementById('formRegistroPago');
    if (formRegistro) {
        formRegistro.addEventListener('submit', function (e) {
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

            if (window._precioPlanSeleccionado > 0) {
                var ingresado = parseFloat(montoInput.value) || 0;
                if (ingresado < window._precioPlanSeleccionado) {
                    e.preventDefault();

                    document.getElementById('parcial_plan').textContent   = window._planTextoSeleccionado;
                    document.getElementById('parcial_precio').textContent = window._precioPlanSeleccionado.toFixed(2);
                    document.getElementById('parcial_monto').textContent  = ingresado.toFixed(2);
                    document.getElementById('parcial_deuda').textContent  = (window._precioPlanSeleccionado - ingresado).toFixed(2);

                    window._abrirModalAnidado('confirmarParcialModal');
                    return;
                }
            }

            var hiddenMonto = document.createElement('input');
            hiddenMonto.type = 'hidden';
            hiddenMonto.name = 'monto';
            hiddenMonto.value = montoInput.value;
            formRegistro.appendChild(hiddenMonto);
            montoInput.disabled = true;
        });
    }
});

// ============================================================
// HANDLERS DE MODALES ANIDADOS
// ============================================================

// Botón "Continuar" del modal Pago Parcial
document.addEventListener('click', function (e) {
    if (!e.target.closest('#confirmarPagoParcial')) return;

    var modalEl = document.getElementById('confirmarParcialModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    var form = document.getElementById('formRegistroPago');
    var montoInput = document.getElementById('monto_input');

    if (form && montoInput) {
        var hiddenMonto = document.createElement('input');
        hiddenMonto.type = 'hidden';
        hiddenMonto.name = 'monto';
        hiddenMonto.value = montoInput.value;
        form.appendChild(hiddenMonto);
        montoInput.disabled = true;
        form.submit();
    }
});

// Botón "Marcar moroso" del modal
document.addEventListener('click', function (e) {
    if (!e.target.closest('#confirmarMarcarMoroso')) return;

    var cedula = window._cedulaMorosoPendiente || '';
    if (!cedula) return;

    var modalEl = document.getElementById('marcarMorosoModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    var fd = new FormData();
    fd.append('cedula', cedula);

    fetch('?page=facturacion&action=marcar_moroso', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                window.cargarEstadoClientes();
                window._mostrarToast(data.message || '✅ Cliente marcado como moroso.');
            } else {
                window._mostrarToast('❌ ' + (data.message || 'No se pudo marcar.'), 'error');
            }
        })
        .catch(function (err) {
            console.error(err);
            window._mostrarToast('❌ Error de conexión.', 'error');
        });
});

// Botón "Reactivar" del modal
document.addEventListener('click', function (e) {
    if (!e.target.closest('#confirmarReactivar')) return;

    var cedula = window._cedulaReactivarPendiente || '';
    if (!cedula) return;

    var modalEl = document.getElementById('reactivarModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    var fd = new FormData();
    fd.append('cedula', cedula);

    fetch('?page=facturacion&action=reactivar_cliente', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                window.cargarEstadoClientes();
                window._mostrarToast(data.message || '✅ Cliente reactivado.');
            } else {
                window._mostrarToast('❌ ' + (data.message || 'No se pudo reactivar.'), 'error');
            }
        })
        .catch(function (err) {
            console.error(err);
            window._mostrarToast('❌ Error de conexión.', 'error');
        });
});

// ============================================================
// Toast FLOTANTE GLOBAL — se ve por encima de todo
// ============================================================
window._mostrarToast = function (msg, tipo) {
    var type = tipo === 'error' ? 'danger' : 'success';
    var icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';

    // Crear contenedor flotante si no existe
    var container = document.getElementById('toastContainerGlobal');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainerGlobal';
        document.body.appendChild(container);
    }

    var div = document.createElement('div');
    div.className = 'alert alert-' + type + ' alert-dismissible fade show';
    div.role = 'alert';
    div.innerHTML = '<i class="fas fa-' + icon + '"></i> ' + msg +
        ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

    container.appendChild(div);

    setTimeout(function () {
        div.classList.add('fade-out');
        setTimeout(function () { div.remove(); }, 500);
    }, 4000);
};