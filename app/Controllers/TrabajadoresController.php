<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Trabajador;
use App\Models\TrabajadorModel;

class TrabajadoresController extends Controller
{
    public function __construct(
        private $logger = new BitacoraModel(),
        private $trabajadorModel = new TrabajadorModel(),
    ) {}

    public function index(): string
    {
        $this->protect("trabajadores:ver");
        return $this->render('trabajadores');
    }

    public function query(): string
    {
        $this->protect("trabajadores:ver");

        $search = Request::query("search") ?? null;
        $id_rol = Request::queryInt("id_rol") ?? 0;

        $trabajadores = $this->trabajadorModel->query($search, $id_rol);
        return Response::json($trabajadores);
    }

    public function summary(): string
    {
        $this->protect("trabajadores:ver");
        $summary = $this->trabajadorModel->getSummary();
        return Response::json($summary);
    }

    public function find(): ?string
    {
        $this->protect("trabajadores:ver");

        $id = $this->getId();
        $trabajador = $this->trabajadorModel->find($id);

        return $trabajador
            ? Response::json($trabajador)
            : Response::noContent();
    }

    public function insert(): string
    {
        $this->protect("trabajadores:crear");

        $new = $this->validateBody();
        $id = $new->cedula;

        if ($this->trabajadorModel->checkDuplicate($id)) {
            return Response::json(
                ['message' => 'El trabajador ya existe'],
                Status::CONFLICT
            );
        }

        $new = $this->trabajadorModel->insert($new);
        $this->logger->log("Trabajador '{cedula}' creado", [
            "modulo" => "trabajadores",
            "accion" => "crear",

            'cedula'        => $new->cedula,
            'datos_nuevos'  => $new,
        ]);

        return Response::json($new, Status::CREATED);
    }

    public function update(): string
    {
        $this->protect("trabajadores:editar");

        $id = $this->getId();
        $new = $this->validateBody();

        $old = $this->trabajadorModel->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $new = $this->trabajadorModel->update($id, $new);
        $this->logger->log("Trabajador '{cedula}' actualizado", [
            "modulo" => "trabajadores",
            "accion" => "editar",

            'cedula'        => $old->cedula,
            'datos_previos' => $old,
            'datos_nuevos'  => $new,
        ]);

        return Response::json($new, Status::CREATED);
    }

    public function delete(): string|null
    {
        $this->protect("trabajadores:eliminar");
        $id = $this->getId();

        $old = $this->trabajadorModel->find($id);
        if (!$old) {
            return $this->notFound();
        }

        $this->trabajadorModel->delete($id);
        $this->logger->log("Trabajador '{cedula}' eliminado", [
            "modulo" => "trabajadores",
            "accion" => "eliminar",

            'cedula'        => $old->cedula,
            'datos_previos' => $old,
        ]);

        return Response::noContent();
    }

    private function notFound(): string
    {
        return Response::json(
            ["message" => "Trabajador no encontrado"],
            Status::NOT_FOUND
        );
    }

    private function getId(): string
    {
        return Request::query("id") ?? "";
    }

    private function validateBody(): Trabajador
    {
        $body = Request::getParsedBody();
        return Tools::map(Trabajador::class, $body);
    }
}
