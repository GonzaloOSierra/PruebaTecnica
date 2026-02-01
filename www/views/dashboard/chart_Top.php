<?php
$topDiez = $scrapModel->topDiez();
?>

<div class="row justify-content-center mb-4">
    <div class="col-md-10">
        <div class="card shadow">
            <div class="card-body">

                <h5 class="text-center mb-4">
                    Top 10 productos más solicitados
                </h5>

                <div style="height: 420px; widht: 480;">
                    <canvas id="topProductos"></canvas>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const data = <?= json_encode($topDiez); ?>;
    if (!data || data.length === 0) return;

    const labels  = data.map(p => p.titulo);
    const valores = data.map(p => Number(p.dias_aparecido));

    new Chart(document.getElementById('topProductos'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Días en los que apareció',
                data: valores,
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: c => `Días: ${c.parsed.x}`
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
})();
</script>
