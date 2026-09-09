<?php
$this->pushJs("pages/ventas/ventas.js");
$this->layout("layout", ["title" => "Gestión de Ventas y Facturación"]);
?>

<style>
    .container {
        --bs-gutter-x: 0;
        border-radius: 28px;
        background-color: white;
        overflow: hidden;
    }

    .header-banner {
        background: #C62828;
        color: white;
        padding: 1.2rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .header-banner h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin: 0;
    }

    .card-custom {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.8rem;
        border: 1px solid #edf2f7;
    }

    .card-header-custom {
        background: #fafbfc;
        padding: 1rem 1.5rem;
        font-weight: 700;
        font-size: 1.1rem;
        border-bottom: 1px solid #edf2f7;
        color: #1e2a3a;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-body-custom {
        padding: 1.5rem;
    }

    .form-grid-custom {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        align-items: flex-end;
    }

    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    label {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #5a6e7a;
    }

    .input-custom,
    .select-custom,
    .btn-custom {
        padding: 0.7rem 1rem;
        border-radius: 14px;
        border: 1px solid #cfdfe8;
        font-size: 0.9rem;
        width: 100%;
    }

    .btn-main {
        background: #C62828;
        color: white;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
    }

    .btn-main:hover {
        background: #b71c1c;
        transform: translateY(-1px);
    }

    .btn-sm-custom {
        padding: 0.2rem 0.6rem !important;
        font-size: 0.7rem !important;
        border-radius: 20px !important;
        width: auto;
    }

    .acciones-botones {
        display: flex;
        gap: 0.3rem;
        flex-wrap: nowrap;
        justify-content: flex-start;
    }

    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    th,
    td {
        padding: 0.6rem 0.5rem;
        text-align: left;
        border-bottom: 1px solid #eef2f6;
        vertical-align: middle;
    }

    th {
        background: #f8fafc;
        font-weight: 600;
    }

    .buscador-contenedor {
        margin-bottom: 1rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .buscador-contenedor input {
        flex: 1;
        width: auto;
    }

    .nav-tabs-custom {
        display: flex;
        gap: 1rem;
        border-bottom: 2px solid #eef2f6;
        margin-bottom: 1.5rem;
        padding: 0 1rem;
    }

    .nav-link-custom {
        padding: 0.8rem 1.5rem;
        color: #5a6e7a;
        font-weight: 600;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }

    .nav-link-custom.active {
        color: #C62828;
        border-bottom-color: #C62828;
    }

    #toastMessage {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #323232;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 9999;
        display: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .bg-danger-custom {
        background-color: #C62828 !important;
    }

    .comprobante-marca {
        text-align: center;
        border-bottom: 2px dashed #cfdfe8;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .comprobante-marca h4 {
        margin: 0;
        font-weight: 800;
        color: #C62828;
        letter-spacing: 1px;
    }

    .comprobante-tabla {
        width: 100%;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .comprobante-tabla th {
        border-bottom: 2px solid #eef2f6;
        background: none;
        padding: 0.4rem;
        font-size: 0.8rem;
    }

    .comprobante-tabla td {
        border-bottom: 1px solid #f8fafc;
        padding: 0.4rem;
        font-size: 0.8rem;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #comprobantePrintArea,
        #comprobantePrintArea * {
            visibility: visible;
        }

        #comprobantePrintArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
        }

        .no-print {
            display: none !important;
        }
    }
</style>

