<?php

$scrapModel = new ScrapModel();

///Fechas por defecto
$hoy = new DateTime();
$desde = $_GET['desde'] ?? (clone $hoy)->modify('-6 days')->format('Y-m-d');
$hasta = $_GET['hasta'] ?? $hoy->format('Y-m-d');

///Datos
$productos = $scrapModel->productosPorRango($desde, $hasta);

$totalDias = (new DateTime($desde))
    ->diff(new DateTime($hasta))
    ->days + 1;

///Contadores
$contadores = [
    'uno' => 0,
    'intermitente' => 0,
    'todos' => 0
];

foreach ($productos as $p) {
    if ($p['dias_aparecido'] == $totalDias) {
        $contadores['todos']++;
    } elseif ($p['dias_aparecido'] > 1) {
        $contadores['intermitente']++;
    } else {
        $contadores['uno']++;
    }
}

$totalProductos = count($productos);

$porcUno    = $totalProductos ? round($contadores['uno'] / $totalProductos * 100) : 0;
$porcInter  = $totalProductos ? round($contadores['intermitente'] / $totalProductos * 100) : 0;
$porcTodos  = $totalProductos ? round($contadores['todos'] / $totalProductos * 100) : 0;

?>
<div>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="date" name="desde" class="form-control" value="<?= $desde ?>" required>
        </div>
        <div class="col-md-4">
            <input type="date" name="hasta" class="form-control" value="<?= $hasta ?>" required>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary w-100">Consultar</button>
        </div>
    </form>

    <div class="mb-3">
        <label class="form-label fw-bold">Distribución de aparición</label>
        <div class="progress" style="height: 30px;">
            <div class="progress-bar bg-secondary"
                style="width: <?= $porcUno ?>%">
                <?= $porcUno ?>% Un día
            </div>
            <div class="progress-bar bg-warning text-dark"
                style="width: <?= $porcInter ?>%">
                <?= $porcInter ?>% Intermitente
            </div>
            <div class="progress-bar bg-success"
                style="width: <?= $porcTodos ?>%">
                <?= $porcTodos ?>% Todos los días
            </div>
        </div>
    </div>

    <button class="btn btn-outline-primary mb-3"
            onclick="toggleTabla()">
        Ver productos
    </button>

    
    <div id="tablaProductos" style="display:none;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Días aparecido</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['titulo']) ?></td>
                        <td><?= $p['dias_aparecido'] ?></td>
                        <td>
                            <?php if ($p['dias_aparecido'] == $totalDias): ?>
                                <span class="badge bg-success">Todos los días</span>
                            <?php elseif ($p['dias_aparecido'] > 1): ?>
                                <span class="badge bg-warning">Intermitente</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Un solo día</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>

    ///////// 7 DIAS //////////

    function toggleTabla() {
        const tabla = document.getElementById('tablaProductos');
        tabla.style.display = tabla.style.display === 'none' ? 'block' : 'none';
    }

</script>