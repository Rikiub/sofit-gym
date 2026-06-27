<?php

namespace App\Controllers;

use App\Core\Auth\UsuarioSession;
use App\Core\BitacoraLogger;
use App\Core\Http\Request;
use App\Core\Http\Response;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\NormalizerBuilder;
use DI\Attribute\Inject;
use League\Plates\Engine;

abstract class Controller
{
    /** 
     * Atributos inyectados por PHP-DI automaticamente.
     * Estan aqui por comodidad ya que se usan en la mayoria de vistas.
     */
    #[Inject]
    protected Engine $templates;
    #[Inject]
    protected BitacoraLogger $logger;

    /** Dependencias para helpers internos. */
    #[Inject]
    private MapperBuilder $mapper;
    #[Inject]
    private NormalizerBuilder $normalizer;

    /** Intentar obtener datos desde el POST o JSON input.
     * 
     * @template T
     * @param ?callable(...): T $dto
     * @return T
     */
    protected function getParsedBody(?callable $dto = null)
    {
        $body = Request::getParsedBody();

        if ($dto) {
            // Validar $body segun el objeto pasado
            return $this->mapper->argumentsMapper()
                ->mapArguments($dto, $body);
        }
        return $body;
    }

    protected function json(mixed $data, int $status = 200): string|null
    {
        // No Content
        if ($data === null) {
            return Response::noContent($status);
        }

        // JSON
        Response::withJsonHeaders();
        return $this->normalizer->normalizer(Format::json())
            ->normalize($data);
    }

    /**
     * Redirigir a pagina de error.
     */
    public static function redirectToError(int $status = 404)
    {
        Response::redirect([
            'page' => 'error',
            'status' => $status,
        ], 404);
    }

    /** Bloquea el acceso a una ruta y redirige a la pagina de error si el usuario no tiene el permiso. */
    protected function protect(string $permiso)
    {
        $usuario = UsuarioSession::getUsuario();

        if (!$usuario || !$usuario->hasPermiso($permiso)) {
            if (Request::wantsJson()) {
                // Enviar JSON con mensaje de error
                echo Response::json(["message" => "No tienes permiso para usar esta accion"], 403);
            } else {
                // Redireccionar a pagina de error
                $this->redirectToError(403);
            }

            exit;
        }
    }
}
