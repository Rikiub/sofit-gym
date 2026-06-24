import { fetchApi } from "@/js/api.js";
import { populateForm } from "@/js/form.js";
import FormDataJson from "form-data-json";
import Alpine from "alpinejs";

/** Remover keys vacias de un objeto
 * @return {Object}
 */
function clearAsArray(obj) {
    return Object.entries(obj).filter(([_, value]) => value !== "" && value !== null && value !== undefined);
}

/**
 * @param {Object} data
 * @param {{
 * mode: "add"|"edit"|"delete",
 * dataId: string|number|null,
 * id: string|number|null,
 * }} detail
 */
export function openModal(data, detail) {
    data.$dispatch("open-modal", detail);
}

/**
 * @param {Object} data
 * @param {{
 * key: string,
 * message?: string,
 * id?: string|number,
 * }} detail
 */
export function setFormValidity(data, detail) {
    data.$dispatch("form-error", detail);
}

/**
 * @typedef {Object} Actions
 * @property {string} onAdd
 * @property {string} onEditFind
 * @property {string} onEdit
 * @property {string} onDelete
 */

/**
 * @param {{
 * page: string,
 * actions: Actions,
 * extraPostBody: object?,
 * elementName: string?,
 * prepareAddData?: Object,
 * editDisableFields?: string[],
 * afterSubmit?: (mode: string) => void,
 * id?: string,
 * }}
 */
