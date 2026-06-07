import Alpine from "alpinejs";
import { fetchApi } from "@/js/api.js";
import { crudTableComponent } from "@/components/crudTable.js";
import { modalFormComponent } from "@/components/modalForm.js";

Alpine.data("crudEquipos", () => (
    crudTableComponent({
        params: {
            page: "equipos",
            action: "query",
        },
        columns: [
            "Codigo",
            "Nombre",
            "Tipo",
            "Estado",
            "Ubicación",
        ],
    })))

Alpine.data("modalEquipos", () => (
    modalFormComponent({
        page: "equipos",
        elementName: "Equipo",
        editDisableFields: ["codigo"],
    })))