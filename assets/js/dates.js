import dayjs from 'dayjs';

/** @param {string|Date} date */
export function toIsoDate(date) {
    return dayjs(date).format("YYYY-MM-DD");
}

/** @param {string|Date} date */
export function toIsoDateTime(date) {
    return dayjs(date).format("YYYY-MM-DD[T]hh:mm");
}

/** @param {string|Date} date */
export function toTime(date) {
    return dayjs(date).format("hh:mm:ss");
}

/**
 * Iterar recursivamente a traves de un objeto y rellenar los inputs acordemente.
 */
export function populateFormRecursive(data, form, prefix = '') {
    for (const key in data) {
        if (!data.hasOwnProperty(key)) continue;

        const value = data[key];
        const inputName = prefix ? `${prefix}.${key}` : key;

        if (value && typeof value === 'object' && !(value instanceof Date)) {
            populateFormRecursive(value, form, inputName);
        } else {
            const element = form.querySelector(`[name="${inputName}"]`);

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

    const date = dayjs(dateValue);
    
    if (!date.isValid()) return '';

    if (inputType === 'date') {
        return date.format('YYYY-MM-DD');
    } 
    if (inputType === 'datetime-local') {
        return date.format('YYYY-MM-DD[T]HH:mm');
    }

    return '';
}