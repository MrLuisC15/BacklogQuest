<?php
  session_start();
  /// 1. Control de seguridad de sesión
  if (!isset($_SESSION['usuario_id'])) {
      header('Location: index.php');
      exit;
  }

  $nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Mi cuenta';
  $usuario_id = $_SESSION['usuario_id'];

  // 2. Conexión y consulta de los juegos del usuario
  try {
      require_once 'includes/conexion.php';

      $stmt = $pdo->prepare("
        SELECT 
            b.id AS biblioteca_id,
            j.titulo,
            j.imagen_url,
            j.duracion_estimada_horas,
            p.nombre AS plataforma,
            b.estado,
            b.horas_jugadas,
            b.notas_checkpoint
        FROM biblioteca_usuario b
        JOIN juegos j ON b.juego_id = j.id
        JOIN plataformas p ON b.plataforma_id = p.id
        WHERE b.usuario_id = ?
        ORDER BY j.titulo ASC
      ");
      $stmt->execute([$usuario_id]);
      $juegosBiblioteca = $stmt->fetchAll();

  } catch (Exception $e) {
      $errorBD = "Error al cargar la biblioteca: " . $e->getMessage();
      $juegosBiblioteca = [];
  }
?>  

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Biblioteca | BacklogQuest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
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
  <body class="d-flex flex-column min-vh-100 bg-body">

    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>

    <!-- CONTENIDO PRINCIPAL: BIBLIOTECA -->
    <main class="flex-grow-1 container py-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
          <h2 class="fw-bold text-body mb-0">Mi Colección</h2>
          <span class="text-muted small">Total: <?php echo count($juegosBiblioteca); ?> juegos</span>
        </div>

        <!-- Buscador en tiempo real -->
        <?php if (!empty($juegosBiblioteca)): ?>
          <div class="w-100" style="max-width: 300px;">
            <input type="text" id="inputBuscarBiblioteca" class="form-control rounded-pill px-3 py-2 bg-body-tertiary border-secondary-subtle" placeholder="🔍 Buscar en mi biblioteca...">
          </div>
        <?php endif; ?>
      </div>

      <?php if (isset($errorBD)): ?>
        <div class="alert alert-danger rounded-4" role="alert">
          <?php echo $errorBD; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($juegosBiblioteca)): ?>
        <?php 
          // Detectar qué familias de plataformas existen en la biblioteca del usuario
          $tieneXbox = false;
          $tienePlay = false;
          $tieneNintendo = false;
          $tienePC = false;

          foreach ($juegosBiblioteca as $j) {
              $plat = strtolower($j['plataforma']);
              if (strpos($plat, 'xbox') !== false) $tieneXbox = true;
              elseif (strpos($plat, 'playstation') !== false || strpos($plat, 'ps') !== false) $tienePlay = true;
              elseif (strpos($plat, 'nintendo') !== false || strpos($plat, 'switch') !== false) $tieneNintendo = true;
              else $tienePC = true; // Agrupa PC o cualquier otra plataforma genérica
          }
        ?>

        <!-- Botones de Filtro por Plataforma -->
        <div class="d-flex flex-wrap gap-2 mb-4" id="contenedorFiltrosPlataforma">
          <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold btn-filtro active" data-filtro="todos">Todos</button>
          
          <?php if ($tienePlay): ?>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold btn-filtro" data-filtro="playstation">PlayStation</button>
          <?php endif; ?>

          <?php if ($tieneXbox): ?>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold btn-filtro" data-filtro="xbox">Xbox</button>
          <?php endif; ?>

          <?php if ($tieneNintendo): ?>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold btn-filtro" data-filtro="nintendo">Nintendo</button>
          <?php endif; ?>

          <?php if ($tienePC): ?>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold btn-filtro" data-filtro="pc">PC / Otros</button>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- Cuadrícula de Juegos adaptada -->
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="gridBiblioteca">
        <?php if (empty($juegosBiblioteca)): ?>
          <!-- Si no hay juegos en la biblioteca -->
          <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1 text-center py-5 my-auto w-100">
            <div class="display-4 mb-3">📭</div>
            <h3 class="fw-bold text-body">Tu biblioteca está vacía</h3>
            <p class="text-muted mb-4" style="max-width: 400px;">
                Aún no has añadido ningún juego. ¡Empieza a buscar y registra tu aventura!
            </p>
            <button type="button" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAnadirJuego">
                🎮 Añadir mi primer juego
            </button>
          </div>
        <?php else: ?>
          <?php foreach ($juegosBiblioteca as $juego): ?>
            <?php 
              $platNombre = $juego['plataforma'] ?? '';
              $platLower = strtolower($platNombre);
              
              // Asignar categoría general para el filtro
              $categoriaFiltro = 'pc';
              if (strpos($platLower, 'xbox') !== false) {
                  $categoriaFiltro = 'xbox';
              } elseif (strpos($platLower, 'playstation') !== false || strpos($platLower, 'ps') !== false) {
                  $categoriaFiltro = 'playstation';
              } elseif (strpos($platLower, 'nintendo') !== false || strpos($platLower, 'switch') !== false) {
                  $categoriaFiltro = 'nintendo';
              }
            ?>
            <div class="col item-tarjeta-juego" data-plataforma="<?php echo $categoriaFiltro; ?>" data-titulo="<?php echo htmlspecialchars(strtolower($juego['titulo'])); ?>">
              <div class="card h-100 shadow-sm border border-secondary-subtle bg-body-tertiary rounded-4 p-3 tarjeta-juego">
                <div class="d-flex gap-3 align-items-stretch h-100">
                  
                  <!-- Carátula Vertical de IGDB -->
                  <div class="tarjeta-caratula-contenedor rounded-3 overflow-hidden shadow-sm flex-shrink-0">
                    <img src="<?php echo htmlspecialchars($juego['imagen_url'] ?: 'assets/portada-placeholder.svg'); ?>" 
                         class="tarjeta-caratula-img w-100 h-100 object-fit-cover" 
                         alt="<?php echo htmlspecialchars($juego['titulo']); ?>">
                  </div>

                  <!-- Cuerpo e Información de la Tarjeta -->
                  <div class="flex-grow-1 d-flex flex-column justify-content-between" style="min-width: 0;">
                    <div>
                      <!-- Título Completo sin recorte -->
                      <h5 class="fw-bold text-body titulo-juego-card mb-2">
                        <?php echo htmlspecialchars($juego['titulo']); ?>
                      </h5>

                      <?php 
                        // 1. LÓGICA DE COLORES PARA LA PLATAFORMA
                        $badgePlatClass = 'bg-secondary-subtle text-secondary border-secondary-subtle';

                        if ($categoriaFiltro === 'playstation') {
                            $badgePlatClass = 'bg-primary-subtle text-primary border-primary-subtle';
                        } elseif ($categoriaFiltro === 'xbox') {
                            $badgePlatClass = 'bg-success-subtle text-success border-success-subtle';
                        } elseif ($categoriaFiltro === 'nintendo') {
                            $badgePlatClass = 'bg-danger-subtle text-danger border-danger-subtle';
                        }

                        // 2. LÓGICA DE COLORES Y BOTÓN RÁPIDO SEGÚN ESTADO
                        $badgeEstadoClass = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                        $badgeStyle = '';
                        $botonAccionRapida = '';

                        switch ($juego['estado']) {
                            case 'Pendiente':
                                $badgeEstadoClass = 'bg-warning-subtle text-warning border-warning';
                                $botonAccionRapida = '<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 btn-estado-rapido" data-id="'.$juego['biblioteca_id'].'" data-siguiente-estado="Jugando" title="Empezar a jugar">▶️</button>';
                                break;
                            case 'Jugando':
                                $badgeEstadoClass = 'bg-success-subtle text-success border-success-subtle';
                                $botonAccionRapida = '
                                  <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-0 btn-estado-rapido" data-id="'.$juego['biblioteca_id'].'" data-siguiente-estado="Pausado" title="Pausar">⏸️</button>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0 btn-estado-rapido" data-id="'.$juego['biblioteca_id'].'" data-siguiente-estado="Completado" title="Completar">✅</button>
                                  </div>
                                ';
                                break;
                            case 'Pausado':
                                $badgeStyle = 'background-color: #fff4e6 !important; color: #fd7e14 !important; border-color: #ffd8a8 !important;';
                                $botonAccionRapida = '<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 btn-estado-rapido" data-id="'.$juego['biblioteca_id'].'" data-siguiente-estado="Jugando" title="Reanudar">▶️</button>';
                                break;
                            case 'Completado':
                                $badgeEstadoClass = 'bg-primary-subtle text-primary border-primary-subtle';
                                $botonAccionRapida = '<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0 btn-estado-rapido" data-id="'.$juego['biblioteca_id'].'" data-siguiente-estado="Jugando" title="Jugar de nuevo">🔄</button>';
                                break;
                            case 'Abandonado':
                                $badgeEstadoClass = 'bg-danger-subtle text-danger border-danger-subtle';
                                $botonAccionRapida = '<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 btn-estado-rapido" data-id="'.$juego['biblioteca_id'].'" data-siguiente-estado="Jugando" title="Retomar">▶️</button>';
                                break;
                        }
                      ?>

                      <!-- Plataforma -->
                      <div class="mb-1">
                        <span class="badge <?php echo $badgePlatClass; ?> border rounded-pill px-2">
                          🎮 <?php echo htmlspecialchars($juego['plataforma']); ?>
                        </span>
                      </div>

                      <!-- Estado y su botón de acción rápida justo debajo -->
                      <div class="mb-2 d-flex align-items-center justify-content-between gap-2">
                        <span class="badge <?php echo $badgeEstadoClass; ?> border rounded-pill px-2" style="<?php echo $badgeStyle; ?>">
                          📌 <?php echo htmlspecialchars($juego['estado']); ?>
                        </span>
                        <div>
                          <?php echo $botonAccionRapida; ?>
                        </div>
                      </div>
                    </div>

                    <!-- Pie de la Tarjeta: Horas, Notas y Acciones -->
                    <div class="mt-auto pt-2 border-top border-secondary-subtle d-flex justify-content-between align-items-center">
                      <small class="text-muted fw-semibold">⏱️ <?php echo number_format($juego['horas_jugadas'], 1); ?> hrs</small>
                      
                      <div class="d-flex gap-1 align-items-center flex-shrink-0">
                        <?php if (!empty($juego['notas_checkpoint'])): ?>
                          <button type="button" 
                                  class="btn btn-outline-info btn-sm rounded-pill px-2 py-0 btn-ver-nota" 
                                  data-titulo="<?php echo htmlspecialchars($juego['titulo']); ?>" 
                                  data-nota="<?php echo htmlspecialchars($juego['notas_checkpoint']); ?>" 
                                  title="Ver última nota">
                            📝
                          </button>
                        <?php endif; ?>

                        <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-2 py-0 btn-abrir-actualizar"
                                data-id="<?php echo $juego['biblioteca_id']; ?>"
                                data-titulo="<?php echo htmlspecialchars($juego['titulo']); ?>"
                                data-estado="<?php echo htmlspecialchars($juego['estado']); ?>"
                                data-horas="<?php echo $juego['horas_jugadas']; ?>"
                                data-nota="<?php echo htmlspecialchars($juego['notas_checkpoint'] ?? ''); ?>"
                                data-bs-toggle="modal" data-bs-target="#modalActualizarJuego"
                                title="Editar progreso">
                          ✏️
                        </button>
                        
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-0"
                                data-id="<?php echo $juego['biblioteca_id']; ?>"
                                data-titulo="<?php echo htmlspecialchars($juego['titulo']); ?>"
                                data-bs-toggle="modal" data-bs-target="#modalEliminarJuego"
                                title="Eliminar juego">
                          🗑️
                        </button>
                      </div>
                    </div>

                  </div>

                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </main>

    <!-- INCLUDES DE LOS MODAL -->
    <?php include 'includes/modal-anadir-juego.php'; ?>
    <?php include 'includes/modal-actualizar-juego.php'; ?>
    <?php include 'includes/modal-eliminar-juego.php'; ?>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="assets/main.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script de filtrado y búsqueda en tiempo real -->
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const botonesFiltro = document.querySelectorAll('.btn-filtro');
        const inputBuscador = document.getElementById('inputBuscarBiblioteca');
        const itemsJuegos = document.querySelectorAll('.item-tarjeta-juego');

        let plataformaActual = 'todos';
        let textoBusqueda = '';

        function aplicarFiltros() {
          itemsJuegos.forEach(item => {
            const platItem = item.dataset.plataforma;
            const tituloItem = item.dataset.titulo;

            const coincidePlataforma = (plataformaActual === 'todos' || platItem === plataformaActual);
            const coincideTexto = tituloItem.includes(textoBusqueda);

            if (coincidePlataforma && coincideTexto) {
              item.style.display = '';
            } else {
              item.style.display = 'none';
            }
          });
        }

        // Eventos para los botones de plataforma
        botonesFiltro.forEach(btn => {
          btn.addEventListener('click', () => {
            botonesFiltro.forEach(b => {
              b.classList.remove('btn-primary', 'active');
              b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-primary', 'active');

            plataformaActual = btn.dataset.filtro;
            aplicarFiltros();
          });
        });

        // Evento para el buscador en tiempo real
        if (inputBuscador) {
          inputBuscador.addEventListener('input', (e) => {
            textoBusqueda = e.target.value.toLowerCase().trim();
            aplicarFiltros();
          });
        }
      });
    </script>
  </body>
</html>