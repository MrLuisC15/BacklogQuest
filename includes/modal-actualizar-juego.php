<!-- includes/modal-actualizar-juego.php -->
<div class="modal fade" id="modalActualizarJuego" tabindex="-1" aria-labelledby="modalActualizarJuegoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalActualizarJuegoLabel">✏️ Actualizar: <span id="tituloJuegoActualizar" class="text-primary"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formActualizarJuego">
          
          <!-- ID oculto del registro en biblioteca_usuario -->
          <input type="hidden" id="updateBibliotecaId" name="biblioteca_id">

          <!-- Estado -->
          <div class="mb-3">
            <label for="updateEstado" class="form-label fw-semibold small">Estado</label>
            <select class="form-select rounded-pill" id="updateEstado" name="estado">
              <option value="Completado">Completado</option>  
              <option value="Jugando">Jugando</option>
              <option value="Pendiente">Pendiente</option>
              <option value="Pausado">Pausado</option>
              <option value="Abandonado">Abandonado</option>
            </select>
          </div>

          <!-- Horas jugadas -->
          <div class="mb-3">
            <label for="updateHoras" class="form-label fw-semibold small">Horas jugadas</label>
            <input type="number" step="0.5" min="0" class="form-control rounded-pill" id="updateHoras" name="horas" required>
          </div>

          <!-- Nota / Checkpoint (Textarea) -->
          <div class="mb-3">
            <label for="updateNota" class="form-label fw-semibold small">Notas / Checkpoint</label>
            <textarea class="form-control rounded-4" id="updateNota" name="notas_checkpoint" rows="3" placeholder="Por dónde vas, notas del juego..."></textarea>
          </div>

          <!-- Botones de acción -->
          <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar cambios</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>