<?php
include_once "../../models/scrapModel.php";
@include_once "../../config/config.php";

        $scrapModel = new ScrapModel();
        $titles = $scrapModel->obtTitles();

function cmp($a, $b) {
    return $a->getId() - $b->getId();
}
usort($titles, "cmp");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Titulos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/../css/style.css">
</head>
<body class="bg-info-subtle p-4">

<?php include '../../addons/barnav.php'; ?>

<div id="content">

    <h1 class="mb-4 text-center">Listado de Titulos</h1>

    <!-- Contenedor con Tabla -->
    <div class="container p-4 bg-success-subtle rounded mt-5">
        <div class="table-responsive">
            <table id="tablaTitles" class="table table-striped table-bordered">
                <thead class="table-success text-center">
                    <button id="btnColocarMark" onclick="colocarMarks()" class="btn btn-primary mb-3">
                        Asignar Marca
                    </button>
                    <tr>
                        <th>Nombre</th>
                        <th>Marca</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php foreach ($titles as $title): ?>
                        <tr id="titleRow<?= $title->getId(); ?>">
                                <td><?= htmlspecialchars($title->getTitle() ?? ''); ?></td>
                                <td><?= htmlspecialchars(!empty($title->getM_Name()) ? $title->getM_Name() : 'No Colocada'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="edit-form-container"></div>
</div>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tablaTitles').DataTable();
    });

    function colocarMarks() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción Añadira las marcas a los productos coincidentes.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, añadir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../../controllers/markController.php', { action: 'colocarMarks'}, function(resp) {
                    if (resp.status === 'success') {
                        Swal.fire(
                            'Marcas colocadas',
                            `Se actualizaron ${resp.total_actualizados} productos.`,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', resp.message, 'error');
                    }
                }, 'json').fail(() => {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                });
            }
        });
    }

</script>

<script src="/addons/barnav.js"></script>
</body>
</html>
