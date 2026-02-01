<?php
require_once '../models/scrapModel.php';
@include_once "../config/config.php";

header('Content-Type: application/json');

// EDITAR MARCA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateMark') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? null;


    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID de Marca no especificado']);
        exit;
    }

    $scrapModel = new ScrapModel();

    $resultado = $scrapModel->actMark($id,$name);

    if ($resultado) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Marca editado exitosamente'
        ]);
        exit;
    } else {
        echo json_encode([
            'success' => 'error',
            'message' => 'Error en editar el Marca'
        ]);
        exit;
    }
}

// Añadir Marca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addMark') {
    $name = $_POST['mark_name'] ?? null;// EDITAR PERFIL VETERINARIO

    $scrapModel = new ScrapModel();

    $resultado = $scrapModel->addMark($name);

    if ($resultado) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Marca agregada exitosamente'
        ]);
        exit;
    } else {
        echo json_encode([
            'success' => 'error',
            'message' => 'Error en agregar Marca'
        ]);
        exit;
    }
}

// Colocar Marcas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'colocarMarks') {

    error_log("entro a colocar marcas");

    $scrapModel = new ScrapModel();
    $marcas = $scrapModel->obtenerMarks();

    $totalAfectados = 0;
    $detalle = [];

    foreach ($marcas as $marca) {
        $id   = $marca->getId_Mark();
        $mark = $marca->getM_Name();

        $afectados = $scrapModel->colocarMarcas($id, $mark);

        $totalAfectados += $afectados;

        $detalle[] = [
            'marca' => $mark,
            'actualizados' => $afectados
        ];
    }

    echo json_encode([
        'status' => 'success',
        'total_actualizados' => $totalAfectados,
        'detalle' => $detalle
    ]);
    exit;
}



// Si no entra en ninguna condición válida
echo json_encode(['status' => 'error', 'message' => 'Acceso no permitido.']);
exit;