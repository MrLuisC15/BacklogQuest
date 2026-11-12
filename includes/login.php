<?php
// includes/login.php
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
    require_once 'conexion.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        exit;
    }

    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Por favor, introduce tu email y contraseña.']);
        exit;
    }

    // 1. Consultar el usuario asegurándonos de traer el campo 'verificado'
    $stmt = $pdo->prepare("SELECT id, nombre, password_hash, verificado FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // 2. Verificar la contraseña
    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        
        // 🔒 3. COMPROBACIÓN CLAVE: Si no está verificado (es 0 o NULL), bloqueamos el login
        if (!isset($usuario['verificado']) || $usuario['verificado'] == 0) {
            echo json_encode([
                'success' => false, 
                'message' => '⚠️ Tu cuenta aún no está verificada. Por favor, revisa tu correo electrónico para activar la cuenta antes de iniciar sesión.'
            ]);
            exit;
        }

        // 4. Si está verificado, iniciamos sesión con normalidad
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];

        // Gestionar "Recordar sesión" si viene marcado
        if (isset($_POST['recordar']) && $_POST['recordar'] === '1') {
            $duracionCookie = 30 * 24 * 60 * 60; // 30 días
            $params = session_get_cookie_params();
            setcookie(session_name(), session_id(), time() + $duracionCookie, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Inicio de sesión correcto.',
            'usuario' => ['id' => $usuario['id'], 'nombre' => $usuario['nombre']]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>