export function modalFormComponent({
    page,
    actions = {
        onAdd: "insert",
        onEdit: "update",
        onEditFind: "find",
        onDelete: "delete",
    },
    extraPostBody = {},
    elementName = "",
    prepareAddData = {},
    editDisableFields = [],
    afterSubmit = () => null,
    id: componentId = null,
} = {}) {
    return {
        currentDataId: null,
        mode: null,

        page,
        actions,
        elementName,

        loading: false,
        errors: {},

        init() {
            this.modal = bootstrap.Modal.getOrCreateInstance(this.$refs.modal);

            self.addEventListener("form-error", (event) => {
                const { id = null, key = "", message = null } = event.detail;
                if (id !== componentId) return;

                if (message) {
                    // Set the error message reactively
                    this.errors[key] = message;
                } else {
                    // Clear the error message reactively
                    delete this.errors[key];
                }
            });
        },
        openModal() {
            this.modal.show();
        },
        closeModal() {
            this.modal.hide();
        },

        handleOpenModal({ mode, id = null, dataId = null }) {
            if (id !== componentId) return;
            if (!mode) return console.error("A 'mode' must be provided");

            // On Add
            if (mode === "add") return this.onAdd();

            // On Edit/Delete
            if (!dataId) {
                return console.error("A 'dataId' must be provided");
            }

            if (mode === "view") return this.onView(dataId);
            if (mode === "edit") return this.onEdit(dataId);
            if (mode === "delete") return this.onDelete(dataId);

            return console.error(
                "'mode' must be one of: 'view', 'add', 'edit', 'delete'",
            );
        },

        async handleSubmit() {
            let valid = true;

            /** @type {HTMLFormElement} */
            const form = this.$refs.form;

            // Validar formulario
            for (const input of form.elements) {
                if (input.checkValidity()) {
                    this.setInputValidity(input, true);
                } else {
                    this.setInputValidity(input, false);
                    valid = false;
                }
            }

            // Validar Eventos de Validación
            this.$dispatch("form-validate", {
                id: componentId,
                setValid: (errorKey) => {
                    delete this.errors[errorKey];
                },
                setInvalid: (errorKey, message) => {
                    this.errors[errorKey] = message;
                    valid = false;
                },
            });

            // Comprobrar que la lista de errores tambien este vacia
            if (clearAsArray(this.errors).some(value => value !== "")) valid = false;

            if (this.mode === "delete" || valid) {
                this.loading = true;
                let body = null;
                let actionParam = {
                    "add": this.actions.onAdd,
                    "edit": this.actions.onEdit,
                    "delete": this.actions.onDelete,
                }[this.mode];
                let params = { action: actionParam };

                if (this.mode == "edit" || this.mode == "delete") {
                    params = {
                        ...params,
                        id: this.currentDataId,
                    };
                }
                if (this.mode == "edit" || this.mode == "add") {
                    body = FormDataJson.toJson(this.$refs.form, {
                        skipEmpty: true,
                        includeDisabled: true,
                    });

                    /** 
                     * Evento de serialización personalizado
                     * Permite a otros componentes definir como pasar sus datos al body.
                     */
                    let customPayload = {};
                    this.$dispatch("form-serialize", {
                        id: componentId,
                        merge: (data) => {
                            customPayload = { ...customPayload, ...data };
                        }
                    });

                    body = { ...body, ...customPayload, ...extraPostBody };

                    // Mostrar datos a enviar
                    if (self.DEBUG) console.log("BODY: ", body);
                }

                try {
                    await fetchApi({ page: this.page, ...params }, {
                        method: "POST",
                        body: body,
                    });
                } catch (error) {
                    alert(error.cause.message);
                    throw error;
                } finally {
                    this.loading = false;
                }

                this.closeModal();

                this.$dispatch("form-success", {
                    id: componentId,
                    action: "refresh",
                });
                afterSubmit(this.mode);
            } else {
                if (self.DEBUG) {
                    console.log("Inputs invalidos: ", clearAsArray(this.errors));
                }
            }
        },

        async onAdd() {
            this.mode = "add";

            this.fillForm(prepareAddData);
            this.openModal();
        },
        async onView(id) {
            this.mode = "view";
            this.currentDataId = id;

            let data = await fetchApi({
                page: this.page,
                action: this.actions.onEditFind,
                id: this.currentDataId,
            });
            this.fillForm(data);
            this.setReadonlyInputs(true);
            this.openModal();
        },
        async onEdit(id) {
            this.mode = "edit";
            this.currentDataId = id;

            let data = await fetchApi({
                page: this.page,
                action: this.actions.onEditFind,
                id: this.currentDataId,
            });
            this.fillForm(data);
            this.setDisableFields(true);

            // Informar a otros componentes que los inputs han cambiado
            for (const el of this.$refs.form.elements) {
                if (
                    el.tagName === 'INPUT'
                    || el.tagName === 'TEXTAREA'
                    || el.tagName === 'SELECT'
                ) {
                    el.dispatchEvent(new Event('input'));
                }
            }

            this.$dispatch("form-ready");
            this.openModal();
        },
        async onDelete(id) {
            this.mode = "delete";
            this.currentDataId = id;
            this.openModal();
        },

        /** @param {HTMLInputElement} input */
        checkValidity(input) {
            this.clearInputValidity(input);

            if (input.checkValidity()) {
                this.setInputValidity(input, true);
            } else {
                this.setInputValidity(input, false);
            }
        },

        /**
         * @param {HTMLInputElement} input
         * @param {boolean} valid
         * @param {string?} message
         */
        setInputValidity(input, valid, message = null) {
            message = valid ? "" : message ?? input.validationMessage;
            valid
                ? input.setCustomValidity("")
                : input.setCustomValidity(message);
            
            this.errors[input.name] = message;
        },
        /** @param {HTMLInputElement} input */
        clearInputValidity(input) {
            input.setCustomValidity("");
            this.errors[input.name] = "";
        },
        /** @param {Object} data */
        fillForm(data) {
            this.resetForm();

            // Alertar a otros componentes que preparen sus datos
            this.$dispatch("form-load", { id: this.currentId, data });

            // Rellenar datos iniciales
            populateForm(this.$refs.form, data);
        },
        resetForm() {
            this.$refs.form.reset();

            // Reactivar inputs desactivados
            this.setDisableFields(false);
            this.setReadonlyInputs(false);

            // Remover validaciones erroneas
            for (const input of this.$refs.form.elements) {
                this.clearInputValidity(input);
            }

            this.errors = {};
            this.$dispatch("form-reset", { id: componentId });
        },
        setDisableFields(disabled) {
            for (const inputName of editDisableFields) {
                if (this.$refs.form[inputName]) {
                    this.$refs.form[inputName].disabled = disabled;
                }
            }
        },
        setReadonlyInputs(readOnly) {
            for (const element of this.$refs.form.elements) {
                if ('readOnly' in element) {
                    element.readOnly = readOnly;
                }
            }
        },

        // Validaciones reutilizables

        /** @param {HTMLInputElement} input */
        async validateCedula(input) {
            this.checkValidity(input);

            if (this.mode === "add") {
                let item = null;

                try {
                    item = await fetchApi({
                        page: this.page,
                        action: this.actions.onEditFind,
                        id: input.value,
                    });
                } catch {}

                if (item) {
                    this.setInputValidity(input, false, "La persona ya existe");
                }
            }
        },
    };
}

Alpine.data("modalForm", modalFormComponent);
