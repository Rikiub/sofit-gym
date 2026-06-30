import { crudTableComponent } from "@/components/crudTable.js";
import { modalFormComponent } from "@/components/modalForm.js";
import { fetchApi } from "@/js/api.js";
import { h } from "gridjs";
import Alpine from "alpinejs";
import dayjs from "dayjs";

/** @returns {number} */
function getDiasRestantes(fecha_inicio, fecha_fin) {
    fecha_inicio = dayjs(fecha_inicio);
    fecha_fin = dayjs(fecha_fin);

    const diasRestantes = fecha_fin.diff(fecha_inicio, "day");
    return diasRestantes;
}

// CLIENTES
const CLIENTES_PAGE = "clientes";
const clientesId = "clientes";

Alpine.data("crudClientes", () =>
    crudTableComponent({
        id: clientesId,
        params: {
            page: CLIENTES_PAGE,
            action: "query",
        },
        gridOptions: {
            search: {
                ignoreHiddenColumns: false,
            }
        },
        columns: [
            {
                name: "Cedula",
                id: "cedula",
                formatter: (cell, row) => {
                    const cedula = row.cells[0].data;
                    return h(
                        "a",
                        { href: `?page=clienteInfo&cedula=${cedula}` },
                        cedula,
                    );
                },
            },
            "Nombre",
            "Apellido",
            { id: "telefono", hidden: true },
            { id: "correo", hidden: true },
            {
                id: "membresia",
                name: "Vencimiento",
                formatter: (cell, row) => {
                    const membresia = cell;

                    if (membresia?.fecha_inicio) {
                        const diasRestantes = getDiasRestantes(
                            dayjs(),
                            membresia.fecha_fin
                        );
                        return h("span", {}, `${diasRestantes} dias restantes`);
                    } else {
                        return h("span", {}, "Sin membresia");
                    }
                },
            }
        ],
    }));

Alpine.data("modalClientes", () => ({
    ...modalFormComponent({
        id: clientesId,
        page: CLIENTES_PAGE,
        elementName: "Cliente",
        prepareAddData: {
            membresia: {
                fecha_inicio: new Date()
            },
        },
        editDisableFields: ["cedula"],
    }),

    /** @param {HTMLInputElement} input */
    async validateCedula(input) {
        this.checkValidity(input);

        if (this.mode === "add") {
            let cliente = null;

            try {
                cliente = await fetchApi({
                    page: this.page,
                    action: this.actions.onEditFind,
                    id: input.value,
                });
            } catch {}

            if (cliente) {
                this.setInputValidity(input, false, "El cliente ya existe");
            }
        }
    },
}));

// CLIENTES ITEM
const clienteInfoPage = "clienteInfo";

Alpine.data("clienteInfo", () => ({
    cliente: {},
    
    async init() {
        this.cliente = await fetchApi({
            page: "clientes",
            action: "find",
            id: new URLSearchParams(location.search).get("cedula"),
        });
    },

    setText(value) {
        return value ?? "Desconocido";
    },

    onlyDate(value) {
        if (!value) return value;
        return dayjs(value).format("DD/MM/YYYY");
    },

    diasRestantes() {
        const membresia = this.cliente.membresia;

        if (membresia.fecha_inicio) {
            return getDiasRestantes(
                dayjs(),
                membresia.fecha_fin
            );
        } else {
            return "";
        }
    }
}));

// SEGUIMIENTO FISICO
const idSegFisico = "fisico";

Alpine.data("crudFisico", () =>
    crudTableComponent({
        id: idSegFisico,
        params: {
            page: clienteInfoPage,
            action: "queryFisico",
            cedula: new URLSearchParams(location.search).get("cedula")
        },
        columns: [
            {
                id: "id_seguimiento",
                hidden: true,
            },
            {
                id: "cedula_cliente",
                hidden: true,
            },
            {
                name: "Fecha",
                formatter: (cell) => dayjs(cell).format("DD/MM/YYYY")
            },
            { name: "Altura", id: "altura_cm" },
            { name: "Peso", id: "peso_kg" },
            { name: "Cintura", id: "cintura_cm" },
            { name: "Cadera", id: "cadera_cm" },
            { name: "Pecho", id: "pecho_cm" },
            { name: "Muslo", id: "muslo_cm" },
            { name: "Hombros", id: "hombros_cm" },
            { name: "Registrado por", id: "registrado_por" },
        ],
        gridOptions: {
            search: false,
            crudButtons: {
                onEdit: false,
                onView: false,
            },
        },
    }));

Alpine.data("modalFisico", () => modalFormComponent({
    id: idSegFisico,
    page: clienteInfoPage,
    transformParams: (currentId) => ({
        id_seguimiento: currentId,
        cedula: new URLSearchParams(location.href).get("cedula"),
    }),
    actions: {
        onAdd: "insertFisico",
        onEdit: "updateFisico",
        onDelete: "deleteFisico",
    },
    elementName: "Seguimiento Fisico",
    prepareAddData: {
        fecha: new Date(),
    },
}));

// SEGUIMIENTO NUTRICIONAL
const idNutricion = "nutricion";

Alpine.data("crudNutricion", () =>
    crudTableComponent({
        id: idNutricion,
        params: {
            page: clienteInfoPage,
            action: "queryNutricion",
            cedula: new URLSearchParams(location.search).get("cedula")
        },
        columns: [
            {
                id: "id_seguimiento",
                hidden: true,
            },
            {
                id: "cedula_cliente",
                hidden: true,
            },
            {
                name: "Fecha",
                formatter: (cell) => dayjs(cell).format("DD/MM/YYYY")
            },
            { name: "Proteinas (g)", id: "proteinas_g" },
            { name: "Carbohidratos (g)", id: "carbohidratos_g" },
            { name: "Grasas (g)", id: "grasas_g" },
            { name: "Calorias Diarias", id: "calorias_diarias" },
            { name: "Registrado por", id: "registrado_por" },
        ],
        gridOptions: {
            search: false,
            crudButtons: {
                onEdit: false,
                onView: false,
            },
        },
    }));

Alpine.data("modalNutricion", () => modalFormComponent({
    id: idNutricion,
    page: clienteInfoPage,
    transformParams: (currentId) => ({
        id_seguimiento: currentId,
        cedula: new URLSearchParams(location.href).get("cedula"),
    }),
    actions: {
        onAdd: "insertNutricion",
        onEdit: "updateNutricion",
        onDelete: "deleteNutricion",
    },
    elementName: "Seguimiento Nutricional",
    prepareAddData: {
        fecha: new Date(),
    },
}));
