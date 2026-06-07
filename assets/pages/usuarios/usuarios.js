import Alpine from "alpinejs";
import { modalFormComponent } from "@/components/modalForm.js";
import { crudTableComponent } from "@/components/crudTable.js";
import dayjs from "dayjs";

const PAGE = "usuarios";

Alpine.data("crudTableUsuarios", () => (
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

Alpine.data("modalFormUsuarios", () => (
    modalFormComponent({
        page: PAGE,
        elementName: "Usuario",
        editDisableFields: ["nombre_usuario", "fecha_registro"],
        prepareAddData: {
            fecha_registro: new Date(),
        },
    })));