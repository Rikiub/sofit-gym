<?php
$this->pushJs("pages/productos/productos.js");
$this->layout("layout", ["title" => "Inventario de Productos"]);
?>

<!-- ==================== ESTILOS ADICIONALES ==================== -->
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

    td:last-child,
    th:last-child {
        width: 140px;
        white-space: nowrap;
    }

    .alerta-stock {
        background: #fff3cd;
        border-left: 5px solid #ffc107;
        color: #856404;
        border-radius: 16px;
        padding: 0.9rem 1.2rem;
        margin-bottom: 1.5rem;
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

    .buscador-contenedor button {
        width: auto;
        background: #6c757d;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    .buscador-contenedor button:hover {
        background: #5a6268;
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

    #toastMessage.success {
        background: #2ecc71;
    }

    #toastMessage.error {
        background: #e74c3c;
    }

    .bg-danger-custom {
        background-color: #C62828 !important;
    }

    .badge-stock {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-weight: 600;
    }

    .stock-ok {
        background-color: #e6f4ea;
        color: #1e7e34;
    }

    .stock-alerta {
        background-color: #fff3cd;
        color: #856404;
    }

    .stock-peligro {
        background-color: #fce8e6;
        color: #c62828;
    }

    /* Pestañas de Navegación */
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

    /* Estilos del Recibo (Comprobante) */
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
    <!-- Encabezado de la Sección -->
    <div class="header-banner">
        <h1><i class="fas fa-boxes"></i> Inventario de Productos y Ventas</h1>
    </div>

    <!-- Div para notificaciones instantáneas JS (Toast) -->
    <div id="toastMessage"></div>

    <div class="p-4">
        <!-- Mostrar alertas de sesión si existen -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show mb-4" role="alert" style="border-radius: 16px;">
                <i class="fas fa-<?= $tipoMensaje == 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Alertas dinámicas sobre Quiebre o Stock Mínimo -->
        <?php if (!empty($bajoStock)): ?>
            <div class="alerta-stock d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm">
                <div>
                    <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                    <strong>Alerta de Inventario:</strong> Hay <?= count($bajoStock) ?> producto(s) por debajo de su cantidad mínima establecida.
                </div>
                <button class="btn btn-warning btn-sm btn-sm-custom fw-bold" data-bs-toggle="collapse" data-bs-target="#collapseBajoStock">
                    <i class="fas fa-eye"></i> Ver Detalles
                </button>
            </div>

            <div class="collapse mb-4" id="collapseBajoStock">
                <div class="card card-custom border-warning">
                    <div class="card-header bg-warning bg-opacity-10 text-warning-emphasis font-weight-bold">
                        Productos Críticos
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Stock Actual</th>
                                    <th>Mínimo</th>
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bajoStock as $bs): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($bs['codigo_producto']) ?></strong></td>
                                        <td><?= htmlspecialchars($bs['nombre']) ?></td>
                                        <td><span class="badge bg-danger"><?= $bs['stock_actual'] ?></span></td>
                                        <td><span class="badge bg-secondary"><?= $bs['stock_minimo'] ?></span></td>
                                        <td><?= htmlspecialchars($bs['unidad_medida']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Sistema de Pestañas Navegables con Soporte Directo de Clics por Inline JS -->
        <div class="nav-tabs-custom">
            <div class="nav-link-custom active" id="tabInventarioBtn" onclick="toggleView('inventario')">
                <i class="fas fa-warehouse me-1"></i> Catálogo e Inventario
            </div>
            <div class="nav-link-custom" id="tabVentaBtn" onclick="toggleView('venta')">
                <i class="fas fa-shopping-cart me-1"></i> Registrar Venta
            </div>
        </div>

        <!-- ==================== SECCIÓN DE INVENTARIO ==================== -->
        <div id="seccionInventario">
            <!-- Formulario de Registro de Producto -->
            <div class="card-custom">
                <div class="card-header-custom"><i class="fas fa-plus-circle"></i> Registrar nuevo producto</div>
                <div class="card-body-custom">
                    <form id="formRegistrarProducto" class="form-grid-custom">
                        <div class="form-group-custom">
                            <label><i class="fas fa-barcode"></i> Código</label>
                            <input type="text" name="codigo_producto" id="prod_codigo" class="form-control input-custom" placeholder="PROT002" required>
                        </div>
                        <div class="form-group-custom">
                            <label><i class="fas fa-tag"></i> Nombre</label>
                            <input type="text" name="nombre" id="prod_nombre" class="form-control input-custom" placeholder="Creatina Monohidratada" required>
                        </div>
                        <div class="form-group-custom">
                            <label><i class="fas fa-folder"></i> Categoría</label>
                            <select name="id_categoria" id="prod_categoria" class="form-select select-custom">
                                <option value="1">Suplementos</option>
                                <option value="2">Bebidas</option>
                                <option value="3">Snacks</option>
                                <option value="4">Accesorios</option>
                                <option value="5">Otros</option>
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <label><i class="fas fa-dollar-sign"></i> Precio Venta</label>
                            <input type="number" step="0.01" name="precio_venta" id="prod_precio" class="form-control input-custom" placeholder="0.00" required>
                        </div>
                        <div class="form-group-custom">
                            <label><i class="fas fa-cubes"></i> Stock Inicial</label>
                            <input type="number" name="stock_actual" id="prod_stock_actual" class="form-control input-custom" value="0" min="0">
                        </div>
                        <div class="form-group-custom">
                            <label><i class="fas fa-shield-alt"></i> Stock Mínimo</label>
                            <input type="number" name="stock_minimo" id="prod_stock_minimo" class="form-control input-custom" value="5" min="0">
                        </div>
                        <div class="form-group-custom">
                            <label><i class="fas fa-balance-scale"></i> Unidad</label>
                            <select name="id_unidad" id="prod_unidad" class="form-select select-custom">
                                <option value="1">Unidad</option>
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <button type="submit" class="btn-custom btn-main"><i class="fas fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Catálogo de Inventario Completo -->
            <div class="card-custom">
                <div class="card-header-custom"><i class="fas fa-list"></i> Productos en Inventario</div>
                <div class="card-body-custom">
                    <div class="buscador-contenedor">
                        <input type="text" id="buscarProductoInput" class="form-control text-muted" placeholder="Buscar por código, nombre o categoría..." value="<?= htmlspecialchars($termino ?? '') ?>">
                        <button id="btnBuscarProducto" class="btn btn-secondary px-4"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="tablaProductos">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio Venta</th>
                                    <th>Stock</th>
                                    <th>Unidad</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductosBody">
                                <?php foreach ($productos as $p): ?>
                                    <?php
                                    $claseStock = 'stock-ok';
                                    if ($p['stock_actual'] <= 0) {
                                        $claseStock = 'stock-peligro';
                                    } elseif ($p['stock_actual'] <= $p['stock_minimo']) {
                                        $claseStock = 'stock-alerta';
                                    }
                                    ?>
                                    <tr data-codigo="<?= htmlspecialchars($p['codigo_producto']) ?>">
                                        <td><strong><?= htmlspecialchars($p['codigo_producto']) ?></strong></td>
                                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($p['nombre_categoria'] ?? 'Sin categoría') ?></span></td>
                                        <td>$<?= number_format($p['precio_venta'], 2) ?></td>
                                        <td>
                                            <span class="badge-stock <?= $claseStock ?>">
                                                <?= $p['stock_actual'] ?> <small class="text-muted">/ <?= $p['stock_minimo'] ?></small>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($p['nombre_unidad']) ?></td>
                                        <td>
                                            <div class="acciones-botones justify-content-end">
                                                <!-- Movimiento Rápido Stock -->
                                                <button class="btn btn-sm btn-outline-success ajustar-stock-btn btn-sm-custom"
                                                    data-codigo="<?= htmlspecialchars($p['codigo_producto']) ?>"
                                                    data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                                    data-stock="<?= $p['stock_actual'] ?>">
                                                    <i class="fas fa-plus-minus"></i> Stock
                                                </button>
                                                <!-- Editar -->
                                                <button class="btn btn-sm btn-warning editar-prod-btn btn-sm-custom"
                                                    data-codigo="<?= htmlspecialchars($p['codigo_producto']) ?>"
                                                    data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                                    data-categoria="<?= htmlspecialchars($p['id_categoria'] ?? '') ?>"
                                                    data-precio="<?= $p['precio_venta'] ?>"
                                                    data-minimo="<?= $p['stock_minimo'] ?>"
                                                    data-unidad="<?= htmlspecialchars($p['id_unidad']) ?>">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <!-- Eliminar -->
                                                <button class="btn btn-sm btn-danger eliminar-prod-btn btn-sm-custom"
                                                    data-codigo="<?= htmlspecialchars($p['codigo_producto']) ?>"
                                                    data-nombre="<?= htmlspecialchars($p['nombre']) ?>">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($productos)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-3 text-muted">No hay productos registrados en este momento.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== SECCIÓN DE NUEVA VENTA (MULTI-PRODUCTOS) ==================== -->
        <div id="seccionVenta" style="display: none;">
            <div class="row">
                <!-- Selector e Item actual para añadir al Carrito -->
                <div class="col-lg-5 col-md-12">
                    <div class="card-custom">
                        <div class="card-header-custom"><i class="fas fa-cart-plus"></i> Agregar Productos a la Venta</div>
                        <div class="card-body-custom">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-user-circle"></i> Asociar Cliente (Opcional)</label>
                                <select id="ventaCliente" class="form-select select-custom">
                                    <option value="">-- Consumidor Final (Sin registrar) --</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <option value="<?= htmlspecialchars($c['cedula_cliente']) ?>">
                                            <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido'] . ' (' . $c['cedula_cliente'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-search"></i> Buscar Producto para Venta</label>
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
                                    <label class="form-label">Stock Disponible</label>
                                    <input type="text" id="ventaStockDisponible" class="form-control bg-light" readonly placeholder="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-calculator"></i> Cantidad a Vender</label>
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
                                            <option value="6">Tarjeta de Débito</option>
                                            <option value="2">Tarjeta de Crédito</option>
                                            <option value="3">Pago Móvil</option>
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
    </div>
</div>

<!-- ==================== MODAL TRANSACCIONES / AJUSTE DE STOCK ==================== -->
<div class="modal fade" id="ajustarStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger-custom text-white">
                <h5 class="modal-title"><i class="fas fa-exchange-alt"></i> Ajustar Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <h6 id="ajuste_nombre_prod" class="text-primary font-weight-bold">---</h6>
                    <p class="text-muted mb-0">Stock actual: <strong id="ajuste_stock_actual">0</strong></p>
                </div>
                <input type="hidden" id="ajuste_codigo">

                <div class="mb-3">
                    <label class="form-label d-block text-center">Tipo de Movimiento</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="tipo_ajuste" id="tipo_entrada" value="entrada" checked>
                        <label class="btn btn-outline-success" for="tipo_entrada"><i class="fas fa-plus-circle"></i> Entrada</label>

                        <input type="radio" class="btn-check" name="tipo_ajuste" id="tipo_salida" value="salida">
                        <label class="btn btn-outline-danger" for="tipo_salida"><i class="fas fa-minus-circle"></i> Salida</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="ajuste_cantidad" class="form-control text-center fs-4" min="1" value="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="guardarAjusteStock">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL EDITAR PRODUCTO ==================== -->
<div class="modal fade" id="editarProductoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger-custom text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Código (No Modificable)</label>
                    <input type="text" id="edit_prod_codigo" class="form-control bg-light" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre del Producto</label>
                    <input type="text" id="edit_prod_nombre" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Categoría</label>
                        <select id="edit_prod_categoria" class="form-select select-custom">
                            <option value="1">Suplementos</option>
                            <option value="2">Bebidas</option>
                            <option value="3">Snacks</option>
                            <option value="4">Accesorios</option>
                            <option value="5">Otros</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Precio de Venta</label>
                        <input type="number" step="0.01" id="edit_prod_precio" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock Mínimo</label>
                        <input type="number" id="edit_prod_minimo" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unidad de Medida</label>
                        <select class="form-select" id="edit_prod_unidad" required>
                            <option value="1" selected>Unidad</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="guardarCambiosProducto">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL ELIMINAR PRODUCTO ==================== -->
<div class="modal fade" id="eliminarProductoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger-custom text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p>¿Estás seguro de que deseas eliminar este producto?</p>
                <h6 id="eliminar_nombre_prod" class="text-danger font-weight-bold">---</h6>
                <p class="text-muted small mb-0">Esta acción ocultará el producto del catálogo.</p>
                <input type="hidden" id="eliminar_codigo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarProducto">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL DE COMPROBANTE DE VENTA ==================== -->
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

<!-- ==================== SCRIPT DE CONMUTACIÓN RESISTENTE (DOUBLE-SAFETY) ==================== -->
<script>
    // Esta función actúa como fallback directo en caso de retrasos del script principal o de persistencia de caché
    function toggleView(view) {
        const tabInventario = document.getElementById('tabInventarioBtn');
        const tabVenta = document.getElementById('tabVentaBtn');
        const seccionInventario = document.getElementById('seccionInventario');
        const seccionVenta = document.getElementById('seccionVenta');

        if (!tabInventario || !tabVenta || !seccionInventario || !seccionVenta) return;

        if (view === 'inventario') {
            tabInventario.classList.add('active');
            tabVenta.classList.remove('active');
            seccionInventario.style.display = 'block';
            seccionVenta.style.display = 'none';
        } else if (view === 'venta') {
            tabVenta.classList.add('active');
            tabInventario.classList.remove('active');
            seccionInventario.style.display = 'none';
            seccionVenta.style.display = 'block';

            // Si existe la función de sincronizar de JS, la disparamos de forma segura
            if (typeof window.actualizarListaClientesGlobal === 'function') {
                window.actualizarListaClientesGlobal();
            }
        }
    }
</script>