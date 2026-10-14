<?php
// api/registro.php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$nombre || !$email || strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Por favor, rellena todos los campos correctamente (contraseña mín. 8 caracteres).']);
        exit;
    }

    try {
        // Verificar si el email ya existe en la tabla usuarios
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado.']);
            exit;
        }

        // Encriptar la contraseña
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insertar usuario
        $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)");
        $stmtInsert->execute([$nombre, $email, $passwordHash]);

        // Autenticar automáticamente al registrarse
        $usuarioId = $pdo->lastInsertId();
        $_SESSION['usuario_id'] = $usuarioId;
        $_SESSION['usuario_nombre'] = $nombre;

        echo json_encode(['success' => true, 'message' => 'Registro completado con éxito.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>