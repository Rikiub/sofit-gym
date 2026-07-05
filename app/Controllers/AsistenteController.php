<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\Auth\UserSession;
use App\Services\Auth\AuthenticatedUser;
use App\Core\Config;
use App\Core\Http\Request;
use App\Core\Http\StatusCode;
use App\Models\AsistenteMensaje;
use App\Models\AsistenteModel;
use App\Models\AsistenteSesion;
use App\Models\RolAsistente;
use LLPhant\Chat\FunctionInfo\FunctionBuilder;
use LLPhant\Chat\Message;
use LLPhant\Chat\OpenAIChat;
use LLPhant\GeminiOpenAIConfig;
use LLPhant\Tool\HumanInTheLoopTool;

class AsistenteController extends Controller
{
    private OpenAIChat $chat;

    private AuthenticatedUser $user;
    private AsistenteSesion $sesion;

    /** @var Message[] */
    private array $messages;

    public function __construct(
        private GeminiOpenAIConfig $config,
        private AsistenteModel $asistenteModel,
    ) {
        $this->chat = new OpenAIChat($config);
        $this->user =  UserSession::get();
    }

    public function index()
    {
        $this->initSesion();
        return $this->templates->render("asistente");
    }

    public function initSesion(): void
    {
        if (isset($this->sesion)) return;

        $systemPrompt = file_get_contents(Config::get("fs.base") . "/config/system_prompt.md");
        $this->chat->setSystemMessage($systemPrompt);

        // Recuperar historial de mensajes
        $_sesion = $this->asistenteModel->getLastSesion($this->user->id);
        if (!$_sesion) {
            $this->newSesion();
        } else {
            $this->sesion = $_sesion;
        }

        $this->messages = array_map(function (AsistenteMensaje $mensaje) {
            return match ($mensaje->rol) {
                RolAsistente::Asistente => Message::assistant($mensaje->contenido),
                RolAsistente::Usuario => Message::user($mensaje->contenido),
            };
        }, $this->sesion->mensajes);

        // Configurar herramientas
        $this->chat->addTool(
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
            if ($this->user->hasPermiso($permission)) {
                foreach ($tools as $tool) {
                    $this->addTool($tool);
                }
            }
        }
    }

    private function addTool(string $method)
    {
        $this->chat->addTool(
            FunctionBuilder::buildFunctionInfo($this->asistenteModel, $method)
        );
    }

    public function generateText()
    {
        $this->initSesion();

        // Obtener mensaje desde el parametro
        $body = Request::getParsedBody();
        $content = $body["message"];
        if (!$content) {
            return $this->json(
                ["message" => "Se debe proporcionar el parametro 'message'"],
                StatusCode::BAD_REQUEST
            );
        }

        // Almacenar mensaje del user
        $this->asistenteModel->insertMensaje(new AsistenteMensaje(
            id_sesion: $this->sesion->id_sesion,
            rol: RolAsistente::Usuario,
            contenido: $content,
        ));
        $this->messages[] = Message::user($content);

        // Loopear hasta que el asistente devuelva la respuesta completa o exceda los intentos
        $maxLoops = 5;
        $loopCount = 0;

        while ($loopCount < $maxLoops) {
            $loopCount++;

            // Llamar al modelo de AI
            $result = $this->chat->generateChatOrReturnFunctionCalled($this->messages);

            // El LLM produjo una respuesta final. Devolver resultado.
            if (is_string($result)) {
                $this->asistenteModel->insertMensaje(new AsistenteMensaje(
                    id_sesion: $this->sesion->id_sesion,
                    rol: RolAsistente::Asistente,
                    contenido: $result,
                ));
                return $this->json([
                    "message" => $result,
                ]);
            }

            // El LLM quiere llamar una o mas herramientas.
            // Resolver la llamada a la herramienta y recolectar los mensajes devuelta.
            foreach ($result as $functionInfo) {
                $toolMessages = $functionInfo->callAndReturnAsOpenAIMessages();
                foreach ($toolMessages as $msg) {
                    $this->messages[] = $msg;
                }
            }
        }

        return $this->json(
            ["message" => "Excedido el límite de vueltas."],
            StatusCode::INTERNAL_SERVER_ERROR
        );
    }

    public function querySesiones()
    {
        $sesiones = $this->asistenteModel->querySesiones();
        return $this->json($sesiones);
    }

    public function findSesion(): string
    {
        $id = (int) $_GET["id"];
        $sesion = $this->asistenteModel->findSesion($id);

        if (!$sesion || $sesion->id_usuario !== $this->user->id) {
            return $this->json(
                ["message" => "Sesion no encontrada o no autorizada"],
                StatusCode::FORBIDDEN
            );
        }

        return $this->json($sesion);
    }

    public function newSesion(): string
    {
        $this->sesion = $this->asistenteModel->insertSesion(new AsistenteSesion(
            id_usuario: $this->user->id,
            modelo_usado: $this->chat->model,
        ));
        $this->messages = [];

        return $this->json($this->sesion);
    }
}
