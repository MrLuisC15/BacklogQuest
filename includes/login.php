<?php
// api/login.php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Por favor, introduce tu email y contraseña.']);
        exit;
    }

    try {
        // Consultar el usuario
        $stmt = $pdo->prepare("SELECT id, nombre, password_hash FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Verificar la contraseña
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];

            echo json_encode([
                'success' => true, 
                'message' => 'Inicio de sesión correcto.',
                'usuario' => ['id' => $usuario['id'], 'nombre' => $usuario['nombre']]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>