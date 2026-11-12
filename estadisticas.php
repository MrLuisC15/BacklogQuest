<?php
  session_start();
  // Si NO hay sesión activa, expulsamos al usuario de vuelta al index.php
  if (!isset($_SESSION['usuario_id'])) {
      header('Location: index.php');
      exit;
  }
  
  $usuario_id = $_SESSION['usuario_id'];
  $nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Mi cuenta';

  // Consulta dinámica de estadísticas desde la base de datos
  try {
      require_once 'includes/conexion.php';

      // 1. Juegos Totales
      $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM biblioteca_usuario WHERE usuario_id = ?");
      $stmtTotal->execute([$usuario_id]);
      $totalJuegos = $stmtTotal->fetchColumn();

      // 2. Juegos por Estado
      $stmtEstados = $pdo->prepare("
          SELECT estado, COUNT(*) as total 
          FROM biblioteca_usuario 
          WHERE usuario_id = ? 
          GROUP BY estado
      ");
      $stmtEstados->execute([$usuario_id]);
      $estadosRaw = $stmtEstados->fetchAll(PDO::FETCH_KEY_PAIR);

      // Normalizar estados para evitar errores si alguno está en 0
      $estados = [
          'Pendiente' => $estadosRaw['Pendiente'] ?? 0,
          'Jugando' => $estadosRaw['Jugando'] ?? 0,
          'Pausado' => $estadosRaw['Pausado'] ?? 0,
          'Completado' => $estadosRaw['Completado'] ?? 0,
          'Abandonado' => $estadosRaw['Abandonado'] ?? 0,
      ];

      $jugandoActivos = $estados['Jugando'];
      $completados = $estados['Completado'];
      
      // Tasa de victoria (Completados / Totales)
      $tasaVictoria = $totalJuegos > 0 ? round(($completados / $totalJuegos) * 100, 1) : 0;

      // 3. Desglose por Plataformas Agrupadas por Marca
      $stmtPlataformas = $pdo->prepare("
          SELECT 
              CASE 
                  WHEN p.nombre LIKE '%playstation%' OR p.nombre LIKE '%ps%' THEN 'PlayStation'
                  WHEN p.nombre LIKE '%xbox%' THEN 'Xbox'
                  WHEN p.nombre LIKE '%nintendo%' OR p.nombre LIKE '%switch%' OR p.nombre LIKE '%ds%' THEN 'Nintendo'
                  ELSE 'PC'
              END AS plataforma_grupo,
              COUNT(*) as total 
          FROM biblioteca_usuario b
          JOIN plataformas p ON b.plataforma_id = p.id
          WHERE b.usuario_id = ?
          GROUP BY plataforma_grupo
          ORDER BY total DESC
      ");
      $stmtPlataformas->execute([$usuario_id]);
      $plataformas = $stmtPlataformas->fetchAll(PDO::FETCH_ASSOC);

  } catch (Exception $e) {
      $errorBD = "Error al cargar las estadísticas: " . $e->getMessage();
      $totalJuegos = 0;
      $jugandoActivos = 0;
      $completados = 0;
      $tasaVictoria = 0;
      $estados = ['Pendiente' => 0, 'Jugando' => 0, 'Pausado' => 0, 'Completado' => 0, 'Abandonado' => 0];
      $plataformas = [];
  }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Estadísticas | BacklogQuest</title>
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

  <a class="visually-hidden-focusable btn btn-primary position-absolute top-0 start-0 m-2 z-3" href="#contenido">Saltar al contenido</a>

  <!-- Header / Navbar Dinámico -->
  <?php include 'includes/header.php'; ?>

  <!-- Contenido Principal: Estadísticas -->
  <main id="contenido" class="container my-4 flex-grow-1">
    
    <!-- Encabezado de la sección -->
    <div class="mb-4">
      <h1 class="h3 fw-bold text-body">📊 Estadísticas de tu Biblioteca</h1>
      <p class="text-muted">Un resumen global del estado de tus juegos, plataformas y progreso general.</p>
    </div>

    <?php if (isset($errorBD)): ?>
      <div class="alert alert-danger rounded-4" role="alert">
        <?php echo $errorBD; ?>
      </div>
    <?php endif; ?>

    <!-- Fila 1: Tarjetas de Resumen General (KPIs) -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border border-secondary-subtle shadow-sm rounded-4 p-3 bg-body-tertiary h-100">
          <div class="text-muted small fw-semibold text-uppercase">Juegos Totales</div>
          <div class="h2 fw-bold text-primary mb-0 mt-1"><?php echo $totalJuegos; ?></div>
          <small class="text-muted">En tu biblioteca</small>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border border-secondary-subtle shadow-sm rounded-4 p-3 bg-body-tertiary h-100">
          <div class="text-muted small fw-semibold text-uppercase">Jugando Activos</div>
          <div class="h2 fw-bold text-info mb-0 mt-1"><?php echo $jugandoActivos; ?></div>
          <small class="text-muted">En progreso actual</small>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border border-secondary-subtle shadow-sm rounded-4 p-3 bg-body-tertiary h-100">
          <div class="text-muted small fw-semibold text-uppercase">Completados</div>
          <div class="h2 fw-bold text-success mb-0 mt-1"><?php echo $completados; ?></div>
          <small class="text-muted">¡Objetivo logrado!</small>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border border-secondary-subtle shadow-sm rounded-4 p-3 bg-body-tertiary h-100">
          <div class="text-muted small fw-semibold text-uppercase">Tasa de Victoria</div>
          <div class="h2 fw-bold text-warning mb-0 mt-1"><?php echo $tasaVictoria; ?>%</div>
          <small class="text-muted">Juegos finalizados</small>
        </div>
      </div>
    </div>

    <!-- Fila 2: Desglose por Estados y Plataformas -->
    <div class="row g-4">
      
      <!-- Distribución por Estados -->
      <div class="col-12 col-lg-6">
        <div class="card border border-secondary-subtle shadow-sm rounded-4 p-4 bg-body-tertiary h-100">
          <h2 class="h5 fw-bold mb-3 text-body">Estado de los Juegos</h2>
          
          <?php 
            $estadosConfig = [
                'Pendiente' => ['label' => 'Pendientes', 'class' => 'bg-dark'],
                'Jugando' => ['label' => 'Jugando', 'class' => 'bg-primary'],
                'Pausado' => ['label' => 'Pausados', 'class' => 'bg-warning'],
                'Completado' => ['label' => 'Completados', 'class' => 'bg-success'],
                'Abandonado' => ['label' => 'Abandonados', 'class' => 'bg-danger']
            ];

            foreach ($estadosConfig as $key => $cfg):
                $cantidad = $estados[$key];
                $porcentaje = $totalJuegos > 0 ? ($cantidad / $totalJuegos) * 100 : 0;
          ?>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1 small fw-semibold text-body">
              <span><?php echo $cfg['label']; ?></span>
              <span class="text-muted"><?php echo $cantidad; ?> juegos (<?php echo round($porcentaje, 1); ?>%)</span>
            </div>
            <div class="progress" style="height: 10px;">
              <div class="progress-bar <?php echo $cfg['class']; ?>" style="width: <?php echo $porcentaje; ?>%"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Distribución por Plataformas (Agrupadas) -->
      <div class="col-12 col-lg-6">
        <div class="card border border-secondary-subtle shadow-sm rounded-4 p-4 bg-body-tertiary h-100">
          <h2 class="h5 fw-bold mb-3 text-body">Plataformas Principales</h2>
          
          <?php if (empty($plataformas)): ?>
            <p class="text-muted small">No hay plataformas registradas aún en tu biblioteca.</p>
          <?php else: ?>
            <?php 
              foreach ($plataformas as $plat):
                  $porcentajePlat = $totalJuegos > 0 ? ($plat['total'] / $totalJuegos) * 100 : 0;
                  $nombreMarca = $plat['plataforma_grupo'] ?? '';
                  
                  // Lógica de colores por marca unificada
                  $colorClass = 'bg-secondary'; // Gris por defecto (PC)

                  if ($nombreMarca === 'PlayStation') {
                      $colorClass = 'bg-primary'; // Azul
                  } elseif ($nombreMarca === 'Xbox') {
                      $colorClass = 'bg-success'; // Verde
                  } elseif ($nombreMarca === 'Nintendo') {
                      $colorClass = 'bg-danger'; // Rojo
                  }
            ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1 small fw-semibold text-body">
                <span><?php echo htmlspecialchars($nombreMarca); ?></span>
                <span class="text-muted"><?php echo $plat['total']; ?> juegos (<?php echo round($porcentajePlat, 1); ?>%)</span>
              </div>
              <div class="progress" style="height: 10px;">
                <div class="progress-bar <?php echo $colorClass; ?>" style="width: <?php echo $porcentajePlat; ?>%"></div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </main>

  <!-- Footer Compartido / Redondeado Suave -->
  <?php include 'includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="assets/main.js"></script>
</body>
</html>