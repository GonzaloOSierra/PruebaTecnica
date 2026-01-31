<?php
require_once '../../models/scrapModel.php';

    $mark_id = (int)$_GET['id'];
    $scrapModel = new ScrapModel();
    $mark = $scrapModel->getMark($mark_id);

?>
<div class="modal fade" id="editarMarkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <h4 class="modal-title">Editar Marca</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formEditarMark" class="row">
          <input type="hidden" name="id" value="<?= $mark['id_mark']; ?>">

          <div class="col-md-6 mb-3">
            <label for="editMarkName" class="form-label">Nombre</label>
            <input type="text" id="editMarkName" name="name" class="form-control" 
                   value="<?= htmlspecialchars($mark['m_name']) ?>" required>
          </div>

          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
$('#editarMarkModal').modal('show');

$('#formEditarMark').on('submit', function(e) {
  e.preventDefault();
  const data = $(this).serialize() + '&action=updateMark';

  $.ajax({
    url: '../../controllers/markController.php',
    method: 'POST',
    data: data,
    dataType: 'json',
    success: function(resp) {
      if (resp.status === 'success') {
        Swal.fire('Actualizado', resp.message, 'success').then(() => location.reload());
      } else {
        Swal.fire('Error', resp.message, 'error');
      }
    },
    error: function(xhr) {
      console.error('Error respuesta:', xhr.responseText);
      Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
    }
  });
});
</script>
