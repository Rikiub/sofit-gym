<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\ClaseGrupal;
use App\Models\ClaseGrupalModel;
use App\Services\Logging\BitacoraLogger;

class ClasesGrupalesController extends Controller
{
    public function __construct(
        private $logger = new BitacoraLogger(),
        private $claseModel = new ClaseGrupalModel(),
    ) {}

    public function index(): string
    {
        $this->protect("clases:ver");
        return $this->render('clases');
    }

    public function query(): string
    {
        $this->protect("clases:ver");
        $clases = $this->claseModel->query();
        return Response::json($clases);
    }

    public function find(): ?string
    {
        $this->protect("clases:ver");

        $id = $this->getId();
        $clase = $this->claseModel->find($id);

        return $clase
            ? Response::json($clase)
            : Response::noContent();
    }

    public function insert(): string
    {
        $this->protect("clases:crear");

        $new = $this->validateBody();
        $new = $this->claseModel->insert($new);

        $this->logger->info("Clase grupal '{nombre}' creada", [
            'nombre' => $new->nombre,
            'id_clase'      => $new->id_clase,
            'datos_nuevos'  => $new,
        ]);

        return Response::json($new, Status::CREATED);
    }

    public function update(): string
    {
        $this->protect("clases:editar");

        $id = $this->getId();
        $new = $this->validateBody();

        $old = $this->claseModel->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $new = $this->claseModel->update($id, $new);
        $this->logger->info("Clase grupal '{nombre}' actualizada", [
            'nombre' => $old->nombre,
            'id_clase' => $id,
            'datos_previos' => $old,
            'datos_nuevos' => $new,
        ]);

        return Response::json($new, Status::CREATED);
    }

    public function delete(): string|null
    {
        $this->protect("clases:eliminar");
        $id = $this->getId();

        $old = $this->claseModel->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $this->claseModel->delete($id);
        $this->logger->info("Clase grupal '{nombre}' eliminada", [
            'nombre' => $old->nombre,
            'id_clase'      => $id,
            'datos_previos' => $old,
        ]);

        return Response::noContent();
    }

    private function getId(): int
    {
        return Request::queryInt("id") ?? 0;
    }

    private function notFound(): string
    {
        return Response::json(["message" => "Clase no encontrada"], Status::NOT_FOUND);
    }

    private function validateBody(): ClaseGrupal
    {
        $body = Request::getParsedBody();
        return Tools::map(ClaseGrupal::class, $body);
    }
}
