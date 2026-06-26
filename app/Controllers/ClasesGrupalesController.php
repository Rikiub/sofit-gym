<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Http\Request;
use App\Models\ClaseGrupalDTO;
use App\Models\ClasesGrupalesModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class ClasesGrupalesController extends BaseController
{
    public function __construct(
        private TreeMapper $mapper,
        private ClasesGrupalesModel $clasesModel,
    ) {}

    public function index(): string
    {
        $this->protect("clasesGrupales:ver");
        return $this->templates->render('clases');
    }

    public function query(): string
    {
        $this->protect("clasesGrupales:ver");
        $clases = $this->clasesModel->query();
        return $this->json($clases);
    }

    public function find(): ?string
    {
        $this->protect("clasesGrupales:ver");

        $cedula = $this->getParamId();
        $clase = $this->clasesModel->find($cedula);

        return $clase
            ? $this->json($clase)
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("clasesGrupales:crear");

        $body = Request::getParsedBody();
        $clase = $this->mapper->map(ClaseGrupalDTO::class, $body);

        $clase = $this->clasesModel->insert($clase);
        return $this->json($clase, 201);
    }

    public function update(): string
    {
        $this->protect("clasesGrupales:editar");

        $body = Request::getParsedBody();
        $clase = $this->mapper->map(ClaseGrupalDTO::class, $body);

        if (!$this->clasesModel->find($clase->id_clase)) {
            return $this->json(['message' => 'No existe'], 400);
        }

        $clase = $this->clasesModel->update($clase);
        return $this->json($clase, 201);
    }

    public function delete(): string|null
    {
        $this->protect("clasesGrupales:eliminar");
        $id = $this->getParamId();

        if (!$this->clasesModel->find($id)) {
            return $this->json(['message' => 'No existe'], 404);
        }

        $this->clasesModel->delete($id);
        return $this->json(null, 204);
    }

    private function getParamId(): int
    {
        return (int) Request::requiredParam("id");
    }
}
