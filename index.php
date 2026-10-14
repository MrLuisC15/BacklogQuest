<<?php
session_start();
// Comprobamos si el usuario ha iniciado sesión
$usuarioAutenticado = isset($_SESSION['usuario_id']);
$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Mi cuenta';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mi Biblioteca | BacklogQuest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/layout.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

  <!-- Enlace de accesibilidad -->
  <a class="visually-hidden-focusable btn btn-primary position-absolute top-0 start-0 m-2 z-3" href="#contenido">Saltar al contenido</a>

  <!-- Header Dinámico -->
  <div class="container my-3">
    <header class="navbar navbar-expand-lg navbar-light bg-white rounded-4 shadow-sm border px-3 py-2">
      <div class="container-fluid">
        <!-- Marca / Logo -->
        <a class="navbar-brand fw-bold text-primary" href="index.php">🎮 BacklogQuest</a>

        <!-- Botón hamburguesa -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Enlaces -->
        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active fw-semibold" aria-current="page" href="index.php">Mi biblioteca</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="estadisticas.php">Estadísticas</a>
            </li>

            <!-- Botón "+ Añadir juego" solo visible si hay sesión iniciada -->
            <?php if ($usuarioAutenticado): ?>
            <li class="nav-item">
              <button type="button" class="btn btn-primary rounded-pill px-3 ms-lg-2" data-bs-toggle="modal" data-bs-target="#modalAnadirJuego">
                + Añadir juego
              </button>
            </li>
            <?php endif; ?>
          </ul>

          <!-- Bloque derecho: Depende del estado de la sesión -->
          <div>
            <?php if ($usuarioAutenticado): ?>
              <!-- Si SÍ está logueado: Muestra el desplegable de cuenta -->
              <div class="dropdown">
                <button class="btn btn-outline-primary rounded-pill dropdown-toggle" type="button" id="dropdownMiCuenta" data-bs-toggle="dropdown" aria-expanded="false">
                  👤 <?php echo htmlspecialchars($nombreUsuario); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3" aria-labelledby="dropdownMiCuenta">
                  <li><a class="dropdown-item text-danger" href="#" id="btnCerrarSesion">Cerrar sesión</a></li>
                </ul>
              </div>
            <?php else: ?>
              <!-- Si NO está logueado: Muestra los botones de Acceso y Registro -->
              <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalLogin">
                  Iniciar sesión
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalRegistro">
                  Registrarse
                </button>
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </header>
  </div>

  <main id="contenido" class="container mb-5">
    <!-- Encabezado con contador -->
    <div class="d-flex align-items-baseline mb-3">
      <h1 class="h2 me-2 mb-0">Mi Backlog</h1>
      <span class="text-muted fs-5">(6 juegos)</span>
    </div>

    <!-- Botones de filtro por estado -->
    <div class="d-flex flex-wrap gap-2 mb-4" role="group" aria-label="Filtro de juegos por estado">
      <button type="button" class="btn btn-primary active">Todos</button>
      <button type="button" class="btn btn-outline-secondary">Jugando</button>
      <button type="button" class="btn btn-outline-secondary">Pendientes</button>
      <button type="button" class="btn btn-outline-secondary">Pausados</button>
      <button type="button" class="btn btn-outline-secondary">Completados</button>
    </div>

    <!-- Controles de Filtro por Plataforma y Buscador -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-4">
        <label for="selectPlataforma" class="form-label fw-bold">Plataforma</label>
        <select id="selectPlataforma" class="form-select">
          <option selected>Todas las plataformas</option>
          <option value="PC">PC</option>
          <option value="PS5">PS5</option>
          <option value="XBOX">Xbox</option>
          <option value="Switch">Switch</option>
        </select>
      </div>
      <div class="col-12 col-md-8">
        <label for="inputBuscar" class="form-label fw-bold">Buscar en mi biblioteca</label>
        <input id="inputBuscar" type="search" class="form-control" placeholder="Nombre del juego…">
      </div>
    </div>
    
    <!-- Rejilla de tarjetas de juegos -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      
      <!-- Elden Ring -->
      <div class="col">
        <article class="card h-100 shadow-sm">
          <img src="assets/portada-placeholder.svg" class="card-img-top" alt="Portada de Elden Ring" width="640" height="360">
          <div class="card-body d-flex flex-column">
            <h2 class="card-title h5">Elden Ring</h2>
            <p class="card-text text-muted mb-2">
              <span class="badge bg-secondary">PC</span>
              <span class="badge bg-primary ms-1">Jugando</span>
            </p>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold">Progreso:</small>
                <small class="fw-bold text-primary">45%</small>
              </div>
              <div class="progress" role="progressbar" aria-label="Progreso de Elden Ring" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 45%"></div>
              </div>
            </div>
            <p class="card-text small mb-3"><strong>Última nota:</strong> En la puerta del jefe de fuego.</p>
            <a href="partida-elden.html" class="btn btn-outline-primary mt-auto">Actualizar partida</a>
          </div>
        </article>
      </div>

      <!-- Zelda: Tears of the Kingdom -->
      <div class="col">
        <article class="card h-100 shadow-sm">
          <img src="assets/portada-placeholder.svg" class="card-img-top" alt="Portada de Zelda: Tears of the Kingdom" width="640" height="360">
          <div class="card-body d-flex flex-column">
            <h2 class="card-title h5">Zelda: Tears of the Kingdom</h2>
            <p class="card-text text-muted mb-2">
              <span class="badge bg-secondary">Switch</span>
              <span class="badge bg-primary ms-1">Jugando</span>
            </p>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold">Progreso:</small>
                <small class="fw-bold text-primary">32%</small>
              </div>
              <div class="progress" role="progressbar" aria-label="Progreso de Zelda: Tears of the Kingdom" aria-valuenow="32" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 32%"></div>
              </div>
            </div>
            <p class="card-text small mb-3"><strong>Última nota:</strong> Explorando la región de Akkala. Buscar el siguiente santuario.</p>
            <a href="partida-zelda.html" class="btn btn-outline-primary mt-auto">Actualizar partida</a>
          </div>
        </article>
      </div>

      <!-- Hollow Knight -->
      <div class="col">
        <article class="card h-100 shadow-sm">
          <img src="assets/portada-placeholder.svg" class="card-img-top" alt="Portada de Hollow Knight" width="640" height="360">
          <div class="card-body d-flex flex-column">
            <h2 class="card-title h5">Hollow Knight</h2>
            <p class="card-text text-muted mb-2">
              <span class="badge bg-secondary">PC</span>
              <span class="badge bg-warning text-dark ms-1">Pausado</span>
            </p>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold">Progreso:</small>
                <small class="fw-bold text-warning">30%</small>
              </div>
              <div class="progress" role="progressbar" aria-label="Progreso de Hollow Knight" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-warning" style="width: 30%"></div>
              </div>
            </div>
            <p class="card-text small mb-3"><strong>Última nota:</strong> Explorando Cruces Olvidados. Volver a la tienda.</p>
            <a href="partida-hollow-knight.html" class="btn btn-outline-primary mt-auto">Actualizar partida</a>
          </div>
        </article>
      </div>

      <!-- Hades -->
      <div class="col">
        <article class="card h-100 shadow-sm">
          <img src="assets/portada-placeholder.svg" class="card-img-top" alt="Portada de Hades" width="640" height="360">
          <div class="card-body d-flex flex-column">
            <h2 class="card-title h5">Hades</h2>
            <p class="card-text text-muted mb-2">
              <span class="badge bg-secondary">Switch</span>
              <span class="badge bg-success ms-1">Completado</span>
            </p>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold">Progreso:</small>
                <small class="fw-bold text-success">100%</small>
              </div>
              <div class="progress" role="progressbar" aria-label="Progreso de Hades" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-success" style="width: 100%"></div>
              </div>
            </div>
            <p class="card-text small mb-3"><strong>Última nota:</strong> Objetivo de la partida completado.</p>
            <a href="partida-hades.html" class="btn btn-outline-primary mt-auto">Actualizar partida</a>
          </div>
        </article>
      </div>

      <!-- God of War Ragnarök -->
      <div class="col">
        <article class="card h-100 shadow-sm">
          <img src="assets/portada-placeholder.svg" class="card-img-top" alt="Portada de God of War Ragnarök" width="640" height="360">
          <div class="card-body d-flex flex-column">
            <h2 class="card-title h5">God of War Ragnarök</h2>
            <p class="card-text text-muted mb-2">
              <span class="badge bg-secondary">PS5</span>
              <span class="badge bg-dark ms-1">Pendiente</span>
            </p>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold">Progreso:</small>
                <small class="fw-bold text-muted">0%</small>
              </div>
              <div class="progress" role="progressbar" aria-label="Progreso de God of War Ragnarök" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 0%"></div>
              </div>
            </div>
            <p class="card-text small mb-3"><strong>Última nota:</strong> Todavía no hay notas de partida.</p>
            <a href="partida-god-of-war.html" class="btn btn-outline-primary mt-auto">Actualizar partida</a>
          </div>
        </article>
      </div>

      <!-- Celeste -->
      <div class="col">
        <article class="card h-100 shadow-sm">
          <img src="assets/portada-placeholder.svg" class="card-img-top" alt="Portada de Celeste" width="640" height="360">
          <div class="card-body d-flex flex-column">
            <h2 class="card-title h5">Celeste</h2>
            <p class="card-text text-muted mb-2">
              <span class="badge bg-secondary">PC</span>
              <span class="badge bg-dark ms-1">Pendiente</span>
            </p>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-bold">Progreso:</small>
                <small class="fw-bold text-muted">0%</small>
              </div>
              <div class="progress" role="progressbar" aria-label="Progreso de Celeste" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 0%"></div>
              </div>
            </div>
            <p class="card-text small mb-3"><strong>Última nota:</strong> Todavía no hay notas de partida.</p>
            <a href="partida-celeste.html" class="btn btn-outline-primary mt-auto">Actualizar partida</a>
          </div>
        </article>
      </div>

    </div>
  </main>

  <!-- Modal Añadir Juego con Búsqueda RAWG -->
  <div class="modal fade" id="modalAnadirJuego" tabindex="-1" aria-labelledby="modalAnadirJuegoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 shadow">
        
        <!-- Cabecera del Modal -->
        <div class="modal-header border-bottom-0 pb-0">
          <h2 class="modal-title h5 fw-bold" id="modalAnadirJuegoLabel">🎮 Añadir nuevo juego</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Cuerpo del Modal -->
        <div class="modal-body py-3">
          
          <!-- 1. BUSCADOR RAWG.IO -->
          <div class="mb-3">
            <label for="inputBuscarRAWG" class="form-label fw-semibold">Buscar juego en RAWG.io</label>
            <div class="input-group">
              <input type="text" class="form-control" id="inputBuscarRAWG" placeholder="Escribe el nombre del juego (ej. Elden Ring, Zelda...)" onkeydown="if(event.key === 'Enter') { event.preventDefault(); document.getElementById('btnBuscarRAWG').click(); }">
              <button class="btn btn-outline-primary fw-medium" type="button" id="btnBuscarRAWG">
                🔍 Buscar
              </button>
            </div>
          </div>

          <!-- LISTA DE RESULTADOS (Se rellenará dinámicamente con JS) -->
          <div id="resultadosRAWG" class="list-group mb-3 d-none" style="max-height: 220px; overflow-y: auto;">
          </div>

          <!-- 2. RECUADRO DE VISTA PREVIA (Muestra el juego e imagen seleccionada) -->
          <div id="previewJuegoSeleccionado" class="card bg-light border rounded-3 mb-3 p-3">
            <div class="d-flex align-items-center gap-3">
              <img id="imagenJuegoSeleccionado" src="assets/portada-placeholder.svg" alt="Vista previa de portada" class="rounded shadow-sm object-fit-cover" width="110" height="65">
              <div class="flex-grow-1">
                <span class="badge bg-success mb-1" id="badgeSeleccionado">Juego seleccionado</span>
                <h5 id="tituloJuegoSeleccionado" class="h6 fw-bold mb-0">Ningún juego seleccionado</h5>
                <small id="infoJuegoSeleccionado" class="text-muted">Busca un juego arriba para vincular la portada e información de RAWG.</small>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill d-none" id="btnCambiarJuego">
                Cambiar
              </button>
            </div>
          </div>

          <!-- 3. FORMULARIO CON DETALLES PERSONALIZADOS -->
          <form id="formAnadirJuego">
            <!-- Inputs ocultos para enviar la URL de la imagen y el ID de RAWG a la base de datos o script -->
            <input type="hidden" id="rawgGameId" name="rawgGameId">
            <input type="hidden" id="rawgImageUrl" name="rawgImageUrl">
            <input type="hidden" id="tituloJuegoOculto" name="tituloJuegoOculto">

            <div class="row g-2 mb-3">
              <div class="col-6">
                <label for="plataformaJuego" class="form-label fw-semibold">Plataforma</label>
                <select class="form-select" id="plataformaJuego" required disabled>
                  <option value="" selected disabled>Primero busca y selecciona un juego...</option>
                </select>
              </div>
              <div class="col-6">
                <label for="estadoJuego" class="form-label fw-semibold">Estado inicial</label>
                <select class="form-select" id="estadoJuego" required>
                  <option value="Pendiente" selected>Pendiente</option>
                  <option value="Jugando">Jugando</option>
                  <option value="Pausado">Pausado</option>
                  <option value="Completado">Completado</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="progresoJuego" class="form-label fw-semibold">Progreso inicial (%)</label>
              <input type="number" class="form-control" id="progresoJuego" min="0" max="100" value="0">
            </div>

            <div class="mb-2">
              <label for="notaJuego" class="form-label fw-semibold">Primera nota o comentario</label>
              <textarea class="form-control" id="notaJuego" rows="2" placeholder="Ej. Comprado en rebajas de Steam..."></textarea>
            </div>
          </form>

        </div>

        <!-- Pie del Modal con botones de acción -->
        <div class="modal-footer border-top-0 pt-0">
          <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" form="formAnadirJuego" class="btn btn-primary rounded-pill">Guardar juego</button>
        </div>

      </div>
    </div>
  </div>

  <!-- Modal Iniciar Sesión -->
  <div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 shadow border-0">
        
        <!-- Cabecera -->
        <div class="modal-header border-bottom-0 pb-0">
          <h2 class="modal-title h4 fw-bold" id="modalLoginLabel">🔑 Iniciar sesión</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Cuerpo / Formulario -->
        <form id="formLogin">
          <div class="modal-body py-4">
            <div class="mb-3">
              <label for="loginEmail" class="form-label fw-semibold">Correo electrónico</label>
              <input type="email" class="form-control" id="loginEmail" placeholder="tu@email.com" required>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="loginPassword" class="form-label fw-semibold mb-0">Contraseña</label>
                <a href="#" class="small text-decoration-none">¿Olvidaste tu contraseña?</a>
              </div>
              <input type="password" class="form-control" id="loginPassword" placeholder="••••••••" required>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="recordarSesion">
              <label class="form-check-label small" for="recordarSesion">
                Recordar mi sesión
              </label>
            </div>
          </div>

          <!-- Pie -->
          <div class="modal-footer border-top-0 pt-0 d-flex flex-column">
            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">Entrar</button>
            <p class="small text-muted mt-3 mb-0">
              ¿No tienes cuenta? 
              <a href="#" data-bs-toggle="modal" data-bs-target="#modalRegistro" class="fw-bold text-decoration-none">Regístrate gratis</a>
            </p>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modal Registro -->
  <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 shadow border-0">
        
        <!-- Cabecera -->
        <div class="modal-header border-bottom-0 pb-0">
          <h2 class="modal-title h4 fw-bold" id="modalRegistroLabel">🚀 Crear cuenta</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Cuerpo / Formulario -->
        <form id="formRegistro">
          <div class="modal-body py-4">
            <div class="mb-3">
              <label for="registroNombre" class="form-label fw-semibold">Nombre de usuario</label>
              <input type="text" class="form-control" id="registroNombre" placeholder="Ej. GamerPro99" required>
            </div>
            <div class="mb-3">
              <label for="registroEmail" class="form-label fw-semibold">Correo electrónico</label>
              <input type="email" class="form-control" id="registroEmail" placeholder="tu@email.com" required>
            </div>
            <div class="mb-3">
              <label for="registroPassword" class="form-label fw-semibold">Contraseña</label>
              <input type="password" class="form-control" id="registroPassword" placeholder="Mínimo 8 caracteres" required minlength="8">
            </div>
            <div class="mb-3">
              <label for="registroConfirmPassword" class="form-label fw-semibold">Confirmar contraseña</label>
              <input type="password" class="form-control" id="registroConfirmPassword" placeholder="Repite tu contraseña" required minlength="8">
            </div>
          </div>

          <!-- Pie -->
          <div class="modal-footer border-top-0 pt-0 d-flex flex-column">
            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">Crear cuenta</button>
            <p class="small text-muted mt-3 mb-0">
              ¿Ya tienes cuenta? 
              <a href="#" data-bs-toggle="modal" data-bs-target="#modalLogin" class="fw-bold text-decoration-none">Inicia sesión</a>
            </p>
          </div>
        </form>

      </div>
    </div>
  </div>

  <footer class="bg-white text-muted text-center py-4 mt-auto border-top rounded-top-4 shadow-sm">
    <div class="container">
      <small>BacklogQuest · Biblioteca</small>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="assets/main.js"></script>
</body>
</html>
