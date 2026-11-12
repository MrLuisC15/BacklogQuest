<?php
// includes/actualizar-juego.php
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
    $estado = trim($_POST['estado'] ?? 'Pendiente');
    $horas = floatval($_POST['horas'] ?? 0);
    $notas_checkpoint = trim($_POST['notas_checkpoint'] ?? ''); // Recogemos el texto del textarea

    if (!$biblioteca_id) {
        echo json_encode(['success' => false, 'message' => 'ID de biblioteca no válido.']);
        exit;
    }

    // Verificar que el registro pertenece al usuario actual por seguridad
    $stmtCheck = $pdo->prepare("SELECT id FROM biblioteca_usuario WHERE id = ? AND usuario_id = ?");
    $stmtCheck->execute([$biblioteca_id, $usuario_id]);
    
    if (!$stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar este registro.']);
        exit;
    }

    // Actualizar los datos en la base de datos
    $stmtUpdate = $pdo->prepare("UPDATE biblioteca_usuario SET estado = ?, horas_jugadas = ?, notas_checkpoint = ? WHERE id = ?");
    $stmtUpdate->execute([$estado, $horas, $notas_checkpoint, $biblioteca_id]);

    echo json_encode([
        'success' => true,
        'message' => '¡Progreso actualizado correctamente!'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>