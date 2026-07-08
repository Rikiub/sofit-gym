<?php

namespace App\Models;

use App\Core\Tools;
use App\Models\Clientes\ClienteModel;
use App\Models\Clientes\SegumientoFisicoModel;
use App\Models\Clientes\SegumientoNutricionalModel;
use DateTimeImmutable;

class AsistenteModel extends Model
{
    public function __construct(
        private $asistenciaModel = new AsistenciaModel(),
        private $rutinaModel = new RutinaModel(),
        private $trabajadorModel = new TrabajadorModel(),
        private $clienteModel = new ClienteModel(),
        private $segFisicoModel = new SegumientoFisicoModel(),
        private $segNutricionalModel = new SegumientoNutricionalModel(),
    ) {
        parent::__construct();
    }

    public function insertSesion(AsistenteSesion $sesion): AsistenteSesion
    {
        $table = $this->dbSecurity("asistente_sesion");

        $array = (array) $sesion;
        unset($array["fecha_creacion"]);
        unset($array["mensajes"]);

        $this->db->dbInsert($table, $array);
        $id = (int) $this->db->lastInsertId();

        return $this->findSesion($id);
    }

    public function insertMensaje(AsistenteMensaje $mensaje): void
    {
        $table = $this->dbSecurity("asistente_mensaje");

        $array = (array) $mensaje;
        unset($array["fecha_creacion"]);
        $array["rol"] = $mensaje->rol ? $mensaje->rol->value : null;

        $this->db->dbInsert($table, $array);
    }

    /** @return AsistenteSesion[] */
    public function querySesiones(): array
    {
        $rows = $this->db->dbQuery(
            <<<SQL
                SELECT * FROM {$this->dbSecurity("asistente_sesion")}
            SQL,
        )->fetchAll();

        return array_map(
            fn($row) => Tools::map(AsistenteSesion::class, $row),
            $rows,
        );
    }

    public function findSesion(int $id_sesion): ?AsistenteSesion
    {
        $sesion = $this->db->dbQuery(
            <<<SQL
                SELECT *
                FROM {$this->dbSecurity("asistente_sesion")}
                WHERE id_sesion = ?
            SQL,
            [$id_sesion]
        )->fetch();
        if (!$sesion) return null;

        $mensajes = $this->db->dbQuery(
            <<<SQL
                SELECT *
                FROM {$this->dbSecurity("asistente_mensaje")}
                WHERE id_sesion = ?
            SQL,
            [$id_sesion]
        )->fetchAll();
        $sesion["mensajes"] = $mensajes;

        return Tools::map(AsistenteSesion::class, $sesion);
    }

    public function getLastSesion(int $id_usuario): ?AsistenteSesion
    {
        $sesion = $this->db->dbQuery(<<<SQL
            SELECT id_sesion
            FROM {$this->dbSecurity("asistente_sesion")}
            WHERE id_usuario = ?
            ORDER BY fecha_creacion DESC
            LIMIT 1
        SQL, [$id_usuario])->fetch();
        $id = (int)($sesion["id_sesion"] ?? 0);

        return $this->findSesion($id);
    }

    /** * Retorna un listado de clientes junto a su información personal (incluyendo su CÉDULA) y estado de membresía.
     * ÚSALO SIEMPRE que necesites averiguar la cédula de un cliente a partir de su nombre, apellido, correo o teléfono.
     * * Filtros soportados en el array $filters:
     * 'cedula'        -> partial match (LIKE %...%)
     * 'nombre'        -> partial match
     * 'apellido'      -> partial match
     * 'correo'        -> partial match
     * 'telefono'      -> partial match
     * 'activo'        -> exact bool (0 or 1)
     * 'id_tipo'       -> exact membership type ID
     * 'id_estado'     -> exact membership state ID
     * 'fecha_inicio_desde' -> membership start date >= value
     * 'fecha_inicio_hasta' -> membership start date <= value
     * 'fecha_fin_desde'    -> membership end date >= value
     * 'fecha_fin_hasta'    -> membership end date <= value
     * * @param string|null $search Texto libre para buscar por nombre, apellido, correo, etc.
     * @param array $filters Filtros específicos estructurados. Mantener vacío [] si no se necesitan.
     * @return string JSON array de todos los clientes con sus datos personales (cédula incluida).
     */
    public function queryClientes(?string $search = null, array $filters = []): string
    {
        $items = $this->clienteModel->query(search: $search, filters: $filters);
        return Tools::normalizeJson($items);
    }

