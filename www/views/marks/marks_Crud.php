<?php
include_once "../../models/scrapModel.php";
@include_once "../../config/config.php";

        $scrapModel = new ScrapModel();
        $marks = $scrapModel->obtenerMarks();

function cmp($a, $b) {
    return $a->getId() - $b->getId();
}
usort($marks, "cmp");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Marcas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/../css/style.css">
</head>
<body class="bg-info-subtle p-4">

<?php include '../../addons/barnav.php'; ?>

<div id="content">

    <h1 class="mb-4 text-center">Listado de Marcas</h1>

    <!-- Contenedor con Tabla -->
    <div class="container p-4 bg-success-subtle rounded mt-5">
        <div class="table-responsive">
            <table id="tablaMarks" class="table table-striped table-bordered">
                <thead class="table-success text-center">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#registrarMarkModal">
                        Registrar Marca
                    </button>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php foreach ($marks as $mark): ?>
                        <tr id="markRow<?= $mark->getId_Mark(); ?>">
                                <td><?= htmlspecialchars($mark->getId_Mark() ?? ''); ?></td>
                                <td><?= htmlspecialchars($mark->getM_Name() ?? ''); ?></td>
                                <td>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <button class="btn btn-warning btn-sm" onclick="openEditMarkModal(<?= $mark->getId_Mark(); ?>)">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php include 'marks_Add.php'; ?>
    <div id="edit-form-container"></div>
</div>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tablaMarks').DataTable();
    });

    function openEditMarkModal(id) {
        $.get('marks_Edit.php', { id: id }, function(html) {
            $('#edit-form-container').html(html);
        });
    }

</script>

<script src="/addons/barnav.js"></script>
</body>
</html>
