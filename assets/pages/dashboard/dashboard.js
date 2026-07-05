import Alpine from "alpinejs";
import { fetchApi } from "@/js/api.js";
import dayjs from "dayjs";

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

Alpine.data("facturacionChart", () => ({
    async init() {
        const ctx = this.$refs.canvas.getContext("2d");
        const data = await fetchApi({
            page: "facturacion",
            action: "resumen_semana",
        });

        // Data from backend: { total_membresias, total_ventas, total_general }
        const labels = ['Membresías', 'Ventas', 'General'];
        const values = [data.total_membresias, data.total_ventas, data.total_general];

        new Chart(ctx, {
            type: 'bar', // or 'doughnut' if you prefer
            data: {
                labels: labels,
                datasets: [{
                    label: 'Totales',
                    data: values,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.6)', // blue
                        'rgba(16, 185, 129, 0.6)', // green
                        'rgba(239, 68, 68, 0.6)'   // red
                    ],
                    borderColor: [
                        '#3b82f6',
                        '#10b981',
                        '#ef4444'
                    ],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { labels: { color: '#1e293b' } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#1e293b' }
                    },
                    x: {
                        ticks: { color: '#1e293b' }
                    }
                }
            }
        });
    },
}));