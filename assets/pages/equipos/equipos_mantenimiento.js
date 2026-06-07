import Alpine from "alpinejs";
import { fetchApi } from "@/js/api.js";
import { crudTableComponent } from "@/components/crudTable.js";
import { modalFormComponent } from "@/components/modalForm.js";

const PAGE_EQUIPOS = "equipos";

Alpine.data("mainData", () => ({
    equipos: {},

    async init() {
        this.equipos = await fetchApi({
            page: PAGE_EQUIPOS,
            action: "query",
        });
    }
}));

const PAGE_MANTENIMIENTO = "equiposMantenimiento";

Alpine.data("crudMantenimiento", () => (
    crudTableComponent({
        params: {
            page: PAGE_MANTENIMIENTO,
            action: "query",
        },
        columns: [
            { name: "id", hidden: true },
            { name: "Codigo Equipo",  id: "codigo_equipo" },
            {
                name: "Fecha",
                formatter: (cell) => new Date(cell).toLocaleDateString("en-US")
            },
            "Tipo",
            { name: "Descripción", id: "descripcion" },
            { name: "Costo", formatter: (cell) => cell ? `\$${cell}` : ""},
            "Tecnico",
        ],
    })))

Alpine.data("modalMantenimiento", () => (
    modalFormComponent({
        page: PAGE_MANTENIMIENTO,
        elementName: "Mantenimiento",
        prepareAddData: {
            fecha: new Date(),
        },
    })))