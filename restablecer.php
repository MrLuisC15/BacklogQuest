<?php
session_start();
require_once 'includes/conexion.php';

$token = $_GET['token'] ?? '';
$error = '';
$exito = false;
$usuarioValido = false;

if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE token_recuperacion = ? AND token_expiracion > NOW()");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $usuarioValido = true;
        $usuario_id = $usuario['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevaPassword = $_POST['password'] ?? '';

            if (strlen($nuevaPassword) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
            } else {
                $passwordHash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
                
                // Actualizar contraseña y limpiar el token de recuperación
                $update = $pdo->prepare("UPDATE usuarios SET password_hash = ?, token_recuperacion = NULL, token_expiracion = NULL WHERE id = ?");
                $update->execute([$passwordHash, $usuario_id]);

                $exito = true;
            }
        }
    } else {
        $error = 'El enlace de recuperación es inválido o ha expirado.';
    }
} else {
    $error = 'No se ha proporcionado ningún token de recuperación.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer Contraseña | BacklogQuest</title>
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
    <h2 class="h5 fw-bold text-body mb-3">Nueva Contraseña</h2>

    <?php if ($exito): ?>
        <div class="alert alert-success rounded-3 small" role="alert">
            ¡Contraseña actualizada con éxito! Ya puedes iniciar sesión con tu nueva clave.
        </div>
        <a href="index.php" class="btn btn-primary rounded-pill mt-3 w-100 fw-semibold">Ir a Iniciar Sesión</a>
    <?php elseif ($usuarioValido): ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-3 small" role="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="text-start mt-3">
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold text-body">Introduce tu nueva contraseña</label>
                <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-semibold mt-2">Guardar nueva contraseña</button>
        </form>
    <?php else: ?>
        <div class="alert alert-danger rounded-3 small" role="alert">
            <?php echo $error; ?>
        </div>
        <a href="index.php" class="btn btn-outline-secondary rounded-pill mt-3 w-100 fw-semibold">Volver al inicio</a>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>