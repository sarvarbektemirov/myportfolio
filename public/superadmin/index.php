<?php
include_once("includes/auth.php");
include_once("includes/db.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$total_users = getTotalUsers($master_link);
$recent_users = getRecentUsers($master_link, 5);
?>

<main class="main-content">
    <header class="header">
        <div class="header-info">
            <h1>Bosh sahifa</h1>
            <p style="color: var(--text-secondary);">Platformaning umumiy holati va statistikasi.</p>
        </div>
        
        <div class="header-right" style="display: flex; align-items: center; gap: 20px;">
            <div class="header-actions">
                <button class="btn-action" onclick="location.href='analytics.php'">
                    <i class="fas fa-chart-pie"></i> Analitika
                </button>
                <button class="btn-action" style="background: var(--accent-primary); border: none;" onclick="location.href='users.php'">
                    <i class="fas fa-plus"></i> Yangi portfolio
                </button>
            </div>
            
            <div class="user-profile">
                <div class="avatar"></div>
                <div class="user-meta-info" style="display: flex; flex-direction: column;">
                    <span style="font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($_SESSION['super_admin_name'] ?? 'Super Admin') ?></span>
                    <span style="font-size: 0.7rem; color: var(--text-secondary);">Onlayn</span>
                </div>
            </div>
        </div>
    </header>

    <div class="stats-grid">
        <div class="stat-card animatsiya1">
            <div class="stat-icon icon-purple"><i class="fas fa-briefcase"></i></div>
            <div class="stat-value"><?= number_format($total_users) ?></div>
            <div class="stat-label">Jami portfoliolar</div>
            <div class="stat-change growth"><i class="fas fa-arrow-up"></i> +12% o'sish</div>
            <i class="fas fa-briefcase card-icon"></i>
        </div>
        <div class="stat-card animatsiya1" style="animation-delay: 0.1s;">
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?= number_format($total_users > 0 ? floor($total_users * 0.8) : 0) ?></div>
            <div class="stat-label">Faol foydalanuvchilar</div>
            <div class="stat-change growth"><i class="fas fa-arrow-up"></i> +5% o'sish</div>
            <i class="fas fa-users card-icon"></i>
        </div>
        <div class="stat-card animatsiya1" style="animation-delay: 0.2s;">
            <div class="stat-icon icon-green"><i class="fas fa-server"></i></div>
            <div class="stat-value">99.9%</div>
            <div class="stat-label">Tizim holati</div>
            <div class="stat-change growth"><i class="fas fa-check"></i> Yaxshi</div>
            <i class="fas fa-server card-icon"></i>
        </div>
        <div class="stat-card animatsiya1" style="animation-delay: 0.3s;">
            <div class="stat-icon icon-red"><i class="fas fa-hdd"></i></div>
            <div class="stat-value">1.2 GB</div>
            <div class="stat-label">Fayllar hajmi</div>
            <div class="stat-change danger"><i class="fas fa-warning"></i> 75% to'lgan</div>
            <i class="fas fa-hdd card-icon"></i>
        </div>
    </div>

    <div class="content-grid">
        <div class="panel animatsiya1" style="animation-delay: 0.4s;">
            <div class="panel-title">Ro'yxatdan o'tish dinamikasi <i class="fas fa-chart-line opacity-50"></i></div>
            <div class="chart-container">
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        <div class="panel animatsiya1" style="animation-delay: 0.5s;">
            <div class="panel-title">Oxirgi foydalanuvchilar <i class="fas fa-users-cog opacity-50"></i></div>
            <div style="margin-top: 1rem;">
                <?php while($user = $recent_users->fetch_assoc()): ?>
                <div class="user-item">
                    <div class="user-avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                    <div class="user-info">
                        <div class="user-name">@<?= htmlspecialchars($user['username']) ?></div>
                        <div class="user-meta"><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></div>
                    </div>
                    <span class="status-badge status-active">Faol</span>
                </div>
                <?php endwhile; ?>
            </div>
            <a href="users.php" style="display: block; text-align: center; margin-top: 1.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                Barcha portfoliolarni ko'rish <i class="fas fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
            </a>
        </div>
    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function getChartColors() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            grid: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
            text: isDark ? '#94a3b8' : '#334155'
        };
    }

    const ctx = document.getElementById('growthChart').getContext('2d');
    let colors = getChartColors();
    
    let growthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Users',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#8b5cf6',
                pointBorderColor: '#fff',
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: 'Inter' },
                    bodyFont: { family: 'Inter' },
                    padding: 12,
                    cornerRadius: 10
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: colors.grid },
                    ticks: { color: colors.text, font: { family: 'Inter' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: colors.text, font: { family: 'Inter' } }
                }
            }
        }
    });

    // Theme change listener for chart
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                const newColors = getChartColors();
                growthChart.options.scales.y.grid.color = newColors.grid;
                growthChart.options.scales.y.ticks.color = newColors.text;
                growthChart.options.scales.x.ticks.color = newColors.text;
                growthChart.update();
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
</script>

</div> <!-- End .admin-container -->
</body>
</html>
