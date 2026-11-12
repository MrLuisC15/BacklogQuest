<?php
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoVerificacion($destinatario, $nombreUsuario, $token) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP (Ejemplo usando Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tucorreo@gmail.com'; // <--- Correo de Gmail
        $mail->Password   = ''; // <--- Contraseña de aplicación de Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remitente y Destinatario
        $mail->setFrom('tucorreo@gmail.com', 'BacklogQuest');
        $mail->addAddress($destinatario, $nombreUsuario);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Confirma tu cuenta en BacklogQuest';
        
        // Enlace de activación (cambia tu-dominio.infinityfreeapp.com por tu dominio real)
        $enlace = "https://tudominio.infinityfree.me/verificar.php?token=" . $token;

        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2 style='color: #245a92;'>¡Bienvenido a BacklogQuest, {$nombreUsuario}!</h2>
                <p>Gracias por registrarte. Para empezar a gestionar tu biblioteca de videojuegos, solo falta un último paso.</p>
                <p>Haz clic en el siguiente botón para activar tu cuenta:</p>
                <p style='margin: 30px 0;'>
                    <a href='{$enlace}' style='background-color: #245a92; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Activar mi cuenta</a>
                </p>
                <p><small>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</small></p>
                <p><small>{$enlace}</small></p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='color: #777; font-size: 12px;'>Si no solicitaste esta cuenta, puedes ignorar este mensaje.</p>
            </div>
        ";
        
        $mail->AltBody = "Hola {$nombreUsuario}, haz clic en el siguiente enlace para activar tu cuenta en BacklogQuest: " . $enlace;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function enviarCorreoRecuperacion($destinatario, $nombreUsuario, $token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tucorreo@gmail.com'; // <--- Correo de Gmail
        $mail->Password   = ''; // <--- Contraseña de aplicación de Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('tucorreo@gmail.com', 'BacklogQuest');
        $mail->addAddress($destinatario, $nombreUsuario);

        $mail->isHTML(true);
        $mail->Subject = 'Recupera tu contraseña en BacklogQuest';
        
        // Enlace hacia la página de restablecimiento
        $enlace = "https://tudominio.infinityfree.me/restablecer.php?token=" . $token;

        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2 style='color: #245a92;'>Recuperación de Contraseña</h2>
                <p>Hola, <strong>{$nombreUsuario}</strong>.</p>
                <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en BacklogQuest.</p>
                <p>Haz clic en el siguiente botón para crear una nueva contraseña (el enlace caduca en 1 hora):</p>
                <p style='margin: 30px 0;'>
                    <a href='{$enlace}' style='background-color: #245a92; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Restablecer contraseña</a>
                </p>
                <p><small>Si no solicitaste este cambio, puedes ignorar este mensaje de forma segura.</small></p>
            </div>
        ";
        
        $mail->AltBody = "Hola {$nombreUsuario}, entra al siguiente enlace para restablecer tu contraseña en BacklogQuest: " . $enlace;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}