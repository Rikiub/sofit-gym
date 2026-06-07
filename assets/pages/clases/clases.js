import Alpine from "alpinejs";
import { modalFormComponent, openModal } from "@/components/modalForm.js";
import { calendarComponent } from "@/components/calendar.js";
import { fetchApi } from "@/js/api.js";
import { toIsoDateTime } from "@/js/dates.js";
import dayjs from "dayjs";

const PAGE = "clasesGrupales";

Alpine.data("calendarClases", () => calendarComponent({
    urlParams: {
        page: PAGE,
        action: "query",
    },
    mapEvent: (data) => ({
        id: data.id_clase,
        title: data.nombre,
        start: data.fecha_inicio,
        end: data.fecha_fin,
    }),
    onAdd: (self) => openModal(self, { mode: "add" }),
    popoverTitle: (data) => `<span class="fw-bold text-secondary small">${data.nombre}</span>`,
    popoverContent: (data) => {
        const startTime = dayjs(data.fecha_inicio).format('hh:mm A');
        const endTime = dayjs(data.fecha_fin).format('hh:mm A');

        return `
            <div class="ctx-popover-card p-1" style="min-width: 250px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-secondary fw-semibold">
                        <i class="fa-solid fa-clock text-muted me-1"></i> ${startTime} / ${endTime}
                    </span>

                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-users text-primary me-1"></i> 
                        <strong>${data.cupos_ocupados || 0}</strong>/${data.capacidad_maxima || '∞'}
                    </span>
                </div>

                <div class="p-2 rounded mb-3 text-muted border-start border-primary border-3" 
                    style="background-color: #f8f9fa; font-size: 0.8rem; line-height: 1.4;">
                    ${data.descripcion || '<em>Sin descripción disponible.</em>'}
                </div>

                <hr class="my-2 opacity-10">

                <div class="d-flex gap-2 mt-2">
                    <button class="btn-ctx-edit btn btn-sm btn-light border text-primary flex-grow-1 fw-semibold d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-pen me-1" style="font-size: 0.75rem;"></i> Editar
                    </button>

                    <button class="btn-ctx-delete btn btn-sm btn-outline-danger flex-grow-1 fw-semibold d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-trash me-1" style="font-size: 0.75rem;"></i> Eliminar
                    </button>
                </div>
            </div>
        `;
    },
    onPopoverHover: (element, data, self) => {
        element.querySelector('.btn-ctx-edit').onclick = () => {
            openModal(self, { mode: "edit", dataId: data.id });
            self.destroyPopover();
        };

        element.querySelector('.btn-ctx-delete').onclick = () => {
            openModal(self, { mode: "delete", dataId: data.id });
            self.destroyPopover();
        };
    },
}));

Alpine.data("modalForm", () => modalFormComponent({
    page: PAGE,
    actions: {
        onAdd: "insert",
        onEdit: "update",
        onEditFind: "find",
        onDelete: "delete",
    },
    elementName: "Clase",
    prepareAddData: {
        fecha_inicio: toIsoDateTime(new Date()),
    },
    transformEditData: (item) => {
        item.fecha_inicio = toIsoDateTime(item.fecha_inicio);
        item.fecha_fin = toIsoDateTime(item.fecha_fin);
        return item;
    }
}));

Alpine.data("listaClientes", () => ({
    keyName: "clientes",

    clientes: [],
    capacidad_actual: 0,

    add(item) {
        // No agregar duplicados
        const exists = this.clientes.some(c => c.cedula === item.cedula);
        if (exists) return;

        this.clientes.push(item);
        this.capacidad_actual += 1;
    },
    remove(index) {
        this.clientes.splice(index, 1);
        this.capacidad_actual -= 1;
    },
    reset() {
        this.clientes = [];
        this.capacidad_actual = 0;
    },

    // EVENTS
    handleItemSelected(item) {
        this.add(item);
    },
    load(item) {
        const clientes = item.data.clientes || [];
        this.clientes = clientes;
        this.capacidad_actual = clientes.length;
    },
    validate(detail) {
        if (this.clientes.length === 0) {
            detail.setInvalid(this.keyName, "Selecciona al menos uno")
        } else {
            detail.setValid(this.keyName);
        }
    },
    serialize(detail) {
        detail.merge({ clientes: this.clientes.map((item) => item.cedula) });
    },
}));