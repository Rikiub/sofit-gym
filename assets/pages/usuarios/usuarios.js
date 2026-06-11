import { modalFormComponent } from "@/components/modalForm.js";
import { crudTableComponent } from "@/components/crudTable.js";
import { h } from "gridjs";
import Alpine from "alpinejs";
import dayjs from "dayjs";

const PAGE = "usuarios";

Alpine.data("crudTableUsuarios", () => (
    crudTableComponent({
        id: "usuarios",
        params: {
            page: PAGE,
            action: "query",
        },
        columns: [
            {
                id: "id_usuario",
                hidden: true,
            },
            {
                id: "imagen_url",
                nombre: "Imagen",
                formatter: (cell, row) => {
                    return h("img", { src: cell, class: "img-fluid rounded", width: 100 });
                },
            },
            {
                name: "Nombre de usuario",
                id: "nombre_usuario",
            },
            "Rol",
            {
                name: "Fecha de registro",
                id: "fecha_creacion",
                formatter: (cell, row) => {
                    return dayjs(cell).format("DD/MM/YYYY");
                }
            },
        ],
    })));
