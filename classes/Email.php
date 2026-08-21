<?php

declare(strict_types=1);

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    public string $email;
    public string $nombre;
    public string $apellido;
    public string $token;

    public function __construct(string $email, string $nombre, string $apellido, string $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->token = $token;
    }

    /**
     * Configuración base de PHPMailer
     * @return PHPMailer
     */
    private function configurarMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP usando variables de entorno (.env)
            $mail->isSMTP();
            $mail->Host       = $_ENV['EMAIL_HOST']; 
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['EMAIL_USER']; 
            $mail->Password   = $_ENV['EMAIL_PASS']; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = $_ENV['EMAIL_PORT']; 

            // Remitente leyendo del .env
            $mail->setFrom($_ENV['EMAIL_FROM'], $_ENV['EMAIL_NAME']);
            
            // Destinatario
            $mail->addAddress($this->email, $this->nombre . ' ' . $this->apellido);

            // Configurar el contenido como HTML
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            return $mail;

        } catch (Exception $e) {
            // Aquí podrías registrar el error en un log
            return $mail;
        }
    }

    /**
     * Envía el email para confirmar la cuenta nueva
     */
    public function enviarConfirmacion(): void
    {
        $mail = $this->configurarMailer();

        $mail->Subject = 'Confirma tu Cuenta';

        // Diseñar el cuerpo del correo
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . " " . $this->apellido . "</strong></p>";
        $contenido .= "<p>Has creado tu cuenta en nuestra Tienda Virtual, solo debes confirmarla presionando el siguiente enlace:</p>";
        
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar el mensaje.</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        // Enviar el correo
        $mail->send();
    }

    /**
     * Envía el email para reestablecer la contraseña
     */
    public function enviarInstrucciones(): void
    {
        $mail = $this->configurarMailer();

        $mail->Subject = 'Reestablece tu Password';

        // Diseñar el cuerpo del correo
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . " " . $this->apellido . "</strong></p>";
        $contenido .= "<p>Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/reestablecer?token=" . $this->token . "'>Reestablecer Password</a></p>";
        $contenido .= "<p>Si tú no solicitaste este cambio, puedes ignorar el mensaje.</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        // Enviar el correo
        $mail->send();
    }
}