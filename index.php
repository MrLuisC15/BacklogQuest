<?php
session_start();
// Si el usuario ya ha iniciado sesión, lo mandamos directo a su biblioteca
if (isset($_SESSION['usuario_id'])) {
    header('Location: biblioteca.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido | BacklogQuest</title>
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
<body class="d-flex flex-column min-vh-100 bg-body position-relative">

    <!-- 🌙 BOTÓN FLOTANTE MODO OSCURO (Esquina superior derecha) -->
    <div class="position-absolute top-0 end-0 p-3 z-3">
        <button type="button" class="btn btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" id="btnToggleTheme" style="width: 42px; height: 42px;" title="Cambiar tema">
            <span id="themeIcon">🌙</span>
        </button>
    </div>

    <!-- Contenedor principal centrado vertical y horizontalmente -->
    <main class="flex-grow-1 container d-flex justify-content-center align-items-center py-5">
        
        <!-- Tarjeta única con relieve adaptada al modo oscuro -->
        <div class="card p-4 p-md-5 rounded-4 shadow border border-secondary-subtle bg-body-tertiary w-100" style="max-width: 480px;">
            
            <!-- LOGO Y TÍTULO -->
            <div class="d-flex flex-column align-items-center text-center mb-4">
                <img src="assets/logo.png" alt="BacklogQuest Logo" width="160" height="160" class="mb-3 rounded-circle shadow-sm p-2 bg-body-secondary object-fit-cover">
                <h1 class="h3 fw-bold text-body mb-1">BacklogQuest</h1>
                <p class="text-muted small mb-0">Gestiona tu colección y playtime con precisión.</p>
                <div class="d-none d-lg-block text-muted small mt-2">
                    <p class="mb-1">✨ Búsqueda integrada con RAWG.io</p>
                    <p class="mb-1">📊 Estadísticas detalladas de tu biblioteca</p>
                    <p class="mb-0">🔒 Tu progreso siempre seguro y sincronizado</p>
                </div>
            </div>

            <!-- Pestañas (Login / Registro) -->
            <ul class="nav nav-pills nav-fill mb-4 bg-body-secondary p-1 rounded-pill" id="authTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-semibold py-2" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab">Iniciar sesión</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-semibold py-2" id="register-tab" data-bs-toggle="tab" data-bs-target="#registro-tab-pane" type="button" role="tab">Registrarse</button>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content w-100" id="authTabsContent">
                
                <!-- Pestaña Iniciar Sesión -->
                <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel">
                    <form id="formLogin" class="w-100">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label fw-semibold text-body">Correo electrónico</label>
                            <input type="email" class="form-control rounded-3" id="loginEmail" placeholder="tu@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label fw-semibold text-body">Contraseña</label>
                            <input type="password" class="form-control rounded-3" id="loginPassword" placeholder="••••••••" required>
                        </div>
                        <div class="text-center mb-3">
                            <a href="#" class="small text-decoration-none text-muted" data-bs-toggle="modal" data-bs-target="#modalRecuperar">¿Has olvidado tu contraseña?</a>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="recordarSesion">
                            <label class="form-check-label small text-muted" for="recordarSesion">Recordar mi sesión</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">Entrar</button>
                    </form>
                </div>

                <!-- Pestaña Registrarse -->
                <div class="tab-pane fade" id="registro-tab-pane" role="tabpanel">
                    <form id="formRegistro" class="w-100">
                        <div class="mb-3">
                            <label for="registroNombre" class="form-label fw-semibold text-body">Nombre de usuario</label>
                            <input type="text" class="form-control rounded-3" id="registroNombre" placeholder="Ej. GamerPro99" required>
                        </div>
                        <div class="mb-3">
                            <label for="registroEmail" class="form-label fw-semibold text-body">Correo electrónico</label>
                            <input type="email" class="form-control rounded-3" id="registroEmail" placeholder="tu@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="registroPassword" class="form-label fw-semibold text-body">Contraseña</label>
                            <input type="password" class="form-control rounded-3" id="registroPassword" placeholder="Mínimo 8 caracteres" required minlength="8">
                        </div>
                        <div class="mb-3">
                            <label for="registroConfirmPassword" class="form-label fw-semibold text-body">Confirmar contraseña</label>
                            <input type="password" class="form-control rounded-3" id="registroConfirmPassword" placeholder="Repite tu contraseña" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">Crear cuenta</button>
                    </form>
                </div>

            </div>

        </div>
    </main>

    <!-- Footer -->

    <?php include 'includes/footer.php'; ?>
    <!--
    <footer class="py-3 text-center text-muted small bg-body-tertiary border-top border-secondary-subtle mt-auto">
        &copy; <?php echo date("Y"); ?> BacklogQuest. Todos los derechos reservados.
    </footer>
    -->

    <!-- Modal Recuperar Contraseña -->
    <div class="modal fade" id="modalRecuperar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border border-secondary-subtle rounded-4 shadow bg-body-tertiary">
                <div class="modal-header border-bottom border-secondary-subtle">
                    <h5 class="modal-title fw-bold text-body">Recuperar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formRecuperar">
                    <div class="modal-body py-4">
                    <p class="text-muted small mb-3">Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
                    <div class="mb-3">
                        <label for="emailRecuperar" class="form-label fw-semibold text-body">Correo electrónico</label>
                        <input type="email" class="form-control rounded-3" id="emailRecuperar" placeholder="tu@email.com" required>
                    </div>
                    </div>
                    <div class="modal-footer border-top border-secondary-subtle">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Enviar instrucciones</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/main.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>