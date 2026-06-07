import Alpine from "alpinejs";
import { modalFormComponent } from "@/components/modalForm.js";
import { crudTableComponent } from "@/components/crudTable.js";
import { toIsoDate } from "@/js/dates.js";
import dayjs from "dayjs";

const PAGE = "usuarios";

Alpine.data("crudTable", () => (
    crudTableComponent({
        params: {
            page: PAGE,
            action: "query",
        },
        columns: [
            { name: "Nombre de usuario", id: "nombre_usuario" },
            "Rol",
            {
                name: "Fecha de registro",
                id: "fecha_registro",
                formatter: (cell, row) => {
                    return dayjs(cell).format("DD/MM/YYYY");
                }
            },
        ],
    })));

Alpine.data("modalForm", () => (
    modalFormComponent({
        page: PAGE,
        actions: {
            onAdd: "insert",
            onEdit: "update",
            onEditFind: "find",
            onDelete: "delete",
        },
        elementName: "Usuario",
        editDisableFields: ["nombre_usuario", "fecha_registro"],
        prepareAddData: {
            fecha_registro: toIsoDate(new Date()),
        },
        transformEditData: (item) => {
            item.fecha_registro = toIsoDate(item.fecha_registro);
            return item;
        }
    })));