<div class="container">
    <div class="header-banner">
        <h1><i class="fas fa-shopping-cart"></i> Facturación y Ventas</h1>
    </div>

    <div id="toastMessage"></div>

    <div class="p-4">
        <!-- Alertas de Sesión -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show mb-4" role="alert" style="border-radius: 16px;">
                <i class="fas fa-<?= $tipoMensaje == 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Navegación de Pestañas -->
        <div class="nav-tabs-custom">
            <div class="nav-link-custom active" id="tabVentaBtn" onclick="toggleView('venta')">
                <i class="fas fa-cart-plus me-1"></i> Registrar Venta
            </div>
            <div class="nav-link-custom" id="tabHistorialBtn" onclick="toggleView('historial')">
                <i class="fas fa-history me-1"></i> Historial de Ventas
            </div>
        </div>

        <!-- ==================== SECCIÓN: REGISTRAR VENTA (POS) ==================== -->
        <div id="seccionVenta">
            <div class="row">
                <!-- Selector e Item actual para añadir al Carrito -->
                <div class="col-lg-5 col-md-12">
                    <div class="card-custom">
                        <div class="card-header-custom"><i class="fas fa-box-open"></i> Agregar Productos</div>
                        <div class="card-body-custom">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-user-circle"></i> Asociar Cliente (Opcional)</label>
                                <select id="ventaCliente" class="form-select select-custom">
                                    <option value="">-- Consumidor Final (Sin registrar) --</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <!-- Se agregó "?? $c['cedula']" como medida extra de seguridad -->
                                        <option value="<?= htmlspecialchars($c['cedula_cliente'] ?? $c['cedula'] ?? '') ?>">
                                            <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido'] . ' (' . ($c['cedula_cliente'] ?? $c['cedula'] ?? '') . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-search"></i> Buscar Producto</label>
                                <select id="ventaSeleccionarProducto" class="form-select select-custom">
                                    <option value="">-- Seleccionar un Producto --</option>
                                    <?php foreach ($productos as $p): ?>
                                        <?php if ($p['stock_actual'] > 0): ?>
                                            <option value="<?= htmlspecialchars($p['codigo_producto']) ?>"
                                                data-precio="<?= $p['precio_venta'] ?>"
                                                data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                                data-stock="<?= $p['stock_actual'] ?>">
                                                <?= htmlspecialchars($p['nombre']) ?> - $<?= number_format($p['precio_venta'], 2) ?> (Dispo: <?= $p['stock_actual'] ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Precio Unitario</label>
                                    <input type="text" id="ventaPrecioUnitario" class="form-control bg-light" readonly placeholder="$0.00">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Stock Disp.</label>
                                    <input type="text" id="ventaStockDisponible" class="form-control bg-light" readonly placeholder="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-calculator"></i> Cantidad</label>
                                <input type="number" id="ventaCantidad" class="form-control input-custom" value="1" min="1">
                            </div>

                            <button type="button" id="btnAgregarAlCarrito" class="btn btn-success w-100 py-2 fw-bold" style="border-radius: 14px;">
                                <i class="fas fa-plus"></i> Añadir a la Lista
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Detalle del Carrito de Ventas -->
                <div class="col-lg-7 col-md-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <span><i class="fas fa-list-ol"></i> Listado de Compra</span>
                            <span class="badge bg-danger rounded-pill px-3" id="carritoContador">0 Productos</span>
                        </div>
                        <div class="card-body-custom">
                            <div class="table-responsive mb-4" style="min-height: 200px;">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Precio Unit.</th>
                                            <th class="text-center" style="width: 100px;">Cantidad</th>
                                            <th>Subtotal</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="carritoTablaBody">
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>
                                                Aún no se han añadido productos al carro.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totalizador y Método de Pago -->
                            <div class="p-3 bg-light rounded-3 mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label"><i class="fas fa-wallet"></i> Método de Pago</label>
                                        <select id="ventaMetodoPago" class="form-select select-custom">
                                            <option value="1">Efectivo</option>
                                            <option value="2">Transferencia Bancaria</option>
                                            <option value="3">Pago Móvil</option>
                                            <option value="4">Tarjeta de Crédito</option>
                                            <option value="6">Tarjeta de Débito</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <span class="text-muted d-block uppercase font-weight-bold" style="font-size: 0.8rem;">MONTO TOTAL A PAGAR</span>
                                        <h2 class="text-danger font-weight-bold mb-0" id="ventaTotalCarrito">$0.00</h2>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="btnProcesarVenta" class="btn-custom btn-main py-3 fs-5" disabled>
                                <i class="fas fa-check-double"></i> Procesar y Generar Comprobante
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== SECCIÓN: HISTORIAL DE VENTAS ==================== -->
        <div id="seccionHistorial" style="display: none;">
            <div class="card-custom">
                <div class="card-header-custom">
                    <span><i class="fas fa-history"></i> Transacciones Registradas</span>
                    <a href="?page=ventas&action=vistaReporte" class="btn btn-sm btn-outline-danger btn-sm-custom ms-auto">
                        <i class="fas fa-file-pdf"></i> Reportes
                    </a>
                </div>
                <div class="card-body-custom">
                    <div class="buscador-contenedor mb-4">
                        <input type="text" id="buscarVentaInput" class="form-control text-muted" placeholder="Buscar por cliente, producto, método o fecha...">
                        <button class="btn btn-secondary px-4"><i class="fas fa-search"></i> Buscar</button>
                    </div>

                    <!-- Botón para ver productos sin vender -->
                    <div class="mb-4 d-flex justify-content-start">
                        <button id="btnProductosSinVender" class="btn btn-sm btn-info px-3 shadow-sm text-white font-weight-bold" type="button" style="border-radius: 12px; background-color: #17a2b8; border: none;">
                            <i class="fas fa-box-open me-1"></i> Ver Productos Nunca Vendidos
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="tablaVentas">
                            <thead>
                                <tr>
                                    <th>N° Venta</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th>Método Pago</th>
                                    <th>Cant. Prod.</th>
                                    <th>Monto Total</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($ventas)): ?>
                                    <?php foreach ($ventas as $v): ?>
                                        <tr>
                                            <td><strong>#<?= str_pad($v['id_venta'] ?? 0, 5, '0', STR_PAD_LEFT) ?></strong></td>
                                            <td><?= htmlspecialchars($v['fecha_venta'] ?? date('Y-m-d')) ?></td>
                                            <td>
                                                <?php if(!empty($v['cedula_cliente'])): ?>
                                                    <?= htmlspecialchars($v['nombre_cliente'] ?? 'Cliente') ?> 
                                                    <small class="text-muted d-block"><?= htmlspecialchars($v['cedula_cliente']) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Consumidor Final</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= htmlspecialchars($v['nombre_producto'] ?? 'N/A') ?></strong></td>
                                            <!-- Se mantiene $v['nombre_metodo'] pero ahora el Modelo sí lo enviará correctamente -->
                                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($v['nombre_metodo'] ?? 'General') ?></span></td>
                                            <td><?= htmlspecialchars($v['cantidad_total'] ?? $v['cantidad_vendida'] ?? 1) ?></td>
                                            <td class="text-success font-weight-bold">$<?= number_format($v['monto_total'] ?? 0, 2) ?></td>
                                            <td>
                                                <div class="acciones-botones justify-content-end">
                                                    <!-- Editar -->
                                                    <button class="btn btn-sm btn-warning editar-venta-btn btn-sm-custom"
                                                        data-id="<?= $v['id_venta'] ?? 0 ?>"
                                                        data-metodo="<?= $v['id_metodo'] ?? 1 ?>"
                                                        data-cliente="<?= $v['cedula_cliente'] ?? '' ?>"
                                                        data-monto="<?= $v['monto_total'] ?? 0 ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <!-- Eliminar -->
                                                    <button class="btn btn-sm btn-danger eliminar-venta-btn btn-sm-custom"
                                                        data-id="<?= $v['id_venta'] ?? 0 ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <!-- Se actualizó el colspan de 7 a 8 por la nueva columna "Producto" -->
                                        <td colspan="8" class="text-center py-4 text-muted">No hay transacciones registradas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODALES ==================== -->

<!-- MODAL: COMPROBANTE DE VENTA -->
<div class="modal fade" id="comprobanteVentaModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white no-print">
                <h5 class="modal-title"><i class="fas fa-print"></i> Comprobante de Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white p-4" id="comprobantePrintArea">
                <div class="comprobante-marca">
                    <h4>SOFIT GYM</h4>
                    <p class="text-muted small mb-0">Rendimiento y Salud al Máximo</p>
                    <p class="text-muted small mb-0">Cerrito Blanco, Barquisimeto - Venezuela</p>
                </div>
                <div class="row small mb-2 text-muted">
                    <div class="col-6">
                        <strong>Fecha:</strong> <span id="compFecha">---</span>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Método de Pago:</strong> <span id="compMetodo">---</span>
                    </div>
                </div>
                <div class="bg-light p-2 rounded mb-3 small text-muted">
                    <strong>Cliente:</strong> <span id="compCliente">Consumidor Final</span>
                </div>

                <table class="comprobante-tabla">
                    <thead>
                        <tr>
                            <th>Cant.</th>
                            <th>Descripción</th>
                            <th class="text-end">P. Unit.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody id="compItems">
                        <!-- Generado dinámicamente -->
                    </tbody>
                </table>

                <div class="text-end mb-3">
                    <p class="mb-1 text-muted small"><strong>Subtotal:</strong> <span id="compSubtotal">$0.00</span></p>
                    <h4 class="text-danger font-weight-bold mb-0">TOTAL: <span id="compTotal">$0.00</span></h4>
                </div>

                <div class="text-center text-muted small mt-4 pt-3 border-top" style="border-top-style: dotted !important;">
                    <p class="mb-0 font-weight-bold">¡Gracias por su compra!</p>
                    <p class="mb-0">Para cualquier cambio o reclamo conserve este ticket.</p>
                </div>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onclick="window.print()"><i class="fas fa-print"></i> Imprimir</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR VENTA (HISTORIAL) -->
<div class="modal fade" id="editarVentaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger-custom text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_venta_id">
                <div class="mb-3">
                    <label class="form-label">Cliente Asociado</label>
                    <select id="edit_venta_cliente" class="form-select select-custom">
                        <option value="">-- Consumidor Final (Sin registrar) --</option>
                        <?php foreach ($clientes as $c): ?>
                            <!-- Seguridad adicional con coalescencia (??) -->
                            <option value="<?= htmlspecialchars($c['cedula_cliente'] ?? $c['cedula'] ?? '') ?>">
                                <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Método de Pago</label>
                        <select id="edit_venta_metodo" class="form-select select-custom">
                            <option value="1">Efectivo</option>
                            <option value="2">Transferencia Bancaria</option>
                            <option value="3">Pago Móvil</option>
                            <option value="4">Tarjeta de Crédito</option>
                            <option value="6">Tarjeta de Débito</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Monto Total Modificado ($)</label>
                        <input type="number" step="0.01" id="edit_venta_monto" class="form-control" required>
                        <small class="text-muted text-xs">Opcional para ajustes manuales.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="guardarCambiosVenta">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ELIMINAR VENTA (HISTORIAL) -->
<div class="modal fade" id="eliminarVentaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger-custom text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Eliminar Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p>¿Estás seguro de que deseas anular y eliminar esta venta?</p>
                <h6 class="text-danger font-weight-bold">Venta #<span id="eliminar_venta_texto_id"></span></h6>
                <p class="text-muted small mb-0">Esta acción no restaurará el inventario automáticamente.</p>
                <input type="hidden" id="eliminar_venta_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarVenta">Confirmar Eliminación</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: PRODUCTOS SIN VENDER -->
<div class="modal fade" id="productosSinVenderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-box-open"></i> Productos Nunca Vendidos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="sticky-top bg-white shadow-sm">
                            <tr>
                                <th class="ps-4">Código</th>
                                <th>Nombre del Producto</th>
                                <th class="text-center pe-4">Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody id="tablaProductosSinVenderBody">
                            <!-- El contenido será inyectado por JavaScript -->
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-3 d-block"></i> Cargando información...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>