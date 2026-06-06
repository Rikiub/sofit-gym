import { fetchApi } from "@/js/api.js";
import Alpine from "alpinejs";
import snarkdown from 'sharkdown';

Alpine.data('chatBot', () => ({
    messages: [],
    inputText: '',
    isTyping: false,

    init() {
        this.resetChat();
    },

    getTime() {
        return new Date().toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    async sendMessage() {
        const text = this.inputText.trim();
        if (!text || this.isTyping) return;

        // Agregar mensaje del usuario
        this.addMessage('user', text);
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

            const botReply = data.message || 'Lo siento, no obtuve una respuesta.';
            this.addMessage('bot', botReply);
        } catch (error) {
            console.error(error);
            this.addMessage('bot', '⚠️ Error al conectar con el asistente. Intenta de nuevo.');
        } finally {
            this.isTyping = false;
            this.$nextTick(() => {
                this.$refs.chatBody.scrollTop = this.$refs.chatBody.scrollHeight;
            });
        }
    },

    resetChat() {
        this.messages = [];
        this.addMessage("bot", '¡Hola! 👋 Soy tu **asistente virtual**. ¿En qué puedo ayudarte?');

        this.inputText = '';
        this.isTyping = false;
    },

    /** 
     * @param {"user"|"bot"} sender
     * @param {string} text
     */
    addMessage(sender, text) {
        const markdown = snarkdown(text);

        this.messages.push({
            sender: sender,
            text: markdown,
            time: this.getTime()
        });
    }
}));