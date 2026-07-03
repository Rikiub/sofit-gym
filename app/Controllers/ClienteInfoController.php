<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\StatusCode;
use App\Models\Clientes\ClientesModel;
use App\Models\Clientes\SeguimientoFisicoDTO;
use App\Models\Clientes\SeguimientoNutricionalDTO;
use App\Models\Clientes\SegumientoFisicoModel;
use App\Models\Clientes\SegumientoNutricionalModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class ClienteInfoController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private ClientesModel $clientesModel,
        private SegumientoFisicoModel $fisicoModel,
        private SegumientoNutricionalModel $nutricionalModel,
    ) {}

    // INFORMACIÓN DE CLIENTE
    public function index(): string
    {
        $this->protect("clientes:ver");
        $cedula = $this->getCedula();

        if (!$this->clientesModel->find($cedula)) {
            Response::redirect([
                "page" => "error",
                "status" => StatusCode::NOT_FOUND,
            ]);
        }

        return $this->templates->render('clientes/info', [
            "cedula" => $cedula,
        ]);
    }

    // SEGUIMIENTO FISICO
    public function queryFisico(): ?string
    {
        $this->protect("clientes:ver");
        $cedula = $this->getCedula();

        if (!$this->clientesModel->find($cedula)) {
            return $this->notFoundCliente();
        }

        $seguimiento = $this->fisicoModel->queryByCliente($cedula);
        return $this->json($seguimiento);
    }

    public function insertFisico(): string
    {
        $this->protect("clientes:crear");

        $seguimiento = $this->validateBodyFisico();
        $cedula = $this->getCedula();

        if (!$this->clientesModel->find($cedula)) {
            return $this->notFoundCliente();
        }

        $new = $this->fisicoModel->insert($cedula, $seguimiento);
        $this->logger->info("Seguimiento físico para cliente '{cedula}' registrado", [
            'cedula'        => $cedula,
            'id_seguimiento' => $new->id_seguimiento,
            'datos_nuevos'  => $new,
        ]);

        return $this->json($new, StatusCode::CREATED);
    }

    public function deleteFisico(): string|null
    {
        $this->protect("clientes:eliminar");
        $id = $this->getIdSeguimiento();

        $old = $this->fisicoModel->find($id);
        if (!$old) {
            return $this->conflict(false, $id);
        }

        $this->fisicoModel->delete($id);
        $this->logger->info("Seguimiento físico '{id_seguimiento}' eliminado", [
            'id_seguimiento' => $id,
            'cedula' => $old->cedula_cliente,
            'datos_previos'  => $old,
        ]);

        return $this->json(null, StatusCode::NO_CONTENT);
    }

    private function validateBodyFisico(): SeguimientoFisicoDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(SeguimientoFisicoDTO::class, $body);
    }

    // SEGUMIENTO NUTRICIONAL
    public function queryNutricion(): ?string
    {
        $this->protect("clientes:ver");
        $cedula = $this->getCedula();

        if (!$this->clientesModel->find($cedula)) {
            return $this->notFoundCliente();
        }

        $seguimientos = $this->nutricionalModel->queryByCliente($cedula);
        return $this->json($seguimientos);
    }

    public function insertNutricion(): string
    {
        $this->protect("clientes:crear");

        $seguimiento = $this->validateBodyNutricion();
        $cedula = $this->getCedula();

        if (!$this->clientesModel->find($cedula)) {
            return $this->notFoundCliente();
        }

        $new = $this->nutricionalModel->insert($cedula, $seguimiento);
        $this->logger->info("Seguimiento nutricional para cliente '{cedula}' registrado", [
            'cedula' => $cedula,
            'id_seguimiento' => $new->id_seguimiento,
            'datos_nuevos' => $new,
        ]);

        return $this->json($new, StatusCode::CREATED);
    }

    public function deleteNutricion(): string|null
    {
        $this->protect("clientes:eliminar");
        $id = $this->getIdSeguimiento();

        $old = $this->nutricionalModel->find($id);
        if (!$old) {
            return $this->conflict(false, $id);
        }

        $this->nutricionalModel->delete($id);
        $this->logger->info("Seguimiento nutricional '{id_seguimiento}' eliminado", [
            'id_seguimiento' => $id,
            'cedula'         => $old->cedula_cliente,
            'datos_previos'  => $old,
        ]);

        return $this->json(null, StatusCode::NO_CONTENT);
    }

    private function validateBodyNutricion(): SeguimientoNutricionalDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(SeguimientoNutricionalDTO::class, $body);
    }

    // Helpers
    private function conflict(bool $exists, string $id): string
    {
        if ($exists) {
            $message = "El seguimiento {$id} ya existe";
            $code = StatusCode::BAD_REQUEST;
        } else {
            $message = "El seguimiento {$id} no existe";
            $code = StatusCode::NOT_FOUND;
        }
        return $this->json(['message' => $message], $code);
    }

    private function getIdSeguimiento(): int
    {
        return Request::queryInt("id_seguimiento") ?? 0;
    }

    private function getCedula(): string
    {
        return Request::query("cedula") ?? "";
    }

    private function notFoundCliente(): string
    {
        return $this->json(["message" => "Cliente no encontrado"], StatusCode::NOT_FOUND);
    }
}
