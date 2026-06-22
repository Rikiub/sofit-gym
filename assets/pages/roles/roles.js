import Alpine from "alpinejs";
import { fetchApi } from "@/js/api.js";
import { crudTableComponent } from "@/components/crudTable.js";
import { modalFormComponent } from "@/components/modalForm.js";

Alpine.data("crudPermisos", () => (
    crudTableComponent({
        params: {
            page: "roles",
            action: "query",
        },
        columns: [
            { id: "id_rol", hidden: true },
            "Nombre",
        ],
        gridOptions: {
            crudButtons: {
                onAdd: false,
                onDelete: false,
            }
        }
    })))

Alpine.data("modalPermisos", () => (
    modalFormComponent({
        page: "roles",
        elementName: "Rol",
    })))