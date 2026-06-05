<?php

namespace App\Helpers\LLM\Tools;

use App\Models\Clientes\ClientesModel;
use CuyZ\Valinor\Normalizer\Normalizer;

class ClientesTool
{
    public function __construct(
        private Normalizer $normalizerJson,
        private ClientesModel $clientesModel
    ) {}

    /** Retorna un listado de todos los clientes junto a su información personal y estado de membresia.
     * Si se proporcionan filtros, retornara todos los clientes encontrados segun los filtros proporcionados.
     * 
     * Filtros soportados:
     *   'cedula'        -> partial match (LIKE %...%)
     *   'nombre'        -> partial match
     *   'apellido'      -> partial match
     *   'correo'        -> partial match
     *   'telefono'      -> partial match
     *   'activo'        -> exact bool (0 or 1)
     *   'id_tipo'       -> exact membership type ID
     *   'id_estado'     -> exact membership state ID
     *   'fecha_inicio_desde' -> membership start date >= value
     *   'fecha_inicio_hasta' -> membership start date <= value
     *   'fecha_fin_desde'    -> membership end date >= value
     *   'fecha_fin_hasta'    -> membership end date <= value
     * 
     * @param ?array $filters JSON array de todos los clientes.
     * @return string
     */
    public function query(?array $filters = []): string
    {
        $result = $this->clientesModel->query(filters: $filters);
        return $this->normalizerJson->normalize($result);
    }
}
