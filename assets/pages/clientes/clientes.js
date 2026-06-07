import { crudTableComponent } from "@/components/crudTable.js";
import { modalFormComponent } from "@/components/modalForm.js";
import { fetchApi } from "@/js/api.js";
import { h } from "gridjs";
import Alpine from "alpinejs";
import dayjs from "dayjs";

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
        columns: [
            {
                name: "Cedula",
                id: "cedula",
                formatter: (cell, row) => {
                    const cedula = row.cells[0].data;
                    return h(
                        "a",
                        { href: `?page=clientesItem&id=${cedula}` },
                        cedula,
                    );
                },
            },
            "Nombre",
            "Apellido",
            "Correo",
            "Telefono",
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
const clientesItemPage = "ClientesItem";

Alpine.data("clienteInfo", () => ({
    cliente: {},
    
    async init() {
        this.cliente = await fetchApi({
            page: "clientes",
            action: "find",
            id: new URLSearchParams(location.search).get("id"),
        });
    },

    setText(value) {
        return value ?? "Desconocido";
    },

    onlyDate(value) {
        if (!value) return value;
        return dayjs(value).format("DD/MM/YYYY");
    }
}));

// SEGUIMIENTO FISICO
const idSegFisico = "seg_fisico";

Alpine.data("crudSegFisico", () =>
    crudTableComponent({
        id: idSegFisico,
        params: {
            page: clientesItemPage,
            action: "getSegFisicoByCliente",
            id: new URLSearchParams(location.search).get("id")
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
        ],
        gridOptions: {
            search: false,
            crudButtons: {
                onEdit: null
            },
        },
    }));

Alpine.data("modalSegFisico", () => modalFormComponent({
    id: idSegFisico,
    page: clientesItemPage,
    actions: {
        onAdd: "insertSegFisico",
        onEdit: "updateSegFisico",
        onDelete: "deleteSegFisico",
    },
    prepareAddData: {
        fecha: new Date(),
    },
    extraPostBody: {
        cedula_cliente: new URLSearchParams(location.search).get("id"),
    }
}));

// SEGUIMIENTO NUTRICIONAL
const idSegNutricional = "seg_nutricional";

Alpine.data("crudSegNutricional", () =>
    crudTableComponent({
        id: idSegNutricional,
        params: {
            page: clientesItemPage,
            action: "getSegNutricionalByCliente",
            id: new URLSearchParams(location.search).get("id")
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
        ],
        gridOptions: {
            search: false,
            crudButtons: {
                onEdit: null
            },
        },
    }));

Alpine.data("modalSegNutricional", () => modalFormComponent({
    id: idSegNutricional,
    page: clientesItemPage,
    actions: {
        onAdd: "insertSegNutricional",
        onEdit: "updateSegNutricional",
        onDelete: "deleteSegNutricional",
    },
    prepareAddData: {
        fecha: new Date(),
    },
    extraPostBody: {
        cedula_cliente: new URLSearchParams(location.search).get("id"),
    }
}));
