<?php
require_once __DIR__ . '/models/scrapModel.php';
@include_once "../config/config.php";

$scrapModel = new ScrapModel();
$regs = $scrapModel->obtenerRegistros();

function cmp($a, $b) {
    return $a->getId() - $b->getId();
}
usort($regs, "cmp");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Registros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/../css/style.css">
</head>

<style>
    .url-cell {
        max-width: 50000px;
        word-break: break-all;
        white-space: normal;
    }

    .url-cell a {
        display: inline-block;
    }

</style>

<body id="bg-scr" class="bg-info-subtle p-4">
<?php include 'addons/barnav.php'; ?>

<div id="content">

    <h1 class="mb-4 text-center">Listado de Registros</h1>

    <div class="container p-4 bg-success-subtle rounded mt-5">
        <div class="table-responsive">
            <table id="tablaRegs" class="table table-striped table-bordered">
                <thead class="table-success text-center">
                    <tr>
                        <th>Titulo</th>
                        <th>Link</th>
                        <th>Imagen</th>
                        <th>Creado en</th>                      
                        <th>Precio actual </th>
                        <th>Precio pasado</th>
                        <th>Registrado en</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php foreach ($regs as $reg): ?>
                        <tr id="userRow<?= $reg->getId(); ?>">
                            <td><?= htmlspecialchars(trim($reg->getTitle() ?? '')); ?></td>
                            <td class="url-cell"><?= htmlspecialchars(trim($reg->getLink() ?? '')); ?></td>
                            <td><?= htmlspecialchars(trim($reg->getImage() ?? '')); ?></td>
                            <td><?= htmlspecialchars(trim($reg->getCreate_At() ?? '')); ?></td>
                            <td><?= htmlspecialchars(trim($reg->getActual_Price() ?? '')); ?></td>
                            <td><?= htmlspecialchars(trim($reg->getLast_Price() ?? '')); ?></td>
                            <td><?= htmlspecialchars(trim( $reg->getRegistered_At() ?? '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tablaRegs').DataTable({
            order: [[6, 'desc']], // última fecha primero
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            }
        });    
    });

</script>

<script src="/addons/barnav.js"></script>

</body>
</html>