<?php
$title = "Inicio de Sesión - SOFIT GYM";
$this->layout("layout", ["title" => $title, "sidebar" => false]);
?>

<script type="module">
    import Alpine from "alpinejs";
    import {
        fetchApi
    } from "@/js/api.js";
    import FormDataJson from "form-data-json";

    Alpine.data("login", () => ({
        timeout: null,

        error: "",
        success: "",
        step: 'login',
        showPassword: false,
        showNewPassword: false,
        showRepeatPassword: false,

        email: '',
        codigo: '',
        new_pass: '',
        repeat_pass: '',

        resetFields() {
            this.email = '';
            this.codigo = '';
            this.new_pass = '';
            this.repeat_pass = '';
            this.error = '';
            this.success = '';
        },

        async handleSubmit() {
            try {
                clearTimeout(this.timeout);

                const data = await fetchApi({
                    page: "login",
                    action: "login"
                }, {
                    method: "POST",
                    body: FormDataJson.toJson(this.$refs.form),
                });

                if (data?.redirect) {
                    self.location.href = data.redirect;
                }
            } catch (e) {
                this.error = e.cause.message;
                this.timeout = setTimeout(() => {
                    this.error = "";
                }, 5000);
            }
        },

        async handleRecover() {
            try {
                clearTimeout(this.timeout);

                await fetchApi({
                    page: "login",
                    action: "recover"
                }, {
                    method: "POST",
                    body: {
                        email: this.email
                    }
                });
                this.step = 'verify';
                this.error = "";

                // Mostramos el mensaje y lo quitamos a los 5 segundos
                this.success = "Código enviado a tu correo";
                this.timeout = setTimeout(() => {
                    this.success = "";
                }, 5000);
            } catch (e) {
                this.error = e.cause.message;
            }
        },

        async handleVerify() {
            try {
                await fetchApi({
                    page: "login",
                    action: "verify"
                }, {
                    method: "POST",
                    body: {
                        codigo: this.codigo.trim()
                    }
                });
                this.step = 'newpassword';
                this.error = "";
            } catch (e) {
                this.error = e.cause.message;
            }
        },

        async handleReset() {
            if (this.new_pass !== this.repeat_pass) {
                this.error = "Las contraseñas no coinciden";
                return;
            }
            try {
                await fetchApi({
                    page: "login",
                    action: "reset"
                }, {
                    method: "POST",
                    body: {
                        new_pass: this.new_pass
                    }
                });

                this.success = "Contraseña cambiada con éxito";

                setTimeout(() => {
                    this.resetFields();
                    this.success = "";
                    this.step = 'login';
                }, 2000);

            } catch (e) {
                this.error = e.cause.message;
            }
        }
    }));
</script>

