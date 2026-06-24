import FormDataJson from "form-data-json";

/**
 * Helper para comunicarse con la API en el backend.
 * Solo acepta y envia JSON.
 * @param {object|URLSearchParams} params,
 * @param {RequestInit} options
 * @returns {Promise<Object>}
 */
export async function fetchApi(params = "", options = {}) {
    let { headers = {}, body, ...restOptions } = options;
    let defaultHeaders = {"Accept": "application/json"};

    // Convertir el body en JSON
    if (body) {
        defaultHeaders["Content-Type"] = "application/json";
        
        if (body instanceof FormData) {
            body = FormDataJson.toJson(body);
        }
        
        if (body?.constructor === Object) {
            body = JSON.stringify(body);
        }
    }

    // Enviar con fetch
    const query = new URLSearchParams(params).toString();
    const url = query ? `?${query}` : '';

    const response = await fetch(url, {
        headers: { ...defaultHeaders, ...headers },
        body,
        ...restOptions,
    });
    return await handleResponse(response);
}

/** 
 * @param {Response} response
 * @returns {Object|null} */
async function handleResponse(response) {
    // Lanzar error si la respuesta no es OK
    if (!response.ok) {
        let errorBody;

        try {
            errorBody = await response.clone().json();
        } catch {
            errorBody = await response.clone().text();
        }

        if (self.DEBUG) console.error(errorBody);

        // Si es un objeto (JSON) se esparce, si es un string se guarda en una propiedad 'message'
        const errorCause = typeof errorBody === "object" 
            ? { ...errorBody } 
            : { message: errorBody };
        
        throw new Error(
            `API error ${response.status}: ${response.statusText}`,
            { cause: { ...errorCause, status: response.status } },
        );
    }

    return response.status === 204 ? null : await response.json();
}