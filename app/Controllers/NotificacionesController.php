<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\Auth\UserSession;
use App\Services\Auth\AuthenticatedUser;
use App\Core\Http\Request;
use App\Core\Http\StatusCode;
use App\Models\Notificacion;
use App\Models\NotificacionModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class NotificacionesController extends Controller
{
    private AuthenticatedUser $user;

    public function __construct(
        private TreeMapper $mapper,
        private NotificacionModel $notifModel,
    ) {
        $this->user = UserSession::get();
    }

    public function query()
    {
        $id_usuario = Request::queryInt("id") ?? $this->user->id;
        $results = $this->notifModel->query($id_usuario);
        return $this->json($results);
    }

    public function find(): ?string
    {
        $id = Request::queryInt("id") ?? 0;
        $data = $this->notifModel->find($this->user->id, $id);

        return $data
            ? $this->json($data)
            : $this->json(null, StatusCode::NOT_FOUND);
    }

    public function leido()
    {
        $id = Request::queryInt("id") ?? 0;
        $leido = Request::queryBool("leido") ?? true;

        $this->notifModel->setLeido($this->user->id, $id, $leido);
        return $this->json(null, StatusCode::NO_CONTENT);
    }

    public function leerTodas()
    {
        $this->notifModel->setLeidoTodas($this->user->id);
        return $this->json(null, StatusCode::NO_CONTENT);
    }

    public function sendMultiple(): null
    {
        $body = Request::getParsedBody();

        $id_usuarios =
            $body["id_usuarios"]
            ?? [$this->user->id]
            ?? throw new Exception("Una lista de 'id_usuarios' es requerido");
        $data = $this->mapper->map(Notificacion::class, $body);

        $this->notifModel->sendByUsuarios(
            $id_usuarios,
            notificacion: $data,
        );
        return $this->json(null, StatusCode::NO_CONTENT);
    }
}