<div class="split-login-wrapper" x-data="login">
    <div class="login-left-panel animate-slide-left">
        <h1 class="welcome-title">
            <span class="text-white">Bienvenido a</span><br>
            <span class="text-red">SOFIT GYM</span>
        </h1>

        <p class="welcome-text">
            Es un hecho establecido que tu transformación comienza con el primer paso. Únete a nuestra comunidad para alcanzar tus metas físicas con las mejores instalaciones y profesionales. El punto de partida de tu nueva versión.
        </p>

        <div class="social-icons">
            <a href="https://maps.app.goo.gl/gbfknAb8R1k7QKDB8?g_st=aw" target="_blank" rel="noopener noreferrer" aria-label="Ubicación">
                <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z" />
                </svg>
            </a>

            <a href="https://www.instagram.com/sofitgymoficial" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M12 2.16c3.2 0 3.58.01 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.64.07-4.85.07s-3.58-.01-4.85-.07c-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.64-.07-4.85s.01-3.58.07-4.85C2.38 3.86 3.9 2.32 7.15 2.17 8.42 2.11 8.8 2.16 12 2.16zM12 0C8.74 0 8.33.01 7.05.07c-4.27.2-6.78 2.71-6.98 6.98C0 8.33 0 8.74 0 12s.01 3.67.07 4.95c.2 4.27 2.71 6.78 6.98 6.98 1.28.06 1.69.07 4.95.07s3.67-.01 4.95-.07c4.27-.2 6.78-2.71 6.98-6.98.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.2-4.27-2.71-6.78-6.98-6.98C15.67.01 15.26 0 12 0zm0 5.84A6.16 6.16 0 1 0 18.16 12 6.16 6.16 0 0 0 12 5.84zm0 10.16A4 4 0 1 1 16 12a4 4 0 0 1-4 4zm6.4-11.44a1.44 1.44 0 1 1-2.88 0 1.44 1.44 0 0 1 2.88 0z" />
                </svg>
            </a>

            <a href="https://www.tiktok.com/@sofitgymoficial?_r=1&_t=ZS-971ecqHf3gK" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.77 0 2.89 2.89 0 0 1 2.89-2.89h.68V9.32h-.68a6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V6.69z" />
                </svg>
            </a>
        </div>
    </div>

    <div class="login-right-panel">
        <div class="login-card-frame animate-slide-right">
            <div class="logo-container animate-fade-in-up" style="animation-delay: 0.8s;">
                <h1 class="logo-text-only">SOFIT GYM</h1>
            </div>

            <div x-show="error" class="error-message" x-text="error" style="display: none;"></div>
            <div x-show="success" class="success-message" x-text="success" style="display: none;"></div>

            <div x-show="step === 'login'">
                <h2 class="form-title text-white">Iniciar Sesión</h2>

                <form class="login-form" x-ref="form" @submit.prevent="handleSubmit">
                    <div class="form-field">
                        <label class="text-white">Usuario</label>
                        <input type="text" name="nombre_usuario" autocomplete="username" required>
                    </div>

                    <div class="form-field">
                        <label class="text-white">Contraseña</label>

                        <div class="password-input-wrapper">
                            <input :type="showPassword ? 'text' : 'password'" name="contrasena" autocomplete="current-password" required>
                            <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                                <svg x-show="!showPassword" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg x-show="showPassword" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Iniciar Sesión</button>

                    <div class="lost-password">
                        <a href="#" @click.prevent="step = 'recover'; resetFields();" class="text-red">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>

            <div x-show="step === 'recover'" style="display: none;">
                <h2 class="form-title text-white">Recuperar contraseña</h2>

                <form @submit.prevent="handleRecover()">
                    <div class="form-field">
                        <label class="text-white">Correo electrónico</label>
                        <input type="email" x-model="email" required placeholder="Ingresa tu correo">
                    </div>

                    <button type="submit" class="btn-submit">Enviar código</button>
                    <div class="lost-password"><a href="#" @click.prevent="step = 'login'; resetFields();" class="text-red">Volver al login</a></div>
                </form>
            </div>

            <div x-show="step === 'verify'" style="display: none;">
                <h2 class="form-title text-white">Verificar código</h2>

                <form @submit.prevent="handleVerify()">
                    <div class="form-field">
                        <label class="text-white">Código de 8 dígitos</label>

                        <div x-data="{ text: '' }">
                            <input
                                class="fs-3 fw-bold text-center"
                                style="letter-spacing: 0.1em;"
                                type="text"
                                minlength="9"
                                maxlength="9"
                                @input="text = $event.target.value.toUpperCase()"
                                :value="text"
                                x-model="codigo"
                                x-mask="****-****"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Verificar código</button>
                </form>
            </div>

            <div x-show="step === 'newpassword'" style="display: none;">
                <h2 class="form-title text-white">Nueva contraseña</h2>

                <form @submit.prevent="handleReset()">
                    <div class="form-field">
                        <label class="text-white">Nueva contraseña</label>

                        <div class="password-input-wrapper">
                            <input :type="showNewPassword ? 'text' : 'password'" x-model="new_pass" required>

                            <button type="button" class="toggle-password" @click="showNewPassword = !showNewPassword">
                                <svg x-show="!showNewPassword" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>

                                <svg x-show="showNewPassword" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="text-white">Repetir contraseña</label>

                        <div class="password-input-wrapper">
                            <input :type="showRepeatPassword ? 'text' : 'password'" x-model="repeat_pass" required>
                            <button type="button" class="toggle-password" @click="showRepeatPassword = !showRepeatPassword">
                                <svg x-show="!showRepeatPassword" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>

                                <svg x-show="showRepeatPassword" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Cambiar contraseña</button>
                </form>
            </div>

            <div x-show="step === 'login'" class="terms-text text-white animate-fade-in-up" style="animation-delay: 1.5s;">
                Al iniciar sesión aceptas los <br>
                <a href="#" class="text-red">Términos de Servicio</a> | <a href="#" class="text-red">Política de Privacidad</a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos base */
    body,
    html {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background-color: #1a1a1a;
        overflow-x: hidden;
    }

    .logo-text-only {
        color: #d11a2a;
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
        text-align: center;
    }

    .password-input-wrapper {
        position: relative;
        width: 100%;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #555;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toggle-password:hover {
        color: #d11a2a;
    }

    /* Animaciones */
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-left {
        opacity: 0;
        animation: slideInLeft 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    .animate-slide-right {
        opacity: 0;
        animation: slideInRight 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        animation-delay: 0.5s;
    }

    .animate-fade-in-up {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }

    /* Textos */
    .text-white {
        color: #ffffff !important;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
    }

    .text-red {
        color: #d11a2a !important;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
    }

    /* Layout */
    .split-login-wrapper {
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6)),
            url('<?= ASSETS_DIR ?>/pages/login/background.jpg');
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;

        display: flex;
        min-height: 100vh;
        width: 100%;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: -1rem;
        width: calc(100% + 2rem);
    }

    .social-icons {
        display: flex;
        gap: 1.2rem;
        position: relative;
        z-index: 99;
    }

    .social-icons a {
        color: #ffffff;
        transition: all 0.3s ease;
        filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.8));
    }

    .social-icons a:hover {
        color: #d11a2a;
        transform: translateY(-4px) scale(1.1);
    }

    .login-left-panel {
        flex: 1.2;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 8%;
    }

    .welcome-title {
        font-size: 4.5rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 1.5rem;
    }

    .welcome-text {
        font-size: 1.1rem;
        line-height: 1.6;
        max-width: 480px;
        margin-bottom: 2.5rem;
        color: #ffffff;
        text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.9);
    }

    .login-right-panel {
        flex: 0.8;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 8%;
        background-color: transparent;
    }

    .login-card-frame {
        width: 100%;
        max-width: 420px;
        background: rgba(15, 15, 15, 0.65);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 2.5rem 2.5rem;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-top: 4px solid #d11a2a;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
    }

    .logo-container {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .form-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .login-form {
        width: 100%;
    }

    .form-field {
        margin-bottom: 1.5rem;
    }

    .form-field label {
        display: block;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* Inputs */
    .form-field input[type="text"],
    .form-field input[type="password"],
    .form-field input[type="email"],
    .form-field input[type="text"][name="codigo"] {
        width: 100%;
        padding: 14px 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        font-size: 1rem;
        background-color: rgba(0, 0, 0, 0.3);
        color: #ffffff;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    .form-field input:focus {
        outline: none;
        background-color: rgba(0, 0, 0, 0.5);
        border-color: #d11a2a;
        box-shadow: 0 0 0 4px rgba(209, 26, 42, 0.25);
        transform: translateY(-2px);
        color: #ffffff;
    }

    .btn-submit {
        background-color: #d11a2a;
        color: #ffffff;
        border: none;
        padding: 14px 25px;
        font-size: 1.05rem;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        display: block;
        width: 100%;
        box-shadow: 0 4px 10px rgba(209, 26, 42, 0.3);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-submit:hover {
        background-color: #b01522;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(209, 26, 42, 0.4);
    }

    .lost-password {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .lost-password a {
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .lost-password a:hover {
        text-decoration: underline;
        color: #ffffff !important;
    }

    .terms-text {
        font-size: 0.85rem;
        line-height: 1.6;
        text-align: center;
        font-weight: 500;
        margin-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1.5rem;
    }

    .terms-text a {
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .terms-text a:hover {
        text-decoration: underline;
        color: #ffffff !important;
    }

    .error-message {
        background-color: rgba(209, 26, 42, 0.2);
        color: #fff;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        text-align: center;
        border: 1px solid #d11a2a;
        font-weight: 600;
    }

    .success-message {
        background-color: rgba(40, 167, 69, 0.2);
        color: #fff;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        text-align: center;
        border: 1px solid #28a745;
        font-weight: 600;
    }

    @media (max-width: 850px) {
        .split-login-wrapper {
            flex-direction: column;
            background-position: top center;
        }

        .login-left-panel,
        .login-right-panel {
            padding: 2.5rem 1.5rem;
            max-width: 100%;
        }

        .login-left-panel {
            flex: auto;
            text-align: center;
        }

        .welcome-title {
            font-size: 3rem;
        }

        .welcome-text {
            margin: 0 auto 2rem auto;
        }

        .social-icons {
            justify-content: center;
        }

        .login-card-frame {
            padding: 2rem 1.5rem;
        }
    }
</style>