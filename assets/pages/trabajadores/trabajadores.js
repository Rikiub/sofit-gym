import Alpine from "alpinejs";
import { modalFormComponent } from "@/components/modalForm.js";
import { crudTableComponent } from "@/components/crudTable.js";

const PAGE = "trabajadores";

Alpine.data("crudTable", () => (
    crudTableComponent({
        params: {
            page: PAGE,
            action: "query",
        },
        columns: [
            "Cedula",
            "Nombre",
            "Apellido",
            {
                name: "Salario",
                formatter: (cell, row) => `\$${cell}`,
            },
            "Rol",
        ],
    })));

Alpine.data("modalForm", () => (
    modalFormComponent({
        page: PAGE,
        editDisableFields: ["cedula", "fecha_contratacion"],
    })));