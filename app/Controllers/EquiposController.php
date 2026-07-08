<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Equipos\Equipo;
use App\Models\Equipos\EquipoModel;

class EquiposController extends Controller
{
    public function __construct(
        private $logger = new BitacoraModel(),
        private $equipoModel = new EquipoModel(),
    ) {}

    public function index()
    {
        return $this->render('equipos');
    }

    public function query()
    {
        $this->protect("equipos:ver");
        $equipos = $this->equipoModel->query();
        return Response::json($equipos);
    }

    public function find(): ?string
    {
        $this->protect("equipos:ver");

        $id = $this->getId();
        $equipo = $this->equipoModel->find($id);

        return $equipo
            ? Response::json($equipo)
            : Response::noContent();
    }

    public function insert(): string
    {
        $this->protect("equipos:crear");

        $new = $this->validateBody();
        $id = $equipo->codigo_equipo ?? "";

        if ($this->equipoModel->find($id)) {
            return Response::json(
                ['message' => 'El equipo ya existe'],
                Status::CONFLICT
            );
        }

        $new = $this->equipoModel->insert($new);
        $this->logger->log("Equipo '{codigo_equipo}' creado", [
            "modulo" => "equipos",
            "accion" => "crear",

            "codigo_equipo" => $new->codigo_equipo,
            "datos_nuevos" => $new,
        ]);

        return Response::json($new, Status::CREATED);
    }

    public function update(): string
    {
        $this->protect("equipos:editar");

        $new = $this->validateBody();
        $id = $this->getId();

        $old = $this->equipoModel->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $new = $this->equipoModel->update($id, $new);
        $this->logger->log("Equipo '{codigo_equipo}' actualizado", [
            "modulo" => "equipos",
            "accion" => "editar",

            "codigo_equipo" => $id,
            "datos_previos" => $old,
            "datos_nuevos" => $new,
        ]);

        return Response::json($new, Status::CREATED);
    }

    public function delete(): string|null
    {
        $this->protect("equipos:eliminar");
        $id = $this->getId();

        $old = $this->equipoModel->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $this->equipoModel->delete($id);
        $this->logger->log("Equipo '{codigo_equipo}' eliminado", [
            "modulo" => "equipos",
            "accion" => "eliminar",

            "codigo_equipo" => $id,
            "datos_previos" => $old,
        ]);

        return Response::noContent();
    }

    private function getId(): string
    {
        return Request::query("id") ?? "";
    }

    private function validateBody(): Equipo
    {
        $body = Request::getParsedBody();
        return Tools::map(Equipo::class, $body);
    }

    private function notFound(): string
    {
        return Response::json(['message' => 'El equipo no existe'], Status::NOT_FOUND);
    }
}
