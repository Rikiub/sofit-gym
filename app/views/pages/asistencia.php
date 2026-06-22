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
            <div class="buscador">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por hora, cédula o nombre...">
                <button id="btnBuscar" class="btn-secondary">Buscar</button>
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
                                <td colspan="4" class="text-center">No hay entradas registradas hoy.<?php echo ""; ?></td>
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
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="delete_id">
                <p>¿Está seguro de eliminar esta entrada?</p>
                <div class="mb-2"><strong>Hora:</strong> <span id="delete_hora"></span></div>
                <div class="mb-2"><strong>Cédula:</strong> <span id="delete_cedula"></span></div>
                <div class="mb-2"><strong>Cliente:</strong> <span id="delete_nombre"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
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
            gap: 1rem;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-group .form-control,
        .form-group select,
        .buscador .form-control,
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
        }

        .btn-secondary:hover {
            background: #5a6268 !important;
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
        }

        .buscador input {
            flex: 1;
            width: auto;
        }

        .buscador button {
            width: auto;
            background: #6c757d;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 14px;
            border: none;
            color: white;
            cursor: pointer;
        }

        .buscador button:hover {
            background: #5a6268;
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

        @media (max-width: 768px) {

            td:last-child,
            th:last-child {
                width: auto;
                white-space: normal;
            }
        }
    }
</style>