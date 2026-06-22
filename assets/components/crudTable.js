import { createGrid } from "@/js/grid.js";
import { openModal } from "@/components/modalForm.js";
import Alpine from "alpinejs";

/**
 * @param {{
 * params: object,
 * columns: array<string|object>,
 * fieldMap?: (item: object) => array<string|number>,
 * gridOptions?: Object?,
 * id?: string,
 * }}
 */
export function crudTableComponent({
    params,
    columns,
    fieldMap = (item) => item,
    gridOptions = {},
    id: componentId = null,
} = {}) {
    return {
        grid: null,

        init() {
            const {
                crudButtons = {
                    onView: null 
                },
                ...restOptions
            } = gridOptions;

            const query = new URLSearchParams(params);
            const url = `?${query.toString()}`;

            this.grid = createGrid({
                columns: columns,
                server: {
                    url,
                    then: (data) => data.map((item) => item),
                },
                crud: {
                    onAdd: () => {
                        openModal(this, {
                            id: componentId,
                            mode: "add"
                        });
                    },
                    onView: (dataId) => {
                        openModal(this, {
                            id: componentId,
                            dataId,
                            mode: "view",
                        });
                    },
                    onEdit: (dataId) => {
                        openModal(this, {
                            id: componentId,
                            dataId,
                            mode: "edit",
                        });
                    },
                    onDelete: (dataId) => {
                        openModal(this, {
                            id: componentId,
                            dataId,
                            mode: "delete",
                        });
                    },
                    ...crudButtons
                },
                ...restOptions,
            });
            this.grid.render(this.$refs.table);
        },

        handleFormSuccess({ id = null, action = null }) {
            if (id !== componentId) return;
            this.refreshGrid();
        },

        refreshGrid() {
            this.grid.forceRender();
        },
    };
}

Alpine.data("crudTable", crudTableComponent);
