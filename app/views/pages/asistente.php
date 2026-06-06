<?php
$this->pushJs("pages/asistente/asistente.js");
$this->layout('layout', ['title' => 'Asistente']);
?>

<!-- Contenido principal del chatbot -->
<div x-data="chatBot" class="d-flex justify-content-center align-items-center p-3 p-md-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="width: 100%; height: 600px;">

        <!-- Header -->
        <div class="card-header text-white d-flex align-items-center gap-3 px-3 py-2 border-0" style="background-color: var(--primary-bg);">
            <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="fa-solid fa-robot fs-5"></i>
            </div>

            <div class="flex-grow-1">
                <h6 class="mb-0 fw-semibold">Asistente Virtual</h6>
                <small class="opacity-75"><span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px;"></span> En línea</small>
            </div>

            <button class="btn btn-sm text-white bg-white bg-opacity-10 border-0 rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" @click="resetChat()" title="Reiniciar chat">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>

        <!-- Cuerpo de mensajes -->
        <div class="card-body overflow-auto p-3" style="background-color: #f8f9fa;" x-ref="chatBody">
            <template x-for="(msg, index) in messages" :key="index">
                <div class="d-flex mb-3" :class="msg.sender === 'user' ? 'justify-content-end' : 'justify-content-start'">
                    <div class="d-flex gap-2" :class="msg.sender === 'user' ? 'flex-row-reverse' : ''">
                        <!-- Avatar pequeño -->
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            :class="msg.sender === 'user' ? 'bg-primary' : 'bg-secondary'"
                            style="width: 30px; height: 30px; font-size: 0.75rem;">
                            <i class="fa-solid" :class="msg.sender === 'user' ? 'fa-user' : 'fa-robot'" style="color: white;"></i>
                        </div>

                        <!-- Burbuja -->
                        <div>
                            <div class="p-2 px-3 rounded-4 shadow-sm"
                                :class="msg.sender === 'user' ? 'bg-primary text-white rounded-bottom-end-0' : 'bg-white text-dark rounded-bottom-start-0'"
                                style="max-width: 600px; word-break: break-word;">
                                <span x-html="msg.text"></span>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;" x-text="msg.time"></small>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Indicador de escritura -->
            <div x-show="isTyping" class="d-flex align-items-center gap-2 mb-3">
                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.75rem; color: white;">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white rounded-pill px-3 py-2 shadow-sm d-flex gap-1">
                    <span class="d-inline-block rounded-circle bg-secondary opacity-75" style="width: 7px; height: 7px; animation: bounce 1.4s infinite ease-in-out both;"></span>
                    <span class="d-inline-block rounded-circle bg-secondary opacity-75" style="width: 7px; height: 7px; animation: bounce 1.4s infinite ease-in-out both; animation-delay: 0.16s;"></span>
                    <span class="d-inline-block rounded-circle bg-secondary opacity-75" style="width: 7px; height: 7px; animation: bounce 1.4s infinite ease-in-out both; animation-delay: 0.32s;"></span>
                </div>
            </div>
        </div>

        <!-- Footer con input -->
        <div class="card-footer bg-white border-top p-3">
            <div class="input-group rounded-pill bg-light p-1">
                <input type="text" class="form-control border-0 bg-transparent shadow-none rounded-pill px-3"
                    placeholder="Escribe un mensaje..."
                    x-model="inputText"
                    @keyup.enter="sendMessage()"
                    :disabled="isTyping"
                    autocomplete="off">
                <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 42px; height: 42px;"
                    @click="sendMessage()"
                    :disabled="isTyping || inputText.trim() === ''">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animación simple para los puntos del indicador de escritura */
    @keyframes bounce {

        0%,
        80%,
        100% {
            transform: scale(0.6);
            opacity: 0.4;
        }

        40% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>