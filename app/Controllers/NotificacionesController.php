<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Auth\UsuarioSession;
use App\Core\Auth\UsuarioSessionDto;
use App\Core\Http\Request;
use App\Models\NotificacionDTO;
use App\Models\NotificacionesModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class NotificacionesController extends Controller
{
    private UsuarioSessionDto $usuario;

    public function __construct(
        private TreeMapper $mapper,
        private NotificacionesModel $notificacionesModel,
    ) {
        $this->usuario = UsuarioSession::getCurrent();
    }

    public function query()
    {
        $id_usuario = Request::queryInt("id") ?? $this->usuario->id;
        $results = $this->notificacionesModel->query($id_usuario);
        return $this->json($results);
    }

    public function find(): ?string
    {
        $id = Request::queryInt("id") ?? 0;
        $data = $this->notificacionesModel->find($this->usuario->id, $id);

        return $data
            ? $this->json($data)
            : $this->json(null, 404);
    }

    public function leido()
    {
        $id = Request::queryInt("id") ?? 0;
        $leido = Request::queryBool("leido") ?? true;

        $this->notificacionesModel->setLeido($this->usuario->id, $id, $leido);
        return $this->json(null, 204);
    }

    public function leerTodas()
    {
        $this->notificacionesModel->setLeidoTodas($this->usuario->id);
        return $this->json(null, 204);
    }

    public function sendMultiple(): null
    {
        $body = $this->getParsedBody();

        $id_usuarios =
            $body["id_usuarios"]
            ?? [$this->usuario->id]
            ?? throw new Exception("Una lista de 'id_usuarios' es requerido");
        $data = $this->mapper->map(NotificacionDTO::class, $body);

        $this->notificacionesModel->sendToUsuarios(
            $id_usuarios,
            notificacion: $data,
        );
        return $this->json(null, 204);
    }
}
