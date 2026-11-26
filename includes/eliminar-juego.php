<?php
// includes/eliminar-juego.php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

try {
    require_once 'conexion.php';

    $usuario_id = $_SESSION['usuario_id'];
    $biblioteca_id = $_POST['biblioteca_id'] ?? null;

    if (!$biblioteca_id) {
        echo json_encode(['success' => false, 'message' => 'ID de biblioteca no válido.']);
        exit;
    }

    // Verificar por seguridad que el registro pertenece al usuario actual
    $stmtCheck = $pdo->prepare("SELECT id FROM biblioteca_usuario WHERE id = ? AND usuario_id = ?");
    $stmtCheck->execute([$biblioteca_id, $usuario_id]);
    
    if (!$stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para eliminar este registro.']);
        exit;
    }

    // Borrar únicamente de la tabla biblioteca_usuario del usuario actual
    $stmtDelete = $pdo->prepare("DELETE FROM biblioteca_usuario WHERE id = ? AND usuario_id = ?");
    $stmtDelete->execute([$biblioteca_id, $usuario_id]);

    echo json_encode([
        'success' => true,
        'message' => '¡Juego eliminado de tu biblioteca correctamente!'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>