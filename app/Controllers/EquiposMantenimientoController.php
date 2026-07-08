<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\MantenimientoEquipo;
use App\Models\MantenimientoEquipoModel;
use App\Services\Logging\BitacoraLogger;

class EquiposMantenimientoController extends Controller
{
    public function __construct(
        private $logger = new BitacoraLogger(),
        private $model = new MantenimientoEquipoModel(),
    ) {}

    public function index()
    {
        $this->protect("equipos:ver");
        return $this->render('equipos_mantenimiento');
    }

    public function query()
    {
        $this->protect("equipos:ver");
        $data = $this->model->query();
        return Response::json($data);
    }

    public function find(): ?string
    {
        $this->protect("equipos:ver");

        $id = $this->getId();
        $data = $this->model->find($id);

        return $data
            ? Response::json($data)
            : Response::noContent();
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

        return Response::json($new, Status::CREATED);
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

        return Response::json($new, Status::CREATED);
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

        return Response::noContent();
    }

    private function notFound(): string
    {
        return Response::json(['message' => 'El mantenimiento no existe'], Status::NOT_FOUND);
    }

    private function getId(): int
    {
        return Request::queryInt("id") ?? 0;
    }

    private function validateBody(): MantenimientoEquipo
    {
        $body = Request::getParsedBody();
        return Tools::map(MantenimientoEquipo::class, $body);
    }
}
