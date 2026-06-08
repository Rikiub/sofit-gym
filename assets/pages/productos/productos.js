// productos.js
document.addEventListener('DOMContentLoaded', function() {
    // ==================== ELEMENTOS DE LA INTERFAZ ====================
    const formRegistrar = document.getElementById('formRegistrarProducto');
    const tablaBody = document.getElementById('tablaProductosBody');
    const buscarInput = document.getElementById('buscarProductoInput');
    const btnBuscar = document.getElementById('btnBuscarProducto');
    let currentSearchTerm = buscarInput ? buscarInput.value : '';

    // ==================== CONTROL SEGURO DE INSTANCIAS DE MODALES DE BOOTSTRAP ====================
    // Para evitar caídas de script si algún modal no está cargado completamente en el DOM, validamos su existencia.
    const modalAjustarEl = document.getElementById('ajustarStockModal');
    const modalEditarEl = document.getElementById('editarProductoModal');
    const modalEliminarEl = document.getElementById('eliminarProductoModal');
    const modalComprobanteEl = document.getElementById('comprobanteVentaModal');

    const ajustarStockModal = (modalAjustarEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalAjustarEl) : null;
    const editarProductoModal = (modalEditarEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalEditarEl) : null;
    const eliminarProductoModal = (modalEliminarEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalEliminarEl) : null;
    const comprobanteVentaModal = (modalComprobanteEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalComprobanteEl) : null;

    // Elementos de Ventas
    const ventaCliente = document.getElementById('ventaCliente');
    const ventaSeleccionarProducto = document.getElementById('ventaSeleccionarProducto');
    const ventaPrecioUnitario = document.getElementById('ventaPrecioUnitario');
    const ventaStockDisponible = document.getElementById('ventaStockDisponible');
    const ventaCantidad = document.getElementById('ventaCantidad');
    const btnAgregarAlCarrito = document.getElementById('btnAgregarAlCarrito');
    const carritoTablaBody = document.getElementById('carritoTablaBody');
    const carritoContador = document.getElementById('carritoContador');
    const ventaTotalCarrito = document.getElementById('ventaTotalCarrito');
    const ventaMetodoPago = document.getElementById('ventaMetodoPago');
    const btnProcesarVenta = document.getElementById('btnProcesarVenta');

    let carrito = []; // Carrito local: [{ codigo, nombre, precio, stock, cantidad, subtotal }]

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

    // Exportar función globalmente para que el toggleView inline la pueda activar de forma transparente
    window.actualizarListaClientesGlobal = function() {
        actualizarListaClientes();
    };

    // ==================== CARGAR / BUSCAR PRODUCTOS ====================
    function cargarProductos(termino) {
        currentSearchTerm = termino;
        fetch(`?page=productos&action=buscarAjax&ajax=buscar_productos&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                actualizarTabla(data);
                actualizarSelectorProductos(data);
            })
            .catch(error => {
                console.error('Error cargando productos:', error);
                showMessage('❌ Error al conectar con el catálogo de productos.', 'error');
            });
    }

    function actualizarTabla(productos) {
        if (!tablaBody) return;
        if (productos.length === 0) {
            tablaBody.innerHTML = '<tr><td colspan="7" class="text-center py-3 text-muted">No se encontraron productos en este momento.</td></tr>';
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

            html += `
                <tr data-codigo="${escapeHtml(p.codigo_producto)}">
                    <td><strong>${escapeHtml(p.codigo_producto)}</strong></td>
                    <td>${escapeHtml(p.nombre)}</td>
                    <td><span class="badge bg-secondary">${escapeHtml(p.categoria || 'Sin categoría')}</span></td>
                    <td>$${precioVenta}</td>
                    <td>
                        <span class="badge-stock ${claseStock}">
                            ${stockActual} <small class="text-muted">/ ${stockMinimo}</small>
                        </span>
                    </td>
                    <td>${escapeHtml(p.unidad_medida)}</td>
                    <td>
                        <div class="acciones-botones justify-content-end">
                            <!-- Movimiento Rápido Stock -->
                            <button class="btn btn-sm btn-outline-success ajustar-stock-btn btn-sm-custom" 
                                    data-codigo="${escapeHtml(p.codigo_producto)}" 
                                    data-nombre="${escapeHtml(p.nombre)}" 
                                    data-stock="${stockActual}">
                                <i class="fas fa-plus-minus"></i> Stock
                            </button>
                            <!-- Editar -->
                            <button class="btn btn-sm btn-warning editar-prod-btn btn-sm-custom" 
                                    data-codigo="${escapeHtml(p.codigo_producto)}" 
                                    data-nombre="${escapeHtml(p.nombre)}" 
                                    data-categoria="${escapeHtml(p.categoria || '')}" 
                                    data-precio="${p.precio_venta}" 
                                    data-minimo="${stockMinimo}" 
                                    data-unidad="${escapeHtml(p.unidad_medida)}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <!-- Eliminar -->
                            <button class="btn btn-sm btn-danger eliminar-prod-btn btn-sm-custom" 
                                    data-codigo="${escapeHtml(p.codigo_producto)}" 
                                    data-nombre="${escapeHtml(p.nombre)}">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        tablaBody.innerHTML = html;
        asignarEventosBotones();
    }

    function actualizarSelectorProductos(productos) {
        if (!ventaSeleccionarProducto) return;
        let html = '<option value="">-- Seleccionar un Producto --</option>';
        productos.forEach(p => {
            if (parseInt(p.stock_actual) > 0) {
                html += `
                    <option value="${escapeHtml(p.codigo_producto)}" 
                            data-precio="${p.precio_venta}" 
                            data-nombre="${escapeHtml(p.nombre)}" 
                            data-stock="${p.stock_actual}">
                        ${escapeHtml(p.nombre)} - $${parseFloat(p.precio_venta).toFixed(2)} (Dispo: ${p.stock_actual})
                    </option>
                `;
            }
        });
        ventaSeleccionarProducto.innerHTML = html;
        resetCamposDetalleProducto();
    }

    function actualizarListaClientes() {
        if (!ventaCliente) return;
        fetch('?page=productos&action=obtenerClientesAjax')
            .then(res => res.json())
            .then(clientes => {
                let html = '<option value="">-- Consumidor Final (Sin registrar) --</option>';
                clientes.forEach(c => {
                    html += `<option value="${escapeHtml(c.cedula_cliente)}">
                        ${escapeHtml(c.nombre)} ${escapeHtml(c.apellido)} (${escapeHtml(c.cedula_cliente)})
                    </option>`;
                });
                ventaCliente.innerHTML = html;
            })
            .catch(e => console.error("Error al sincronizar clientes:", e));
    }

    function buscarProductos() {
        if (!buscarInput) return;
        const termino = buscarInput.value.trim();
        cargarProductos(termino);
    }

    if (btnBuscar) {
        btnBuscar.addEventListener('click', buscarProductos);
    }
    if (buscarInput) {
        buscarInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarProductos();
            }
        });
    }

    // ==================== REGISTRAR PRODUCTO ====================
    if (formRegistrar) {
        formRegistrar.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(formRegistrar);

            fetch('?page=productos&action=crear', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    formRegistrar.reset();
                    document.getElementById('prod_stock_actual').value = 0;
                    document.getElementById('prod_stock_minimo').value = 5;
                    document.getElementById('prod_unidad').value = 'unidad';
                    cargarProductos(currentSearchTerm);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión al registrar producto', 'error'));
        });
    }

    // ==================== DETALLES DEL PRODUCTO SELECCIONADO ====================
    if (ventaSeleccionarProducto) {
        ventaSeleccionarProducto.addEventListener('change', function() {
            const selectedOpt = ventaSeleccionarProducto.options[ventaSeleccionarProducto.selectedIndex];
            if (selectedOpt && selectedOpt.value !== "") {
                const precio = parseFloat(selectedOpt.getAttribute('data-precio')).toFixed(2);
                const stock = selectedOpt.getAttribute('data-stock');
                ventaPrecioUnitario.value = `$${precio}`;
                ventaStockDisponible.value = stock;
                ventaCantidad.max = stock;
                ventaCantidad.value = 1;
            } else {
                resetCamposDetalleProducto();
            }
        });
    }

    function resetCamposDetalleProducto() {
        if (ventaPrecioUnitario) ventaPrecioUnitario.value = '';
        if (ventaStockDisponible) ventaStockDisponible.value = '';
        if (ventaCantidad) {
            ventaCantidad.value = 1;
            ventaCantidad.removeAttribute('max');
        }
    }

    // ==================== AÑADIR AL CARRITO DE COMPRA ====================
    if (btnAgregarAlCarrito) {
        btnAgregarAlCarrito.addEventListener('click', function() {
            const codigo = ventaSeleccionarProducto.value;
            if (!codigo) {
                showMessage('⚠️ Seleccione un producto válido.', 'error');
                return;
            }

            const opt = ventaSeleccionarProducto.options[ventaSeleccionarProducto.selectedIndex];
            const nombre = opt.getAttribute('data-nombre');
            const precio = parseFloat(opt.getAttribute('data-precio'));
            const stock = parseInt(opt.getAttribute('data-stock'));
            const cantidad = parseInt(ventaCantidad.value);

            if (isNaN(cantidad) || cantidad <= 0) {
                showMessage('⚠️ Ingrese una cantidad válida.', 'error');
                return;
            }

            if (cantidad > stock) {
                showMessage(`⚠️ No puede vender más del stock disponible (${stock}).`, 'error');
                return;
            }

            const itemExistente = carrito.find(item => item.codigo === codigo);
            if (itemExistente) {
                const nuevaCantidad = itemExistente.cantidad + cantidad;
                if (nuevaCantidad > stock) {
                    showMessage(`⚠️ El total en carrito (${nuevaCantidad}) excede el stock disponible (${stock}).`, 'error');
                    return;
                }
                itemExistente.cantidad = nuevaCantidad;
                itemExistente.subtotal = itemExistente.cantidad * itemExistente.precio;
            } else {
                carrito.push({
                    codigo: codigo,
                    nombre: nombre,
                    precio: precio,
                    stock: stock,
                    cantidad: cantidad,
                    subtotal: precio * cantidad
                });
            }

            renderCarrito();
            showMessage('📦 Producto agregado al listado de venta.');
            ventaSeleccionarProducto.value = '';
            resetCamposDetalleProducto();
        });
    }

    function renderCarrito() {
        if (!carritoTablaBody) return;
        
        if (carrito.length === 0) {
            carritoTablaBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>
                        Aún no se han añadido productos al carro.
                    </td>
                </tr>
            `;
            if (carritoContador) carritoContador.textContent = '0 Productos';
            if (ventaTotalCarrito) ventaTotalCarrito.textContent = '$0.00';
            if (btnProcesarVenta) btnProcesarVenta.disabled = true;
            return;
        }

        let html = '';
        let total = 0;
        let count = 0;

        carrito.forEach((item, index) => {
            total += item.subtotal;
            count += item.cantidad;

            html += `
                <tr>
                    <td><strong>${escapeHtml(item.codigo)}</strong></td>
                    <td>${escapeHtml(item.nombre)}</td>
                    <td>$${item.precio.toFixed(2)}</td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center mx-auto cambiar-cant-input" 
                               style="width: 70px; border-radius: 8px;" 
                               value="${item.cantidad}" 
                               min="1" 
                               max="${item.stock}" 
                               data-index="${index}">
                    </td>
                    <td><strong>$${item.subtotal.toFixed(2)}</strong></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-danger quitar-item-btn" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        carritoTablaBody.innerHTML = html;
        if (carritoContador) carritoContador.textContent = `${count} Elemento(s)`;
        if (ventaTotalCarrito) ventaTotalCarrito.textContent = `$${total.toFixed(2)}`;
        if (btnProcesarVenta) btnProcesarVenta.disabled = false;

        // Eventos interactivos en el carrito
        document.querySelectorAll('.quitar-item-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                carrito.splice(index, 1);
                renderCarrito();
            });
        });

        document.querySelectorAll('.cambiar-cant-input').forEach(input => {
            input.addEventListener('change', function() {
                const index = parseInt(this.getAttribute('data-index'));
                let nuevaCant = parseInt(this.value);
                const item = carrito[index];

                if (isNaN(nuevaCant) || nuevaCant <= 0) {
                    nuevaCant = 1;
                }

                if (nuevaCant > item.stock) {
                    showMessage(`⚠️ Stock insuficiente. El límite es ${item.stock}.`, 'error');
                    nuevaCant = item.stock;
                }

                item.cantidad = nuevaCant;
                item.subtotal = item.cantidad * item.precio;
                renderCarrito();
            });
        });
    }

    // ==================== PROCESAR LA VENTA ====================
    if (btnProcesarVenta) {
        btnProcesarVenta.addEventListener('click', function() {
            if (carrito.length === 0) return;

            const payload = {
                cedula_cliente: ventaCliente.value || null,
                metodo_pago: ventaMetodoPago.value,
                productos: carrito.map(item => ({
                    codigo: item.codigo,
                    cantidad: item.cantidad
                }))
            };

            btnProcesarVenta.disabled = true;
            btnProcesarVenta.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando venta...';

            fetch('?page=productos&action=registrarVenta', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    mostrarComprobante(data.comprobante);
                    carrito = [];
                    renderCarrito();
                    cargarProductos(currentSearchTerm);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showMessage('❌ Error crítico al procesar la venta.', 'error');
            })
            .finally(() => {
                btnProcesarVenta.disabled = false;
                btnProcesarVenta.innerHTML = '<i class="fas fa-check-double"></i> Procesar y Generar Comprobante';
            });
        });
    }

    // ==================== DESPLEGAR COMPROBANTE DE VENTA ====================
    function mostrarComprobante(comp) {
        if (!comprobanteVentaModal) return;
        
        document.getElementById('compFecha').innerText = comp.fecha;
        document.getElementById('compMetodo').innerText = comp.metodo_pago;

        if (comp.cedula_cliente && ventaCliente) {
            const clientText = ventaCliente.options[ventaCliente.selectedIndex].text;
            document.getElementById('compCliente').innerText = clientText;
        } else {
            document.getElementById('compCliente').innerText = "Consumidor Final";
        }

        let rowsHtml = '';
        comp.items.forEach(item => {
            rowsHtml += `
                <tr>
                    <td>${item.cantidad_vendida}</td>
                    <td>${escapeHtml(item.nombre)}</td>
                    <td class="text-end">$${item.precio_unitario.toFixed(2)}</td>
                    <td class="text-end">$${item.monto_total.toFixed(2)}</td>
                </tr>
            `;
        });

        document.getElementById('compItems').innerHTML = rowsHtml;
        document.getElementById('compSubtotal').innerText = `$${comp.total.toFixed(2)}`;
        document.getElementById('compTotal').innerText = `$${comp.total.toFixed(2)}`;

        comprobanteVentaModal.show();
    }

    // ==================== MODALES AUXILIARES DE AJUSTE, EDICIÓN Y BORRADO ====================
    function abrirAjustarStockModal(e) {
        if (!ajustarStockModal) return;
        const btn = e.currentTarget;
        const codigo = btn.getAttribute('data-codigo');
        const nombre = btn.getAttribute('data-nombre');
        const stock = btn.getAttribute('data-stock');

        document.getElementById('ajuste_codigo').value = codigo;
        document.getElementById('ajuste_nombre_prod').innerText = nombre;
        document.getElementById('ajuste_stock_actual').innerText = stock;
        document.getElementById('ajuste_cantidad').value = 1;
        document.getElementById('tipo_entrada').checked = true;

        ajustarStockModal.show();
    }

    const btnGuardarAjuste = document.getElementById('guardarAjusteStock');
    if (btnGuardarAjuste) {
        btnGuardarAjuste.addEventListener('click', () => {
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

            fetch('?page=productos&action=actualizarStock', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    if (ajustarStockModal) ajustarStockModal.hide();
                    cargarProductos(currentSearchTerm);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión al actualizar el stock.', 'error'));
        });
    }

    function abrirEditarModal(e) {
        if (!editarProductoModal) return;
        const btn = e.currentTarget;
        const codigo = btn.getAttribute('data-codigo');
        const nombre = btn.getAttribute('data-nombre');
        const categoria = btn.getAttribute('data-categoria');
        const precio = btn.getAttribute('data-precio');
        const minimo = btn.getAttribute('data-minimo');
        const unidad = btn.getAttribute('data-unidad');

        document.getElementById('edit_prod_codigo').value = codigo;
        document.getElementById('edit_prod_nombre').value = nombre;
        document.getElementById('edit_prod_categoria').value = categoria;
        document.getElementById('edit_prod_precio').value = precio;
        document.getElementById('edit_prod_minimo').value = minimo;
        document.getElementById('edit_prod_unidad').value = unidad;

        editarProductoModal.show();
    }

    const btnGuardarCambios = document.getElementById('guardarCambiosProducto');
    if (btnGuardarCambios) {
        btnGuardarCambios.addEventListener('click', () => {
            const codigo = document.getElementById('edit_prod_codigo').value;
            const nombre = document.getElementById('edit_prod_nombre').value;
            const categoria = document.getElementById('edit_prod_categoria').value;
            const precio = document.getElementById('edit_prod_precio').value;
            const minimo = document.getElementById('edit_prod_minimo').value;
            const unidad = document.getElementById('edit_prod_unidad').value;

            if (!nombre || !precio || !minimo || !unidad) {
                showMessage('⚠️ Todos los campos son requeridos para guardar.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('codigo_producto', codigo);
            formData.append('nombre', nombre);
            formData.append('categoria', categoria);
            formData.append('precio_venta', precio);
            formData.append('stock_minimo', minimo);
            formData.append('unidad_medida', unidad);

            fetch('?page=productos&action=editar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    if (editarProductoModal) editarProductoModal.hide();
                    cargarProductos(currentSearchTerm);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión al actualizar producto.', 'error'));
        });
    }

    function abrirEliminarModal(e) {
        if (!eliminarProductoModal) return;
        const btn = e.currentTarget;
        const codigo = btn.getAttribute('data-codigo');
        const nombre = btn.getAttribute('data-nombre');

        document.getElementById('eliminar_codigo').value = codigo;
        document.getElementById('eliminar_nombre_prod').innerText = nombre;

        eliminarProductoModal.show();
    }

    const btnConfirmarEliminar = document.getElementById('confirmarEliminarProducto');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', () => {
            const codigo = document.getElementById('eliminar_codigo').value;

            const formData = new FormData();
            formData.append('codigo_producto', codigo);
            formData.append('fisico', 'false'); 

            fetch('?page=productos&action=eliminar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    if (eliminarProductoModal) eliminarProductoModal.hide();
                    cargarProductos(currentSearchTerm);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión al intentar eliminar el producto.', 'error'));
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
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m] || m));
    }

    // Carga inicial
    asignarEventosBotones();
});