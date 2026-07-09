<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Clientes\ClienteModel;
use App\Models\Clientes\SeguimientoFisico;
use App\Models\Clientes\SeguimientoNutricional;
use App\Models\Clientes\SegumientoFisicoModel;
use App\Models\Clientes\SegumientoNutricionalModel;

class ClienteInfoController extends Controller
{
    public function __construct(
        private $logger = new BitacoraModel(),
        private $clienteModel = new ClienteModel(),
        private $fisicoModel = new SegumientoFisicoModel(),
        private $nutricionalModel = new SegumientoNutricionalModel(),
    ) {}

    // INFORMACIÓN DE CLIENTE
    public function index(): string
    {
        $this->protect("clientes:ver");
        $cedula = $this->getCedula();

        if (!$this->clienteModel->find($cedula)) {
            Response::redirect([
                "page" => "error",
                "status" => Status::NOT_FOUND,
            ]);
        }

        return $this->render('clientes/info', [
            "cedula" => $cedula,
        ]);
    }

    // SEGUIMIENTO FISICO
    public function queryFisico(): ?string
    {
        $this->protect("clientes:ver");
        $cedula = $this->getCedula();

        if (!$this->clienteModel->find($cedula)) {
            return $this->notFoundCliente();
        }

        $seguimiento = $this->fisicoModel->queryByCliente($cedula);
        return Response::json($seguimiento);
    }

    public function insertFisico(): string
    {
        $this->protect("clientes:crear");

        $seguimiento = $this->validateBodyFisico();
        $cedula = $this->getCedula();

        if (!$this->clienteModel->find($cedula)) {
            return $this->notFoundCliente();
        }

        $new = $this->fisicoModel->insert($cedula, $seguimiento);
        $this->logger->log("Seguimiento físico para cliente '{cedula}' registrado", [
            "modulo" => "cliente_info",
            "accion" => "crear_seg_fisico",

            'cedula'        => $cedula,
            'id_seguimiento' => $new->id_seguimiento,
            'datos_nuevos'  => $new,
        ]);

        return Response::json($new, Status::CREATED);
    }

    public function deleteFisico(): string|null
    {
        $this->protect("clientes:eliminar");
        $id = $this->getIdSeguimiento();

        $old = $this->fisicoModel->find($id);
        if (!$old) {
            return $this->notFoundSeg();
        }

        $this->fisicoModel->delete($id);
        $this->logger->log("Seguimiento físico '{id_seguimiento}' eliminado", [
            "modulo" => "cliente_info",
            "accion" => "eliminar_seg_fisico",

            'id_seguimiento' => $id,
            'cedula' => $old->cedula_cliente,
            'datos_previos'  => $old,
        ]);

        return Response::noContent();
    }

    private function validateBodyFisico(): SeguimientoFisico
    {
        $body = Request::getParsedBody();
        return Tools::map(SeguimientoFisico::class, $body);
    }

    // SEGUMIENTO NUTRICIONAL
    public function queryNutricion(): ?string
    {
        $this->protect("clientes:ver");
        $cedula = $this->getCedula();

        if (!$this->clienteModel->find($cedula)) {
            return $this->notFoundCliente();
        }

        $seguimientos = $this->nutricionalModel->queryByCliente($cedula);
        return Response::json($seguimientos);
    }

    public function insertNutricion(): string
    {
        $this->protect("clientes:crear");

        $seguimiento = $this->validateBodyNutricion();
        $cedula = $this->getCedula();

        if (!$this->clienteModel->find($cedula)) {
            return $this->notFoundCliente();
        }

        $new = $this->nutricionalModel->insert($cedula, $seguimiento);
        $this->logger->log("Seguimiento nutricional para cliente '{cedula}' registrado", [
            "modulo" => "cliente_info",
            "accion" => "crear_seg_nutricion",

            'cedula' => $cedula,
            'id_seguimiento' => $new->id_seguimiento,
            'datos_nuevos' => $new,
        ]);

        return Response::json($new, Status::CREATED);
    }

    public function deleteNutricion(): string|null
    {
        $this->protect("clientes:eliminar");
        $id = $this->getIdSeguimiento();

        $old = $this->nutricionalModel->find($id);
        if (!$old) {
            return $this->notFoundSeg();
        }

        $this->nutricionalModel->delete($id);
        $this->logger->log("Seguimiento nutricional '{id_seguimiento}' eliminado", [
            "modulo" => "cliente_info",
            "accion" => "eliminar_seg_nutricion",

            'id_seguimiento' => $id,
            'cedula'         => $old->cedula_cliente,
            'datos_previos'  => $old,
        ]);

        return Response::noContent();
    }

    private function validateBodyNutricion(): SeguimientoNutricional
    {
        $body = Request::getParsedBody();
        return Tools::map(SeguimientoNutricional::class, $body);
    }

    // Helpers
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
        return Response::json(["message" => "Cliente no encontrado"], Status::NOT_FOUND);
    }

    private function notFoundSeg(): string
    {
        return Response::json(['message' => "El seguimiento no existe"], Status::NOT_FOUND);
    }
}
