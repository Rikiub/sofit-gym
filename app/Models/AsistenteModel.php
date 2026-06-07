<?php

namespace App\Models;

use App\Helpers\Validator;
use App\Models\Clientes\ClientesModel;
use App\Models\Clientes\SegumientoFisicoModel;
use App\Models\Clientes\SegumientoNutricionalModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\Normalizer\Normalizer;
use DateTimeImmutable;
use PDO;

enum RolAsistente: string
{
    case Sistema = "sistema";
    case Asistente = "asistente";
    case Usuario = "usuario";
    case Herramienta = "herramienta";
}

readonly class AsistenteMensajeDTO
{
    public function __construct(
        public ?int $id_mensaje = null,
        public ?int $id_sesion = null,
        public ?RolAsistente $rol = null,
        public ?string $contenido = null,
        public ?DateTimeImmutable $fecha_creacion = new DateTimeImmutable(),
    ) {}
}

readonly class AsistenteSesionDTO
{
    public function __construct(
        public ?int $id_sesion = null,
        public ?int $id_usuario = null,
        public ?string $titulo = null,
        public ?string $modelo_usado = null,
        public ?DateTimeImmutable $fecha_creacion = new DateTimeImmutable(),

        /** @var AsistenteMensajeDTO[] */
        public ?array $mensajes = [],
    ) {}
}

class AsistenteModel extends BaseModel
{
    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
        private Normalizer $normalizer,
        private AsistenciaModel $asistenciaModel,
        private RutinasModel $rutinasModel,
        private TrabajadoresModel $trabajadoresModel,
        private ClientesModel $clientesModel,
        private SegumientoFisicoModel $segFisicoModel,
        private SegumientoNutricionalModel $segNutricionalModel,
    ) {
        parent::__construct($pdo);
    }

    public function insertSesion(AsistenteSesionDTO $sesion): AsistenteSesionDTO
    {
        $table = "asistente_sesion";

        $array = (array) $sesion;
        $array["fecha_creacion"] = Validator::dateToString($sesion->fecha_creacion);
        unset($array["mensajes"]);

        $this->pdoInsert($table, $array);
        $id = (int) $this->pdo->lastInsertId();

        return $this->findSesion($id);
    }

    public function insertMensaje(AsistenteMensajeDTO $mensaje): void
    {
        $table = "asistente_mensaje";

        $array = (array) $mensaje;
        $array["rol"] = $mensaje->rol ? $mensaje->rol->value : null;
        $array["fecha_creacion"] = Validator::dateToString($mensaje->fecha_creacion);

        $this->pdoInsert($table, $array);
    }

    /** @return AsistenteSesionDTO[] */
    public function querySesiones(): array
    {
        $rows = $this->pdoQuery(
            <<<SQL
                SELECT * FROM asistente_sesion
            SQL,
        )->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(AsistenteSesionDTO::class, $row),
            $rows,
        );
    }

    public function findSesion(int $id_sesion): ?AsistenteSesionDTO
    {
        $sesion = $this->pdoQuery(
            <<<SQL
                SELECT * FROM asistente_sesion
                WHERE id_sesion = ?
            SQL,
            [$id_sesion]
        )->fetch();
        if (!$sesion) return null;

        $mensajes = $this->pdoQuery(
            <<<SQL
                SELECT * FROM asistente_mensaje
                WHERE id_sesion = ?
            SQL,
            [$id_sesion]
        )->fetchAll();
        $sesion["mensajes"] = $mensajes;

        return $this->mapper->map(AsistenteSesionDTO::class, $sesion);
    }

    public function getLastSesion(int $id_usuario): ?AsistenteSesionDTO
    {
        $sesion = $this->pdoQuery(<<<SQL
            SELECT id_sesion FROM asistente_sesion
            WHERE id_usuario = ?
            ORDER BY fecha_creacion DESC
            LIMIT 1
        SQL, [$id_usuario])->fetch();

        return $this->findSesion($sesion["id_sesion"]);
    }

    /** Retorna un listado de clientes junto a su información personal y estado de membresia.
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
     * @param string $search Filtro de busqueda.
     * @param array $filters Filtros especificos. Mantener vacio [] en caso de no necesitar filtrado.
     * @return string JSON array de todos los clientes.
     */
    public function queryClientes(?string $search = null, array $filters = []): string
    {
        $items = $this->clientesModel->query(search: $search, filters: $filters);
        return $this->normalizer->normalize($items);
    }

    /** Busca un cliente segun su cedula y devuelve su información personal si es encontrado.
     * 
     * @param string $cedula_cliente 
     * @return string JSON array de todos los clientes.
     */
    public function findCliente(string $cedula_cliente): string
    {
        $items = $this->clientesModel->find($cedula_cliente);
        return $this->normalizer->normalize($items);
    }

    /** Retorna un listado de los seguimientos fisicos de un cliente ordenados por fecha.
     * @param string $cedula_cliente Cedula a utilizar para encontrar los seguimientos.
     * @return string JSON array.
     */
    public function querySegFisico(string $cedula_cliente): string
    {
        $items = $this->segFisicoModel->queryByCliente($cedula_cliente);
        return $this->normalizer->normalize($items);
    }

    /** Retorna un listado de los seguimientos nutricionales de un cliente ordenados por fecha.
     * @param string $cedula_cliente Cedula a utilizar para encontrar los seguimientos.
     * @return string JSON array.
     */
    public function querySegNutricional(string $cedula_cliente): string
    {
        $items = $this->segNutricionalModel->queryByCliente($cedula_cliente);
        return $this->normalizer->normalize($items);
    }

    /** Retorna un listado de trabajadores.
     * 
     * @param string $search Filtro de busqueda.
     * @return string JSON array de todos los trabajadores.
     */
    public function queryTrabajadores(?string $search = null): string
    {
        $items = $this->trabajadoresModel->query($search);
        return $this->normalizer->normalize($items);
    }

    /** Retorna un listado de asistencias, es decir un historial de fechas en las que ingresaron los clientes al gimnasio.
     * 
     * @param string $search Filtro de busqueda. Puedes filtrar por cedula o nombre cliente, e incluso fecha.
     * @return string JSON array.
     */
    public function queryAsistencias(?string $search = ""): string
    {
        $items = $this->asistenciaModel->buscarEntradas($search);
        return $this->normalizer->normalize($items);
    }

    /** Retorna el historial de rutinas asignadas a un cliente específico.
     * 
     * @param string $cedula_cliente Cedula del cliente a buscar.
     * @return string JSON array.
     */
    public function queryRutinas(string $cedula_cliente): string
    {
        $items = $this->rutinasModel->obtenerAsignacionesPorCliente($cedula_cliente);
        return $this->normalizer->normalize($items);
    }
}
