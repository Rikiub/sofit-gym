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

        $id = Request::queryInt("id") ?? 0;
        $clase = $this->clasesModel->find($id);

        return $clase
            ? $this->json($clase)
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("clases:crear");

        $body = $this->getParsedBody();
        $clase = $this->mapper->map(ClaseGrupalDTO::class, $body);

        $clase = $this->clasesModel->insert($clase);
        return $this->json($clase, 201);
    }

    public function update(): string
    {
        $this->protect("clases:editar");

        $body = $this->getParsedBody();
        $clase = $this->mapper->map(ClaseGrupalDTO::class, $body);

        if (!$this->clasesModel->find($clase->id_clase)) {
            return $this->json(['message' => 'No existe'], 400);
        }

        $clase = $this->clasesModel->update($clase);
        return $this->json($clase, 201);
    }

    public function delete(): string|null
    {
        $this->protect("clases:eliminar");
        $id = Request::queryInt("id") ?? 0;

        if (!$this->clasesModel->find($id)) {
            return $this->json(['message' => 'No existe'], 404);
        }

        $this->clasesModel->delete($id);
        return $this->json(null, 204);
    }
}
