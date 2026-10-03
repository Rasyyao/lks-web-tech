/**
 * Chart.js Doughnut Chart Helper for PintarMenabung
 * Digunakan pada halaman Detail Wallet (/wallets/:walletId)
 * Menampilkan ringkasan pengeluaran & pemasukan per bulan
 */

let doughnutChartInstance = null;

function renderExpenseDoughnutChart(canvasElementId, summaryData) {
    const ctx = document.getElementById(canvasElementId);
    if (!ctx) return;

    if (doughnutChartInstance) {
        doughnutChartInstance.destroy();
    }

    const labels = summaryData.map(item => item.category.name);
    const data = summaryData.map(item => item.amount);
    const backgroundColors = summaryData.map(item => item.category.color || '#6c757d');

    doughnutChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColors,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 14,
                        padding: 12,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const val = context.parsed;
                            return ` ${context.label}: IDR ${new Intl.NumberFormat('id-ID').format(val)}`;
                        }
                    }
                }
            }
        }
    });

    return doughnutChartInstance;
}
