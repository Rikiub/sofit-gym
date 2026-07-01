import Alpine from "alpinejs";
import { fetchApi } from "@/js/api.js";

Alpine.data("menu", (id) => ({
    show: false,
    usuario: null,

    async init() {
        this.refresh();
    },

    async refresh() {
        this.usuario = await fetchApi({
            page: "usuarios",
            action: "find",
            id: id,
        })
    }
}));