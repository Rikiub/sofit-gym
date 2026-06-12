<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Response;
use App\Models\ClaseGrupalDTO;
use App\Models\ClasesGrupalesModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class ClasesGrupalesController extends BaseController
{
    public function __construct(
        private Response $response,
        private TreeMapper $mapper,
        private ClasesGrupalesModel $clasesModel,
    ) {}

    public function index(): string
    {
        return $this->templates->render('clases');
    }

    private function getIdParam(): int
    {
        $id = $_GET['id'] ?? '';
        if (!$id) {
            throw new Exception("'id' param is required");
        }
        return (int) $id;
    }

    public function query(): string
    {
        $clases = $this->clasesModel->query();
        return $this->response->json($clases);
    }

    public function find(): ?string
    {
        $cedula = $this->getIdParam();
        $clase = $this->clasesModel->find($cedula);

        if (!$clase) {
            return $this->response->empty(404);
        }

        return $this->response->json($clase);
    }

    public function insert(): string
    {
        $body = $this->response->getParsedBody();
        $clase = $this->mapper->map(ClaseGrupalDTO::class, $body);

        $clase = $this->clasesModel->insert($clase);
        return $this->response->json($clase, 201);
    }

    public function update(): string
    {
        $body = $this->response->getParsedBody();
        $clase = $this->mapper->map(ClaseGrupalDTO::class, $body);

        if (!$this->clasesModel->find($clase->id_clase)) {
            return $this->response->json(['message' => 'No existe'], 400);
        }

        $clase = $this->clasesModel->update($clase);
        return $this->response->json($clase, 201);
    }

    public function delete(): string|null
    {
        $id = $this->getIdParam();

        if (!$this->clasesModel->find($id)) {
            return $this->response->json(['message' => 'No existe'], 404);
        }

        $this->clasesModel->delete($id);
        return $this->response->empty(204);
    }
}
