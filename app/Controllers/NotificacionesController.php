<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Auth\UsuarioSession;
use App\Helpers\Auth\UsuarioSessionDto;
use App\Helpers\Response;
use App\Models\NotificacionDTO;
use App\Models\NotificacionesModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class NotificacionesController extends BaseController
{
    private UsuarioSessionDto $usuario;

    public function __construct(
        private Response $response,
        private TreeMapper $mapper,
        private NotificacionesModel $notificacionesModel,
    ) {
        $this->usuario = UsuarioSession::getUsuario();
    }

    public function query()
    {
        $id_usuario = (int)($_GET["id_usuario"] ?? $this->usuario->id);
        $results = $this->notificacionesModel->query($id_usuario);
        return $this->response->json($results);
    }

    public function find(): ?string
    {
        $id = $this->getParamId();
        $data = $this->notificacionesModel->find($this->usuario->id, $id);

        return $data
            ? $this->response->json($data)
            : $this->response->empty(404);
    }

    public function leido()
    {
        $id = $this->getParamId();
        $leido = (bool)($_GET["leido"] ?? true);

        $this->notificacionesModel->setLeido($this->usuario->id, $id, $leido);
        return $this->response->empty(204);
    }

    public function leerTodas()
    {
        $this->notificacionesModel->setLeidoTodas($this->usuario->id);
        return $this->response->empty(204);
    }

    public function sendMultiple(): null
    {
        $body = $this->response->getParsedBody();

        $id_usuarios =
            $body["id_usuarios"]
            ?? [$this->usuario->id]
            ?? throw new Exception("Una lista de 'id_usuarios' es requerido");
        $data = $this->mapper->map(NotificacionDTO::class, $body);

        $this->notificacionesModel->sendToUsuarios(
            $id_usuarios,
            notificacion: $data,
        );
        return $this->response->empty(204);
    }

    private function getParamId(): int
    {
        return (int)(
            $_GET['id']
            ?? throw new Exception("'id' param is required")
        );
    }
}
