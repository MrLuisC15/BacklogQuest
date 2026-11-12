<?php
// includes/procesar-recuperacion.php
session_start();
require_once 'conexion.php';
require_once 'enviar-correo.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Por favor, introduce un correo electrónico válido.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Por seguridad, aunque el correo no exista, respondemos como si se hubiera enviado
        // para evitar que descubran qué emails están registrados en la plataforma.
        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            // Expira en 1 hora
            $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $update = $pdo->prepare("UPDATE usuarios SET token_recuperacion = ?, token_expiracion = ? WHERE id = ?");
            $update->execute([$token, $expiracion, $usuario['id']]);

            enviarCorreoRecuperacion($email, $usuario['nombre'], $token);
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Si el correo está registrado, recibirás las instrucciones para restablecer tu contraseña.'
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor. Inténtalo más tarde.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>