<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
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
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("clases:crear");
        $clase = $this->validateBody();

        $clase = $this->clasesModel->insert($clase);
        return $this->json($clase, 201);
    }

    public function update(): string
    {
        $this->protect("clases:editar");

        $clase = $this->validateBody();
        $id = $this->getId();

        if (!$this->clasesModel->find($id)) {
            return $this->notFound();
        }

        $clase = $this->clasesModel->update($id, $clase);
        return $this->json($clase, 201);
    }

    public function delete(): string|null
    {
        $this->protect("clases:eliminar");
        $id = $this->getId();

        if (!$this->clasesModel->find($id)) {
            return $this->notFound();
        }

        $this->clasesModel->delete($id);
        return $this->json(null, 204);
    }

    private function getId(): int
    {
        return Request::queryInt("id") ?? 0;
    }

    private function notFound(): string
    {
        return $this->json(["message" => "Clase no encontrada"], 404);
    }

    private function validateBody(): ClaseGrupalDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(ClaseGrupalDTO::class, $body);
    }
}
