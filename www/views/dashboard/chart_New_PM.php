<?php
$mes  = $_GET['mes']  ?? date('m');
$anio = $_GET['anio'] ?? date('Y');

/* Resumen nuevos vs repetidos */
$resumenMes = $scrapModel->newPM($mes, $anio);
$resumenMes = $resumenMes ?: ['nuevos' => 0, 'repetidos' => 0];

/* Listado de productos nuevos del mes */
$nuevosMes = $scrapModel->prodNewMes($mes, $anio);
?>



<div class="row justify-content-center mb-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body text-center py-4">
                <h5 class="mb-3 text-center">
                    Productos nuevos vs existentes (<?= $mes ?>/<?= $anio ?>)
                </h5>

                <div class="d-flex justify-content-center mb-3">
                    <div style="width: 220px; height: 220px;">
                        <canvas id="donaNuevosMes"></canvas>
                    </div>
                </div>

                <button class="btn btn-outline-primary btn-sm" onclick="toggleNuevos()">
                    Ver productos nuevos
                </button>
            </div>
        </div>
    </div>
</div>


<div id="tablaNuevos" style="display:none;">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Primera aparición</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($nuevosMes as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['titulo']) ?></td>
                    <td><?= htmlspecialchars($p['primera_aparicion']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dataPM = <?= json_encode($resumenMes); ?>;
console.log(dataPM);

const valores = [Number(dataPM.nuevos), Number(dataPM.repetidos)];
const total = valores.reduce((a, b) => a + b, 0);

new Chart(document.getElementById('donaNuevosMes'), {
    type: 'doughnut',
    data: {
        labels: ['Nuevos', 'Existentes'],
        datasets: [{
            data: valores,
            backgroundColor: ['#198754', '#0d6efd'],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        const valor = ctx.parsed;
                        const porcentaje = total
                            ? ((valor / total) * 100).toFixed(1)
                            : 0;

                        return `${ctx.label}: ${valor} (${porcentaje}%)`;
                    }
                }
            }
        }
    }
});

function toggleNuevos() {
    const tabla = document.getElementById('tablaNuevos');
    tabla.style.display = tabla.style.display === 'none' ? 'block' : 'none';
}
</script>
