<?php

namespace App\Models;

use App\Core\Validator;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use PDO;

class NotificacionesModel extends BaseModel
{
    private string $dbSeguridad = "sofit_gym_seguridad";
    private string $table = "sofit_gym_seguridad.notificacion";

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        parent::__construct($pdo);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT
                    notif.*,
                    nu.leido,
                    nu.fecha_leido
                FROM {$this->table} notif
                LEFT JOIN {$this->dbSeguridad}.notificacion_usuario nu
                    ON nu.id_notificacion = notif.id_notificacion
                {$where}
                ORDER BY
                    nu.leido = 0 DESC,
                    notif.fecha_envio DESC
                LIMIT 25
            SQL;
    }

    /**
     * @return NotificacionDTO[]
     */
    public function query(int $id_usuario): array
    {
        $rows = $this->pdoQuery(
            $this->sqlSelect("WHERE id_usuario = ?"),
            [$id_usuario]
        )->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(NotificacionDTO::class, $row),
            $rows
        );
    }

    public function find(int $id_usuario, int $id_notificacion): ?NotificacionDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect(
                <<<SQL
                    WHERE
                        nu.id_usuario = ?
                        nu.id_notificacion = ?
                SQL
            ),
            [$id_usuario, $id_notificacion]
        )->fetch();

        return $row
            ? $this->mapper->map(NotificacionDTO::class, $row)
            : null;
    }

    public function sendToUsuarios(array $id_usuarios, NotificacionDTO $notificacion)
    {
        $notificacion->validateInsert();

        $this->pdoTransaction(function () use ($id_usuarios, $notificacion) {
            $this->pdoInsert($this->table, [
                'titulo' => $notificacion->titulo,
                'contenido' => $notificacion->contenido,
            ]);
            $id_notificacion = (int) $this->pdo->lastInsertId();

            foreach ($id_usuarios as $id) {
                $this->pdoInsert("{$this->dbSeguridad}.notificacion_usuario", [
                    "id_notificacion" => $id_notificacion,
                    "id_usuario" => $id,
                ]);
            }
        });
    }

    public function setLeido(int $id_usuario, int $id_notificacion, bool $leido)
    {
        $this->pdoUpdate(
            "{$this->dbSeguridad}.notificacion_usuario",
            ["leido" => $leido],
            [
                "id_usuario" => $id_usuario,
                "id_notificacion" => $id_notificacion,
            ]

        );
    }

    public function setLeidoTodas(int $id_usuario)
    {
        $this->pdoUpdate(
            "{$this->dbSeguridad}.notificacion_usuario",
            ["leido" => true],
            ["id_usuario" => $id_usuario]
        );
    }
}

// DTO
readonly class NotificacionDTO
{
    public function __construct(
        public ?int $id_notificacion = null,
        public ?string $titulo = null,
        public ?string $contenido = null,
        public ?bool $leido = false,
        public ?DateTimeImmutable $fecha_envio = null,
        public ?DateTimeImmutable $fecha_leido = null,
    ) {}

    public function validateInsert()
    {
        Validator::required($this->titulo, "titulo");
        Validator::required($this->contenido, "contenido");
    }
}
