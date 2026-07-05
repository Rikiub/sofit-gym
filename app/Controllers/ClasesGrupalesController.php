<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\StatusCode;
use App\Models\ClaseGrupal;
use App\Models\ClaseGrupalModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class ClasesGrupalesController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private ClaseGrupalModel $claseModel,
    ) {}

    public function index(): string
    {
        $this->protect("clases:ver");
        return $this->templates->render('clases');
    }

    public function query(): string
    {
        $this->protect("clases:ver");
        $clases = $this->claseModel->query();
        return $this->json($clases);
    }

    public function find(): ?string
    {
        $this->protect("clases:ver");

        $id = $this->getId();
        $clase = $this->claseModel->find($id);

        return $clase
            ? $this->json($clase)
            : $this->json(null, StatusCode::NOT_FOUND);
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

        return $this->json($new, StatusCode::CREATED);
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

        return $this->json($new, StatusCode::CREATED);
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

        return $this->json(null, StatusCode::NO_CONTENT);
    }

    private function getId(): int
    {
        return Request::queryInt("id") ?? 0;
    }

    private function notFound(): string
    {
        return $this->json(["message" => "Clase no encontrada"], StatusCode::NOT_FOUND);
    }

    private function validateBody(): ClaseGrupal
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(ClaseGrupal::class, $body);
    }
}
