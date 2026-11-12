<?php
// includes/anadir-juego.php
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
    $rawg_id = $_POST['rawg_id'] ?? null;
    $titulo = trim($_POST['titulo'] ?? '');
    $imagen_url = trim($_POST['imagen_url'] ?? '');
    $plataforma_nombre = trim($_POST['plataforma'] ?? '');
    $estado = trim($_POST['estado'] ?? 'Pendiente');
    $horas = floatval($_POST['horas'] ?? 0);
    $notas_checkpoint = trim($_POST['notas_checkpoint'] ?? '');
    $duracion_estimada = intval($_POST['duracion_estimada_horas'] ?? 0); // <-- Capturamos la duración de RAWG

    if (!$rawg_id || empty($titulo) || empty($plataforma_nombre)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios del juego.']);
        exit;
    }

    // 1. Verificar o insertar el juego en la tabla 'juegos' (incluyendo la duración estimada)
    $stmtCheckGame = $pdo->prepare("SELECT id FROM juegos WHERE rawg_id = ?");
    $stmtCheckGame->execute([$rawg_id]);
    $juego = $stmtCheckGame->fetch();

    if ($juego) {
        $juego_id = $juego['id'];
        
        // Opcional: Actualizar la duración si venía a 0 y ahora sí la tenemos
        $pdo->prepare("UPDATE juegos SET duracion_estimada_horas = ? WHERE id = ? AND (duracion_estimada_horas = 0 OR duracion_estimada_horas IS NULL)")
            ->execute([$duracion_estimada, $juego_id]);
    } else {
        $stmtInsertGame = $pdo->prepare("INSERT INTO juegos (rawg_id, titulo, imagen_url, duracion_estimada_horas) VALUES (?, ?, ?, ?)");
        $stmtInsertGame->execute([$rawg_id, $titulo, $imagen_url, $duracion_estimada]);
        $juego_id = $pdo->lastInsertId();
    }

    // 2. Verificar o insertar la plataforma en la tabla 'plataformas'
    $stmtCheckPlat = $pdo->prepare("SELECT id FROM plataformas WHERE nombre = ?");
    $stmtCheckPlat->execute([$plataforma_nombre]);
    $plataforma = $stmtCheckPlat->fetch();

    if ($plataforma) {
        $plataforma_id = $plataforma['id'];
    } else {
        $stmtInsertPlat = $pdo->prepare("INSERT INTO plataformas (nombre) VALUES (?)");
        $stmtInsertPlat->execute([$plataforma_nombre]);
        $plataforma_id = $pdo->lastInsertId();
    }

    // 3. Comprobar si el usuario ya tiene este juego en su biblioteca
    $stmtCheckLib = $pdo->prepare("SELECT id FROM biblioteca_usuario WHERE usuario_id = ? AND juego_id = ? AND plataforma_id = ?");
    $stmtCheckLib->execute([$usuario_id, $juego_id, $plataforma_id]);

    if ($stmtCheckLib->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya tienes este juego añadido en esta plataforma.']);
        exit;
    }

    // 4. Insertar en la biblioteca del usuario
    $stmtInsertLib = $pdo->prepare("INSERT INTO biblioteca_usuario (usuario_id, juego_id, plataforma_id, estado, horas_jugadas, notas_checkpoint) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtInsertLib->execute([$usuario_id, $juego_id, $plataforma_id, $estado, $horas, $notas_checkpoint]);

    echo json_encode([
        'success' => true,
        'message' => '¡Juego añadido correctamente a tu biblioteca!'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>