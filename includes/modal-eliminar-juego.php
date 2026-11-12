<!-- includes/modal-eliminar-juego.php -->
<div class="modal fade" id="modalEliminarJuego" tabindex="-1" aria-labelledby="modalEliminarJuegoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border border-secondary-subtle shadow bg-body-tertiary text-body">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger" id="modalEliminarJuegoLabel">⚠️ Eliminar juego</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de que deseas eliminar <strong id="tituloJuegoEliminar" class="text-danger"></strong> de tu biblioteca?</p>
        <p class="small text-muted mb-0">Esta acción solo borrará el juego de tu lista personal y no afectará al catálogo general.</p>
        
        <!-- Input oculto para el ID de biblioteca -->
        <input type="hidden" id="eliminarBibliotecaId">
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btnConfirmarEliminar" class="btn btn-danger rounded-pill px-4">Sí, eliminar</button>
      </div>
    </div>
  </div>
</div>