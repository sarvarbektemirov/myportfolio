<?php
include_once("includes/auth.php");
include_once("includes/db.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

// 1. Data Aggregation Logic
$total_publications = 0;
$total_feedbacks = 0;
$total_messages = 0;

// Revenue Calculation
$p1 = getSetting($master_link, 'price_1_month', '49,000');
$p3 = getSetting($master_link, 'price_3_month', '129,000');
$p12 = getSetting($master_link, 'price_12_month', '399,000');
$p1_val = (int)str_replace([',', ' '], '', $p1);
$p3_val = (int)str_replace([',', ' '], '', $p3);
$p12_val = (int)str_replace([',', ' '], '', $p12);

$rev_res = $master_link->query("SELECT subscription_plan, COUNT(*) as c FROM users WHERE subscription_plan IN ('1', '3', '12') GROUP BY subscription_plan");
$total_revenue = 0;
while($r = $rev_res->fetch_assoc()) {
    if($r['subscription_plan'] == '1') $total_revenue += $r['c'] * $p1_val;
    if($r['subscription_plan'] == '3') $total_revenue += $r['c'] * $p3_val;
    if($r['subscription_plan'] == '12') $total_revenue += $r['c'] * $p12_val;
}

// 2. Growth Chart Data (Last 6 Months)
$months = [];
$counts = [];
$growth_res = $master_link->query("
    SELECT DATE_FORMAT(created_at, '%b') as month, COUNT(*) as cnt 
    FROM users 
    WHERE (role = 0 OR role IS NULL) 
    GROUP BY month 
    ORDER BY MIN(created_at) ASC 
    LIMIT 6
");
while($row = $growth_res->fetch_assoc()) {
    $months[] = $row['month'];
    $counts[] = $row['cnt'];
}

// If no data, provide defaults for labels
if(empty($months)) $months = ['No Data'];
if(empty($counts)) $counts = [0];
?>

<main class="main-content">
    <header class="header">
        <div class="header-info">
            <h1>Tizim analitikasi</h1>
            <p style="color: var(--text-secondary);">Platforma o'sishi va kontent taqsimoti haqida batafsil ma'lumotlar.</p>
        </div>
        
        <div style="display: flex; gap: 12px;">
            <button class="btn-action" onclick="window.print()">
                <i class="fas fa-download me-2"></i> Hisobotni yuklash
            </button>
            <button class="btn-action" style="background: var(--accent-primary); border: none; color: white;" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i> Yangilash
            </button>
        </div>
    </header>

    <div class="stats-grid" style="margin-bottom: 3rem;">

        <div class="stat-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.02)); border-color: rgba(16, 185, 129, 0.2);">
            <div class="stat-icon" style="background: var(--success); color: white;">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-value"><?= number_format($total_revenue) ?> <small style="font-size: 0.8rem; font-weight: 400;">so'm</small></div>
            <div class="stat-label">Taxminiy daromad</div>
        </div>
    </div>

    <div class="content-grid">
        <!-- New Users Over Time -->
        <div class="panel">
            <div class="panel-title">
                Portfoliolar ro'yxatdan o'tishi (O'sish)
                <span style="font-size: 0.75rem; font-weight: 500; color: var(--success);">+12% kutilmoqda</span>
            </div>
            <div style="height: 300px;">
                <canvas id="growthDetails"></canvas>
            </div>
        </div>


    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Theme aware colors helper
function getThemeColors() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    return {
        grid: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
        text: isDark ? '#94a3b8' : '#64748b',
        accent: '#8b5cf6',
        secondary: '#06b6d4',
        success: '#10b981'
    };
}

const colors = getThemeColors();

// Custom Plugin for Center Text in Doughnut
const centerTextPlugin = {
    id: 'centerText',
    afterDraw: (chart) => {
        if (chart.config.type === 'doughnut') {
            const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
            ctx.save();
            
            const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
            
            ctx.font = 'bold 2rem Inter';
            ctx.textAlign = 'center';
            ctx.fillStyle = document.documentElement.getAttribute('data-theme') === 'dark' ? '#ffffff' : '#1e293b';
            ctx.fillText(total.toLocaleString(), width / 2, height / 2 + top + 5);
            
            ctx.font = '500 0.8rem Inter';
            ctx.fillStyle = '#94a3b8';
            ctx.fillText('JAMI', width / 2, height / 2 + top - 25);
            ctx.restore();
        }
    }
};

// Line Chart
const ctxLine = document.getElementById('growthDetails').getContext('2d');
const gradient = ctxLine.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(6, 182, 212, 0.3)');
gradient.addColorStop(1, 'rgba(6, 182, 212, 0)');

let growthChart = new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Yangi portfoliolar',
            data: <?= json_encode($counts) ?>,
            borderColor: '#06b6d4',
            tension: 0.45,
            borderWidth: 4,
            pointRadius: 4,
            pointBackgroundColor: '#06b6d4',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            fill: true,
            backgroundColor: gradient
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 },
                displayColors: false
            }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                grid: { color: colors.grid, drawBorder: false }, 
                ticks: { color: colors.text, padding: 10 } 
            },
            x: { 
                grid: { display: false }, 
                ticks: { color: colors.text, padding: 10 } 
            }
        }
    }
});



// Theme change listener for charts
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
            location.reload(); // Simple way for analytics to ensure all colors refresh
        }
    });
});
observer.observe(document.documentElement, { attributes: true });
</script>

</div> <!-- End .admin-container -->
</body>
</html>
