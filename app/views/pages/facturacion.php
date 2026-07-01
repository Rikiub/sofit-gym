<?php
// Page JS
$this->pushJs("pages/facturacion/facturacion.js");

// Layout principal
$this->layout("layout", ["title" => "Facturacion"]);
?>

<div class="container">
    <div class="header">
        <h1><i class="fas fa-dumbbell"></i> Facturación y Control de pagos</h1>
    </div>

    <div id="alertContainer" style="margin: 0 2rem;">
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?= $tipoMensaje == 'success' ? 'check-circle' : ($tipoMensaje == 'danger' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <div class="tabs">
        <button class="tab-btn <?= $activeTab == 'tab-pagos' ? 'active' : '' ?>" data-tab="tab-pagos"><i class="fas fa-plus-circle"></i> Registrar Pago</button>
        <button class="tab-btn <?= $activeTab == 'tab-lista' ? 'active' : '' ?>" data-tab="tab-lista"><i class="fas fa-list"></i> Lista Pagos</button>
    </div>

    <!-- TAB Registrar Pago -->
    <div id="tab-pagos" class="tab-content <?= $activeTab == 'tab-pagos' ? 'active' : '' ?>">
        <div class="card-header"><i class="fas fa-hand-holding-usd"></i> Nuevo Pago de Membresía</div>

        <div class="card-body">
            <form method="POST" action="?page=facturacion&action=registrar" id="formRegistroPago">
                <input type="hidden" name="action" value="registrar_pago">
                <input type="hidden" name="cedula" id="selected_cliente_id">
                <input type="hidden" name="metodo_pago" id="selected_metodo" value="Efectivo">
                <input type="hidden" name="plan_tipo" id="selected_plan" value="">

                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Cliente</label>
                        <div class="modal-select-btn" data-bs-toggle="modal" data-bs-target="#clienteModal">
                            <span id="cliente_selected_text">Seleccione cliente</span>
                            <span><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> Cédula</label>
                        <input type="text" id="cliente_cedula" class="form-control" readonly placeholder="Seleccionar cliente primero" style="background:#f1f5f9;">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-dollar-sign"></i> Monto (USD)</label>
                        <input class="form-control" type="number" step="0.01" id="monto_input" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-credit-card"></i> Método de pago</label>
                        <div class="modal-select-btn" data-bs-toggle="modal" data-bs-target="#metodoModal">
                            <span id="metodo_selected_text">Efectivo</span>
                            <span><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-link"></i> URL comprobante (opcional)</label>
                        <input class="form-control" type="url" name="comprobante_url" placeholder="https://ejemplo.com/comprobante.jpg">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Fecha de pago</label>
                        <input type="text" value="<?= date('d/m/Y') ?>" readonly disabled style="background:#f1f5f9;">
                        <small class="text-muted">Se registrará automáticamente</small>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-tags"></i> Tipo de plan (solo primer pago)</label>
                        <div class="modal-select-btn" data-bs-toggle="modal" data-bs-target="#planModal">
                            <span id="plan_selected_text">Cliente ya tiene membresía</span>
                            <span><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </div>
                    <div class="form-group" style="justify-content: flex-end;">
                        <button type="submit"><i class="fas fa-save"></i> Registrar Pago</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB Lista Pagos -->
    <div id="tab-lista" class="tab-content <?= $activeTab == 'tab-lista' ? 'active' : '' ?>">
        <div class="card">
            <div class="card-header"><i class="fas fa-credit-card"></i> Historial de Pagos</div>
            <div class="card-body">
                <div class="buscador-pagos">
                    <input type="text" id="searchPagos" class="form-control" placeholder="🔍 Buscar por ID, cédula o nombre del cliente...">
                    <button id="btnBuscar" class="btn btn-secondary">Buscar</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="tablaPagos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cédula</th>
                                <th>Cliente</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Estado Pago</th>
                                <th>Estado Cliente</th>
                                <th>Días restantes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaPagosBody">
                            <?php if (isset($pagos) && is_array($pagos) && count($pagos) > 0): ?>
                                <?php foreach ($pagos as $p): ?>
                                    <tr data-id="<?= $p['id_pago'] ?>">
                                        <td><?= $p['id_pago'] ?></td>
                                        <td><?= htmlspecialchars($p['cedula_cliente']) ?></td>
                                        <td><?= htmlspecialchars(explode(' ', $p['nombre_cliente'])[0]) ?></td>
                                        <td>$<?= number_format($p['monto'], 2) ?></td>
                                        <td><?= $p['metodo_pago'] ?></td>
                                        <td>
                                            <?php if ($p['estado_pago'] == 'Pagado'): ?>
                                                <span class="badge-pagado">Pagado</span>
                                            <?php elseif ($p['estado_pago'] == 'Atrasado'): ?>
                                                <span class="badge-atrasado">Atrasado</span>
                                            <?php else: ?>
                                                <span class="badge-pendiente">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $estadoClase = match ($p['estado_cliente']) {
                                                'Activo' => 'estado-activo',
                                                'Próximo a vencer' => 'estado-proximo',
                                                'Moroso' => 'estado-vencido',
                                                default => 'estado-vencido',
                                            };
                                            ?>
                                            <span class="<?= $estadoClase ?>"><?= $p['estado_cliente'] ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $dias = (int)$p['dias_restantes'];
                                            if ($dias < 0) echo "Vencido hace " . abs($dias) . " días";
                                            elseif ($dias == 0) echo '<span class="aviso-vencimiento">⚠️ ¡Vence hoy!</span>';
                                            elseif ($dias <= 5) echo '<span class="aviso-vencimiento">⚠️ ¡Te quedan ' . $dias . ' días!</span>';
                                            else echo "Faltan {$dias} días";
                                            ?>
                                        </td>
                                        <td>
                                            <div class="acciones-botones">
                                                <button class="btn btn-sm btn-warning editar-btn" data-bs-toggle="modal" data-bs-target="#editarModal"
                                                    data-id="<?= $p['id_pago'] ?>"
                                                    data-cliente="<?= htmlspecialchars($p['cedula_cliente']) ?>"
                                                    data-nombre="<?= htmlspecialchars($p['nombre_cliente']) ?>"
                                                    data-monto="<?= $p['monto'] ?>"
                                                    data-metodo="<?= $p['metodo_pago'] ?>"
                                                    data-estado="<?= $p['estado_pago'] ?>"
                                                    data-fecha_pago="<?= $p['fecha_pago'] ?>"
                                                    data-fecha_vencimiento="<?= $p['fecha_vencimiento'] ?>">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button class="btn btn-sm btn-danger eliminar-btn" data-bs-toggle="modal" data-bs-target="#eliminarModal"
                                                    data-id="<?= $p['id_pago'] ?>">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                                <button class="btn btn-sm btn-info ver-btn" data-bs-toggle="modal" data-bs-target="#verModal"
                                                    data-id="<?= $p['id_pago'] ?>"
                                                    data-cliente="<?= htmlspecialchars($p['cedula_cliente']) ?>"
                                                    data-nombre="<?= htmlspecialchars(explode(' ', $p['nombre_cliente'])[0]) ?>"
                                                    data-monto="<?= $p['monto'] ?>"
                                                    data-metodo="<?= $p['metodo_pago'] ?>"
                                                    data-estado="<?= $p['estado_pago'] ?>"
                                                    data-fecha_pago="<?= $p['fecha_pago'] ?>"
                                                    data-fecha_vencimiento="<?= $p['fecha_vencimiento'] ?>"
                                                    data-comprobante="<?= htmlspecialchars($p['comprobante_url'] ?? 'No disponible') ?>">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No hay pagos registrados.<?php echo "<!-- debug: pagos vacío -->"; ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- MODALES (igual que antes) -->
<div class="modal fade" id="clienteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search"></i> Buscar Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchClient" class="search-client form-control" placeholder="Seleccionar Cliente (buscar por cédula, nombre, correo o teléfono)">
                <div class="table-responsive">
                    <table class="table table-hover" id="clientesTabla">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Acción</th>
                            <tr>
                        </thead>
                        <tbody>
                            <?php if (isset($clientes) && is_array($clientes) && count($clientes) > 0): ?>
                                <?php foreach ($clientes as $c): ?>
                                    <tr data-id="<?= $c['cedula_cliente'] ?>" data-nombre="<?= htmlspecialchars($c['nombre']) ?>">
                                        <td><?= htmlspecialchars($c['cedula_cliente']) ?></td>
                                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                                        <td><?= htmlspecialchars($c['correo'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($c['telefono'] ?? '—') ?></td>
                                        <td><button type="button" class="btn btn-select-client"><i class="fas fa-check-circle"></i> Seleccionar</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                    </table>
                    <td colspan="5" class="text-center text-danger">⚠️ No hay clientes registrados.<?php echo "<!-- debug: clientes vacío -->"; ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
                </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="metodoModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Método de pago</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="method-item p-2 border-bottom" data-metodo="Efectivo">💵 Efectivo</div>
                <div class="method-item p-2 border-bottom" data-metodo="Tarjeta crédito">💳 Tarjeta crédito</div>
                <div class="method-item p-2 border-bottom" data-metodo="Transferencia">🏦 Transferencia</div>
                <div class="method-item p-2 border-bottom" data-metodo="Pago móvil">📱 Pago móvil</div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="planModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tipo de plan (solo primer pago)</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="plan-item p-2 border-bottom" data-plan="" data-text="Cliente ya tiene membresía">🔁 Cliente ya tiene membresía</div>
                <div class="plan-item p-2 border-bottom" data-plan="1" data-text="Mensual (30 días)">📆 Mensual (30 días)</div>
                <div class="plan-item p-2 border-bottom" data-plan="2" data-text="Trimestral (90 días)">📅 Trimestral (90 días)</div>
                <div class="plan-item p-2 border-bottom" data-plan="3" data-text="Anual (365 días)">🗓️ Anual (365 días)</div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="editarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Pago</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?page=facturacion&action=editar">
                <div class="modal-body">
                    <input type="hidden" name="action" value="editar_pago">
                    <input type="hidden" name="id_pago" id="edit_id">
                    <div class="mb-3"><label>Cédula</label><input type="text" class="form-control" id="edit_cliente" readonly></div>
                    <div class="mb-3"><label>Cliente</label><input type="text" class="form-control" id="edit_nombre" readonly></div>
                    <div class="mb-3"><label>Monto (USD)</label><input type="number" step="0.01" class="form-control" name="monto" id="edit_monto" required></div>
                    <div class="mb-3"><label>Método</label><select name="metodo_pago" id="edit_metodo" class="form-select">
                            <option>Efectivo</option>
                            <option>Tarjeta crédito</option>
                            <option>Transferencia</option>
                            <option>Pago móvil</option>
                        </select></div>
                    <div class="mb-3"><label>Estado</label><select name="estado" id="edit_estado" class="form-select">
                            <option>Pagado</option>
                            <option>Pendiente</option>
                            <option>Atrasado</option>
                        </select></div>
                    <div class="mb-3"><label>Fecha pago</label><input type="date" class="form-control" name="fecha_pago" id="edit_fecha_pago" required></div>
                    <div class="mb-3"><label>Fecha vencimiento</label><input type="date" class="form-control" name="fecha_vencimiento" id="edit_fecha_vencimiento" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Guardar cambios</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Confirmar eliminación</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="fw-bold">¿Está seguro de eliminar este pago permanentemente?</p><input type="hidden" id="delete_id">
            </div>
            <div class="modal-footer justify-content-center"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><a href="#" id="confirmDeleteBtn" class="btn btn-danger">Eliminar</a></div>
        </div>
    </div>
</div>

<div class="modal fade" id="verModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detalles del Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-2"><strong>ID Pago:</strong> <span id="ver_id"></span></div>
                    <div class="col-md-6 mb-2"><strong>Cédula:</strong> <span id="ver_cedula"></span></div>
                    <div class="col-md-6 mb-2"><strong>Cliente:</strong> <span id="ver_nombre"></span></div>
                    <div class="col-md-6 mb-2"><strong>Monto (USD):</strong> <span id="ver_monto"></span></div>
                    <div class="col-md-6 mb-2"><strong>Método de pago:</strong> <span id="ver_metodo"></span></div>
                    <div class="col-md-6 mb-2"><strong>Estado pago:</strong> <span id="ver_estado"></span></div>
                    <div class="col-md-6 mb-2"><strong>Fecha pago:</strong> <span id="ver_fecha_pago"></span></div>
                    <div class="col-md-6 mb-2"><strong>Fecha vencimiento:</strong> <span id="ver_fecha_vencimiento"></span></div>
                    <div class="col-12 mb-2"><strong>URL comprobante:</strong> <span id="ver_comprobante"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .container {
        --bs-gutter-x: 0;
        max-width: 1400px;
        margin: 0 auto;
        background: white;
        border-radius: 28px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .header {
        background: #C62828;
        color: white;
        padding: 1.2rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin: 0;
    }

    .header .badge {
        background: #8B0000;
        padding: 0.3rem 1rem;
        border-radius: 40px;
        font-size: 0.8rem;
    }

    .tabs {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 0 1.5rem;
        gap: 0.2rem;
    }

    .tab-btn {
        background: transparent;
        border: none;
        padding: 0.9rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        color: #5a5a5a;
        border-radius: 12px 12px 0 0;
    }

    .tab-btn.active {
        background: white;
        color: #C62828;
        border-bottom: 3px solid #C62828;
        margin-bottom: -1px;
    }

    .tab-content {
        display: none;
        padding-bottom: 2rem;
        animation: fade 0.25s ease;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fade {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .card {
        background: white;
        border-radius: 20px;
        border: unset;
    }

    .card-header {
        background: #fafbfc;
        padding: 1rem 1.5rem;
        font-weight: 700;
        font-size: 1.1rem;
        border-bottom: 1px solid #edf2f7;
        color: #1e2a3a;
    }

    .card-body {
        padding: 1.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.2rem;
    }

    .form-group {
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

    .modal-select-btn {
        background: white;
        border: 1px solid #cfdfe8;
        padding: 0.7rem 1rem;
        border-radius: 14px;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: 0.2s;
    }

    .modal-select-btn:hover {
        border-color: #C62828;
        background: #fef2f2;
    }

    input,
    select,
    button {
        padding: 0.7rem 1rem;
        border-radius: 14px;
        border: 1px solid #cfdfe8;
        font-size: 0.9rem;
        transition: 0.2s;
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: #C62828;
        box-shadow: 0 0 0 3px rgba(198, 40, 40, 0.1);
    }

    button {
        background: #C62828;
        color: white;
        font-weight: 600;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background: #b71c1c;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #6c757d;
    }

    .btn-sm {
        padding: 0.1rem 0.3rem !important;
        font-size: 0.6rem !important;
        border-radius: 10px !important;
    }

    .acciones-botones {
        display: flex;
        gap: 0.15rem;
        flex-wrap: nowrap;
    }

    .table-responsive {
        overflow-x: auto;
        width: 100%;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.7rem;
        min-width: 850px;
    }

    th,
    td {
        padding: 0.2rem 0.3rem;
        text-align: left;
        border-bottom: 1px solid #eef2f6;
        vertical-align: middle;
        white-space: nowrap;
    }

    th {
        background: #f8fafc;
        font-weight: 600;
        color: #1e2a3a;
    }

    .aviso-vencimiento {
        background-color: #ffeb3b;
        color: #c62828;
        font-weight: bold;
        padding: 0.15rem 0.25rem;
        border-radius: 20px;
        display: inline-block;
        font-size: 0.65rem;
    }

    .alert {
        padding: 0.9rem 1.2rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        transition: opacity 0.5s ease;
    }

    .alert-success {
        background: #e6f4ea;
        border-left: 5px solid #2e7d32;
        color: #1e4620;
    }

    .alert-danger {
        background: #fce8e6;
        border-left: 5px solid #c62828;
        color: #8b1e1e;
    }

    .alert-warning {
        background: #fff4e5;
        border-left: 5px solid #f57c00;
        color: #a85900;
    }

    .badge-pagado,
    .badge-atrasado,
    .badge-pendiente,
    .estado-activo,
    .estado-proximo,
    .estado-vencido {
        padding: 0.1rem 0.3rem;
        border-radius: 40px;
        font-size: 0.6rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-pagado {
        background-color: #2ecc71;
        color: white;
    }

    .badge-atrasado {
        background-color: #e74c3c;
        color: white;
    }

    .badge-pendiente {
        background-color: #f39c12;
        color: white;
    }

    .estado-activo {
        background-color: #2ecc71;
        color: white;
    }

    .estado-proximo {
        background-color: #f39c12;
        color: white;
    }

    .estado-vencido {
        background-color: #e74c3c;
        color: white;
    }

    footer {
        text-align: center;
        padding: 1.2rem;
        background: #fafbfc;
        color: #7f8c8d;
        font-size: 0.75rem;
        border-top: 1px solid #edf2f7;
    }

    .modal-header {
        background: #C62828;
        color: white;
        border-bottom: none;
    }

    .btn-close-white {
        filter: brightness(0) invert(1);
    }

    .search-client {
        margin-bottom: 1rem;
        padding: 0.5rem;
        border-radius: 12px;
        border: 1px solid #cfdfe8;
        width: 100%;
    }

    .btn-select-client {
        background: #6c757d !important;
        color: white !important;
        border: none !important;
        padding: 0.3rem 0.8rem !important;
        border-radius: 30px !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.2rem !important;
        transition: 0.2s !important;
    }

    .btn-select-client:hover {
        background: #5a6268 !important;
        transform: scale(1.02) !important;
    }

    .buscador-pagos {
        margin-bottom: 1rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .buscador-pagos input {
        flex: 1;
        padding: 0.5rem;
        border-radius: 20px;
        border: 1px solid #cfdfe8;
    }

    .fade-out {
        opacity: 0;
    }

    @media (max-width: 768px) {
        body {
            padding: 1rem;
        }

        .tab-btn {
            padding: 0.6rem 1rem;
        }

        td,
        th {
            white-space: normal;
        }

        .acciones-botones {
            flex-wrap: wrap;
        }
    }
</style>