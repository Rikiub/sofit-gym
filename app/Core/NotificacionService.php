<?php

namespace App\Core;

use App\Core\Auth\UserRol;
use App\Models\NotificacionDTO;
use App\Models\NotificacionesModel;
use PHPMailer\PHPMailer\PHPMailer;
use Exception;

class NotificacionService
{
    public function __construct(
        private NotificacionesModel $notificacionesModel,
        private PHPMailer $mailer,
    ) {}

    /**
     * Envía una notificación a todos los usuarios con los roles asignados
     * 
     * @param UserRol[] $roles
     */
    public function sendByRol(array $roles, string $titulo, string $contenido): void
    {
        $roles = array_map(
            fn($item) => $item instanceof UserRol ? $item->value : $item,
            $roles
        );

        $this->notificacionesModel->sendByRol(
            $roles,
            new NotificacionDTO(
                titulo: $titulo,
                contenido: $contenido,
            )
        );
    }

    /**
     * Envía una notificación a uno o varios usuarios
     */
    public function sendToUsers(array $userIds, string $titulo, string $contenido): void
    {
        $this->notificacionesModel->sendByUsuarios(
            $userIds,
            new NotificacionDTO(
                titulo: $titulo,
                contenido: $contenido,
            )
        );
    }

    /**
     * Envía una notificación por correo electrónico
     */
    public function sendEmail(string $email, string $subject, string $body): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return false;
        }
    }
}
