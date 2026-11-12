<?php
session_start();
require_once 'includes/conexion.php';

$token = $_GET['token'] ?? '';
$mensajeAlerta = "";
$tipoAlerta = "danger";

if (!empty($token)) {
    try {
        // Buscar si existe un usuario con ese token y que aún no esté verificado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE token_verificacion = ? AND verificado = 0");
        $stmt->execute([$token]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Actualizar el estado a verificado y vaciar el token de verificación
            $update = $pdo->prepare("UPDATE usuarios SET verificado = 1, token_verificacion = NULL WHERE id = ?");
            $update->execute([$usuario['id']]);

            $mensajeAlerta = "¡Cuenta verificada con éxito! Ya puedes iniciar sesión en tu biblioteca.";
            $tipoAlerta = "success";
        } else {
            $mensajeAlerta = "El enlace de verificación es inválido o la cuenta ya ha sido activada previamente.";
        }
    } catch (Exception $e) {
        $mensajeAlerta = "Ocurrió un error en el servidor al intentar verificar la cuenta.";
    }
} else {
    $mensajeAlerta = "Token de verificación no proporcionado.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificación de Cuenta | BacklogQuest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/layout.css">
    <!-- Favicons y Webmanifest (Organizados en assets/favicon/) -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/site.webmanifest">
    <link rel="shortcut icon" href="assets/favicon/favicon.ico">
    <script>
      // Script crítico para evitar el destello blanco en modo oscuro
      (function() {
        const savedTheme = localStorage.getItem('theme');
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme ? savedTheme : (systemDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', theme);
      })();
    </script>
</head>
<body class="d-flex flex-column min-vh-100 bg-body justify-content-center align-items-center">

  <div class="card shadow-sm border border-secondary-subtle rounded-4 p-4 text-center bg-body-tertiary" style="max-width: 450px; width: 100%;">
    <div class="h1 mb-3"><?php echo ($tipoAlerta === 'success') ? '✅' : '❌'; ?></div>
    <h2 class="h5 fw-bold text-body mb-3">Verificación de Cuenta</h2>
    
    <div class="alert alert-<?php echo $tipoAlerta; ?> rounded-3 small" role="alert">
      <?php echo $mensajeAlerta; ?>
    </div>

    <a href="index.php" class="btn btn-primary rounded-pill mt-3 w-100 fw-semibold">Ir a Iniciar Sesión</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>