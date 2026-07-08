<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Status;
use App\Models\Equipos\Equipo;
use App\Models\Equipos\EquipoModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class EquiposController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private EquipoModel $equipoModel,
    ) {}

    public function index()
    {
        return $this->templates->render('equipos');
    }

    public function query()
    {
        $this->protect("equipos:ver");
        $equipos = $this->equipoModel->query();
        return $this->json($equipos);
    }

    public function find(): ?string
    {
        $this->protect("equipos:ver");

        $id = $this->getId();
        $equipo = $this->equipoModel->find($id);

        return $equipo
            ? $this->json($equipo)
            : $this->json(null, Status::NOT_FOUND);
    }

    public function insert(): string
    {
        $this->protect("equipos:crear");

        $new = $this->validateBody();
        $id = $equipo->codigo_equipo ?? "";

        if ($this->equipoModel->find($id)) {
            return $this->json(
                ['message' => 'El equipo ya existe'],
                Status::CONFLICT
            );
        }

        $new = $this->equipoModel->insert($new);
        $this->logger->info("Equipo '{codigo_equipo}' creado", [
            "codigo_equipo" => $new->codigo_equipo,
            "datos_nuevos" => $new,
        ]);

        return $this->json($new, Status::CREATED);
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
        $this->logger->info("Equipo '{codigo_equipo}' actualizado", [
            "codigo_equipo" => $id,
            "datos_previos" => $old,
            "datos_nuevos" => $new,
        ]);

        return $this->json($new, Status::CREATED);
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
        $this->logger->info("Equipo '{codigo_equipo}' eliminado", [
            "codigo_equipo" => $id,
            "datos_previos" => $old,
        ]);

        return $this->json(null, Status::NO_CONTENT);
    }

    private function getId(): string
    {
        return Request::query("id") ?? "";
    }

    private function validateBody(): Equipo
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(Equipo::class, $body);
    }

    private function notFound(): string
    {
        return $this->json(['message' => 'El equipo no existe'], Status::NOT_FOUND);
    }
}
