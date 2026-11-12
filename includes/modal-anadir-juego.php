<!-- includes/modal-anadir-juego.php -->
<div class="modal fade" id="modalAnadirJuego" tabindex="-1" aria-labelledby="modalAnadirJuegoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 border border-secondary-subtle shadow bg-body-tertiary text-body">
        
        <!-- Cabecera del Modal -->
        <div class="modal-header border-bottom-0 pb-0">
          <h2 class="modal-title h5 fw-bold text-body" id="modalAnadirJuegoLabel">🎮 Añadir nuevo juego</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Cuerpo del Modal -->
        <div class="modal-body py-3">
          
          <!-- 1. BUSCADOR RAWG.IO -->
          <div class="mb-3">
            <label for="inputBuscarRAWG" class="form-label fw-semibold text-body">Buscar juego en IGDB</label>
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
          <div id="previewJuegoSeleccionado" class="card bg-body-secondary border border-secondary-subtle rounded-3 mb-3 p-3">
            <div class="d-flex align-items-center gap-3">
              <img id="imagenJuegoSeleccionado" src="assets/portada-placeholder.svg" alt="Vista previa de portada" class="rounded shadow-sm object-fit-cover" width="110" height="65">
              <div class="flex-grow-1">
                <span class="badge bg-success mb-1" id="badgeSeleccionado">Juego seleccionado</span>
                <h5 id="tituloJuegoSeleccionado" class="h6 fw-bold mb-0 text-body">Ningún juego seleccionado</h5>
                <small id="infoJuegoSeleccionado" class="text-muted">Busca un juego arriba para vincular la portada e información de RAWG.</small>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill d-none" id="btnCambiarJuego">
                Cambiar
              </button>
            </div>
          </div>

          <!-- 3. FORMULARIO CON DETALLES PERSONALIZADOS -->
          <form id="formAnadirJuego">
            <!-- Inputs ocultos con los names adaptados para el backend -->
            <input type="hidden" id="rawgGameId" name="rawg_id">
            <input type="hidden" id="rawgImageUrl" name="imagen_url">
            <input type="hidden" id="tituloJuegoOculto" name="titulo">
            <input type="hidden" id="rawgPlaytime" name="duracion_estimada_horas" value="0">

            <div class="row g-2 mb-3">
              <div class="col-6">
                <label for="plataformaJuego" class="form-label fw-semibold text-body">Plataforma</label>
                <select class="form-select" id="plataformaJuego" name="plataforma" required disabled>
                  <option value="" selected disabled>Primero busca y selecciona un juego...</option>
                </select>
              </div>
              <div class="col-6">
                <label for="estadoJuego" class="form-label fw-semibold text-body">Estado inicial</label>
                <select class="form-select" id="estadoJuego" name="estado" required>
                  <option value="Pendiente" selected>Pendiente</option>
                  <option value="Jugando">Jugando</option>
                  <option value="Pausado">Pausado</option>
                  <option value="Completado">Completado</option>
                  <option value="Abandonado">Abandonado</option>
                </select>
              </div>
            </div>

            <!-- Horas jugadas iniciales -->
            <div class="mb-3">
              <label for="horasJuego" class="form-label fw-semibold text-body">Horas jugadas iniciales</label>
              <input type="number" step="0.5" min="0" class="form-control" id="horasJuego" name="horas" value="0">
            </div>

            <!-- Nota / Checkpoint inicial -->
            <div class="mb-3">
                <label for="notaJuego" class="form-label fw-semibold text-body small">Notas / Checkpoint</label>
                <textarea class="form-control rounded-4" id="notaJuego" name="notas_checkpoint" rows="2" placeholder="Notas iniciales, por dónde vas..."></textarea>
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