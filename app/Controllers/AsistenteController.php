<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Auth\Rol;
use App\Helpers\Auth\UsuarioSession;
use App\Helpers\Auth\UsuarioSessionDto;
use App\Helpers\Response;
use App\Models\AsistenteMensajeDTO;
use App\Models\AsistenteModel;
use App\Models\AsistenteSesionDTO;
use App\Models\RolAsistente;
use LLPhant\Chat\Enums\ChatRole;
use LLPhant\Chat\FunctionInfo\FunctionBuilder;
use LLPhant\Chat\Message;
use LLPhant\Chat\OpenAIChat;
use LLPhant\GeminiOpenAIConfig;
use LLPhant\Tool\HumanInTheLoopTool;

class AsistenteController extends BaseController
{
    private OpenAIChat $chat;

    private UsuarioSessionDto $usuario;
    private AsistenteSesionDTO $sesion;

    /** @var Message[] */
    private array $messages;

    public function __construct(
        private Response $response,
        private GeminiOpenAIConfig $config,
        private AsistenteModel $asistenteModel,
    ) {
        $this->chat = new OpenAIChat($config);
        $this->usuario =  UsuarioSession::getUsuario();
    }

    public function index()
    {
        $this->initSesion();
        return $this->templates->render("asistente");
    }

    public function initSesion(): void
    {
        if (isset($this->sesion)) return;

        $systemPrompt = file_get_contents(ROOT_DIR . "/app/system_prompt.md");
        $this->chat->setSystemMessage($systemPrompt);

        // Recuperar historial de mensajes
        $_sesion = $this->asistenteModel->getLastSesion($this->usuario->id);
        if (!$_sesion) {
            $this->newSesion();
        } else {
            $this->sesion = $_sesion;
        }

        $this->messages = array_map(function (AsistenteMensajeDTO $mensaje) {
            return match ($mensaje->rol) {
                RolAsistente::Sistema => Message::system($mensaje->contenido),
                RolAsistente::Asistente => Message::assistant($mensaje->contenido),
                RolAsistente::Usuario => Message::user($mensaje->contenido),
                RolAsistente::Herramienta => Message::toolResult($mensaje->contenido),
            };
        }, $this->sesion->mensajes);

        // Configurar herramientas
        $this->chat->addTool(
            FunctionBuilder::buildFunctionInfo(new HumanInTheLoopTool(), "askUser")
        );

        // Herramientas accesibles por todos los roles
        $this->addTool("queryClientes");

        // Limitar herramientas segun rol
        if ($this->usuario->rol === Rol::Administrador) {
            $this->addTool("queryTrabajadores");
        }
    }

    private function addTool(string $method)
    {
        $this->chat->addTool(
            FunctionBuilder::buildFunctionInfo($this->asistenteModel, $method)
        );
    }

    public function querySesiones()
    {
        $sesiones = $this->asistenteModel->querySesiones();
        return $this->response->json($sesiones);
    }

    public function findSesion(): string
    {
        $id = (int) $_GET["id"];
        $sesion = $this->asistenteModel->findSesion($id);

        if (!$sesion || $sesion->id_usuario !== $this->usuario->id) {
            return $this->response->json(["message" => "Sesion no encontrada o no autorizada"], 403);
        }

        return $this->response->json($sesion);
    }

    public function newSesion(): string
    {
        $this->sesion = $this->asistenteModel->insertSesion(new AsistenteSesionDTO(
            id_usuario: $this->usuario->id,
            modelo_usado: $this->chat->model,
        ));
        $this->messages = [];

        return $this->response->json($this->sesion);
    }

    public function generateText()
    {
        $this->initSesion();

        $body = $this->response->getParsedBody();
        $content = $body["message"];
        if (!$content)
            return $this->response->json(["message" => "Se debe proporcionar el parametro 'message'"], 400);

        // Precargar mensaje del usuario
        $this->messages[] = Message::user($content);

        // Loopear hasta que el asistente devuelva la respuesta completa o exceda los 5 intentos
        $maxLoops = 5;
        $loopCount = 0;

        while ($loopCount < $maxLoops) {
            $loopCount++;

            // Llamar al modelo de AI
            $result = $this->chat->generateChatOrReturnFunctionCalled($this->messages);

            // Si hubo exito, entonces guardar mensaje del usuario en la base de datos
            $this->asistenteModel->insertMensaje(new AsistenteMensajeDTO(
                id_sesion: $this->sesion->id_sesion,
                rol: RolAsistente::Usuario,
                contenido: $content,
            ));

            // El LLM produjo una respuesta final. Devolver resultado.
            if (is_string($result)) {
                $this->asistenteModel->insertMensaje(new AsistenteMensajeDTO(
                    id_sesion: $this->sesion->id_sesion,
                    rol: RolAsistente::Asistente,
                    contenido: $result,
                ));
                return $this->response->json([
                    "message" => $result,
                ]);
            }

            // El LLM quiere llamar una o mas herramientas.
            foreach ($result as $functionInfo) {
                // Resolver la llamada a la herramienta y recolectar los mensajes devuelta.
                $toolMessages = $functionInfo->callAndReturnAsOpenAIMessages();

                foreach ($toolMessages as $msg) {
                    $realRol = ($msg->role === ChatRole::Assistant)
                        ? RolAsistente::Asistente
                        : RolAsistente::Herramienta;

                    $this->asistenteModel->insertMensaje(new AsistenteMensajeDTO(
                        id_sesion: $this->sesion->id_sesion,
                        rol: $realRol,
                        contenido: $msg->content,
                    ));
                    $this->messages[] = $msg;
                }
            }
        }
    }
}
