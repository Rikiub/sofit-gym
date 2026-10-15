<?php
$this->pushJs("pages/asistencia/asistencia.js");

$this->layout("layout", ["title" => "Control de asistencia"]);
?>

<div class="container asistencia-scope">
    <div class="header">
        <h1><i class="fas fa-door-open"></i> Control de Asistencia</h1>
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
        <div class="card-header"><i class="fas fa-plus-circle"></i> Registrar nueva entrada</div>
        <div class="card-body">
            <form id="formRegistroEntrada" class="form-grid">
                <div class="form-group form-group-wide">
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
                    <label><i class="fas fa-clock"></i> Hora (ACTUAL)</label>
                    <input type="time" id="hora" name="hora" class="form-control" step="1">
                </div>
                <div class="form-group">
                    <button type="submit" id="btnRegistrar"><i class="fas fa-save"></i> Registrar Entrada</button>
                </div>
                <input type="hidden" name="cedula" id="selected_cliente_id">
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fas fa-list"></i> Entradas de hoy</div>
        <div class="card-body">
            <div class="buscador-pagos">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Buscar por hora, cédula o nombre...">
                <button id="btnBuscar" class="btn btn-secondary" type="button">Buscar</button>
                <button type="button" id="btnHistorial" class="btn-estado" title="Ver historial de asistencias">
                    <i class="fas fa-history"></i> Historial del cliente
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="tablaEntradas">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Cédula</th>
                            <th>Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEntradasBody">
                        <?php foreach ($entradasHoy as $e): ?>
                            <tr data-id="<?= $e['id_asistencia'] ?>">
                                <td><?= date('H:i:s', strtotime($e['fecha'])) ?></td>
                                <td><?= htmlspecialchars($e['cedula_cliente']) ?></td>
                                <td><?= htmlspecialchars($e['nombre_cliente']) ?></td>
                                <td>
                                    <div class="acciones-botones">
                                        <button class="btn btn-sm btn-warning editar-btn" data-id="<?= $e['id_asistencia'] ?>" data-hora="<?= date('H:i:s', strtotime($e['fecha'])) ?>" data-cedula="<?= htmlspecialchars($e['cedula_cliente']) ?>" data-nombre="<?= htmlspecialchars($e['nombre_cliente']) ?>"><i class="fas fa-edit"></i> Editar</button>
                                        <button class="btn btn-sm btn-danger eliminar-btn" data-id="<?= $e['id_asistencia'] ?>" data-hora="<?= date('H:i:s', strtotime($e['fecha'])) ?>" data-cedula="<?= htmlspecialchars($e['cedula_cliente']) ?>" data-nombre="<?= htmlspecialchars($e['nombre_cliente']) ?>"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($entradasHoy)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay entradas registradas hoy.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade asistencia-scope" id="clienteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
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

