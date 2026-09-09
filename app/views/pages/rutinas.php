<?php
$this->pushJs("pages/rutinas/rutinas.js");
$this->layout("layout", ["title" => "Gestion de Rutinas"]);
// Recibe $rutinas, $dificultades, $mensaje, $tipoMensaje del controlador
?>

<div class="container">
    <div class="header">
        <h1><i class="fas fa-dumbbell"></i> Planes de Entrenamiento</h1>
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
        <div class="card-header"><i class="fas fa-plus-circle"></i> Crear Nueva Rutina Base</div>
        <div class="card-body">
            <form id="formRegistroRutina" class="form-grid">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nombre de la Rutina</label>
                    <input type="text" id="nombre_rutina" name="nombre" class="form-control" placeholder="Ej. Fuerza Básica de Piernas" required>
                    <div class="error-msg"></div> </div>
                <div class="form-group">
                    <label><i class="fas fa-layer-group"></i> Dificultad</label>
                    <select id="id_dificultad" name="id_dificultad" class="form-control" required>
                        <option value="" disabled selected>Seleccione dificultad</option>
                        <?php foreach ($dificultades as $dif): ?>
                            <option value="<?= $dif['id_dificultad'] ?>"><?= htmlspecialchars($dif['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Duración (Semanas)</label>
                    <input type="number" id="duracion_semanas" name="duracion_semanas" class="form-control" placeholder="Ej. 6" min="1">
                    <div class="error-msg"></div> </div>
                <div class="form-group">
                    <label><i class="fas fa-bullseye"></i> Objetivo Principal</label>
                    <input type="text" id="objetivo" name="objetivo" class="form-control" placeholder="Ej. Hipertrofia de cuádriceps">
                    <div class="error-msg"></div> </div>
                <div class="form-group col-span-full">
                    <label><i class="fas fa-align-left"></i> Descripción de la Rutina</label>
                    <textarea id="descripcion" name="descripcion" class="form-control form-control-textarea" placeholder="Describe los ejercicios y las especificaciones básicas de la rutina..."></textarea>
                    <div class="error-msg"></div> </div>
                <div class="form-group btn-submit-group">
                    <button type="submit" id="btnGuardarRutina"><i class="fas fa-save"></i> Guardar Rutina</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fas fa-folder-open"></i> Rutinas en el Catálogo</div>
        <div class="card-body">
            <div class="buscador">
                <input type="text" id="searchInputRutinas" class="form-control" placeholder="Buscar por Nombre, Dificultad, Duración u Objetivo...">
                <button id="btnBuscarRutina" class="btn-secondary">Buscar</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="tablaRutinas">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Dificultad</th>
                            <th>Duración</th>
                            <th>Objetivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaRutinasBody">
                        <?php foreach ($rutinas as $r): ?>
                            <tr data-id="<?= $r['id_rutina'] ?>">
                                <td><strong><?= htmlspecialchars($r['nombre']) ?></strong></td>
                                <td>
                                    <span class="dificultad-badge dif-<?= strtolower(str_replace('ñ', 'n', $r['nombre_dificultad'])) ?>">
                                        <?= htmlspecialchars($r['nombre_dificultad']) ?>
                                    </span>
                                </td>
                                <td><?= !empty($r['duracion_semanas']) ? htmlspecialchars($r['duracion_semanas'] . ' semanas') : '—' ?></td>
                                <td><?= !empty($r['objetivo']) ? htmlspecialchars($r['objetivo']) : '—' ?></td>
                                <td>
                                    <div class="acciones-botones">
                                        <button class="btn btn-sm btn-warning editar-rutina-btn"
                                            data-id="<?= $r['id_rutina'] ?>"
                                            data-nombre="<?= htmlspecialchars($r['nombre']) ?>"
                                            data-dificultad="<?= $r['id_dificultad'] ?>"
                                            data-duracion="<?= $r['duracion_semanas'] ?>"
                                            data-objetivo="<?= htmlspecialchars($r['objetivo'] ?? '') ?>"
                                            data-descripcion="<?= htmlspecialchars($r['descripcion'] ?? '') ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button class="btn btn-sm btn-danger eliminar-rutina-btn"
                                            data-id="<?= $r['id_rutina'] ?>"
                                            data-nombre="<?= htmlspecialchars($r['nombre']) ?>">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($rutinas)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay rutinas creadas en el sistema todavía.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editarRutinaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Modificar Rutina Base</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id_rutina">
                <div class="mb-3">
                    <label class="form-label-modal">Nombre de la Rutina</label>
                    <input type="text" id="edit_nombre_rutina" class="form-control" required>
                    <div class="error-msg"></div> </div>
                <div class="mb-3">
                    <label class="form-label-modal">Dificultad</label>
                    <select id="edit_id_dificultad" class="form-control" required>
                        <?php foreach ($dificultades as $dif): ?>
                            <option value="<?= $dif['id_dificultad'] ?>"><?= htmlspecialchars($dif['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-modal">Duración (Semanas)</label>
                    <input type="number" id="edit_duracion_semanas" class="form-control" min="1">
                    <div class="error-msg"></div> </div>
                <div class="mb-3">
                    <label class="form-label-modal">Objetivo Principal</label>
                    <input type="text" id="edit_objetivo" class="form-control">
                    <div class="error-msg"></div> </div>
                <div class="mb-3">
                    <label class="form-label-modal">Descripción</label>
                    <textarea id="edit_descripcion" class="form-control form-control-textarea" rows="4"></textarea>
                    <div class="error-msg"></div> </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="guardarEdicionRutina">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eliminarRutinaModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Eliminar Rutina</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="delete_id_rutina">
                <p>¿Está seguro de eliminar esta rutina base? Esta acción no se puede deshacer si existen asignaciones asociadas.</p>
                <div class="mb-2"><strong>Rutina:</strong> <span id="delete_nombre_rutina_txt"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarRutina">Eliminar</button>
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
        align-items: flex-start; /* Cambiado a flex-start para que el texto de error no descuadre */
    }

    .col-span-full {
        grid-column: 1 / -1;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        position: relative;
    }

    .btn-submit-group {
        justify-content: flex-end;
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
    textarea,
    button {
        padding: 0.7rem 1rem;
        border-radius: 14px;
        border: 1px solid #cfdfe8;
        font-size: 0.9rem;
        width: 100%;
    }

    .form-control-textarea {
        padding: 0.7rem 1rem;
        border-radius: 14px;
        border: 1px solid #cfdfe8;
        font-size: 0.9rem;
        width: 100%;
        min-height: 80px;
        font-family: inherit;
        resize: vertical;
    }

    /* Clases de validación de error */
    .error-msg {
        display: none;
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .form-control.is-invalid, .input-custom.is-invalid {
        border-color: #dc3545;
        background-image: none;
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

    /* Estilos para badges de dificultad */
    .dificultad-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: bold;
        border-radius: 12px;
        text-align: center;
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
    }

    .buscador button:hover {
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