    /** * Busca un cliente específico utilizando su cédula exacta y devuelve su información personal.
     * * @param string $cedula_cliente La cédula de identidad exacta del cliente.
     * @return string JSON con la información del cliente.
     */
    public function findCliente(string $cedula_cliente): string
    {
        $items = $this->clienteModel->find($cedula_cliente);
        return Tools::normalizeJson($items);
    }

    /** * Retorna el listado de seguimientos físicos de un cliente ordenados por fecha.
     * IMPORTANT: Requiere la cédula. Si el usuario te da un nombre (ej: "Juan"), NO le preguntes su cédula; 
     * primero debes llamar de forma autónoma a 'queryClientes' para buscar el nombre y obtener su cédula.
     * * @param string $cedula_cliente La cédula de identidad del cliente (obligatorio, no enviar nombres aquí).
     * @return string JSON array con los seguimientos físicos.
     */
    public function querySegFisico(string $cedula_cliente): string
    {
        $items = $this->segFisicoModel->queryByCliente($cedula_cliente);
        return Tools::normalizeJson($items);
    }

    /** * Retorna el listado de seguimientos nutricionales de un cliente ordenados por fecha.
     * IMPORTANT: Requiere la cédula. Si el usuario te da un nombre (ej: "Juan"), NO le preguntes su cédula; 
     * primero debes llamar de forma autónoma a 'queryClientes' para buscar el nombre y obtener su cédula.
     * * @param string $cedula_cliente La cédula de identidad del cliente (obligatorio, no enviar nombres aquí).
     * @return string JSON array con los seguimientos nutricionales.
     */
    public function querySegNutricional(string $cedula_cliente): string
    {
        $items = $this->segNutricionalModel->queryByCliente($cedula_cliente);
        return Tools::normalizeJson($items);
    }

    /** * Retorna un listado de trabajadores del gimnasio.
     * * @param string|null $search Filtro para buscar trabajadores por nombre o datos.
     * @return string JSON array de todos los trabajadores.
     */
    public function queryTrabajadores(?string $search = null): string
    {
        $items = $this->trabajadorModel->query($search);
        return Tools::normalizeJson($items);
    }

    /** * Retorna un listado de asistencias (historial de accesos al gimnasio).
     * * @param string $search Filtro de búsqueda. Puedes pasar una cédula, un nombre de cliente o una fecha.
     * @return string JSON array con el historial de asistencias.
     */
    public function queryAsistencias(?string $search = ""): string
    {
        $items = $this->asistenciaModel->buscarEntradas($search);
        return Tools::normalizeJson($items);
    }

    /** * Retorna el historial de rutinas asignadas a un cliente específico.
     * IMPORTANT: Requiere la cédula. Si el usuario te da un nombre (ej: "Juan"), NO le preguntes su cédula; 
     * primero debes llamar de forma autónoma a 'queryClientes' para buscar el nombre y obtener su cédula.
     * * @param string $cedula_cliente La cédula de identidad del cliente (obligatorio, no enviar nombres aquí).
     * @return string JSON array con las rutinas.
     */
    public function queryRutinas(string $cedula_cliente): string
    {
        $items = $this->rutinaModel->obtenerAsignacionesPorCliente($cedula_cliente);
        return Tools::normalizeJson($items);
    }
}

// DTO
readonly class AsistenteSesion
{
    public function __construct(
        public ?int $id_sesion = null,
        public ?int $id_usuario = null,
        public ?string $titulo = null,
        public ?string $modelo_usado = null,
        public ?DateTimeImmutable $fecha_creacion = new DateTimeImmutable(),

        /** @var AsistenteMensaje[] */
        public ?array $mensajes = [],
    ) {}
}

readonly class AsistenteMensaje
{
    public function __construct(
        public ?int $id_mensaje = null,
        public ?int $id_sesion = null,
        public ?RolAsistente $rol = null,
        public ?string $contenido = null,
        public ?DateTimeImmutable $fecha_creacion = null,
    ) {}
}

enum RolAsistente: string
{
    case Asistente = "asistente";
    case Usuario = "usuario";
}