<div class="modal fade asistencia-scope" id="editarModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Hora de Entrada</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="mb-3">
                    <label>Cédula</label>
                    <input type="text" id="edit_cedula" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label>Cliente</label>
                    <input type="text" id="edit_nombre" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label>Hora (HH:MM:SS)</label>
                    <input type="time" id="edit_hora" class="form-control" step="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="guardarEdicion">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade asistencia-scope" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="fw-bold">¿Está seguro de eliminar esta entrada?</p>
                <div class="bg-light p-3 rounded text-start" style="margin: 0 auto; max-width: 350px;">
                    <div><strong>Hora:</strong> <span id="delete_hora"></span></div>
                    <div><strong>Cédula:</strong> <span id="delete_cedula"></span></div>
                    <div><strong>Cliente:</strong> <span id="delete_nombre"></span></div>
                </div>
                <input type="hidden" id="delete_id">
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade asistencia-scope" id="historialModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-history"></i> Historial de asistencias
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="historial-resumen mb-3">
                    <div class="resumen-item">
                        <span class="resumen-label">Hoy</span>
                        <span class="resumen-valor" id="hist_hoy">—</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Semana</span>
                        <span class="resumen-valor" id="hist_semana">—</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Mes</span>
                        <span class="resumen-valor" id="hist_mes">—</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Total</span>
                        <span class="resumen-valor" id="hist_total">—</span>
                    </div>
                    <div class="resumen-item resumen-item-wide">
                        <span class="resumen-label">Última visita</span>
                        <span class="resumen-valor" id="hist_ultima">—</span>
                    </div>
                </div>

                <div class="historial-filtros mb-3">
                    <div class="filtro-group filtro-group-wide">
                        <label>Buscar cliente</label>
                        <input type="text" id="hist_termino" class="form-control" placeholder="Cédula o nombre...">
                    </div>
                    <div class="filtro-group">
                        <label>Desde</label>
                        <input type="date" id="hist_inicio" class="form-control">
                    </div>
                    <div class="filtro-group">
                        <label>Hasta</label>
                        <input type="date" id="hist_fin" class="form-control">
                    </div>
                    <button type="button" id="hist_btnFiltrar" class="btn btn-info">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <button type="button" id="hist_btnTodo" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Ver todo
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Cédula</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody id="historialBody">
                            <tr><td colspan="5" class="text-center">Cargando...</td></tr>
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
    .asistencia-scope {
        &.container {
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
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            min-width: 0;
        }

        .form-group-wide {
            min-width: 0;
        }

        @media (max-width: 900px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
            .form-group-wide {
                grid-column: 1 / -1;
            }
        }

        .form-group .form-control,
        .form-group select,
        .buscador-pagos .form-control,
        .search-client {
            padding: 0.7rem 1rem;
            border-radius: 14px;
            border: 1px solid #cfdfe8;
            font-size: 0.9rem;
            width: 100%;
        }

        label {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5a6e7a;
        }

        #btnRegistrar {
            padding: 0.7rem 1rem;
            border-radius: 14px;
            font-size: 0.9rem;
            width: 100%;
            background: #C62828;
            color: white;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }

        #btnRegistrar:hover {
            background: #b71c1c;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 0.2rem 0.6rem !important;
            font-size: 0.7rem !important;
            border-radius: 20px !important;
            width: auto !important;
        }

        .btn-secondary {
            background: #6c757d !important;
            color: white !important;
            padding: 0.7rem 1.5rem !important;
            border-radius: 14px !important;
            border: none !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            white-space: nowrap !important;
            transition: background 0.2s, transform 0.2s !important;
        }

        .btn-secondary:hover {
            background: #5a6268 !important;
            transform: translateY(-1px) !important;
        }

        .btn-warning {
            background: #ffc107 !important;
            color: #1e2a3a !important;
        }

        .btn-warning:hover {
            background: #e0a800 !important;
        }

        .btn-danger {
            background: #dc3545 !important;
            color: white !important;
        }

        .btn-danger:hover {
            background: #bd2130 !important;
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

        .buscador-pagos {
            margin-bottom: 1rem;
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .buscador-pagos input {
            flex: 1;
            min-width: 200px;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            border: 1px solid #cfdfe8;
            font-size: 0.9rem;
        }

        .buscador-pagos input:focus {
            outline: none;
            border-color: #C62828;
            box-shadow: 0 0 0 3px rgba(198, 40, 40, 0.1);
        }

        .btn-estado {
            padding: 0.7rem 1.1rem;
            border-radius: 14px;
            border: none;
            background: #17a2b8;
            color: white;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            white-space: nowrap;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-estado:hover {
            background: #138496;
            transform: translateY(-1px);
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

        .modal-select-btn span:first-child {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
            width: auto !important;
        }

        .search-client {
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 12px;
            border: 1px solid #cfdfe8;
            width: 100%;
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

        .modal-header.bg-info {
            background-color: #17a2b8 !important;
        }

        .modal-header.bg-danger .btn-close-white,
        .modal-header.bg-info .btn-close-white {
            filter: brightness(0) invert(1);
        }

        .modal-body .bg-light {
            background-color: #f8f9fa !important;
        }

        .historial-resumen {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 0.6rem;
        }

        .resumen-item {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 0.6rem 0.8rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .resumen-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #5a6e7a;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .resumen-valor {
            font-size: 1.15rem;
            font-weight: 700;
            color: #C62828;
        }

        .resumen-item-wide {
            grid-column: span 2;
        }

        .historial-filtros {
            display: flex;
            gap: 0.6rem;
            align-items: flex-end;
            flex-wrap: wrap;
            padding: 0.8rem 1rem;
            background: #f8fafc;
            border-radius: 12px;
        }

        .historial-filtros .filtro-group {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            min-width: 140px;
            flex: 1;
        }

        .historial-filtros .filtro-group-wide {
            flex: 2;
            min-width: 200px;
        }

        .historial-filtros label {
            font-size: 0.7rem;
        }

        .historial-filtros .form-control {
            padding: 0.5rem 0.7rem;
            border-radius: 10px;
            border: 1px solid #cfdfe8;
            font-size: 0.85rem;
        }

        #hist_btnFiltrar,
        #hist_btnTodo {
            padding: 0.55rem 1rem;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            white-space: nowrap;
            color: white;
        }

        #hist_btnFiltrar {
            background: #17a2b8;
        }
        #hist_btnFiltrar:hover {
            background: #138496;
        }

        #hist_btnTodo {
            background: #6c757d;
        }
        #hist_btnTodo:hover {
            background: #5a6268;
        }

        @media (max-width: 768px) {

            td:last-child,
            th:last-child {
                width: auto;
                white-space: normal;
            }

            .buscador-pagos {
                flex-wrap: wrap;
            }

            .buscador-pagos input {
                flex: 1 1 100%;
            }

            .buscador-pagos .btn-secondary,
            .buscador-pagos .btn-estado {
                width: 100%;
                justify-content: center;
            }

            .resumen-item-wide {
                grid-column: 1 / -1;
            }
        }
    }
</style>