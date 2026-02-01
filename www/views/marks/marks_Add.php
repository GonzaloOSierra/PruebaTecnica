
<div class="modal fade" id="registrarMarkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Registrar Nueva Marca</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formRegistrarMark" class="row">
          <div class="col-md-6 mb-3">
            <label for="mark_name" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="mark_name" id="mark_name" required maxlength="100">
          </div>

          <button type="submit" class="btn btn-success">Registrar</button>
        </form>
      </div>

    </div>
  </div>
</div>
<script>
    $('#formRegistrarMark').submit(function (e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      formData.append('action', 'addMark');

      $.ajax({
        url: '../../controllers/markController.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
          if (response.status === 'success') {
            Swal.fire('¡Agregado!', response.message, 'success').then(() => {
                window.location.reload();
            });
            $('#formRegistrarMark')[0].reset();
            $('.input-group-text').html('').removeClass('text-success text-danger');
          } else {
            Swal.fire('Error', response.message || 'No se pudo agregar', 'error');
          }
        },
        error: function (xhr, status, error) {
          console.error("Error AJAX:", error);
          console.log("Respuesta del servidor:", xhr.responseText);
          Swal.fire('Error', 'Error en el servidor: ' + error, 'error');
        }
      });
    })

</script>
