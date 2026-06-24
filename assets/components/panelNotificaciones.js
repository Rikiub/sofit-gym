import Alpine from "alpinejs";
import dayjs from "dayjs";
import { fetchApi } from "@/js/api.js";

const ENDPOINT = "notificaciones";

Alpine.data("panelNotificaciones", () => ({
	notificaciones: [],
	unreadCount: 0,

	async init() {
		await this.refresh();

		// Refrescar notificaciones cada 1 minuto
		setInterval(async () => await this.refresh(), 60000);
	},

	// Cargar notificaciones desde el servidor
	async refresh() {
		const data = await fetchApi({
			page: ENDPOINT,
			action: "query",
		});

		this.notificaciones = data.map(notif => ({
            ...notif,
            expanded: false,
		}));
		this.unreadCount = data.filter((n) => !n.leido).length;

		this.notifyUpdate();
	},

	async notifyUpdate() {
		this.$dispatch("update-notifications", { unreaded: this.unreadCount });
	},

	// Marcar una notificación individual como leída
	async marcarLeida(id, leido) {
		await fetchApi({
			page: ENDPOINT,
			action: "leido",
			id: id,
			leido: !leido,
		});
		this.refresh();

		const notif = this.notificaciones.find((n) => n.id === id);
		if (notif) {
			notif.leido = true;
			this.unreadCount = this.notificaciones.filter((n) => !n.leido).length;
			this.notifyUpdate();
		}
	},

	// Marcar todas como leídas
	async marcarTodasLeidas() {
		await fetchApi({
			page: ENDPOINT,
			action: "leerTodas",
		});
		this.refresh();
		
		this.notificaciones.forEach((n) => (n.leido = true));
		this.unreadCount = 0;

		this.notifyUpdate();
	},

	// Agregar una notificación nueva
	async add() {
		await fetchApi({
			page: ENDPOINT,
			action: "sendMultiple",
		}, {
			method: "POST",
			body: {
				titulo: "Titulo",
				contenido: "Mensaje",
			}
		});
		await this.refresh();
	},

	humanDate(date) {
		return dayjs(date).format("HH:mm DD/MM/YYYY");
	}
}));