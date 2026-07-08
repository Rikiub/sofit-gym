<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\Auth\UserSession;
use App\Services\Auth\AuthenticatedUser;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\Notificacion;
use App\Models\NotificacionModel;
use Exception;

class NotificacionesController extends Controller
{
    private AuthenticatedUser $user;

    public function __construct(
        private $notifModel = new NotificacionModel(),
    ) {
        $this->user = UserSession::get();
    }

    public function query()
    {
        $id_usuario = Request::queryInt("id") ?? $this->user->id;
        $results = $this->notifModel->query($id_usuario);
        return Response::json($results);
    }

    public function find(): ?string
    {
        $id = Request::queryInt("id") ?? 0;
        $data = $this->notifModel->find($this->user->id, $id);

        return $data
            ? Response::json($data)
            : Response::noContent();
    }

    public function leido()
    {
        $id = Request::queryInt("id") ?? 0;
        $leido = Request::queryBool("leido") ?? true;

        $this->notifModel->setLeido($this->user->id, $id, $leido);
        return Response::noContent();
    }

    public function leerTodas()
    {
        $this->notifModel->setLeidoTodas($this->user->id);
        return Response::noContent();
    }

    public function sendMultiple(): null
    {
        $body = Request::getParsedBody();

        $id_usuarios =
            $body["id_usuarios"]
            ?? [$this->user->id]
            ?? throw new Exception("Una lista de 'id_usuarios' es requerido");
        $data = Tools::map(Notificacion::class, $body);

        $this->notifModel->sendByUsuarios(
            $id_usuarios,
            notificacion: $data,
        );

        return Response::noContent();
    }
}
