import { fetchApi } from "@/js/api.js";
import Alpine from "alpinejs";
import snarkdown from 'sharkdown';
import dayjs from "dayjs";

const PROMPTS = {
    bienvenida: '¡Hola! 👋 Soy tu **asistente virtual**. ¿En qué puedo ayudarte?',
    sinRespuesta: 'Lo siento, no obtuve una respuesta.',
    error: '⚠️ Error al conectar con el asistente. Intenta de nuevo.',
}

Alpine.data('chatBot', () => ({
    sesion: null,
    mensajes: [],
    inputText: '',
    isTyping: false,

    async init() {
        const sesiones = await fetchApi({
            page: "asistente",
            action: "querySesiones",
        });

        this.sesion = await fetchApi({
            page: "asistente",
            action: "findSesion",
            id: sesiones.at(-1).id_sesion,
        });
        for (const msg of this.sesion.mensajes) {
            if (msg.rol === "herramienta") return;
            this.addMessage(msg.rol, msg.contenido, msg.fecha_creacion);
        }

        if (this.mensajes.length === 0) this.resetChat();
    },

    async sendMessage() {
        const text = this.inputText.trim();
        if (!text || this.isTyping) return;

        // Agregar mensaje del usuario
        this.addMessage('usuario', text);
        this.inputText = '';

        // Scroll al final
        this.$nextTick(() => {
            this.$refs.chatBody.scrollTop = this.$refs.chatBody.scrollHeight;
        });

        // Activar indicador de escritura
        this.isTyping = true;

        try {
            // Conectar con backend
            const data = await fetchApi({
                page: "asistente",
                action: "generateText",
            }, {
                method: "POST",
                body: {
                    message: text
                }
            });

            const botReply = data.message || PROMPTS.sinRespuesta;
            this.addMessage('asistente', botReply);
        } catch (error) {
            console.error(error);
            this.addMessage('asistente', PROMPTS.error);
        } finally {
            this.isTyping = false;
            this.$nextTick(() => {
                this.$refs.chatBody.scrollTop = this.$refs.chatBody.scrollHeight;
            });
        }
    },

    async newChat() {
        this.sesion = await fetchApi({
            page: "asistente",
            action: "newSesion",
        });
        this.resetChat();
    },

    resetChat() {
        this.mensajes = [];
        this.addMessage('sistema', PROMPTS.bienvenida);

        this.inputText = '';
        this.isTyping = false;
    },

    /** 
     * @param {"sistema"|"asistente"|"usuario"} rol
     * @param {string} contenido
     * @param {string} fecha_creacion
     */
    addMessage(rol, contenido, fecha_creacion = new Date()) {
        const markdown = snarkdown(contenido);
        const tiempo = dayjs(fecha_creacion).format("HH:mm");

        this.mensajes.push({
            rol: rol,
            contenido: markdown,
            tiempo: tiempo,
        });
    },
}));