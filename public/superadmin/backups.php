<?php
include_once('includes/db.php');

// Secret token for Cron Jobs (server-side automation)
define('BACKUP_TOKEN', 'portfolio_secret_777'); 

// Check if it's a cron job with a valid token OR a logged-in super admin
$is_cron = (isset($_GET['token']) && $_GET['token'] === BACKUP_TOKEN);

if (!$is_cron) {
    include_once("includes/auth.php");
}

$backup_dir = __DIR__ . '/backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Handle Manual Backup Trigger
if (isset($_GET['action']) && $_GET['action'] === 'run') {
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql.gz';
    $filepath = $backup_dir . $filename;
    
    $zp = gzopen($filepath, "w9");
    
    // 1. Get all databases to backup
    $dbs = [];
    $res = $master_link->query("SHOW DATABASES LIKE 'portfolio%'");
    while($row = $res->fetch_row()) {
        $dbs[] = $row[0];
    }
    
    foreach($dbs as $db) {
        gzwrite($zp, "\n-- DATABASE: $db\n");
        gzwrite($zp, "CREATE DATABASE IF NOT EXISTS `$db`;\n");
        gzwrite($zp, "USE `$db`;\n\n");
        
        $db_link = new mysqli('mysql-8.4', 'root', '', $db);
        if ($db_link->connect_error) continue;
        
        $tables = [];
        $t_res = $db_link->query("SHOW TABLES");
        while($t_row = $t_res->fetch_row()) {
            $tables[] = $t_row[0];
        }
        
        foreach($tables as $table) {
            gzwrite($zp, "DROP TABLE IF EXISTS `$table`;\n");
            $create = $db_link->query("SHOW CREATE TABLE `$table`")->fetch_row();
            gzwrite($zp, $create[1] . ";\n\n");
            
            $rows = $db_link->query("SELECT * FROM `$table`");
            while($data = $rows->fetch_assoc()) {
                $keys = array_keys($data);
                $vals = array_map(function($v) use ($db_link) {
                    if ($v === null) return "NULL";
                    return "'" . $db_link->real_escape_string($v) . "'";
                }, array_values($data));
                gzwrite($zp, "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $vals) . ");\n");
            }
            gzwrite($zp, "\n");
        }
        $db_link->close();
    }
    gzclose($zp);
    header("Location: backups.php?success=1");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $file = basename($_GET['delete']);
    if (file_exists($backup_dir . $file)) {
        unlink($backup_dir . $file);
    }
    header("Location: backups.php");
    exit;
}

// Get Backup List
$files = glob($backup_dir . "*.sql.gz");
array_multisort(array_map('filemtime', $files), SORT_DESC, $files);

// Calculate Stats
$total_size = 0;
foreach($files as $f) $total_size += filesize($f);
$last_backup = !empty($files) ? date('d.m.Y H:i', filemtime($files[0])) : 'Mavjud emas';

include_once('includes/header.php');
include_once('includes/sidebar.php');
?>

<main class="main-content">
    <header class="header animatsiya1">
        <div class="header-info">
            <h1><i class="fa-solid fa-cloud-arrow-down me-2 text-primary"></i> Zaxira Nusxalari</h1>
            <p style="color: var(--text-secondary);">Tizim ma'lumotlar bazasini to'liq arxivlash va tiklash markazi.</p>
        </div>
        
        <div class="header-right">
            <a href="backups.php?action=run" class="btn-premium">
                <i class="fa-solid fa-plus"></i> Yangi zaxira yaratish
            </a>
        </div>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-premium animatsiya1">
            <div class="alert-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="alert-text">Zaxira nusxasi muvaffaqiyatli yaratildi va siqildi (.gz formatida).</div>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-row mb-4">
        <div class="mini-stat-card animatsiya1" style="animation-delay: 0.1s;">
            <div class="mini-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;"><i class="fa-solid fa-database"></i></div>
            <div>
                <div class="mini-label">Jami fayllar</div>
                <div class="mini-value"><?= count($files) ?> ta</div>
            </div>
        </div>
        <div class="mini-stat-card animatsiya1" style="animation-delay: 0.2s;">
            <div class="mini-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;"><i class="fa-solid fa-weight-hanging"></i></div>
            <div>
                <div class="mini-label">Umumiy hajm</div>
                <div class="mini-value"><?= round($total_size / 1024 / 1024, 2) ?> MB</div>
            </div>
        </div>
        <div class="mini-stat-card animatsiya1" style="animation-delay: 0.3s;">
            <div class="mini-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div>
                <div class="mini-label">Oxirgi zaxira</div>
                <div class="mini-value"><?= $last_backup ?></div>
            </div>
        </div>
    </div>

    <div class="backup-grid animatsiya1" style="animation-delay: 0.4s;">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="fa-solid fa-list-ul me-2"></i> Mavjud zaxira fayllari</div>
            </div>
            
            <div class="table-responsive mt-3">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Fayl nomi</th>
                            <th>Yaratilgan sana</th>
                            <th>Hajmi</th>
                            <th class="text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($files)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
                                        <p>Hali hech qanday zaxira nusxasi olinmagan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($files as $file): 
                                $name = basename($file);
                                $size = round(filesize($file) / 1024 / 1024, 2);
                                $time = date('d.m.Y H:i:s', filemtime($file));
                            ?>
                                <tr>
                                    <td>
                                        <div class="file-name">
                                            <div class="file-icon"><i class="fa-solid fa-file-zipper"></i></div>
                                            <span><?= $name ?></span>
                                        </div>
                                    </td>
                                    <td><span class="text-muted small"><?= $time ?></span></td>
                                    <td><span class="size-badge"><?= $size ?> MB</span></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="backups/<?= $name ?>" class="btn-action-icon btn-download" download title="Yuklab olish">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                            <a href="backups.php?delete=<?= $name ?>" class="btn-action-icon btn-delete" onclick="return confirm('O\'chirib tashlansinmi?')" title="O'chirish">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="info-card">
            <div class="info-header">
                <i class="fa-solid fa-shield-halved"></i>
                <h5>Xavfsizlik tizimi</h5>
            </div>
            <p>Ushbu zaxira nusxalari tizimdagi **barcha** (master va user) ma'lumotlar bazalarini o'z ichiga oladi. Biror hujum yoki xatolik yuz berganda, eng oxirgi zaxira nusxasini yuklab olib, MySQL serverga qayta import qilish orqali tizimni to'liq tiklash mumkin.</p>
            <div class="info-footer">
                <i class="fa-solid fa-circle-info me-1"></i> Tavsiya: Zaxira nusxasini haftada bir marta o'z kompyuteringizga yuklab oling.
            </div>
        </div>
    </div>
