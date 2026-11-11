<!-- Chart.js: Tren Sewa 7 Hari -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('chartSewa7Hari');
        if (!canvas) return;

        const c2d = canvas.getContext('2d');
        const gradient = c2d.createLinearGradient(0, 0, 0, canvas.parentElement.clientHeight || 220);
        gradient.addColorStop(0, 'rgba(94, 221, 91, 0.40)');
        gradient.addColorStop(1, 'rgba(94, 221, 91, 0)');

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#0e1b15',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#5edd5b',
                    pointBorderColor: '#0e1b15',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0e1b15',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + ' Pesanan Sewa';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        ticks: { precision: 0, font: { size: 10 }, color: '#a8a29e' },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        border: { display: false },
                        ticks: { font: { size: 10 }, color: '#a8a29e' },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
