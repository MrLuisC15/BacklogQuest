<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $biblioteca_id = $_POST['biblioteca_id'] ?? null;
    $nuevo_estado = $_POST['nuevo_estado'] ?? null;

    $estadosValidos = ['Pendiente', 'Jugando', 'Pausado', 'Completado', 'Abandonado'];

    if (!$biblioteca_id || !in_array($nuevo_estado, $estadosValidos)) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
        exit;
    }

    try {
        require_once 'conexion.php';

        // Verificar que el juego pertenece al usuario antes de actualizar
        $stmt = $pdo->prepare("UPDATE biblioteca_usuario SET estado = ? WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$nuevo_estado, $biblioteca_id, $usuario_id]);

        echo json_encode(['success' => true, 'message' => "Estado actualizado a: $nuevo_estado"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}