<?php

namespace App\Controllers;

use App\Core\Route;
use App\Services\Auth\UserSession;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Tools;
use App\Models\Notificacion;
use App\Models\NotificacionModel;
use Exception;

$notifModel = new NotificacionModel();
$user = UserSession::get();

switch (Route::action()) {
    case "query":
        $id_usuario = Request::queryInt("id") ?? $user->id;
        $results = $notifModel->query($id_usuario);
        return Response::json($results);

    case "find":
        $id = Request::queryInt("id") ?? 0;
        $data = $notifModel->find($user->id, $id);

        return $data
            ? Response::json($data)
            : Response::noContent();

    case "leido":
        $id = Request::queryInt("id") ?? 0;
        $leido = Request::queryBool("leido") ?? true;

        $notifModel->setLeido($user->id, $id, $leido);
        return Response::noContent();

    case "leerTodas":
        $notifModel->setLeidoTodas($user->id);
        return Response::noContent();

    case "sendMultiple":
        $body = Request::getParsedBody();

        $id_usuarios =
            $body["id_usuarios"]
            ?? [$user->id]
            ?? throw new Exception("Una lista de 'id_usuarios' es requerido");
        $data = Tools::map(Notificacion::class, $body);

        $notifModel->sendByUsuarios(
            $id_usuarios,
            notificacion: $data,
        );

        return Response::noContent();
}
