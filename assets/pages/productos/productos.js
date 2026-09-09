document.addEventListener('DOMContentLoaded', function() {

    // ==================== ELEMENTOS DE LA INTERFAZ ====================
    const formRegistrar = document.getElementById('formRegistrarProducto');
    const tablaBody = document.getElementById('tablaProductosBody');
    const tablaProductos = document.getElementById('tablaProductos');
    const buscarInput = document.getElementById('buscarProductoInput');
    const btnBuscar = document.getElementById('btnBuscarProducto');
    const btnAumentarSuplementos = document.getElementById('btnAumentarSuplementos');

    // ==================== CONTROL SEGURO DE INSTANCIAS DE MODALES DE BOOTSTRAP ====================
    const modalAjustarEl = document.getElementById('ajustarStockModal');
    const modalEditarEl = document.getElementById('editarProductoModal');
    const modalEliminarEl = document.getElementById('eliminarProductoModal');

    const ajustarStockModal = (modalAjustarEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalAjustarEl) : null;
    const editarProductoModal = (modalEditarEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalEditarEl) : null;
    const eliminarProductoModal = (modalEliminarEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalEliminarEl) : null;

    // ==================== TOAST NOTIFICACIONES ====================
    function showMessage(message, type = 'success') {
        const toast = document.getElementById('toastMessage');
        if (!toast) return;
        toast.textContent = message;
        toast.className = ''; 
        toast.classList.add(type);
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }

    // ==================== VALIDACIONES EN TIEMPO REAL ====================
    const inputsAValidar = [
        { id: 'prod_codigo', type: 'codigo' },
        { id: 'prod_nombre', type: 'nombre' },
        { id: 'prod_precio', type: 'numero' },
        { id: 'prod_stock_actual', type: 'numero' },
        { id: 'prod_stock_minimo', type: 'numero' },
        { id: 'edit_prod_nombre', type: 'nombre' },
        { id: 'edit_prod_precio', type: 'numero' },
        { id: 'edit_prod_minimo', type: 'numero' }
    ];

    function validarInput(inputElement, tipo) {
        if (!inputElement) return true;
        
        let isValid = true;
        let errorMsg = '';
        const val = inputElement.value;

        if (val !== '') {
            if (tipo === 'codigo') {
                if (!/^[a-zA-Z0-9]+$/.test(val)) {
                    isValid = false;
                    errorMsg = 'Solo se aceptan letras y números sin espacios.';
                }
            } else if (tipo === 'nombre') {
                if (!/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/.test(val)) {
                    isValid = false;
                    errorMsg = 'Solo se aceptan letras, números y espacios.';
                }
            } else if (tipo === 'numero') {
                if (parseFloat(val) < 0 || val.includes('-')) {
                    isValid = false;
                    errorMsg = 'No se permiten valores negativos.';
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

    // ==================== BUSCADOR LOCAL EN TABLA DE PRODUCTOS (IGUAL A VENTAS.JS) ====================
    if (buscarInput) {
        buscarInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            // Buscar las filas dentro del tbody
            const targetContainer = tablaBody || (tablaProductos ? tablaProductos.querySelector('tbody') : null);
            if (!targetContainer) return;

            const rows = targetContainer.querySelectorAll('tr');
            
            rows.forEach(row => {
                // Ignorar filas informativas de "No hay productos" (1 sola columna)
                if (row.cells.length === 1) return; 
                
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    if (btnBuscar) {
        btnBuscar.addEventListener('click', function(e) {
            e.preventDefault();
            if (buscarInput) {
                buscarInput.dispatchEvent(new Event('input'));
            }
        });
    }

    // ==================== CARGAR Y ACTUALIZAR PRODUCTOS ====================
    async function cargarProductos() {
        try {
            const response = await fetch(`?page=productos&action=buscarAjax&ajax=buscar_productos&termino=`);
            if (!response.ok) throw new Error('Error en la red');
            const data = await response.json();
            actualizarTabla(data);
        } catch (error) {
            console.error('Error cargando productos:', error);
        }
    }

    function actualizarTabla(productos) {
        if (!tablaBody) return;
        if (productos.length === 0) {
            tablaBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted"><i class="fas fa-box-open fa-2x mb-2 d-block"></i>No hay productos registrados en este momento.</td></tr>';
            return;
        }

        let html = '';
        productos.forEach(p => {
            let claseStock = 'stock-ok';
            const stockActual = parseInt(p.stock_actual);
            const stockMinimo = parseInt(p.stock_minimo);

            if (stockActual <= 0) {
                claseStock = 'stock-peligro';
            } else if (stockActual <= stockMinimo) {
                claseStock = 'stock-alerta';
            }

            const precioVenta = parseFloat(p.precio_venta).toFixed(2);
            const nombreCategoria = p.nombre_categoria ? escapeHtml(p.nombre_categoria) : 'Sin categoría';
            const imagenSrc = p.imagen ? escapeHtml(p.imagen) : 'https://placehold.co/100x100?text=No+Img';

            html += `
                <tr data-codigo="${escapeHtml(p.codigo_producto)}">
                    <td>
                        <img src="${imagenSrc}" class="img-producto" alt="Imagen del producto">
                    </td>
                    <td><strong>${escapeHtml(p.codigo_producto)}</strong></td>
                    <td>${escapeHtml(p.nombre)}</td>
                    <td><span class="badge bg-secondary">${nombreCategoria}</span></td>
                    <td>$${precioVenta}</td>
                    <td>
                        <span class="badge-stock ${claseStock}">
                            ${stockActual} <small class="text-muted">/ ${stockMinimo}</small>
                        </span>
                    </td>
                    <td>${escapeHtml(p.nombre_unidad)}</td>
                    <td>
                        <div class="acciones-botones justify-content-end">
                            <button class="btn btn-sm btn-outline-success ajustar-stock-btn btn-sm-custom" 
                                    data-codigo="${escapeHtml(p.codigo_producto)}" 
                                    data-nombre="${escapeHtml(p.nombre)}" 
                                    data-stock="${stockActual}">
                                <i class="fas fa-plus-minus"></i> Stock
                            </button>
                            <button class="btn btn-sm btn-warning editar-prod-btn btn-sm-custom" 
                                    data-codigo="${escapeHtml(p.codigo_producto)}" 
                                    data-nombre="${escapeHtml(p.nombre)}" 
                                    data-categoria="${p.id_categoria || ''}" 
                                    data-precio="${p.precio_venta}" 
                                    data-minimo="${stockMinimo}" 
                                    data-unidad="${p.id_unidad}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger eliminar-prod-btn btn-sm-custom" 
                                    data-codigo="${escapeHtml(p.codigo_producto)}" 
                                    data-nombre="${escapeHtml(p.nombre)}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        tablaBody.innerHTML = html;
        asignarEventosBotones();

        // Aplicar de nuevo el filtro de búsqueda local si hay texto en el input
        if (buscarInput && buscarInput.value.trim() !== '') {
            buscarInput.dispatchEvent(new Event('input'));
        }
    }

    // ==================== REGISTRAR PRODUCTO ====================
    if (formRegistrar) {
        formRegistrar.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const camposFormulario = ['prod_codigo', 'prod_nombre', 'prod_precio', 'prod_stock_actual', 'prod_stock_minimo'];
            if (!validarFormularioCompleto(camposFormulario)) {
                showMessage('⚠️ Hay errores en el formulario. Por favor, revise los campos en rojo.', 'error');
                return; 
            }

            const formData = new FormData(formRegistrar);

            try {
                const response = await fetch('?page=productos&action=crear', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    formRegistrar.reset();
                    camposFormulario.forEach(id => {
                        const input = document.getElementById(id);
                        if(input) {
                            input.classList.remove('is-invalid');
                            const div = input.nextElementSibling;
                            if(div && div.classList.contains('error-msg')) div.style.display = 'none';
                        }
                    });
                    
                    document.getElementById('prod_stock_actual').value = 0;
                    document.getElementById('prod_stock_minimo').value = 5;
                    document.getElementById('prod_unidad').value = '1';
                    cargarProductos();
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                console.error("Error al registrar:", error);
                showMessage('❌ Error de conexión al registrar producto', 'error');
            }
        });
    }

    // ==================== AUMENTAR PRECIOS A SUPLEMENTOS ====================
    if (btnAumentarSuplementos) {
        btnAumentarSuplementos.addEventListener('click', async function() {
            try {
                // Dar feedback visual deshabilitando el botón temporalmente
                const btnHtmlOriginal = btnAumentarSuplementos.innerHTML;
                btnAumentarSuplementos.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';
                btnAumentarSuplementos.disabled = true;

                const response = await fetch('?page=productos&action=aumentarPreciosSuplementos', {
                    method: 'POST'
                });
                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    cargarProductos(); // Refrescar los datos para ver los nuevos precios de inmediato
                } else {
                    showMessage(data.message, 'error');
                }

                // Restaurar estado visual del botón
                btnAumentarSuplementos.innerHTML = btnHtmlOriginal;
                btnAumentarSuplementos.disabled = false;

            } catch (error) {
                console.error("Error al aumentar precios:", error);
                showMessage('❌ Error de conexión al procesar el aumento de precios.', 'error');
                
                btnAumentarSuplementos.innerHTML = '<i class="fas fa-arrow-trend-up me-1"></i> Aumentar 10% a Suplementos';
                btnAumentarSuplementos.disabled = false;
            }
        });
    }

    // ==================== MODALES AUXILIARES DE AJUSTE, EDICIÓN Y BORRADO ====================
    
    function abrirAjustarStockModal(e) {
        if (!ajustarStockModal) return;
        const btn = e.currentTarget;
        document.getElementById('ajuste_codigo').value = btn.getAttribute('data-codigo');
        document.getElementById('ajuste_nombre_prod').innerText = btn.getAttribute('data-nombre');
        document.getElementById('ajuste_stock_actual').innerText = btn.getAttribute('data-stock');
        document.getElementById('ajuste_cantidad').value = 1;
        document.getElementById('tipo_entrada').checked = true;
        ajustarStockModal.show();
    }

    const btnGuardarAjuste = document.getElementById('guardarAjusteStock');
    if (btnGuardarAjuste) {
        btnGuardarAjuste.addEventListener('click', async () => {
            const codigo = document.getElementById('ajuste_codigo').value;
            const cantidadInput = parseInt(document.getElementById('ajuste_cantidad').value);
            const tipoAjuste = document.querySelector('input[name="tipo_ajuste"]:checked').value;

            if (isNaN(cantidadInput) || cantidadInput <= 0) {
                showMessage('⚠️ Por favor ingrese una cantidad válida mayor que cero.', 'error');
                return;
            }

            const cantidadFinal = tipoAjuste === 'salida' ? -cantidadInput : cantidadInput;
            const formData = new FormData();
            formData.append('codigo_producto', codigo);
            formData.append('cantidad', cantidadFinal);

            try {
                const response = await fetch('?page=productos&action=actualizarStock', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    if (ajustarStockModal) ajustarStockModal.hide();
                    cargarProductos();
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('❌ Error de conexión al actualizar el stock.', 'error');
            }
        });
    }

    function abrirEditarModal(e) {
        if (!editarProductoModal) return;
        const btn = e.currentTarget;

        document.getElementById('edit_prod_codigo').value = btn.getAttribute('data-codigo');
        document.getElementById('edit_prod_nombre').value = btn.getAttribute('data-nombre');
        document.getElementById('edit_prod_categoria').value = btn.getAttribute('data-categoria');
        document.getElementById('edit_prod_precio').value = btn.getAttribute('data-precio');
        document.getElementById('edit_prod_minimo').value = btn.getAttribute('data-minimo');
        document.getElementById('edit_prod_unidad').value = btn.getAttribute('data-unidad');
        document.getElementById('edit_prod_imagen').value = "";
        
        ['edit_prod_nombre', 'edit_prod_precio', 'edit_prod_minimo'].forEach(id => {
            const input = document.getElementById(id);
            if(input) {
                input.classList.remove('is-invalid');
                const errorDiv = input.nextElementSibling;
                if(errorDiv && errorDiv.classList.contains('error-msg')) errorDiv.style.display = 'none';
            }
        });

        editarProductoModal.show();
    }

    const btnGuardarCambios = document.getElementById('guardarCambiosProducto');
    if (btnGuardarCambios) {
        btnGuardarCambios.addEventListener('click', async () => {
            const camposEdicion = ['edit_prod_nombre', 'edit_prod_precio', 'edit_prod_minimo'];
            if (!validarFormularioCompleto(camposEdicion)) {
                showMessage('⚠️ Hay errores en el formulario. Por favor, revise los campos en rojo.', 'error');
                return;
            }

            const codigo = document.getElementById('edit_prod_codigo').value;
            const nombre = document.getElementById('edit_prod_nombre').value;
            const categoria = document.getElementById('edit_prod_categoria').value;
            const precio = document.getElementById('edit_prod_precio').value;
            const minimo = document.getElementById('edit_prod_minimo').value;
            const unidad = document.getElementById('edit_prod_unidad').value;
            const inputImagen = document.getElementById('edit_prod_imagen');

            if (!nombre || !precio || !minimo || !unidad) {
                showMessage('⚠️ Todos los campos son requeridos para guardar.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('codigo_producto', codigo);
            formData.append('nombre', nombre);
            formData.append('id_categoria', categoria);
            formData.append('precio_venta', precio);
            formData.append('stock_minimo', minimo);
            formData.append('id_unidad', unidad);

            if (inputImagen.files.length > 0) {
                formData.append('imagen', inputImagen.files[0]);
            }

            try {
                const response = await fetch('?page=productos&action=editar', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    if (editarProductoModal) editarProductoModal.hide();
                    cargarProductos();
                } else {
                    showMessage(data.message, 'error');
                }
            } catch(error) {
                showMessage('❌ Error de conexión al actualizar producto.', 'error');
            }
        });
    }

    function abrirEliminarModal(e) {
        if (!eliminarProductoModal) return;
        const btn = e.currentTarget;
        document.getElementById('eliminar_codigo').value = btn.getAttribute('data-codigo');
        document.getElementById('eliminar_nombre_prod').innerText = btn.getAttribute('data-nombre');
        eliminarProductoModal.show();
    }

    const btnConfirmarEliminar = document.getElementById('confirmarEliminarProducto');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', async () => {
            const codigo = document.getElementById('eliminar_codigo').value;
            const formData = new FormData();
            formData.append('codigo_producto', codigo);
            formData.append('fisico', 'false');

            try {
                const response = await fetch('?page=productos&action=eliminar', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    if (eliminarProductoModal) eliminarProductoModal.hide();
                    cargarProductos();
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('❌ Error de conexión al intentar eliminar el producto.', 'error');
            }
        });
    }

    // ==================== ASIGNACIÓN DE EVENTOS ====================
    function asignarEventosBotones() {
        document.querySelectorAll('.ajustar-stock-btn').forEach(btn => {
            btn.addEventListener('click', abrirAjustarStockModal);
        });
        document.querySelectorAll('.editar-prod-btn').forEach(btn => {
            btn.addEventListener('click', abrirEditarModal);
        });
        document.querySelectorAll('.eliminar-prod-btn').forEach(btn => {
            btn.addEventListener('click', abrirEliminarModal);
        });
    }

    // ==================== AUXILIARES ====================
    function escapeHtml(str) {
        if (!str) return '';
        return str.toString().replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m] || m));
    }

    // Carga e inicialización de eventos iniciales
    asignarEventosBotones();
    
    // Disparar evento inicial si el input ya contenía texto
    if (buscarInput && buscarInput.value.trim() !== '') {
        buscarInput.dispatchEvent(new Event('input'));
    }
});