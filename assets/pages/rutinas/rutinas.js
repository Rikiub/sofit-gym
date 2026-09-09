// rutinas.js - Control del CRUD para Rutinas y Asignación de Rutinas de Sofit Gym
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // NOTIFICACIONES TOAST
    // ==========================================
    function showMessage(message, type = 'success') {
        const toast = document.getElementById('toastMessage');
        if (!toast) return;
        toast.textContent = message;
        toast.className = type;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m] || m));
    }

    // =========================================================================
    // VALIDACIONES EN TIEMPO REAL
    // =========================================================================
    const inputsAValidar = [
        { id: 'nombre_rutina', type: 'alfanumerico' },
        { id: 'duracion_semanas', type: 'numero_mayor_cero' },
        { id: 'objetivo', type: 'alfanumerico' },
        { id: 'descripcion', type: 'texto_descriptivo' },
        { id: 'edit_nombre_rutina', type: 'alfanumerico' },
        { id: 'edit_duracion_semanas', type: 'numero_mayor_cero' },
        { id: 'edit_objetivo', type: 'alfanumerico' },
        { id: 'edit_descripcion', type: 'texto_descriptivo' },
        // Validaciones añadidas para Asignaciones de Rutinas
        { id: 'fecha_asignacion', type: 'fecha_futura' },
        { id: 'fecha_inicio', type: 'fecha_futura' },
        { id: 'fecha_fin', type: 'fecha_futura' },
        { id: 'progreso_asignacion', type: 'porcentaje' },
        { id: 'edit_fecha_asignacion', type: 'fecha_futura' },
        { id: 'edit_fecha_inicio', type: 'fecha_futura' },
        { id: 'edit_fecha_fin', type: 'fecha_futura' },
        { id: 'edit_progreso', type: 'porcentaje' }
    ];

    function validarInput(inputElement, tipo) {
        if (!inputElement) return true;
        
        let isValid = true;
        let errorMsg = '';
        const val = inputElement.value;

        if (val !== '') {
            if (tipo === 'alfanumerico') {
                if (!/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/.test(val)) {
                    isValid = false;
                    errorMsg = 'Solo se aceptan letras, números y espacios.';
                }
            } else if (tipo === 'numero_mayor_cero') {
                if (!/^\d+$/.test(val) || parseInt(val) <= 0) {
                    isValid = false;
                    errorMsg = 'Solo se aceptan números mayores a 0.';
                }
            } else if (tipo === 'texto_descriptivo') {
                if (!/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,]+$/.test(val)) {
                    isValid = false;
                    errorMsg = 'Solo se aceptan letras, números, puntos y comas.';
                }
            } else if (tipo === 'fecha_futura') {
                // Validación para que no permita elegir fechas anteriores al día actual
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const inputParts = val.split('-');
                if (inputParts.length === 3) {
                    const inputDate = new Date(inputParts[0], inputParts[1] - 1, inputParts[2]);
                    if (inputDate < today) {
                        isValid = false;
                        errorMsg = 'La fecha debe ser igual o mayor al día actual.';
                    }
                }
            } else if (tipo === 'porcentaje') {
                // Validación para que el progreso solo sea de 0 a 100
                const num = parseInt(val, 10);
                if (isNaN(num) || num < 0 || num > 100) {
                    isValid = false;
                    errorMsg = 'El valor debe estar entre 0 y 100.';
                }
            }
        }

        const errorDiv = inputElement.nextElementSibling;
        
        if (!isValid) {
            inputElement.classList.add('is-invalid');
            if (errorDiv && errorDiv.classList.contains('error-msg')) {
                errorDiv.textContent = errorMsg;
                errorDiv.style.display = 'block';
            }
        } else {
            inputElement.classList.remove('is-invalid');
            if (errorDiv && errorDiv.classList.contains('error-msg')) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
        }
        
        return isValid;
    }

    inputsAValidar.forEach(config => {
        const input = document.getElementById(config.id);
        if (input) {
            input.addEventListener('input', () => validarInput(input, config.type));
        }
    });

    function validarFormularioCompleto(ids) {
        let formularioValido = true;
        ids.forEach(id => {
            const config = inputsAValidar.find(c => c.id === id);
            const input = document.getElementById(id);
            if (config && input && !validarInput(input, config.type)) {
                formularioValido = false;
            }
        });
        return formularioValido;
    }

    // =========================================================================
    // I. MÓDULO: GESTIÓN DE RUTINAS BASE (rutinas.php)
    // =========================================================================
    const formRegistroRutina = document.getElementById('formRegistroRutina');
    const tablaRutinasBody = document.getElementById('tablaRutinasBody');
    const searchInputRutinas = document.getElementById('searchInputRutinas');
    const btnBuscarRutina = document.getElementById('btnBuscarRutina');

    const editarRutinaModalEl = document.getElementById('editarRutinaModal');
    const eliminarRutinaModalEl = document.getElementById('eliminarRutinaModal');
    const editarRutinaModal = editarRutinaModalEl ? new bootstrap.Modal(editarRutinaModalEl) : null;
    const eliminarRutinaModal = eliminarRutinaModalEl ? new bootstrap.Modal(eliminarRutinaModalEl) : null;

    let currentRutinasSearch = '';

    if (formRegistroRutina) {
        formRegistroRutina.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const camposFormulario = ['nombre_rutina', 'duracion_semanas', 'objetivo', 'descripcion'];
            if (!validarFormularioCompleto(camposFormulario)) {
                showMessage('⚠️ Hay errores en el formulario. Por favor, revise los campos marcados en rojo.', 'error');
                return; 
            }

            const formData = new FormData(formRegistroRutina);

            fetch('?page=rutinas&action=registrar_rutina', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('✅ ' + data.message, 'success');
                    formRegistroRutina.reset();
                    camposFormulario.forEach(id => {
                        const input = document.getElementById(id);
                        if(input) {
                            input.classList.remove('is-invalid');
                            const div = input.nextElementSibling;
                            if(div && div.classList.contains('error-msg')) div.style.display = 'none';
                        }
                    });
                    cargarRutinas(currentRutinasSearch);
                } else {
                    showMessage('❌ Error: ' + data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión al servidor.', 'error'));
        });

        // =======================================================
        // NUEVA BÚSQUEDA LOCAL EN TIEMPO REAL PARA RUTINAS
        // =======================================================
        function filtrarRutinasLocales() {
            const termino = searchInputRutinas.value.trim().toLowerCase();
            if (!tablaRutinasBody) return;
            const filas = tablaRutinasBody.querySelectorAll('tr');
            
            filas.forEach(fila => {
                // Ignorar fila de información ("No se encontraron rutinas")
                if (fila.cells.length === 1) return;
                
                const textoFila = fila.innerText.toLowerCase();
                if (textoFila.includes(termino)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        if (btnBuscarRutina) {
            btnBuscarRutina.addEventListener('click', function(e) {
                e.preventDefault();
                filtrarRutinasLocales();
            });
        }
        
        if (searchInputRutinas) {
            // Activa el filtrado al escribir o borrar texto instantáneamente
            searchInputRutinas.addEventListener('input', filtrarRutinasLocales);
            searchInputRutinas.addEventListener('keyup', filtrarRutinasLocales);
        }

        function cargarRutinas(termino) {
            currentRutinasSearch = termino;
            fetch(`?page=rutinas&action=buscar_rutinas_ajax&ajax=buscar_rutinas&termino=${encodeURIComponent(termino)}`)
                .then(response => response.json())
                .then(data => actualizarTablaRutinas(data))
                .catch(console.error);
        }

        function actualizarTablaRutinas(rutinas) {
            if (!tablaRutinasBody) return;
            if (rutinas.length === 0) {
                tablaRutinasBody.innerHTML = '<tr><td colspan="5" class="text-center">No se encontraron rutinas en el catálogo.</td></tr>';
                return;
            }

            let html = '';
            rutinas.forEach(r => {
                const duracionStr = r.duracion_semanas ? `${r.duracion_semanas} semanas` : '—';
                const objetivoStr = r.objetivo ? escapeHtml(r.objetivo) : '—';
                const difClase = r.nombre_dificultad ? r.nombre_dificultad.toLowerCase().replace('ñ', 'n') : 'principiante';
                
                html += `
                    <tr data-id="${r.id_rutina}">
                        <td><strong>${escapeHtml(r.nombre)}</strong></td>
                        <td>
                            <span class="dificultad-badge dif-${difClase}">
                                ${escapeHtml(r.nombre_dificultad)}
                            </span>
                        </td>
                        <td>${duracionStr}</td>
                        <td>${objetivoStr}</td>
                        <td>
                            <div class="acciones-botones">
                                <button class="btn btn-sm btn-warning editar-rutina-btn" 
                                        data-id="${r.id_rutina}" 
                                        data-nombre="${escapeHtml(r.nombre)}"
                                        data-dificultad="${r.id_dificultad}"
                                        data-duracion="${r.duracion_semanas || ''}"
                                        data-objetivo="${escapeHtml(r.objetivo || '')}"
                                        data-descripcion="${escapeHtml(r.descripcion || '')}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-danger eliminar-rutina-btn" 
                                        data-id="${r.id_rutina}"
                                        data-nombre="${escapeHtml(r.nombre)}">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tablaRutinasBody.innerHTML = html;
            vincularEventosTablaRutinas();
        }

        function vincularEventosTablaRutinas() {
            document.querySelectorAll('.editar-rutina-btn').forEach(btn => {
                btn.addEventListener('click', abrirEditarRutinaModal);
            });
            document.querySelectorAll('.eliminar-rutina-btn').forEach(btn => {
                btn.addEventListener('click', abrirEliminarRutinaModal);
            });
        }

        function abrirEditarRutinaModal(e) {
            const btn = e.currentTarget;
            document.getElementById('edit_id_rutina').value = btn.getAttribute('data-id');
            document.getElementById('edit_nombre_rutina').value = btn.getAttribute('data-nombre');
            document.getElementById('edit_id_dificultad').value = btn.getAttribute('data-dificultad');
            document.getElementById('edit_duracion_semanas').value = btn.getAttribute('data-duracion');
            document.getElementById('edit_objetivo').value = btn.getAttribute('data-objetivo');
            document.getElementById('edit_descripcion').value = btn.getAttribute('data-descripcion');
            
            ['edit_nombre_rutina', 'edit_duracion_semanas', 'edit_objetivo', 'edit_descripcion'].forEach(id => {
                const input = document.getElementById(id);
                if(input) {
                    input.classList.remove('is-invalid');
                    const errorDiv = input.nextElementSibling;
                    if(errorDiv && errorDiv.classList.contains('error-msg')) errorDiv.style.display = 'none';
                }
            });

            editarRutinaModal.show();
        }

        const btnGuardarEdicionRutina = document.getElementById('guardarEdicionRutina');
        if (btnGuardarEdicionRutina) {
            btnGuardarEdicionRutina.addEventListener('click', function() {
                const camposEdicion = ['edit_nombre_rutina', 'edit_duracion_semanas', 'edit_objetivo', 'edit_descripcion'];
                if (!validarFormularioCompleto(camposEdicion)) {
                    showMessage('⚠️ Hay errores en el formulario. Por favor, revise los campos marcados en rojo.', 'error');
                    return;
                }

                const id = document.getElementById('edit_id_rutina').value;
                const nombre = document.getElementById('edit_nombre_rutina').value.trim();
                const dificultad = document.getElementById('edit_id_dificultad').value;
                const duracion = document.getElementById('edit_duracion_semanas').value;
                const objetivo = document.getElementById('edit_objetivo').value.trim();
                const descripcion = document.getElementById('edit_descripcion').value.trim();

                if (!nombre || !dificultad) {
                    showMessage('⚠️ El nombre y la dificultad son obligatorios.', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('id_rutina', id);
                formData.append('nombre', nombre);
                formData.append('id_dificultad', dificultad);
                formData.append('duracion_semanas', duracion);
                formData.append('objetivo', objetivo);
                formData.append('descripcion', descripcion);

                fetch('?page=rutinas&action=editar_rutina', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('✅ ' + data.message, 'success');
                        editarRutinaModal.hide();
                        cargarRutinas(currentRutinasSearch);
                    } else {
                        showMessage('❌ Error: ' + data.message, 'error');
                    }
                })
                .catch(() => showMessage('❌ Error de conexión', 'error'));
            });
        }

        function abrirEliminarRutinaModal(e) {
            const btn = e.currentTarget;
            document.getElementById('delete_id_rutina').value = btn.getAttribute('data-id');
            document.getElementById('delete_nombre_rutina_txt').innerText = btn.getAttribute('data-nombre');
            eliminarRutinaModal.show();
        }

        const btnConfirmarEliminarRutina = document.getElementById('confirmarEliminarRutina');
        if (btnConfirmarEliminarRutina) {
            btnConfirmarEliminarRutina.addEventListener('click', function() {
                const id = document.getElementById('delete_id_rutina').value;
                const formData = new FormData();
                formData.append('id_rutina', id);

                fetch('?page=rutinas&action=eliminar_rutina', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('🗑️ Rutina eliminada de catálogo.', 'success');
                        eliminarRutinaModal.hide();
                        cargarRutinas(currentRutinasSearch);
                    } else {
                        showMessage('❌ Error: ' + data.message, 'error');
                    }
                })
                .catch(() => showMessage('❌ Error de conexión.', 'error'));
            });
        }

        vincularEventosTablaRutinas();
    }

    // =========================================================================
    // II. MÓDULO: ASIGNACIÓN DE RUTINAS A CLIENTES (rutinasAsignadas.php)
    // =========================================================================
    const formAsignarRutina = document.getElementById('formAsignarRutina');
    const searchClientAsignar = document.getElementById('searchClientAsignar');
    const clientesTablaAsignacionBody = document.querySelector('#clientesTablaAsignacion tbody');
    const cedulaClienteAsignacion = document.getElementById('cedula_cliente_asignacion');
    const labelClienteAsignarText = document.getElementById('cliente_selected_text_asignar');

    const clienteModalAsignacionEl = document.getElementById('clienteModalAsignacion');
    const clienteModalAsignacion = clienteModalAsignacionEl ? new bootstrap.Modal(clienteModalAsignacionEl) : null;
    const editarAsignacionModalEl = document.getElementById('editarAsignacionModal');
    const editarAsignacionModal = editarAsignacionModalEl ? new bootstrap.Modal(editarAsignacionModalEl) : null;
    const eliminarAsignacionModalEl = document.getElementById('eliminarAsignacionModal');
    const eliminarAsignacionModal = eliminarAsignacionModalEl ? new bootstrap.Modal(eliminarAsignacionModalEl) : null;

    if (formAsignarRutina) {
        function cargarClientesParaAsignar(termino) {
            fetch(`?page=asistencia&action=buscar_clientes_ajax&ajax=buscar_clientes&termino=${encodeURIComponent(termino)}`)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if (data.length === 0) {
                        html = '<tr><td colspan="5" class="text-center text-muted">Ningún cliente coincide con la búsqueda.</td></tr>';
                    } else {
                        data.forEach(c => {
                            html += `
                                <tr data-cedula="${c.cedula_persona}" data-nombre="${escapeHtml(c.nombre)}">
                                    <td>${escapeHtml(c.cedula_persona)}</td>
                                    <td>${escapeHtml(c.nombre)}</td>
                                    <td>${escapeHtml(c.correo || '—')}</td>
                                    <td>${escapeHtml(c.telefono || '—')}</td>
                                    <td><button type="button" class="btn btn-select-client seleccionar-cliente-asig-btn">Seleccionar</button></td>
                                </tr>
                            `;
                        });
                    }
                    clientesTablaAsignacionBody.innerHTML = html;
                })
                .catch(console.error);
        }

        let searchTimeout;
        if (searchClientAsignar) {
            searchClientAsignar.addEventListener('keyup', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => cargarClientesParaAsignar(searchClientAsignar.value), 400);
            });
        }

        if (clienteModalAsignacionEl) {
            clienteModalAsignacionEl.addEventListener('show.bs.modal', () => cargarClientesParaAsignar(''));
        }

        if (clientesTablaAsignacionBody) {
            clientesTablaAsignacionBody.addEventListener('click', function(e) {
                const btn = e.target.closest('.seleccionar-cliente-asig-btn');
                if (!btn) return;
                const row = btn.closest('tr');
                const cedula = row.getAttribute('data-cedula');
                const nombre = row.getAttribute('data-nombre');

                cedulaClienteAsignacion.value = cedula;
                labelClienteAsignarText.innerText = nombre;
                clienteModalAsignacion.hide();
            });
        }

        formAsignarRutina.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Evaluamos las nuevas validaciones integradas en las asignaciones
            const camposFormularioAsignacion = ['fecha_inicio', 'fecha_fin', 'progreso_asignacion'];
            if (!validarFormularioCompleto(camposFormularioAsignacion)) {
                showMessage('⚠️ Hay errores en el formulario. Por favor, revise los campos marcados en rojo.', 'error');
                return;
            }

            const cedula = cedulaClienteAsignacion.value;
            if (!cedula) {
                showMessage('⚠️ Debe seleccionar un cliente antes de guardar.', 'error');
                return;
            }

            const formData = new FormData(formAsignarRutina);
            fetch('?page=rutinas&action=asignar_rutina', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('✅ ' + data.message, 'success');
                    
                    formAsignarRutina.reset();
                    labelClienteAsignarText.innerText = 'Seleccione cliente';
                    cedulaClienteAsignacion.value = '';
                    
                    // Limpiar clases de las validaciones en tiempo real
                    camposFormularioAsignacion.forEach(id => {
                        const input = document.getElementById(id);
                        if(input) {
                            input.classList.remove('is-invalid');
                            const div = input.nextElementSibling;
                            if(div && div.classList.contains('error-msg')) div.style.display = 'none';
                        }
                    });

                    setTimeout(() => { location.reload(); }, 1000);
                } else {
                    showMessage('❌ Error: ' + data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión al servidor.', 'error'));
        });

        const searchInputAsignaciones = document.getElementById('searchInputAsignaciones');
        const btnBuscarAsignacion = document.getElementById('btnBuscarAsignacion');
        const tablaAsignacionesBody = document.getElementById('tablaAsignacionesBody');

        function filtrarAsignaciones() {
            const termino = searchInputAsignaciones.value.trim().toLowerCase();
            const filas = tablaAsignacionesBody.querySelectorAll('tr');
            
            filas.forEach(fila => {
                const textoFila = fila.innerText.toLowerCase();
                if (textoFila.includes(termino)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        if (btnBuscarAsignacion) {
            btnBuscarAsignacion.addEventListener('click', filtrarAsignaciones);
        }
        if (searchInputAsignaciones) {
            searchInputAsignaciones.addEventListener('keyup', filtrarAsignaciones);
        }

        function abrirEditarAsignacionModal(e) {
            const btn = e.currentTarget;
            document.getElementById('edit_id_asignacion').value = btn.getAttribute('data-id');
            document.getElementById('edit_cedula_cliente_asignado').value = btn.getAttribute('data-cedula');
            document.getElementById('edit_nombre_cliente_asignado').value = btn.getAttribute('data-nombre-cliente');
            document.getElementById('edit_id_rutina_asignacion').value = btn.getAttribute('data-rutina');
            document.getElementById('edit_fecha_asignacion').value = btn.getAttribute('data-fecha-asig');
            document.getElementById('edit_fecha_inicio').value = btn.getAttribute('data-fecha-ini') || '';
            document.getElementById('edit_fecha_fin').value = btn.getAttribute('data-fecha-fin') || '';
            document.getElementById('edit_estado_asignacion').value = btn.getAttribute('data-estado');
            document.getElementById('edit_progreso').value = Math.round(parseFloat(btn.getAttribute('data-progreso')) || 0);

            // Resetea las clases de error al abrir para no arrastrar mensajes previos
            ['edit_fecha_inicio', 'edit_fecha_fin', 'edit_progreso'].forEach(id => {
                const input = document.getElementById(id);
                if(input) {
                    input.classList.remove('is-invalid');
                    const errorDiv = input.nextElementSibling;
                    if(errorDiv && errorDiv.classList.contains('error-msg')) errorDiv.style.display = 'none';
                }
            });

            editarAsignacionModal.show();
        }

        const btnGuardarEdicionAsignacion = document.getElementById('guardarEdicionAsignacion');
        if (btnGuardarEdicionAsignacion) {
            btnGuardarEdicionAsignacion.addEventListener('click', function() {
                
                // Evaluamos las nuevas validaciones integradas en las asignaciones
                const camposEdicionAsignacion = ['edit_fecha_inicio', 'edit_fecha_fin', 'edit_progreso'];
                if (!validarFormularioCompleto(camposEdicionAsignacion)) {
                    showMessage('⚠️ Hay errores en el formulario. Por favor, revise los campos marcados en rojo.', 'error');
                    return;
                }

                const idAsignacion = document.getElementById('edit_id_asignacion').value;
                const cedula = document.getElementById('edit_cedula_cliente_asignado').value;
                const rutina = document.getElementById('edit_id_rutina_asignacion').value;
                const fAsig = document.getElementById('edit_fecha_asignacion').value;
                const fIni = document.getElementById('edit_fecha_inicio').value;
                const fFin = document.getElementById('edit_fecha_fin').value;
                const estado = document.getElementById('edit_estado_asignacion').value;
                const progreso = document.getElementById('edit_progreso').value;

                if (!rutina || !fAsig) {
                    showMessage('⚠️ La rutina y la fecha de asignación son obligatorias.', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('id_asignacion', idAsignacion);
                formData.append('cedula_cliente', cedula);
                formData.append('id_rutina', rutina);
                formData.append('fecha_asignacion', fAsig);
                formData.append('fecha_inicio', fIni);
                formData.append('fecha_fin', fFin);
                formData.append('estado', estado);
                formData.append('progreso', progreso);

                fetch('?page=rutinas&action=editar_asignacion', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('✅ ' + data.message, 'success');
                        editarAsignacionModal.hide();
                        setTimeout(() => { location.reload(); }, 1000);
                    } else {
                        showMessage('❌ Error: ' + data.message, 'error');
                    }
                })
                .catch(() => showMessage('❌ Error de conexión al servidor.', 'error'));
            });
        }

        function abrirEliminarAsignacionModal(e) {
            const btn = e.currentTarget;
            document.getElementById('delete_id_asignacion').value = btn.getAttribute('data-id');
            document.getElementById('delete_cliente_asignacion_txt').innerText = btn.getAttribute('data-cliente');
            document.getElementById('delete_rutina_asignacion_txt').innerText = btn.getAttribute('data-rutina');
            eliminarAsignacionModal.show();
        }

        const btnConfirmarEliminarAsignacion = document.getElementById('confirmarEliminarAsignacion');
        if (btnConfirmarEliminarAsignacion) {
            btnConfirmarEliminarAsignacion.addEventListener('click', function() {
                const idAsignacion = document.getElementById('delete_id_asignacion').value;
                const formData = new FormData();
                formData.append('id_asignacion', idAsignacion);

                fetch('?page=rutinas&action=eliminar_asignacion', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('🗑️ Asignación cancelada con éxito.', 'success');
                        eliminarAsignacionModal.hide();
                        setTimeout(() => { location.reload(); }, 1000);
                    } else {
                        showMessage('❌ Error: ' + data.message, 'error');
                    }
                })
                .catch(() => showMessage('❌ Error de conexión.', 'error'));
            });
        }

        function vincularEventosTablaAsignaciones() {
            document.querySelectorAll('.editar-asignacion-btn').forEach(btn => {
                btn.addEventListener('click', abrirEditarAsignacionModal);
            });
            document.querySelectorAll('.eliminar-asignacion-btn').forEach(btn => {
                btn.addEventListener('click', abrirEliminarAsignacionModal);
            });
        }

        vincularEventosTablaAsignaciones();
    }

    // =========================================================================
    // III. MÓDULO: CONSULTAS Y TRANSACCIONES AVANZADAS (NUEVO)
    // =========================================================================
    const btnSub2 = document.getElementById('btnSub2');
    const btnSub3 = document.getElementById('btnSub3');
    const theadResultadosAvanzados = document.getElementById('theadResultadosAvanzados');
    const tbodyResultadosAvanzados = document.getElementById('tbodyResultadosAvanzados');
    const tituloModalAvanzados = document.getElementById('tituloModalAvanzados');

    function mostrarModalAvanzados() {
        const modalEl = document.getElementById('modalResultadosAvanzados');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    if (btnSub2) {
        btnSub2.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            fetch('?page=rutinas&action=obtener_asignaciones_avanzadas_ajax&ajax=true')
                .then(res => res.text())
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        console.error("La respuesta del servidor no es JSON válido:", text);
                        throw new Error("Respuesta inválida del servidor");
                    }
                })
                .then(data => {
                    if (data.success) {
                        if (tituloModalAvanzados) tituloModalAvanzados.innerHTML = '<i class="fas fa-fire-alt text-warning"></i> Asignaciones de Nivel Avanzado';
                        if (theadResultadosAvanzados) {
                            theadResultadosAvanzados.innerHTML = `
                                <tr>
                                    <th># Asignación</th>
                                    <th>Cédula Cliente</th>
                                    <th>Fecha Inicio</th>
                                    <th>Estado</th>
                                </tr>
                            `;
                        }
                        
                        let html = '';
                        if (!data.data || data.data.length === 0) {
                            html = '<tr><td colspan="4" class="text-center text-muted">No se encontraron asignaciones con dificultad Avanzado.</td></tr>';
                        } else {
                            data.data.forEach(item => {
                                const estadoClase = item.estado ? item.estado.toLowerCase() : 'activa';
                                html += `
                                    <tr>
                                        <td>${item.id_asignacion}</td>
                                        <td><strong>${escapeHtml(item.cedula_cliente)}</strong></td>
                                        <td>${item.fecha_inicio || 'Pendiente'}</td>
                                        <td><span class="estado-badge est-${estadoClase}">${escapeHtml(item.estado)}</span></td>
                                    </tr>
                                `;
                            });
                        }
                        if (tbodyResultadosAvanzados) tbodyResultadosAvanzados.innerHTML = html;
                        
                        mostrarModalAvanzados();
                    } else {
                        showMessage('❌ Error: ' + (data.message || 'No se pudo obtener la información'), 'error');
                    }
                })
                .catch(() => showMessage('❌ Error al obtener asignaciones avanzadas. Revisa la consola.', 'error'));
        });
    }

    if (btnSub3) {
        btnSub3.addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch('?page=rutinas&action=obtener_rutinas_mas_largas_ajax&ajax=true')
                .then(res => res.text())
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        console.error("La respuesta del servidor no es JSON válido:", text);
                        throw new Error("Respuesta inválida del servidor");
                    }
                })
                .then(data => {
                    if (data.success) {
                        if (tituloModalAvanzados) tituloModalAvanzados.innerHTML = '<i class="fas fa-stopwatch text-info"></i> Rutinas con Mayor Duración';
                        if (theadResultadosAvanzados) {
                            theadResultadosAvanzados.innerHTML = `
                                <tr>
                                    <th>Cliente</th>
                                    <th>Nombre del Plan</th>
                                    <th>Duración (Semanas)</th>
                                    <th>Objetivo Principal</th>
                                </tr>
                            `;
                        }
                        let html = '';
                        if (!data.data || data.data.length === 0) {
                            html = '<tr><td colspan="4" class="text-center text-muted">No se encontraron rutinas registradas.</td></tr>';
                        } else {
                            data.data.forEach(item => {
                                const clienteInfo = item.cedula_cliente 
                                    ? `<strong>${escapeHtml(item.nombre_cliente)}</strong><br><small class="text-muted"><i class="fas fa-id-card"></i> ${escapeHtml(item.cedula_cliente)}</small>` 
                                    : '<span class="text-muted"><i class="fas fa-user-times"></i> Sin asignar</span>';

                                html += `
                                    <tr>
                                        <td>${clienteInfo}</td>
                                        <td><strong>${escapeHtml(item.nombre_rutina)}</strong></td>
                                        <td><span class="badge bg-primary">${item.duracion_semanas} semanas</span></td>
                                        <td>${escapeHtml(item.objetivo || '—')}</td>
                                    </tr>
                                `;
                            });
                        }
                        if (tbodyResultadosAvanzados) tbodyResultadosAvanzados.innerHTML = html;
                        
                        mostrarModalAvanzados();
                    } else {
                        showMessage('❌ Error: ' + (data.message || 'No se pudo obtener la información'), 'error');
                    }
                })
                .catch(() => showMessage('❌ Error al obtener rutinas más largas. Revisa la consola.', 'error'));
        });
    }

    const checkAllAsignaciones = document.getElementById('checkAllAsignaciones');
    if (checkAllAsignaciones) {
        checkAllAsignaciones.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.check-asignacion');
            checkboxes.forEach(chk => {
                if (chk.closest('tr').style.display !== 'none') {
                    chk.checked = this.checked;
                }
            });
        });
    }

    const btnCancelarMasivoUI = document.getElementById('btnCancelarMasivoUI');
    const modalConfirmarCancelacionMasivaEl = document.getElementById('modalConfirmarCancelacionMasiva');
    const modalConfirmarCancelacionMasiva = modalConfirmarCancelacionMasivaEl ? new bootstrap.Modal(modalConfirmarCancelacionMasivaEl) : null;
    const btnConfirmarBajaMedicaMasiva = document.getElementById('btnConfirmarBajaMedicaMasiva');

    if (btnCancelarMasivoUI) {
        btnCancelarMasivoUI.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.check-asignacion:checked');
            if (checkboxes.length === 0) {
                showMessage('⚠️ Seleccione al menos una rutina en la tabla marcando su casilla.', 'error');
                return;
            }

            const clientesMap = new Map();
            checkboxes.forEach(chk => {
                const cedula = chk.getAttribute('data-cedula');
                const row = chk.closest('tr');
                const nombreElement = row.querySelector('strong');
                const nombre = nombreElement ? nombreElement.innerText : 'Cliente';
                if (!clientesMap.has(cedula)) {
                    clientesMap.set(cedula, nombre);
                }
            });

            const listaContenedor = document.getElementById('listaClientesCancelacion');
            if (listaContenedor) {
                let html = '<ul class="list-group" style="max-height: 200px; overflow-y: auto;">';
                clientesMap.forEach((nombre, cedula) => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${escapeHtml(nombre)}
                        <span class="badge bg-secondary rounded-pill">${escapeHtml(cedula)}</span>
                    </li>`;
                });
                html += '</ul>';
                listaContenedor.innerHTML = html;
            }

            if (modalConfirmarCancelacionMasiva) modalConfirmarCancelacionMasiva.show();
        });
    }

    if (btnConfirmarBajaMedicaMasiva) {
        btnConfirmarBajaMedicaMasiva.addEventListener('click', async function() {
            const checkboxes = document.querySelectorAll('.check-asignacion:checked');
            const cedulasUnicas = [...new Set(Array.from(checkboxes).map(chk => chk.getAttribute('data-cedula')))];

            if (cedulasUnicas.length === 0) return;

            btnConfirmarBajaMedicaMasiva.disabled = true;
            btnConfirmarBajaMedicaMasiva.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            try {
                const promesas = cedulasUnicas.map(cedula => {
                    const formData = new FormData();
                    formData.append('cedula_cliente', cedula);
                    
                    return fetch('?page=rutinas&action=cancelar_rutinas_cliente', {
                        method: 'POST',
                        body: formData
                    }).then(res => res.json());
                });

                const resultados = await Promise.all(promesas);
                const exitosos = resultados.filter(res => res.success).length;
                
                if (exitosos > 0) {
                    showMessage(`✅ Se procesaron cancelaciones para ${exitosos} cliente(s).`, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage('❌ No se pudo cancelar ninguna rutina. Verifique el estado.', 'error');
                    btnConfirmarBajaMedicaMasiva.disabled = false;
                    btnConfirmarBajaMedicaMasiva.innerHTML = '<i class="fas fa-ban"></i> Confirmar Cancelación';
                }
            } catch (error) {
                showMessage('❌ Error de red al procesar las cancelaciones múltiples.', 'error');
                btnConfirmarBajaMedicaMasiva.disabled = false;
                btnConfirmarBajaMedicaMasiva.innerHTML = '<i class="fas fa-ban"></i> Confirmar Cancelación';
            }
        });
    }
});