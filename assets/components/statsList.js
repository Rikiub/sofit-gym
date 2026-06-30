import { fetchApi } from "@/js/api.js";
import Alpine from "alpinejs";

Alpine.data("stat", ({
    page,
    action,
    valueKey,
}) => ({
    data: {},
    valueKey,

    async refresh() {
        this.data = await fetchApi({ page, action });
    },
}));