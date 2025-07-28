<!-- Modal Externo: Reporte de Falla -->
<div class="modal fade" id="modalReporteFalla" tabindex="-1" aria-labelledby="modalReporteFallaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reportar Falla de Material</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="formReporteFalla">
          <!-- Seleccionar Material -->
          <div class="mb-3">
            <label for="material" class="form-label">Material afectado</label>
            <select class="form-control" id="material" name="material">
              @foreach ($materiales as $material)
                <option value="{{ $material->clave_material }}">{{ $material->descripcion }}</option>
              @endforeach
            </select>
          </div>

          <!-- Cantidad a descontar -->
          <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad a descontar</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
          </div>

          <!-- Autorización de Admin -->
          <div class="mb-3">
            <label for="autorizado_por" class="form-label">Contraseña Admin</label>
            <input type="password" class="form-control" id="autorizado_por" name="autorizado_por" required>
          </div>

          <!-- Botones de acción -->
          <div class="mb-3 d-flex justify-content-between">
            <button type="button" class="btn btn-primary" id="btnEnviarReporte">Enviar</button>
            <button type="button" class="btn btn-secondary" id="btnVerReporte">Ver PDF</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
