<?php
include_once "../../models/scrapModel.php";
@include_once "../../config/config.php";

$scrapModel = new ScrapModel();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/../css/style.css">
</head>
<body class="bg-info-subtle p-4">

<?php include '../../addons/barnav.php'; ?>

<div id="content">

    <h1 class="mb-4 text-center">Estadisticas</h1>

    <?php include 'chart_Marcas.php'; ?>

    <br>

    <?php include 'chart_Days.php'; ?>

    <br>

    <?php include 'chart_New_PM.php'; ?>

    <br>

    <?php include 'chart_Top.php'; ?>

    <div id="edit-form-container"></div>
</div>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="/addons/barnav.js"></script>
</body>
</html>
