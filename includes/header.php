<?php
$nombreUsuario = $_SESSION['usuario_nombre'] ?? $_SESSION['usuario'] ?? 'Mi Cuenta';
$paginaActual = basename($_SERVER['PHP_SELF']);
?>

<div class="container my-3">
  <header class="navbar navbar-expand-lg navbar-light bg-body-tertiary rounded-4 shadow-sm border px-3 py-2">
    <div class="container-fluid">
      
      <!-- BRAND / LOGO -->
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-body text-decoration-none" href="biblioteca.php">
        <img src="assets/logo.png" alt="BacklogQuest Logo" width="58" height="58" class="rounded-circle bg-light p-1 shadow-sm object-fit-cover">
        <span>BacklogQuest</span>
      </a>

      <!-- BOTÓN HAMBURGUESA MÓVIL -->
      <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- CONTENIDO NAVEGACIÓN -->
      <div class="collapse navbar-collapse" id="navbarContent">
        
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-3">
          <li class="nav-item">
            <a class="nav-link fw-semibold <?php echo ($paginaActual === 'biblioteca.php') ? 'active text-body fw-bold' : 'text-secondary'; ?>" <?php echo ($paginaActual === 'biblioteca.php') ? 'aria-current="page"' : ''; ?> href="biblioteca.php">
              Mi biblioteca
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold <?php echo ($paginaActual === 'estadisticas.php') ? 'active text-body fw-bold' : 'text-secondary'; ?>" <?php echo ($paginaActual === 'estadisticas.php') ? 'aria-current="page"' : ''; ?> href="estadisticas.php">
              Estadísticas
            </a>
          </li>

          <?php if ($paginaActual === 'biblioteca.php'): ?>
          <li class="nav-item mt-2 mt-lg-0">
            <button type="button" class="btn btn-primary rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAnadirJuego">
              + Añadir juego
            </button>
          </li>
          <?php endif; ?>
        </ul>

        <!-- BOTÓN MODO OSCURO + DROPDOWN USUARIO -->
        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
          
          <!-- Botón de Conmutación de Tema -->
          <button type="button" class="btn btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" id="btnToggleTheme" style="width: 38px; height: 38px;" title="Cambiar tema">
            <span id="themeIcon">🌙</span>
          </button>

          <!-- Dropdown Usuario -->
          <div class="dropdown">
            <button class="btn btn-outline-secondary rounded-pill dropdown-toggle fw-semibold text-body" type="button" id="dropdownMiCuenta" data-bs-toggle="dropdown" aria-expanded="false">
              👤 <?php echo htmlspecialchars($nombreUsuario); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 mt-2" aria-labelledby="dropdownMiCuenta">
              <li>
                <button class="dropdown-item text-danger fw-semibold" type="button" id="btnCerrarSesion">
                  🚪 Cerrar sesión
                </button>
              </li>
            </ul>
          </div>

        </div>

      </div>
      
    </div>
  </header>
</div>