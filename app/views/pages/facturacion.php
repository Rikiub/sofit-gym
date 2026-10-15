<?php
$this->pushJs("pages/facturacion/facturacion.js");
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

    <div class="card">
        <div class="card-header"><i class="fas fa-hand-holding-usd"></i> Nuevo Pago de Membresía</div>
        <div class="card-body">
            <form method="POST" action="?page=facturacion&action=registrar" id="formRegistroPago">
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
                        <input class="form-control" type="text" id="monto_input" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-credit-card"></i> Método de pago</label>
                        <div class="modal-select-btn" data-bs-toggle="modal" data-bs-target="#metodoModal">
                            <span id="metodo_selected_text">Efectivo</span>
                            <span><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Fecha de pago</label>
                        <input type="text" value="<?= date('d/m/Y') ?>" readonly disabled style="background:#f1f5f9;">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-tags"></i> Tipo de plan</label>
                        <div class="modal-select-btn" data-bs-toggle="modal" data-bs-target="#planModal">
                            <span id="plan_selected_text">Mantener membresía actual</span>
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

    <div class="card" style="margin-top: 2rem;">
        <div class="card-header"><i class="fas fa-credit-card"></i> Historial de Pagos</div>
        <div class="card-body">
            <div class="buscador-pagos">
                <input type="text" id="searchPagos" class="form-control" placeholder="🔍 Buscar por ID, cédula o nombre...">
                <button id="btnBuscar" class="btn btn-secondary" type="button">Buscar</button>
                <button type="button"
                        id="btnEstadoClientes"
                        class="btn-estado-clientes"
                        onclick="abrirModalEstadoClientes(); return false;"
                        title="Ver estado de clientes">
                    <i class="fas fa-users-cog"></i> Estado de Clientes
                </button>
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
                            <th>Estado</th>
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
                                    <td>$<?= number_format((float) $p['monto'], 2) ?></td>
                                    <td><?= htmlspecialchars($p['metodo_pago'] ?? '—') ?></td>
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
                                        $estadoClase = match ($p['estado_cliente'] ?? '') {
                                            'Activo'           => 'estado-activo',
                                            'Próximo a vencer' => 'estado-proximo',
                                            'Moroso'           => 'estado-vencido',
                                            default            => 'estado-vencido',
                                        };
                                        ?>
                                        <span class="<?= $estadoClase ?>"><?= htmlspecialchars($p['estado_cliente'] ?? '—') ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $dias = (int) ($p['dias_restantes'] ?? 0);
                                        if ($dias < 0) {
                                            echo "Vencido hace " . abs($dias) . " días";
                                        } elseif ($dias == 0) {
                                            echo '<span class="aviso-vencimiento">⚠️ ¡Vence hoy!</span>';
                                        } elseif ($dias <= 5) {
                                            echo '<span class="aviso-vencimiento">⚠️ ¡Te quedan ' . $dias . ' días!</span>';
                                        } else {
                                            echo "Faltan {$dias} días";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="acciones-botones">
                                            <button class="btn btn-sm btn-warning editar-btn" data-bs-toggle="modal" data-bs-target="#editarModal"
                                                data-id="<?= $p['id_pago'] ?>"
                                                data-cliente="<?= htmlspecialchars($p['cedula_cliente']) ?>"
                                                data-nombre="<?= htmlspecialchars($p['nombre_cliente'] ?? '') ?>"
                                                data-monto="<?= $p['monto'] ?>"
                                                data-metodo="<?= htmlspecialchars($p['metodo_pago'] ?? '') ?>"
                                                data-estado="<?= htmlspecialchars($p['estado_pago'] ?? '') ?>"
                                                data-fecha_pago="<?= $p['fecha_pago'] ?>"
                                                data-fecha_vencimiento="<?= $p['fecha_vencimiento'] ?? '' ?>">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-danger eliminar-btn" data-bs-toggle="modal" data-bs-target="#eliminarModal"
                                                data-id="<?= $p['id_pago'] ?>">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </button>
                                            <button class="btn btn-sm btn-info ver-btn" data-bs-toggle="modal" data-bs-target="#verModal"
                                                data-id="<?= $p['id_pago'] ?>"
                                                data-cliente="<?= htmlspecialchars($p['cedula_cliente']) ?>"
                                                data-nombre="<?= htmlspecialchars(explode(' ', $p['nombre_cliente'] ?? '')[0]) ?>"
                                                data-monto="<?= $p['monto'] ?>"
                                                data-metodo="<?= htmlspecialchars($p['metodo_pago'] ?? '') ?>"
                                                data-estado="<?= htmlspecialchars($p['estado_pago'] ?? '') ?>"
                                                data-fecha_pago="<?= $p['fecha_pago'] ?>"
                                                data-fecha_vencimiento="<?= $p['fecha_vencimiento'] ?? '' ?>">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center">No hay pagos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODALES ========== -->

<!-- Modal Buscar Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search"></i> Buscar Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchClient" class="search-client form-control" placeholder="Buscar por cédula, nombre, correo o teléfono">
                <div class="table-responsive">
                    <table class="table table-hover" id="clientesTabla">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($clientes) && is_array($clientes) && count($clientes) > 0): ?>
                                <?php foreach ($clientes as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['cedula_cliente']) ?></td>
                                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                                        <td><?= htmlspecialchars($c['correo'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($c['telefono'] ?? '—') ?></td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-select-client"
                                                    onclick="
                                                        var cedula = '<?= htmlspecialchars($c['cedula_cliente'], ENT_QUOTES) ?>';
                                                        var nombre = '<?= htmlspecialchars($c['nombre'], ENT_QUOTES) ?>';
                                                        document.getElementById('selected_cliente_id').value = cedula;
                                                        document.getElementById('cliente_cedula').value = cedula;
                                                        document.getElementById('cliente_selected_text').textContent = nombre;
                                                        var m = document.getElementById('clienteModal');
                                                        if (m && typeof bootstrap !== 'undefined') {
                                                            var inst = bootstrap.Modal.getInstance(m) || new bootstrap.Modal(m);
                                                            inst.hide();
                                                        }
                                                        var s = document.getElementById('searchClient');
                                                        if (s) s.value = '';
                                                        return false;
                                                    ">
                                                <i class="fas fa-check-circle"></i> Seleccionar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-danger">⚠️ No hay clientes registrados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>

<!-- Modal Método de pago -->
<div class="modal fade" id="metodoModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Método de pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

<!-- Modal Plan -->
<div class="modal fade" id="planModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-tags"></i> Seleccione el tipo de membresía</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="plan-item p-2 border-bottom"
                     data-plan=""
                     data-text="Mantener membresía actual"
                     data-precio="0">
                    🔁 <strong>Mantener membresía actual</strong>
                </div>
                <?php if (isset($tiposMembresia) && is_array($tiposMembresia)): ?>
                    <?php foreach ($tiposMembresia as $tm): ?>
                        <div class="plan-item p-2 border-bottom"
                             data-plan="<?= (int) $tm['id_tipo'] ?>"
                             data-text="<?= htmlspecialchars($tm['nombre']) ?> (<?= (int) $tm['duracion_dias'] ?> días)"
                             data-precio="<?= (float) $tm['monto'] ?>">
                            📆 <strong><?= htmlspecialchars($tm['nombre']) ?></strong> (<?= (int) $tm['duracion_dias'] ?> días)
                            — <span style="color:#C62828;font-weight:700;">$<?= number_format((float) $tm['monto'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>

<!-- Modal Editar Pago -->
<div class="modal fade" id="editarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?page=facturacion&action=editar">
                <div class="modal-body">
                    <input type="hidden" name="id_pago" id="edit_id">
                    <div class="mb-3"><label>Cédula</label><input type="text" class="form-control" id="edit_cliente" readonly></div>
                    <div class="mb-3"><label>Cliente</label><input type="text" class="form-control" id="edit_nombre" readonly></div>
                    <div class="mb-3"><label>Monto (USD)</label><input type="number" step="0.01" class="form-control" name="monto" id="edit_monto" required></div>
                    <div class="mb-3"><label>Método</label><select name="metodo_pago" id="edit_metodo" class="form-select">
                            <option>Efectivo</option><option>Tarjeta crédito</option><option>Transferencia</option><option>Pago móvil</option>
                        </select></div>
                    <div class="mb-3"><label>Estado</label><select name="estado" id="edit_estado" class="form-select">
                            <option>Pagado</option><option>Pendiente</option><option>Atrasado</option>
                        </select></div>
                    <div class="mb-3"><label>Fecha pago</label><input type="date" class="form-control" name="fecha_pago" id="edit_fecha_pago" required></div>
                    <div class="mb-3"><label>Fecha vencimiento</label><input type="date" class="form-control" name="fecha_vencimiento" id="edit_fecha_vencimiento" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Guardar cambios</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="fw-bold">¿Está seguro de eliminar este pago permanentemente?</p>
                <input type="hidden" id="delete_id">
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles -->
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
                    <div class="col-md-6 mb-2"><strong>Método:</strong> <span id="ver_metodo"></span></div>
                    <div class="col-md-6 mb-2"><strong>Estado:</strong> <span id="ver_estado"></span></div>
                    <div class="col-md-6 mb-2"><strong>Fecha pago:</strong> <span id="ver_fecha_pago"></span></div>
                    <div class="col-md-6 mb-2"><strong>Fecha venc.:</strong> <span id="ver_fecha_vencimiento"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Pago Parcial (anidado) -->
<div class="modal fade modal-anidado" id="confirmarParcialModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar pago parcial</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p class="fw-bold mb-3">El monto es MENOR al precio del plan</p>
                <div class="bg-light p-3 rounded text-start" style="margin: 0 auto; max-width: 320px;">
                    <div><strong>Plan:</strong> <span id="parcial_plan"></span></div>
                    <div><strong>Precio del plan:</strong> $<span id="parcial_precio"></span></div>
                    <div><strong>Monto ingresado:</strong> $<span id="parcial_monto"></span></div>
                    <div class="text-danger fw-bold mt-2" style="border-top:1px solid #ddd; padding-top:0.5rem;">
                        <strong>Deuda pendiente:</strong> $<span id="parcial_deuda"></span>
                    </div>
                </div>
                <p class="mt-3 mb-0">El cliente será marcado como <strong>MOROSO</strong>.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="confirmarPagoParcial">
                    <i class="fas fa-check"></i> Continuar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Marcar Moroso (anidado) -->
<div class="modal fade modal-anidado" id="marcarMorosoModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#6a1b9a;">
                <h5 class="modal-title"><i class="fas fa-user-slash"></i> Marcar como moroso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-user-slash fa-3x mb-3" style="color:#6a1b9a;"></i>
                <p class="fw-bold">¿Marcar a este cliente como MOROSO?</p>
                <div class="bg-light p-3 rounded text-start" style="margin: 0 auto; max-width: 350px;">
                    <div><strong>Cédula:</strong> <span id="moroso_cedula"></span></div>
                    <div><strong>Cliente:</strong> <span id="moroso_nombre"></span></div>
                </div>
                <p class="mt-3 mb-0 text-muted" style="font-size:0.85rem;">El cliente aparecerá en la pestaña "Morosos".</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn" id="confirmarMarcarMoroso" style="background:#6a1b9a;color:white;">
                    <i class="fas fa-user-slash"></i> Marcar moroso
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reactivar Cliente (anidado) -->
<div class="modal fade modal-anidado" id="reactivarModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title"><i class="fas fa-user-check"></i> Reactivar cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-user-check fa-3x text-success mb-3"></i>
                <p class="fw-bold">¿Reactivar a este cliente?</p>
                <div class="bg-light p-3 rounded text-start" style="margin: 0 auto; max-width: 350px;">
                    <div><strong>Cédula:</strong> <span id="reactivar_cedula"></span></div>
                    <div><strong>Cliente:</strong> <span id="reactivar_nombre"></span></div>
                </div>
                <p class="mt-3 mb-0 text-muted" style="font-size:0.85rem;">Volverá al estado Activo.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmarReactivar">
                    <i class="fas fa-check"></i> Reactivar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Estado de Clientes -->
<div class="modal fade" id="estadoClientesModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-teal">
                <h5 class="modal-title"><i class="fas fa-users-cog"></i> Estado de Clientes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="estado-resumen">
                    <div class="estado-card" data-estado="Activo" onclick="filtrarEstadoClientes('Activo'); return false;">
                        <div class="estado-numero" id="cnt_activo">0</div>
                        <div class="estado-label">Activos</div>
                    </div>
                    <div class="estado-card" data-estado="Próximo a vencer" onclick="filtrarEstadoClientes('Próximo a vencer'); return false;">
                        <div class="estado-numero" id="cnt_proximo">0</div>
                        <div class="estado-label">Próximos a vencer</div>
                    </div>
                    <div class="estado-card" data-estado="Vencido" onclick="filtrarEstadoClientes('Vencido'); return false;">
                        <div class="estado-numero" id="cnt_vencido">0</div>
                        <div class="estado-label">Vencidos</div>
                    </div>
                    <div class="estado-card" data-estado="Moroso" onclick="filtrarEstadoClientes('Moroso'); return false;">
                        <div class="estado-numero" id="cnt_moroso">0</div>
                        <div class="estado-label">Morosos</div>
                    </div>
                    <div class="estado-card" data-estado="Sin membresía" onclick="filtrarEstadoClientes('Sin membresía'); return false;">
                        <div class="estado-numero" id="cnt_sin">0</div>
                        <div class="estado-label">Sin membresía</div>
                    </div>
                </div>

                <div class="estado-filtros">
                    <button type="button" class="filtro-pill active" data-filtro="todos"   onclick="filtrarEstadoClientes('todos'); return false;">Todos</button>
                    <button type="button" class="filtro-pill"        data-filtro="Activo"  onclick="filtrarEstadoClientes('Activo'); return false;">🟢 Activos</button>
                    <button type="button" class="filtro-pill"        data-filtro="Proximo" onclick="filtrarEstadoClientes('Próximo a vencer'); return false;">🟡 Próximos</button>
                    <button type="button" class="filtro-pill"        data-filtro="Vencido" onclick="filtrarEstadoClientes('Vencido'); return false;">🔴 Vencidos</button>
                    <button type="button" class="filtro-pill"        data-filtro="Moroso"  onclick="filtrarEstadoClientes('Moroso'); return false;">⚫ Morosos</button>
                    <button type="button" class="filtro-pill"        data-filtro="Sin"     onclick="filtrarEstadoClientes('Sin membresía'); return false;">⚪ Sin membresía</button>
                </div>

                <div class="estado-filtros-fecha">
                    <div class="filtro-group">
                        <label>Vence desde</label>
                        <input type="date" id="estadoDesde" class="form-control">
                    </div>
                    <div class="filtro-group">
                        <label>Vence hasta</label>
                        <input type="date" id="estadoHasta" class="form-control">
                    </div>
                    <button type="button" class="btn btn-info" onclick="aplicarFiltrosEstado(); return false;">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="refrescarEstadoClientes(); return false;">
                        <i class="fas fa-list"></i> Ver todo
                    </button>
                </div>

                <div class="buscador-pagos" style="margin-top: 0.5rem;">
                    <input type="text" id="searchEstadoClientes" class="form-control" placeholder="🔍 Buscar por cédula o nombre...">
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="tablaEstadoClientes">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha pago</th>
                                <th>Fecha venc.</th>
                                <th>Días</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaEstadoClientesBody">
                            <tr><td colspan="9" class="text-center">Presione "Estado de Clientes" para cargar.</td></tr>
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

<style>
    .monto-valid { border-color: #0d6efd !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important; }
    .monto-invalid { border-color: #dc3545 !important; box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important; }
    .container { --bs-gutter-x: 0; max-width: 1400px; margin: 0 auto; background: white; border-radius: 28px; box-shadow: 0 12px 30px rgba(0,0,0,0.08); overflow: hidden; padding: 1.5rem; }
    .header { background: #C62828; color: white; padding: 1.2rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin: -1.5rem -1.5rem 1.5rem -1.5rem; border-radius: 28px 28px 0 0; }
    .header h1 { font-size: 1.6rem; font-weight: 600; margin: 0; }
    .card { background: white; border-radius: 20px; border: 1px solid #edf2f7; margin-bottom: 1.5rem; overflow: hidden; }
    .card-header { background: #fafbfc; padding: 1rem 1.5rem; font-weight: 700; font-size: 1.1rem; border-bottom: 1px solid #edf2f7; color: #1e2a3a; }
    .card-body { padding: 1.5rem; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; }
    .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
    label { font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #5a6e7a; }
    .modal-select-btn { background: white; border: 1px solid #cfdfe8; padding: 0.7rem 1rem; border-radius: 14px; text-align: left; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: 0.2s; }
    .modal-select-btn:hover { border-color: #C62828; background: #fef2f2; }
    input, select, button { padding: 0.7rem 1rem; border-radius: 14px; border: 1px solid #cfdfe8; font-size: 0.9rem; transition: 0.2s; }
    input:focus, select:focus { outline: none; border-color: #C62828; box-shadow: 0 0 0 3px rgba(198,40,40,0.1); }
    button { background: #C62828; color: white; font-weight: 600; border: none; cursor: pointer; }
    button:hover { background: #b71c1c; transform: translateY(-1px); }
    .btn-secondary { background: #6c757d; }
    .btn-sm { padding: 0.1rem 0.3rem !important; font-size: 0.6rem !important; border-radius: 10px !important; }
    .acciones-botones { display: flex; gap: 0.15rem; flex-wrap: nowrap; }
    .table-responsive { overflow-x: auto; width: 100%; }
    table { width: 100%; border-collapse: collapse; font-size: 0.7rem; min-width: 850px; }
    th, td { padding: 0.2rem 0.3rem; text-align: left; border-bottom: 1px solid #eef2f6; vertical-align: middle; white-space: nowrap; }
    th { background: #f8fafc; font-weight: 600; color: #1e2a3a; }
    .aviso-vencimiento { background-color: #ffeb3b; color: #c62828; font-weight: bold; padding: 0.15rem 0.25rem; border-radius: 20px; display: inline-block; font-size: 0.65rem; }
    .alert { padding: 0.9rem 1.2rem; border-radius: 16px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem; transition: opacity 0.5s ease; }
    .alert-success { background: #e6f4ea; border-left: 5px solid #2e7d32; color: #1e4620; }
    .alert-danger { background: #fce8e6; border-left: 5px solid #c62828; color: #8b1e1e; }
    .alert-warning { background: #fff4e5; border-left: 5px solid #f57c00; color: #a85900; }
    .badge-pagado, .badge-atrasado, .badge-pendiente, .estado-activo, .estado-proximo, .estado-vencido { padding: 0.1rem 0.3rem; border-radius: 40px; font-size: 0.6rem; font-weight: 600; white-space: nowrap; }
    .badge-pagado { background-color: #2ecc71; color: white; }
    .badge-atrasado { background-color: #e74c3c; color: white; }
    .badge-pendiente { background-color: #f39c12; color: white; }
    .estado-activo { background-color: #2ecc71; color: white; }
    .estado-proximo { background-color: #f39c12; color: white; }
    .estado-vencido { background-color: #e74c3c; color: white; }
    .modal-header { background: #C62828; color: white; border-bottom: none; }
    .modal-header-teal { background: #17a2b8 !important; color: white !important; border-bottom: none !important; }
    .btn-close-white { filter: brightness(0) invert(1); }
    .search-client { margin-bottom: 1rem; padding: 0.5rem; border-radius: 12px; border: 1px solid #cfdfe8; width: 100%; }
    .btn-select-client { background: #6c757d !important; color: white !important; border: none !important; padding: 0.3rem 0.8rem !important; border-radius: 30px !important; font-size: 0.7rem !important; font-weight: 600 !important; cursor: pointer !important; display: inline-flex !important; align-items: center !important; gap: 0.2rem !important; transition: 0.2s !important; }
    .btn-select-client:hover { background: #5a6268 !important; transform: scale(1.02) !important; }
    .buscador-pagos { margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
    .buscador-pagos input { flex: 1; min-width: 200px; padding: 0.5rem; border-radius: 20px; border: 1px solid #cfdfe8; }
    .fade-out { opacity: 0; }
    .btn-estado-clientes { background: #17a2b8; color: white; border: none; font-weight: 600; padding: 0.7rem 1.1rem; border-radius: 14px; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.4rem; transition: 0.2s; }
    .btn-estado-clientes:hover { background: #138496; transform: translateY(-1px); }

    .estado-resumen {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0;
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
    }
    .estado-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.9rem 0.5rem;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        cursor: pointer;
        transition: background 0.15s;
        text-align: center;
        min-width: 0;
    }
    .estado-card:last-child { border-right: none; }
    .estado-card:hover { background: #f8fafc; }
    .estado-card.active { background: #e6f7fa; box-shadow: inset 0 -3px 0 #17a2b8; }
    .estado-numero { font-size: 1.6rem; font-weight: 700; color: #1e2a3a; line-height: 1; margin-bottom: 0.3rem; }
    .estado-card.active .estado-numero { color: #17a2b8; }
    .estado-label { font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }

    .estado-filtros { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 0.5rem; }
    .filtro-pill { padding: 0.35rem 0.9rem !important; border-radius: 30px; border: 1px solid #cfdfe8; background: white; color: #5a6e7a; font-size: 0.75rem !important; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .filtro-pill:hover { background: #f8fafc; transform: none; }
    .filtro-pill.active { background: #17a2b8; color: white; border-color: #17a2b8; }
    .estado-filtros-fecha { display: flex; gap: 0.6rem; align-items: flex-end; flex-wrap: wrap; padding: 0.8rem 1rem; background: #f8fafc; border-radius: 12px; margin-bottom: 0.5rem; }
    .estado-filtros-fecha .filtro-group { display: flex; flex-direction: column; gap: 0.2rem; min-width: 140px; flex: 1; }
    .estado-filtros-fecha label { font-size: 0.7rem; }
    .estado-filtros-fecha .form-control { padding: 0.5rem 0.7rem; border-radius: 10px; border: 1px solid #cfdfe8; font-size: 0.85rem; }
    .estado-filtros-fecha .btn-info { background: #17a2b8; color: white; padding: 0.55rem 1rem; border-radius: 10px; border: none; font-weight: 600; font-size: 0.85rem; cursor: pointer; white-space: nowrap; }
    .estado-filtros-fecha .btn-info:hover { background: #138496; }
    .estado-filtros-fecha .btn-secondary { color: white; padding: 0.55rem 1rem; border-radius: 10px; border: none; font-weight: 600; font-size: 0.85rem; cursor: pointer; white-space: nowrap; }

    #tablaEstadoClientes th:nth-child(4),
    #tablaEstadoClientes td:nth-child(4) { text-align: center; width: 140px; }
    #tablaEstadoClientes td:nth-child(4) > span {
        display: inline-block;
        width: 120px;
        padding: 0.35rem 0.2rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        box-sizing: border-box;
    }
    #tablaEstadoClientes th:nth-child(9),
    #tablaEstadoClientes td:nth-child(9) { width: 130px; text-align: center; }

    .btn-moroso { background: #6a1b9a; color: white; border: none; padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.68rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: 0.15s; }
    .btn-moroso:hover { background: #4a148c; }
    .btn-reactivar { background: #2ecc71; color: white; border: none; padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.68rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: 0.15s; }
    .btn-reactivar:hover { background: #27ae60; }

    .modal-header.bg-warning { background: #f39c12 !important; color: white !important; }
    .modal-header.bg-success { background: #2ecc71 !important; color: white !important; }

    /* ===== Modales anidados por encima del modal Estado de Clientes ===== */
    .modal-anidado { z-index: 1090 !important; }
    .modal-backdrop.modal-anidado-backdrop { z-index: 1085 !important; }

    /* ===== Toast flotante global (por encima de modales) ===== */
    #toastContainerGlobal {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        max-width: 420px;
        pointer-events: none;
    }
    #toastContainerGlobal .alert {
        pointer-events: auto;
        margin: 0;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.18);
        border-radius: 14px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        body { padding: 0.5rem; }
        .container { padding: 1rem; }
        .header { margin: -1rem -1rem 1rem -1rem; border-radius: 20px 20px 0 0; }
        td, th { white-space: normal; }
        .acciones-botones { flex-wrap: wrap; }
        .form-grid { grid-template-columns: 1fr; }
        .estado-resumen { grid-template-columns: repeat(2, 1fr); }
        .estado-card { border-bottom: 1px solid #e2e8f0; }
        .estado-card:nth-child(2n) { border-right: none; }
        .estado-label { font-size: 0.62rem; white-space: normal; }
    }
</style>