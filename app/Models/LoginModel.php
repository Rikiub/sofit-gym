<?php

namespace App\Models;

use App\Models\BaseModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO;

class LoginModel extends BaseModel
{
    public function __construct(
        PDO $pdo,
        private PHPMailer $mailer,
    ) {
        parent::__construct($pdo);
    }

    /**
     * Método para enviar el correo de recuperación mediante PHPMailer
     */
    public function enviarCorreo(string $email, string $codigo)
    {
        $mail = $this->mailer;
        $mail->addAddress($email);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de cuenta - Sofit Gym';
        $mail->Body = <<<HTML
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    
                <h1 style='color: #d11a2a; font-family: Arial, sans-serif; font-size: 32px; margin: 0; text-transform: uppercase; letter-spacing: 2px;'>
                    SOFIT GYM
                </h1>
            </div>
                <h2 style='color: #333;'>Recuperación de contraseña</h2>
                <p>Hola,</p>
                <p>Hemos recibido una solicitud para recuperar el acceso a tu cuenta en <strong>Sofit Gym</strong>.</p>
                
                <div style='background-color: #f4f4f4; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;'>
                    <p style='margin: 0; font-size: 16px;'>Tu código de verificación es:</p>
                    <h1 style='color: #e67e22; font-size: 32px; margin: 10px 0;'>$codigo</h1>
                </div>
                
                <p style='font-size: 12px; color: #777;'>Este código expirará en 15 minutos. Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
                
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p style='font-size: 12px; color: #999; text-align: center;'>Sofit Gym - Sistema de Gestión Interna</p>
            </div>
        HTML;

        try {
            return $mail->send();
        } catch (Exception $e) {
            error_log("Error SMTP: " . $mail->ErrorInfo);
            return false;
        }
    }
}
