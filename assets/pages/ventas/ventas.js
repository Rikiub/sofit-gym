document.addEventListener('DOMContentLoaded', function() {
    
    // ==================== ELEMENTOS DE LA INTERFAZ ====================
    // Elementos del POS (Punto de Venta)
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
    
    // Elementos del Historial
    const buscarVentaInput = document.getElementById('buscarVentaInput');
    const tablaVentas = document.getElementById('tablaVentas');

    // Estado del carrito local
    let carrito = []; // [{ codigo, nombre, precio, stock, cantidad, subtotal }]

    // ==================== CONTROL SEGURO DE INSTANCIAS DE MODALES ====================
    const modalComprobanteEl = document.getElementById('comprobanteVentaModal');
    const modalEditarVentaEl = document.getElementById('editarVentaModal');
    const modalEliminarVentaEl = document.getElementById('eliminarVentaModal');
    const modalProductosSinVenderEl = document.getElementById('productosSinVenderModal');

    const comprobanteVentaModal = (modalComprobanteEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalComprobanteEl) : null;
    const editarVentaModal = (modalEditarVentaEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalEditarVentaEl) : null;
    const eliminarVentaModal = (modalEliminarVentaEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalEliminarVentaEl) : null;
    const productosSinVenderModal = (modalProductosSinVenderEl && typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modalProductosSinVenderEl) : null;

    // Recargar página al cerrar el comprobante para refrescar inventario y vista de historial PHP
    if (modalComprobanteEl) {
        modalComprobanteEl.addEventListener('hidden.bs.modal', function () {
            location.reload();
        });
    }


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

    // ==================== UTILIDADES ====================
    function escapeHtml(str) {
        if (!str) return '';
        return str.toString().replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m] || m));
    }


    // ==================== DETALLES DEL PRODUCTO SELECCIONADO (POS) ====================
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

            // Verificar si el producto ya está en el carrito
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

        asignarEventosCarrito();
    }

    function asignarEventosCarrito() {
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
                cedula: ventaCliente ? ventaCliente.value : null,
                metodo_pago: ventaMetodoPago ? ventaMetodoPago.value : 1,
                productos: carrito.map(item => ({
                    codigo: item.codigo,
                    cantidad: item.cantidad
                }))
            };

            btnProcesarVenta.disabled = true;
            btnProcesarVenta.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando venta...';

            // Petición al VentasController
            fetch('?page=ventas&action=registrarVenta', {
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
                    carrito = [];
                    renderCarrito();
                    mostrarComprobante(data.comprobante);
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
        if (!comprobanteVentaModal || !comp) return;
        
        document.getElementById('compFecha').innerText = comp.fecha;
        
        // Obtener texto del método de pago
        const metodoSelect = document.getElementById('ventaMetodoPago');
        let metodoText = 'Efectivo';
        if(metodoSelect && comp.id_metodo) {
            const opt = Array.from(metodoSelect.options).find(o => o.value == comp.id_metodo);
            if(opt) metodoText = opt.text;
        }
        document.getElementById('compMetodo').innerText = metodoText;

        if (comp.cedula_cliente && ventaCliente) {
            const opt = Array.from(ventaCliente.options).find(o => o.value == comp.cedula_cliente);
            document.getElementById('compCliente').innerText = opt ? opt.text : comp.cedula_cliente;
        } else {
            document.getElementById('compCliente').innerText = "Consumidor Final";
        }

        let rowsHtml = '';
        comp.items.forEach(item => {
            rowsHtml += `
                <tr>
                    <td>${item.cantidad_vendida}</td>
                    <td>${escapeHtml(item.nombre)}</td>
                    <td class="text-end">$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                    <td class="text-end">$${parseFloat(item.monto_total).toFixed(2)}</td>
                </tr>
            `;
        });

        document.getElementById('compItems').innerHTML = rowsHtml;
        document.getElementById('compSubtotal').innerText = `$${parseFloat(comp.total).toFixed(2)}`;
        document.getElementById('compTotal').innerText = `$${parseFloat(comp.total).toFixed(2)}`;

        comprobanteVentaModal.show();
    }


    // ==================== BUSCADOR LOCAL EN HISTORIAL DE VENTAS ====================
    if (buscarVentaInput && tablaVentas) {
        buscarVentaInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = tablaVentas.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                // Ignorar filas de "No hay transacciones"
                if(row.cells.length === 1) return; 
                
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }


    // ==================== MODALES DEL HISTORIAL (EDITAR Y ELIMINAR) ====================
    
    // Asignar eventos a los botones generados por PHP en la tabla
    function asignarEventosHistorial() {
        // Editar Venta
        document.querySelectorAll('.editar-venta-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!editarVentaModal) return;
                
                const id = this.getAttribute('data-id');
                const metodo = this.getAttribute('data-metodo');
                const cliente = this.getAttribute('data-cliente');
                const monto = this.getAttribute('data-monto');

                document.getElementById('edit_venta_id').value = id;
                document.getElementById('edit_venta_metodo').value = metodo || "1";
                document.getElementById('edit_venta_cliente').value = cliente || "";
                document.getElementById('edit_venta_monto').value = parseFloat(monto).toFixed(2);

                editarVentaModal.show();
            });
        });

        // Eliminar Venta
        document.querySelectorAll('.eliminar-venta-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!eliminarVentaModal) return;
                
                const id = this.getAttribute('data-id');
                document.getElementById('eliminar_venta_id').value = id;
                document.getElementById('eliminar_venta_texto_id').innerText = String(id).padStart(5, '0');

                eliminarVentaModal.show();
            });
        });
    }

    // Guardar Cambios de Edición de Venta
    const btnGuardarCambiosVenta = document.getElementById('guardarCambiosVenta');
    if (btnGuardarCambiosVenta) {
        btnGuardarCambiosVenta.addEventListener('click', function() {
            const formData = new FormData();
            formData.append('id_venta', document.getElementById('edit_venta_id').value);
            formData.append('id_metodo', document.getElementById('edit_venta_metodo').value);
            formData.append('cedula_cliente', document.getElementById('edit_venta_cliente').value);
            formData.append('monto_total', document.getElementById('edit_venta_monto').value);

            fetch('?page=ventas&action=editar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    if (editarVentaModal) editarVentaModal.hide();
                    setTimeout(() => location.reload(), 1000); // Recargar para reflejar cambios en PHP
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión al actualizar la venta.', 'error'));
        });
    }

    // Confirmar Eliminación de Venta
    const btnConfirmarEliminarVenta = document.getElementById('confirmarEliminarVenta');
    if (btnConfirmarEliminarVenta) {
        btnConfirmarEliminarVenta.addEventListener('click', function() {
            const idVenta = document.getElementById('eliminar_venta_id').value;
            const formData = new FormData();
            formData.append('id_venta', idVenta);

            fetch('?page=ventas&action=eliminar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    if (eliminarVentaModal) eliminarVentaModal.hide();
                    setTimeout(() => location.reload(), 1000); // Recargar para reflejar cambios en PHP
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(() => showMessage('❌ Error de conexión al intentar eliminar la venta.', 'error'));
        });
    }

    // Inicializar eventos de botones si existen en el DOM
    asignarEventosHistorial();

    // ==================== PRODUCTOS NUNCA VENDIDOS ====================
    if (btnProductosSinVender) {
        btnProductosSinVender.addEventListener('click', function() {
            if (!productosSinVenderModal) return;

            // Abrimos el modal mostrando el estado de "Cargando"
            productosSinVenderModal.show();
            if (tablaProductosSinVenderBody) {
                tablaProductosSinVenderBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x mb-3 d-block"></i> Cargando información...
                        </td>
                    </tr>
                `;
            }

            // Consultamos al backend mediante AJAX
            fetch('?page=ventas&action=obtenerProductosSinVenderAjax')
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        let html = '';
                        if (res.data && res.data.length > 0) {
                            res.data.forEach(prod => {
                                // Determinar color del badge según el stock
                                const badgeClass = prod.stock_actual > 0 ? 'bg-success' : 'bg-danger';
                                
                                html += `
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">${escapeHtml(prod.codigo_producto)}</td>
                                        <td>${escapeHtml(prod.nombre)}</td>
                                        <td class="text-center pe-4">
                                            <span class="badge ${badgeClass} rounded-pill px-3 py-2 shadow-sm">
                                                ${prod.stock_actual}
                                            </span>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            html = `
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-success fw-bold">
                                        <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                        ¡Excelente!<br><small class="text-muted">Todos los productos del catálogo han registrado al menos una venta.</small>
                                    </td>
                                </tr>
                            `;
                        }
                        if (tablaProductosSinVenderBody) tablaProductosSinVenderBody.innerHTML = html;
                    } else {
                        showMessage('Error al obtener los datos.', 'error');
                        productosSinVenderModal.hide();
                    }
                })
                .catch(err => {
                    console.error("Error cargando productos sin vender:", err);
                    showMessage('❌ Error de conexión al consultar productos.', 'error');
                    productosSinVenderModal.hide();
                });
        });
    }
});

