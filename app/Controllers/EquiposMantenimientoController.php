<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Models\Equipos\MantenimientoEquipoDTO;
use App\Models\Equipos\MantenimientoEquipoModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class EquiposMantenimientoController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private MantenimientoEquipoModel $model,
    ) {}

    public function index()
    {
        $this->protect("equipos:ver");
        return $this->templates->render('equipos_mantenimiento');
    }

    public function query()
    {
        $this->protect("equipos:ver");
        $data = $this->model->query();
        return $this->json($data);
    }

    public function find(): ?string
    {
        $this->protect("equipos:ver");

        $id = $this->getId();
        $data = $this->model->find($id);

        return $data
            ? $this->json($data)
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("equipos:crear");

        $new = $this->validateBody();
        $new = $this->model->insert($new);

        $this->logger->info("Mantenimiento de equipo '{id_mantenimiento}' registrado", [
            'id_mantenimiento' => $new->id_mantenimiento,
            'codigo_equipo'    => $new->codigo_equipo,
            'datos_nuevos' => $new,
        ]);

        return $this->json($new, 201);
    }

    public function update(): string
    {
        $this->protect("equipos:editar");

        $id = $this->getId();
        $old = $this->model->find($id);

        if (!$old) {
            return $this->notFound();
        }

        $new = $this->validateBody();
        $new = $this->model->update($id, $new);

        $this->logger->info("Mantenimiento de equipo '{id_mantenimiento}' actualizado", [
            'id' => $id,
            'codigo_equipo'    => $old->codigo_equipo,
            'datos_previos'    => $old,
            'datos_nuevos'     => $new,
        ]);

        return $this->json($new, 201);
    }

    public function delete(): string|null
    {
        $this->protect("equipos:eliminar");
        $id = $this->getId();

        $old = $this->model->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $this->model->delete($id);
        $this->logger->info("Mantenimiento de equipo '{id_mantenimiento}' eliminado", [
            'id_mantenimiento' => $id,
            'codigo_equipo'    => $old->codigo_equipo,
            'datos_previos' => $old,
        ]);

        return $this->json(null, 204);
    }

    private function notFound(): string
    {
        return $this->json(['message' => 'El mantenimiento no existe'], 404);
    }

    private function getId(): int
    {
        return Request::queryInt("id") ?? 0;
    }

    private function validateBody(): MantenimientoEquipoDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(MantenimientoEquipoDTO::class, $body);
    }
}
