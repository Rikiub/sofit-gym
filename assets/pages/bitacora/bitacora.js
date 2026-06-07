import Alpine from "alpinejs";
import { modalFormComponent } from "@/components/modalForm.js";
import { crudTableComponent } from "@/components/crudTable.js";
import { html } from "gridjs";
import dayjs from "dayjs";

const PAGE = "bitacora";

const BADGES = {
    'DEBUG': 'bg-secondary text-white',
    'INFO': 'bg-success text-white',
    'NOTICE': 'bg-info text-dark',
    'WARNING': 'bg-warning text-dark',
    'ERROR': 'bg-danger text-white',
    'CRITICAL': 'bg-danger text-white border border-light fw-bold',
    'ALERT': 'bg-danger text-white animate-pulse',
    'EMERGENCY': 'bg-danger text-white fw-bolder text-uppercase shadow-sm',
}

function capitalizeWords(str) {
  return str.replace(/\b\w/g, char => char.toUpperCase());
}
    
Alpine.data("crudTable", () => (
    crudTableComponent({
        params: {
            page: PAGE,
            action: "query",
        },
        columns: [
            {
                id: "fecha",
                name: "Fecha y Hora",
                formatter: (cell, row) => dayjs(cell).format("DD/MM/YYYY HH:MM"),
            },
            "Mensaje",
            {
                name: "Nivel",
                formatter: (cell, row) => {
                    const badge = BADGES[cell.toUpperCase()];

                    return html(`
                        <span class="${badge} p-1 rounded">${cell}</span>
                    `)
                },
            },
            {
                name: "Modulo",
                formatter: (cell, row) => capitalizeWords(cell),
            },
            {
                name: "Accion",
                formatter: (cell, row) => capitalizeWords(cell),
            },
            { name: "ID Usuario", id: "id_usuario" },
        ],
        gridOptions: {
            crudButtons: {
                onAdd: null,
                onEdit: null,
                onDelete: null,
            },
        }
    })));