</main>

<style>
.main-content { padding: 2.5rem; margin-left: 260px; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; }
.header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }

.btn-premium {
    background: linear-gradient(135deg, var(--accent-primary), #6366f1);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: 0.3s;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
}
.btn-premium:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4); color: white; }

.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 2rem; margin-bottom: 3rem; }
.mini-stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.mini-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
.mini-label { font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
.mini-value { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); }

.backup-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; align-items: start; margin-bottom: 3rem; }
.panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 1.5rem; }
.panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.panel-title { font-weight: 700; font-size: 1.1rem; color: var(--text-primary); }

.modern-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
.modern-table th { padding: 12px; font-size: 0.85rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; }
.modern-table td { padding: 16px 12px; background: rgba(255, 255, 255, 0.02); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.modern-table td:first-child { border-left: 1px solid var(--border); border-radius: 12px 0 0 12px; }
.modern-table td:last-child { border-right: 1px solid var(--border); border-radius: 0 12px 12px 0; }

.file-name { display: flex; align-items: center; gap: 12px; font-weight: 600; font-size: 0.95rem; }
.file-icon { width: 36px; height: 36px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.size-badge { 
    background: rgba(59, 130, 246, 0.1); 
    color: #3b82f6; 
    padding: 6px 14px; 
    border-radius: 20px; 
    font-size: 0.8rem; 
    font-weight: 700; 
    white-space: nowrap;
    display: inline-block;
}

.action-btns { display: flex; justify-content: flex-end; gap: 12px; }
.btn-action-icon {
    width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    text-decoration: none; transition: 0.2s; border: 1px solid var(--border);
}
.btn-download { color: #10b981; background: rgba(16, 185, 129, 0.05); }
.btn-download:hover { background: #10b981; color: white; transform: translateY(-2px); }
.btn-delete { color: #ef4444; background: rgba(239, 68, 68, 0.05); }
.btn-delete:hover { background: #ef4444; color: white; transform: translateY(-2px); }

.info-card { background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 20px; padding: 2rem; color: white; border: 1px solid rgba(255,255,255,0.1); }
.info-header { display: flex; align-items: center; gap: 12px; margin-bottom: 1rem; }
.info-header i { font-size: 1.5rem; color: #3b82f6; }
.info-header h5 { margin: 0; font-weight: 700; }
.info-card p { font-size: 0.9rem; line-height: 1.6; color: #94a3b8; margin-bottom: 1.5rem; }
.info-footer { font-size: 0.75rem; color: #6366f1; font-weight: 600; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); }

.alert-premium {
    background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 16px; padding: 1rem 1.5rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;
}
.alert-icon { font-size: 1.5rem; color: #10b981; }
.alert-text { font-weight: 600; color: #10b981; }

.empty-state { padding: 3rem 0; opacity: 0.5; }
.empty-icon { font-size: 3rem; margin-bottom: 1rem; }

@media (max-width: 1200px) { .backup-grid { grid-template-columns: 1fr; } }
@media (max-width: 991px) { .main-content { margin-left: 0; } }
</style>

</div> <!-- End .admin-container -->
</body>
</html>
