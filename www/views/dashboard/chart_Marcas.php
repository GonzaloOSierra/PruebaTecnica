<?php 
    $porcTotal = $scrapModel->obtPorcTotal();
?>
<div class="row justify-content-center mb-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body text-center">
                <h5 class="mb-3">Distribución de productos por marca</h5>
                <div class="d-flex justify-content-center">
                    <canvas id="pieMarcas" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const dataMarcas = <?= json_encode($porcTotal); ?>;

        if (!dataMarcas || dataMarcas.length === 0) return;

        const labels = dataMarcas.map(item => item.marca);
        const porcentajes = dataMarcas.map(item => item.porcentaje);

        const colores = labels.map((_, i) => {
            const hue = Math.round((i * 360) / labels.length);
            return `hsl(${hue}, 70%, 55%)`;
        });

        const ctx = document.getElementById('pieMarcas');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: porcentajes,
                    backgroundColor: colores,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: c => `${c.label}: ${c.parsed}%`
                        }
                    }
                }
            }
        });
    })();
</script>
