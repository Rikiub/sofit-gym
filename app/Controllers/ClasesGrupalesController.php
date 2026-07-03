<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\StatusCode;
use App\Models\ClaseGrupalDTO;
use App\Models\ClasesGrupalesModel;
use CuyZ\Valinor\Mapper\TreeMapper;


class ClasesGrupalesController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private ClasesGrupalesModel $clasesModel,
    ) {}

    public function index(): string
    {
        $this->protect("clases:ver");
        return $this->templates->render('clases');
    }

    public function query(): string
    {
        $this->protect("clases:ver");
        $clases = $this->clasesModel->query();
        return $this->json($clases);
    }

    public function find(): ?string
    {
        $this->protect("clases:ver");

        $id = $this->getId();
        $clase = $this->clasesModel->find($id);

        return $clase
            ? $this->json($clase)
            : $this->json(null, StatusCode::NOT_FOUND);
    }

    public function insert(): string
    {
        $this->protect("clases:crear");

        $new = $this->validateBody();
        $new = $this->clasesModel->insert($new);

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

        $old = $this->clasesModel->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $new = $this->clasesModel->update($id, $new);
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

        $old = $this->clasesModel->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $this->clasesModel->delete($id);
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

    private function validateBody(): ClaseGrupalDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(ClaseGrupalDTO::class, $body);
    }
}
