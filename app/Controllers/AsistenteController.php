<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Response;
use App\Models\AsistenteModel;
use LLPhant\Chat\FunctionInfo\FunctionBuilder;
use LLPhant\Chat\Message;
use LLPhant\Chat\OpenAIChat;
use LLPhant\GeminiOpenAIConfig;
use LLPhant\Tool\HumanInTheLoopTool;

class AsistenteController extends BaseController
{
    private OpenAIChat $chat;

    public function __construct(
        private Response $response,
        private GeminiOpenAIConfig $config,
        private AsistenteModel $tools,
    ) {
        $this->chat = new OpenAIChat($config);

        $this->chat->addTool(
            FunctionBuilder::buildFunctionInfo(new HumanInTheLoopTool(), "askUser")
        );
        $this->chat->addTool(
            FunctionBuilder::buildFunctionInfo($tools, "queryClientes")
        );

        $this->chat->setSystemMessage(
            "Eres un instructor de entrenamiento para el gimnasio Sofit Gym.
            Tienes acceso a herramientas para consultar datos.
            Cuando utilices una herramienta, analiza el resultado y responde siempre de manera natural y útil al usuario."
        );
    }

    public function index()
    {
        return $this->templates->render("asistente");
    }

    public function generateText()
    {
        $body = $this->response->getParsedBody();
        $query = $body["message"];
        $messages = [Message::user($query)];

        // Agentic loop
        while (true) {
            $result = $this->chat->generateChatOrReturnFunctionCalled([$messages]);

            // The LLM produced a final text answer — we are done.
            if (is_string($result)) {
                return $this->response->json([
                    "message" => $result,
                ]);
            }

            // The LLM wants to call one or more tools.
            foreach ($result as $functionInfo) {
                // Resolve the tool call and collect messages to send back.
                $toolMessages = $functionInfo->callAndReturnAsOpenAIMessages();
                $messages = array_merge($messages, $toolMessages);
            }
        }
    }
}
