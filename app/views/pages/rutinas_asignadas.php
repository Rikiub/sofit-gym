<?php
$this->pushJs("pages/rutinas/rutinas.js");
$this->layout("layout", ["title" => "Asignar Rutinas"]);
// Recibe $asignaciones, $rutinas, $mensaje, $tipoMensaje del controlador
?>

<div class="container">
    <div class="header">
        <h1><i class="fas fa-user-tag"></i> Asignación de Rutinas</h1>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert" style="margin: 1rem 2rem 0 2rem;">
            <i class="fas fa-<?= $tipoMensaje == 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div id="toastMessage"></div>

    <div class="card">
        <div class="card-header"><i class="fas fa-handshake"></i> Vincular Rutina a un Cliente</div>
        <div class="card-body">
            <form id="formAsignarRutina" class="form-grid">
                <!-- Selector del cliente mediante modal de búsqueda (igual que asistencia) -->
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Cliente</label>
                    <div class="modal-select-btn" data-bs-toggle="modal" data-bs-target="#clienteModalAsignacion">
                        <span id="cliente_selected_text_asignar">Seleccione cliente</span>
                        <span><i class="fas fa-chevron-right"></i></span>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> Cédula Cliente</label>
                    <input type="text" id="cedula_cliente_asignacion" name="cedula_cliente" class="form-control" readonly placeholder="Seleccionar cliente primero" style="background:#f1f5f9;" required>
                </div>
                <!-- Selector de rutina base -->
                <div class="form-group">
                    <label><i class="fas fa-dumbbell"></i> Rutina Base</label>
                    <select id="id_rutina_asignacion" name="id_rutina" class="form-control" required>
                        <option value="" disabled selected>Seleccione rutina</option>
                        <?php foreach ($rutinas as $rut): ?>
                            <option value="<?= $rut['id_rutina'] ?>"><?= htmlspecialchars($rut['nombre']) ?> (<?= htmlspecialchars($rut['nombre_dificultad']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-calendar-plus"></i> Fecha Asignación</label>
                    <!-- Se agregó min para bloquear fechas anteriores a hoy -->
                    <input type="date" id="fecha_asignacion" name="fecha_asignacion" class="form-control" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                    <div class="error-msg"></div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-day"></i> Fecha Inicio Plan</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" min="<?= date('Y-m-d') ?>">
                    <div class="error-msg"></div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-times"></i> Fecha Fin Plan</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" min="<?= date('Y-m-d') ?>">
                    <div class="error-msg"></div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Estado Inicial</label>
                    <select id="estado_asignacion" name="estado" class="form-control">
                        <option value="Activa" selected>Activa</option>
                        <option value="Completada">Completada</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-tasks"></i> Progreso Inicial (%)</label>
                    <input type="number" id="progreso_asignacion" name="progreso" class="form-control" min="0" max="100" step="1" value="0">
                    <div class="error-msg"></div>
                </div>
                <div class="form-group" style="grid-column: 1 / -1; display: flex; flex-direction: row; gap: 1rem;">
                    <button type="submit" id="btnAsignar" style="flex: 1;"><i class="fas fa-save"></i> Registrar Asignación</button>
                    <button type="button" id="btnCancelarMasivoUI" class="btn-secondary" style="flex: 1; background: #343a40; color: white;"><i class="fas fa-ban"></i> Cancelar Rutinas Seleccionadas</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fas fa-stream"></i> Clientes con Planificaciones Activas</div>
        <div class="card-body">
            <!-- Barra de Búsqueda unificada con el Botón de Filtros Especiales -->
            <div class="buscador">
                <input type="text" id="searchInputAsignaciones" class="form-control" placeholder="Buscar por cédula, cliente o rutina asignada...">
                <button id="btnBuscarAsignacion" class="btn-secondary" type="button"><i class="fas fa-search"></i> Buscar</button>
                
                <!-- Botón Dropdown de Filtros Avanzados -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" id="btnFiltrosAvanzados" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.5rem 1rem; border-radius: 14px;">
                        <i class="fas fa-filter"></i> Filtros
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="btnFiltrosAvanzados">
                        <li><h6 class="dropdown-header">Consultas Especiales</h6></li>
                        <li>
                            <a class="dropdown-item py-2" href="#" id="btnSub2">
                                <i class="fas fa-fire-alt text-warning me-2"></i> Asignaciones Nivel Avanzado
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="#" id="btnSub3">
                                <i class="fas fa-stopwatch text-info me-2"></i> Rutinas más largas
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped" id="tablaAsignaciones">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="checkAllAsignaciones" style="width: 16px; height: 16px; cursor: pointer;">
                            </th>
                            <th>Cliente</th>
                            <th>Rutina</th>
                            <th>Asignado</th>
                            <th>Período de Plan</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAsignacionesBody">
                        <?php foreach ($asignaciones as $a): ?>
                            <tr data-id="<?= $a['id_asignacion'] ?>">
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="checkbox" class="check-asignacion" data-cedula="<?= htmlspecialchars($a['cedula_cliente']) ?>" style="width: 16px; height: 16px; cursor: pointer;">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($a['nombre_cliente']) ?></strong><br>
                                    <small class="text-muted"><i class="fas fa-id-card"></i> <?= htmlspecialchars($a['cedula_cliente']) ?></small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($a['nombre_rutina']) ?></strong><br>
                                    <span class="dificultad-badge dif-<?= strtolower(str_replace('ñ', 'n', $a['nombre_dificultad'] ?? 'Principiante')) ?> scale-08">
                                        <?= htmlspecialchars($a['nombre_dificultad'] ?? 'Principiante') ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($a['fecha_asignacion'])) ?></td>
                                <td>
                                    <small>
                                        <strong>Inicia:</strong> <?= !empty($a['fecha_inicio']) ? date('d/m/Y', strtotime($a['fecha_inicio'])) : 'Pendiente' ?><br>
                                        <strong>Termina:</strong> <?= !empty($a['fecha_fin']) ? date('d/m/Y', strtotime($a['fecha_fin'])) : 'Pendiente' ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="estado-badge est-<?= strtolower($a['estado']) ?>">
                                        <?= htmlspecialchars($a['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="progress-container">
                                        <div class="progress-bar-val" style="width: <?= floatval($a['progreso']) ?>%"></div>
                                        <span><?= floatval($a['progreso']) ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="acciones-botones">
                                        <button class="btn btn-sm btn-warning editar-asignacion-btn"
                                            data-id="<?= $a['id_asignacion'] ?>"
                                            data-cedula="<?= htmlspecialchars($a['cedula_cliente']) ?>"
                                            data-nombre-cliente="<?= htmlspecialchars($a['nombre_cliente']) ?>"
                                            data-rutina="<?= $a['id_rutina'] ?>"
                                            data-fecha-asig="<?= $a['fecha_asignacion'] ?>"
                                            data-fecha-ini="<?= $a['fecha_inicio'] ?>"
                                            data-fecha-fin="<?= $a['fecha_fin'] ?>"
                                            data-estado="<?= $a['estado'] ?>"
                                            data-progreso="<?= $a['progreso'] ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button class="btn btn-sm btn-danger eliminar-asignacion-btn"
                                            data-id="<?= $a['id_asignacion'] ?>"
                                            data-cliente="<?= htmlspecialchars($a['nombre_cliente']) ?>"
                                            data-rutina="<?= htmlspecialchars($a['nombre_rutina']) ?>">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($asignaciones)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay rutinas asignadas a ningún cliente actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buscar Cliente -->
<div class="modal fade" id="clienteModalAsignacion" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-search"></i> Buscar Cliente para Asignar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchClientAsignar" class="form-control search-client" placeholder="Filtrar por Cédula, Nombre, Correo o Teléfono...">
                <div class="table-responsive">
                    <table class="table table-hover" id="clientesTablaAsignacion">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Mostrar Resultados Avanzados (Requerido por rutinas.js) -->
<div class="modal fade" id="modalResultadosAvanzados" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="tituloModalAvanzados"><i class="fas fa-list"></i> Resultados de Consulta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead id="theadResultadosAvanzados"></thead>
                        <tbody id="tbodyResultadosAvanzados"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Asignación -->
<div class="modal fade" id="editarAsignacionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-user-edit"></i> Modificar Asignación de Rutina</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id_asignacion">
                <div class="mb-3">
                    <label class="form-label-modal">Cliente</label>
                    <input type="text" id="edit_nombre_cliente_asignado" class="form-control" readonly style="background:#f1f5f9;">
                    <input type="hidden" id="edit_cedula_cliente_asignado">
                </div>
                <div class="mb-3">
                    <label class="form-label-modal">Rutina Asignada</label>
                    <select id="edit_id_rutina_asignacion" class="form-control" required>
                        <?php foreach ($rutinas as $rut): ?>
                            <option value="<?= $rut['id_rutina'] ?>"><?= htmlspecialchars($rut['nombre']) ?> (<?= htmlspecialchars($rut['nombre_dificultad']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-modal">Fecha de Asignación</label>
                    <!-- Se agregó min para bloquear fechas anteriores a hoy -->
                    <input type="date" id="edit_fecha_asignacion" class="form-control" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-modal">Fecha de Inicio</label>
                        <input type="date" id="edit_fecha_inicio" class="form-control" min="<?= date('Y-m-d') ?>">
                        <div class="error-msg"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label-modal">Fecha de Vencimiento</label>
                        <input type="date" id="edit_fecha_fin" class="form-control" min="<?= date('Y-m-d') ?>">
                        <div class="error-msg"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-modal">Estado del Plan</label>
                        <select id="edit_estado_asignacion" class="form-control">
                            <option value="Activa">Activa</option>
                            <option value="Completada">Completada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label-modal">Progreso Actual (%)</label>
                        <input type="number" id="edit_progreso" class="form-control" min="0" max="100" step="1">
                        <div class="error-msg"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="guardarEdicionAsignacion">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación de Asignación -->
<div class="modal fade" id="eliminarAsignacionModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Cancelar Asignación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="delete_id_asignacion">
                <p>¿Está seguro de eliminar esta planificación para el cliente?</p>
                <div class="mb-2"><strong>Cliente:</strong> <span id="delete_cliente_asignacion_txt"></span></div>
                <div class="mb-2"><strong>Rutina:</strong> <span id="delete_rutina_asignacion_txt"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarAsignacion">Desvincular</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Cancelación Masiva (Baja) -->
<div class="modal fade" id="modalConfirmarCancelacionMasiva" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-warning"></i> Cancelar Múltiples Rutinas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea cancelar <strong>todas</strong> las rutinas activas para los clientes seleccionados?</p>
                <p class="text-muted small">Al confirmar, se procesará la cancelación para las rutinas de estos clientes.</p>
                <div id="listaClientesCancelacion" class="mb-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarBajaMedicaMasiva"><i class="fas fa-ban"></i> Confirmar Cancelación</button>
            </div>
        </div>
    </div>
</div>

<style>
    .container {
        --bs-gutter-x: 0;
        max-width: 1100px;
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

    .card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.8rem;
        border: 1px solid #edf2f7;
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
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.2rem;
        align-items: flex-start;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        position: relative;
    }

    /* Clases de validación de error */
    .error-msg {
        display: none;
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: none;
    }

    label,
    .form-label-modal {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #5a6e7a;
    }

    input,
    select,
    button {
        padding: 0.7rem 1rem;
        border-radius: 14px;
        border: 1px solid #cfdfe8;
        font-size: 0.9rem;
        width: 100%;
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

    .btn-sm {
        padding: 0.2rem 0.6rem !important;
        font-size: 0.7rem !important;
        border-radius: 20px !important;
        width: auto;
    }

    .btn-secondary {
        background: #6c757d;
    }

    .btn-warning {
        background: #ffc107;
        color: #1e2a3a;
    }

    .btn-danger {
        background: #dc3545;
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
        width: 120px;
        white-space: nowrap;
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
        width: 100%;
    }

    .modal-select-btn:hover {
        border-color: #C62828;
        background: #fef2f2;
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
        width: auto;
    }

    .search-client {
        margin-bottom: 1rem;
        padding: 0.5rem;
        border-radius: 12px;
        border: 1px solid #cfdfe8;
        width: 100%;
    }

    /* Estilos para badges de dificultad */
    .dificultad-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: bold;
        border-radius: 12px;
        text-align: center;
    }

    .scale-08 {
        transform: scale(0.9);
        transform-origin: left;
    }

    .dif-principiante {
        background-color: #e2f0d9;
        color: #385723;
    }

    .dif-intermedio {
        background-color: #fff2cc;
        color: #7f6000;
    }

    .dif-avanzado {
        background-color: #fce4d6;
        color: #c65911;
    }

    /* Estilos para badges de estado */
    .estado-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: bold;
        border-radius: 12px;
        text-align: center;
    }

    .est-activa {
        background-color: #d5e8d4;
        color: #274e13;
    }

    .est-completada {
        background-color: #dae8fc;
        color: #1c4587;
    }

    .est-cancelada {
        background-color: #f8cecc;
        color: #660000;
    }

    /* Barra de progreso visual */
    .progress-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100px;
    }

    .progress-bar-val {
        height: 8px;
        background-color: #c62828;
        border-radius: 4px;
        transition: width 0.3s;
    }

    table tr td .progress-container {
        background-color: #e2e8f0;
        border-radius: 4px;
        height: 8px;
        position: relative;
        flex: 1;
        min-width: 60px;
    }

    .progress-container span {
        font-size: 0.7rem;
        font-weight: bold;
        color: #475569;
        margin-left: auto;
    }

    .alert {
        padding: 0.9rem 1.2rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
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

    footer {
        text-align: center;
        padding: 1rem;
        background: #fafbfc;
        color: #7f8c8d;
        font-size: 0.75rem;
    }

    .buscador {
        margin-bottom: 1rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .buscador input {
        flex: 1;
        min-width: 200px;
    }

    .buscador button {
        width: auto;
    }

    .buscador .btn-secondary {
        background: #6c757d;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    .buscador .btn-secondary:hover {
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

    .modal-header.bg-danger {
        background-color: #C62828 !important;
    }

    @media (max-width: 768px) {
        body {
            padding: 1rem;
        }

        td:last-child,
        th:last-child {
            width: auto;
            white-space: normal;
        }
    }
</style>