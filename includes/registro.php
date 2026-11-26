<?php
// includes/registro.php
session_start();
require_once 'conexion.php';
require_once 'enviar-correo.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$nombre || !$email || strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Por favor, rellena todos los campos correctamente (contraseña mín. 8 caracteres).']);
        exit;
    }

    try {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado.']);
            exit;
        }

        // Encriptar contraseña y generar token único
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $token = bin2hex(random_bytes(32));

        // Insertar usuario con verificado = 0 y guardar el token
        $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash, verificado, token_verificacion) VALUES (?, ?, ?, 0, ?)");
        $stmtInsert->execute([$nombre, $email, $passwordHash, $token]);

        // Enviar correo de verificación
        $correoEnviado = enviarCorreoVerificacion($email, $nombre, $token);

        if ($correoEnviado) {
            echo json_encode([
                'success' => true, 
                'message' => '¡Registro exitoso! Hemos enviado un enlace de confirmación a tu correo electrónico. Por favor, revísalo para activar tu cuenta.'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Usuario registrado, pero hubo un error al enviar el correo de verificación.'
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>