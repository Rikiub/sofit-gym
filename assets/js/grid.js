import { Grid, h, PluginPosition } from "gridjs";

/** Crea una tabla de datos adaptada para CRUDs
 * @param {object} options
 * @returns {Grid}
 */
export function createGrid(options = {}) {
    if (options.crud?.onEdit || options.crud?.onDelete) {
        options.columns?.push(
            crudButtons(options.crud.onEdit, options.crud.onDelete),
        );
    }

    const grid = new Grid({
        language: {
            search: {
                placeholder: "Buscar...",
            },
            sort: {
                sortAsc: "Sortear columna ascendentemente",
                sortDesc: "Sortear columna descendentemente",
            },
            pagination: {
                previous: "Anterior",
                next: "Siguiente",
                navigate: (page, pages) => `Pagina ${page} de ${pages}`,
                page: (page) => `Pagina ${page}`,
                showing: "Mostrando",
                results: "Resultados",
                of: "de",
                to: "a",
            },
            loading: "Cargando...",
            noRecordsFound: "Sin resultados",
            error: "Un error ha ocurrido mientras se obtenian los datos",
        },
        sort: true,
        search: {
            debounceTimeout: 1000,
        },
        pagination: {
            limit: 25,
            summary: false,
        },
        className: {
            table: "table table-striped",
            paginationButton: "btn",
            paginationButtonNext: "btn",
            paginationButtonCurrent: "btn",
            paginationButtonPrev: "btn",
        },
        ...options,
    });

    if (options.crud?.onAdd) {
        grid.plugin.add({
            id: "add",
            component: () => addButton(options.crud.onAdd),
            position: PluginPosition.Header,
        });
    }

    return grid;
}

function addButton(callback) {
    return h("button", {
        className: "btn btn-primary d-flex align-items-center gap-1",
        "title": "Registrar",
        onClick: callback,
    }, [
        h("i", { className: "fa-solid fa-square-plus" }),
        "Registrar"
    ]);
}

export function crudButtons(onEdit, onDelete) {
    return {
        name: "Acciones",
        width: "150px",
        sort: false,
        data: () => null,
        formatter: (cell, row) => {
            return h("div", { className: "crud-actions d-flex gap-1" }, [
                onEdit
                    ? h("button", {
                        className: "btn btn-sm btn-warning d-flex align-items-center",
                        "title": "Editar",
                        onClick: () => onEdit(row.cells[0].data),
                    }, [
                        h("i", { className: "fa-solid fa-pen-to-square" }),
                        "Editar",
                    ])
                    : "",
                onDelete
                    ? h("button", {
                        className: "btn btn-sm btn-danger d-flex align-items-center",
                        "title": "Eliminar",
                        onClick: () => onDelete(row.cells[0].data),
                    }, [
                        h("i", { className: "fa-solid fa-trash-can" }),
                        "Eliminar"
                    ])
                    : "",
            ]);
        },
    };
}
