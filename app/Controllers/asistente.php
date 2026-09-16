<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Services\Auth\UserSession;
use App\Services\Auth\CurrentUser;
use App\Core\Config;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Models\AsistenteMensaje;
use App\Models\AsistenteModel;
use App\Models\AsistenteSesion;
use App\Models\OpcionModel;
use App\Models\RolAsistente;
use Error;
use LLPhant\Chat\FunctionInfo\FunctionBuilder;
use LLPhant\Chat\Message;
use LLPhant\Chat\OpenAIChat;
use LLPhant\GeminiOpenAIConfig;
use LLPhant\Tool\HumanInTheLoopTool;

$asistenteModel = new AsistenteModel();
$opcionModel = new OpcionModel();

$apiKey = $opcionModel->get("ai.api_key") ?: Config::get("ai.api_key");
$model = $opcionModel->get("ai.modelo") ?: Config::get("ai.model");

if (!$apiKey) {
    throw new Error("Debes definir la variable de entorno AI_API_KEY en el archivo .env");
}
if (!$model) {
    throw new Error("Debes definir la variable de entorno AI_MODEL en el archivo .env");
}

$config = new GeminiOpenAIConfig();
$config->apiKey = $apiKey;
$config->model = $model;

$chat = new OpenAIChat($config);
$user = UserSession::get();

// Estado compartido entre los helpers y los cases del switch
$state = [
    'sesion'   => null,
    'messages' => [],
];

function addTool(string $method): void
{
    global $chat, $asistenteModel;
    $chat->addTool(
        FunctionBuilder::buildFunctionInfo($asistenteModel, $method)
    );
}

/** Crea una nueva sesión y actualiza el estado global. Devuelve la sesión. */
function createSesion(): AsistenteSesion
{
    global $state, $asistenteModel, $user, $chat;

    $state['sesion'] = $asistenteModel->insertSesion(new AsistenteSesion(
        id_usuario: $user->id,
        modelo_usado: $chat->model,
    ));
    $state['messages'] = [];

    return $state['sesion'];
}

/** Inicializa chat, historial y herramientas. Idempotente. */
function initSesion(): void
{
    global $chat, $user, $state, $asistenteModel;

    if ($state['sesion'] !== null) return;

    $systemPrompt = file_get_contents(Config::get("fs.base") . "/config/system_prompt.md");
    $chat->setSystemMessage($systemPrompt);

    $_sesion = $asistenteModel->getLastSesion($user->id);
    if (!$_sesion) {
        createSesion();
    } else {
        $state['sesion'] = $_sesion;
    }

    $state['messages'] = array_map(function (AsistenteMensaje $mensaje) {
        return match ($mensaje->rol) {
            RolAsistente::Asistente => Message::assistant($mensaje->contenido),
            RolAsistente::Usuario => Message::user($mensaje->contenido),
        };
    }, $state['sesion']->mensajes);

    // Configurar herramientas
    $chat->addTool(
        FunctionBuilder::buildFunctionInfo(new HumanInTheLoopTool(), "askUser")
    );

    // Limitar herramientas segun permisos
    $permissionTools = [
        'trabajadores:ver' => ['queryTrabajadores'],
        'clientes:ver'     => [
            'queryClientes',
            'findCliente',
            'querySegFisico',
            'querySegNutricional'
        ],
        'asistencia:ver'   => ['queryAsistencias'],
        'rutinas:ver'      => ['queryRutinas'],
    ];

    foreach ($permissionTools as $permission => $tools) {
        if ($user->hasPermiso($permission)) {
            foreach ($tools as $tool) {
                addTool($tool);
            }
        }
    }
}

switch (ControllerTools::action()) {
    case "index":
        initSesion();
        return ControllerTools::render("asistente");

    case "generateText":
        initSesion();

        // Obtener mensaje desde el parametro
        $body = Request::getParsedBody();
        $content = $body["message"];
        if (!$content) {
            return Response::json(
                ["message" => "Se debe proporcionar el parametro 'message'"],
                Status::BAD_REQUEST
            );
        }

        // Almacenar mensaje del user
        $asistenteModel->insertMensaje(new AsistenteMensaje(
            id_sesion: $state['sesion']->id_sesion,
            rol: RolAsistente::Usuario,
            contenido: $content,
        ));
        $state['messages'][] = Message::user($content);

        // Loopear hasta que el asistente devuelva la respuesta completa o exceda los intentos
        $maxLoops = 5;
        $loopCount = 0;

        while ($loopCount < $maxLoops) {
            $loopCount++;

            // Llamar al modelo de AI
            $result = $chat->generateChatOrReturnFunctionCalled($state['messages']);

            // El LLM produjo una respuesta final. Devolver resultado.
            if (is_string($result)) {
                $asistenteModel->insertMensaje(new AsistenteMensaje(
                    id_sesion: $state['sesion']->id_sesion,
                    rol: RolAsistente::Asistente,
                    contenido: $result,
                ));
                return Response::json([
                    "message" => $result,
                ]);
            }

            // El LLM quiere llamar una o mas herramientas.
            foreach ($result as $functionInfo) {
                $toolMessages = $functionInfo->callAndReturnAsOpenAIMessages();
                foreach ($toolMessages as $msg) {
                    $state['messages'][] = $msg;
                }
            }
        }

        return Response::json(
            ["message" => "Excedido el límite de vueltas."],
            Status::INTERNAL_SERVER_ERROR
        );

    case "querySesiones":
        $sesiones = $asistenteModel->querySesiones();
        return Response::json($sesiones);

    case "findSesion":
        $id = (int) $_GET["id"];
        $sesionFound = $asistenteModel->findSesion($id);

        if (!$sesionFound || $sesionFound->id_usuario !== $user->id) {
            return Response::json(
                ["message" => "Sesion no encontrada o no autorizada"],
                Status::FORBIDDEN
            );
        }

        return Response::json($sesionFound);

    case "newSesion":
        $nueva = createSesion();
        return Response::json($nueva);
}
