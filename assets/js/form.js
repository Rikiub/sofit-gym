import dayjs from 'dayjs';
import FormDataJson from "form-data-json";

/**
 * Iterar recursivamente a traves de un objeto y rellenar los inputs acordemente.
 * Cada key debe corresponder a un "name" del formulario.
 * @param {HTMLFormElement} form
 * @param {Object} data
 * @param {Object?} options
 */
export function populateForm(form, data, options = {}) {
    FormDataJson.fromJson(form, data, {
        clearOthers: true,
        includeDisabled: true,
    });
    populateFormRecursive(form, data);
}

/**
 * @param {HTMLFormElement} form
 * @param {Object} data
 * @param {string?} prefix
 */
function populateFormRecursive(form, data, prefix = '') {
    for (const key in data) {
        if (!data.hasOwnProperty(key)) continue;

        const value = data[key];
        const inputName = prefix ? `${prefix}[${key}]` : key;

        if (
            value
            && typeof value === 'object'
            && !(value instanceof Date)
        ) {
            populateFormRecursive(form, value, inputName);
        } else {
            const element = form.elements[inputName];

            if (element) {
                if (element.type === 'date' || element.type === 'datetime-local') {
                    element.value = formatDateWithDayJS(value, element.type);
                } else {
                    element.value = value ?? '';
                }
            }
        }
    }
}

/**
 * Formatear fechas para inputs HTML5.
 */
function formatDateWithDayJS(dateValue, inputType) {
    if (!dateValue) return '';

    // Prevenir que Day.js intente parsear un string de hora puro ("14:30") como fecha inválida
    if (
        inputType === 'time'
        && typeof dateValue === 'string'
        && /^([0-1]\d|2[0-3]):([0-5]\d)(:([0-5]\d))?$/.test(dateValue)
    ) {
        return dateValue.substring(0, 5);
    }

    const date = dayjs(dateValue);
    if (!date.isValid()) return '';

    if (inputType === 'date') {
        return date.format('YYYY-MM-DD');
    } 
    if (inputType === 'datetime-local') {
        return date.format('YYYY-MM-DD[T]HH:mm');
    }
    if (inputType === 'time') {
        return date.format('HH:mm');
    }

    return '';
}