import { fetchApi } from "@/js/api.js";
import Alpine from "alpinejs";

Alpine.data("stat", ({ params }) => ({
    data: {},

    async refresh() {
        this.data = await fetchApi(params);
    },
}));