// Conmutador de vistas (Registrar Venta <-> Historial)
window.toggleView = function(view) {
    const tabVenta = document.getElementById('tabVentaBtn');
    const tabHistorial = document.getElementById('tabHistorialBtn');
    const seccionVenta = document.getElementById('seccionVenta');
    const seccionHistorial = document.getElementById('seccionHistorial');

    if (!tabVenta || !tabHistorial || !seccionVenta || !seccionHistorial) return;

    if (view === 'venta') {
        tabVenta.classList.add('active');
        tabHistorial.classList.remove('active');
        seccionVenta.style.display = 'block';
        seccionHistorial.style.display = 'none';
    } else if (view === 'historial') {
        tabHistorial.classList.add('active');
        tabVenta.classList.remove('active');
        seccionVenta.style.display = 'none';
        seccionHistorial.style.display = 'block';
    }
    
    // Guardar la vista actual en sessionStorage para mantenerla al recargar
    sessionStorage.setItem('ventasActiveTab', view);
};

// Al cargar la página, restaurar la pestaña activa guardada
document.addEventListener('DOMContentLoaded', function() {
    // Obtenemos la pestaña guardada, o por defecto cargamos 'venta'
    const activeTab = sessionStorage.getItem('ventasActiveTab') || 'venta';
    if (typeof window.toggleView === 'function') {
        window.toggleView(activeTab);
    }
});