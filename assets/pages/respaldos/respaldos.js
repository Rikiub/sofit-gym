import Alpine from "alpinejs";
import dayjs from "dayjs";
import { fetchApi } from "@/js/api.js";
import { crudTableComponent } from "@/components/crudTable.js";

const PAGE = "respaldos";

Alpine.data("crudRespaldos", function() {
    return {
        ...crudTableComponent({
            params: {
                page: PAGE,
                action: "query",
            },
            columns: [
                { id: "timestamp", hidden: true },
                {
                    id: "datetime",
                    name: "Fecha",
                    formatter: (cell) => dayjs(cell).format("DD/MM/YYYY H:mm"),
                },
                { id: "total_size_human", name: "Tamaño" },
            ],
            gridOptions: {
                crudButtons: {
                    onView: null,
                    onAdd: null,
                    onEdit: null,
                    onDelete: null,
                }
            }
        }),
    }
});

Alpine.data("respaldos", () => ({
    data: {},

    modal: null,
    loading: false,

    init() {
        this.modal = bootstrap.Modal.getOrCreateInstance(this.$refs.modal);
    },
    openModal(timestamp, datetime) {
        const date = dayjs(datetime);
        this.data = {
            timestamp,
            datetime: date.format("DD/MM/YYYY HH:mm"),
            datetimeHuman: new Date(date).toLocaleDateString('es-ES', { dateStyle: 'full' }),
        };

        this.modal.show();
    },
    closeModal() {
        this.modal.hide();
    },

    async handleBackup() {
        this.loading = true;

        try {
            await fetchApi({
                page: PAGE,
                action: "backup",
            });
            this.$dispatch("form-success");
        } finally {
            this.loading = false;
        }
    },

    async handleRestore() {
        this.loading = true;

        try {
            
        } finally {
            this.loading = false;
        }
        
        this.closeModal();
    },
}));