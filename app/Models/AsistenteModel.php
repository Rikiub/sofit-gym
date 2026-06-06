<?php

namespace App\Models;

use App\Models\Clientes\ClientesModel;
use CuyZ\Valinor\Normalizer\Normalizer;

class AsistenteModel
{
    public function __construct(
        private Normalizer $normalizer,
        private ClientesModel $clientesModel,
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
     * @param string $search Filtro de busqueda general.
     * @param array $filters Filtros especificos. Mantener vacio [] en caso de no necesitar filtrado.
     * @return string JSON array de todos los clientes.
     */
    public function queryClientes(?string $search = null, array $filters = []): string
    {
        $result = $this->clientesModel->query(search: $search, filters: $filters);
        return $this->normalizer->normalize($result);
    }
}
