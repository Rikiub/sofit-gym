<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Status;
use App\Models\Trabajador;
use App\Models\TrabajadorModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class TrabajadoresController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private TrabajadorModel $trabajadorModel,
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
        return $this->json($trabajadores);
    }

    public function summary(): string
    {
        $this->protect("trabajadores:ver");
        $summary = $this->trabajadorModel->getSummary();
        return $this->json($summary);
    }

    public function find(): ?string
    {
        $this->protect("trabajadores:ver");

        $id = $this->getId();
        $trabajador = $this->trabajadorModel->find($id);

        return $trabajador
            ? $this->json($trabajador)
            : $this->json(null, Status::NOT_FOUND);
    }

    public function insert(): string
    {
        $this->protect("trabajadores:crear");

        $new = $this->validateBody();
        $id = $new->cedula;

        if ($this->trabajadorModel->checkDuplicate($id)) {
            return $this->json(
                ['message' => 'El trabajador ya existe'],
                Status::CONFLICT
            );
        }

        $new = $this->trabajadorModel->insert($new);
        $this->logger->info("Trabajador '{cedula}' creado", [
            'cedula'        => $new->cedula,
            'datos_nuevos'  => $new,
        ]);

        return $this->json($new, Status::CREATED);
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
        $this->logger->info("Trabajador '{cedula}' actualizado", [
            'cedula'        => $old->cedula,
            'datos_previos' => $old,
            'datos_nuevos'  => $new,
        ]);

        return $this->json($new, Status::CREATED);
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
        $this->logger->info("Trabajador '{cedula}' eliminado", [
            'cedula'        => $old->cedula,
            'datos_previos' => $old,
        ]);

        return $this->json(null, Status::NO_CONTENT);
    }

    private function notFound(): string
    {
        return $this->json(
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
        return $this->mapper->map(Trabajador::class, $body);
    }
}
