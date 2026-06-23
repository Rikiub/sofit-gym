import Alpine from "alpinejs";
import { fetchApi } from "@/js/api.js";
import dayjs from "dayjs";

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

Alpine.data("asistenciasChart", () => ({
    async init() {
        const ctx = this.$refs.canvas.getContext("2d");
        const data = await fetchApi({
            page: "asistencia",
            action: "obtener_totales",
            inicio: dayjs().subtract(6, 'day').format('YYYY-MM-DD'),
            fin: dayjs().format('YYYY-MM-DD'),
        });
        
        const labels = data.map(row => dayjs(row.dia).format("DD"));
        const values = data.map(row => row.total_asistencias);

        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Asistencias",
                    data: values,
                    borderColor: "#dc2626",
                    backgroundColor: "rgba(220,38,38,0.1)",
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { labels: { color: "#1e293b" } } },
                scales: {
                    y: { ticks: { color: "#1e293b" } },
                    x: { ticks: { color: "#1e293b" } },
                },
            },
        });
    },
}));
