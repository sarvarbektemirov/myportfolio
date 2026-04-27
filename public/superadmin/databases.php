<?php
include_once("includes/auth.php");
include_once("includes/db.php");

$msg = "";
$msg_type = "success";

// --- 1. Handle Database Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $target_db = $_POST['db_name'];
    
    // Only process if it's a portfolio database
    if (strpos($target_db, 'portfolio_') === 0) {
        $conn = new mysqli('mysql-8.4', 'root', '', $target_db);
        
        if ($conn->connect_error) {
            $msg = "Xatolik: Baza bilan ulanib bo'lmadi.";
            $msg_type = "danger";
        } else {
            if ($action === 'optimize') {
                $tables = $conn->query("SHOW TABLES");
                while ($t = $tables->fetch_array()) {
                    $conn->query("OPTIMIZE TABLE `{$t[0]}`");
                }
                $msg = "<b>$target_db</b> muvaffaqiyatli optimizatsiya qilindi.";
            } 
            elseif ($action === 'verify') {
                $tables = $conn->query("SHOW TABLES");
                $errors = [];
                while ($t = $tables->fetch_array()) {
                    $check = $conn->query("CHECK TABLE `{$t[0]}`");
                    $row = $check->fetch_assoc();
                    if ($row['Msg_type'] === 'error') $errors[] = $t[0];
                }
                if (empty($errors)) {
                    $msg = "<b>$target_db</b> butunligi tekshirildi: Hammasi joyida!";
                } else {
                    $msg = "Diqqat! Quyidagi jadvallarda xatolik topildi: " . implode(', ', $errors);
                    $msg_type = "danger";
                }
            }
            elseif ($action === 'backup') {
                // Simplified SQL Export
                $tables = $conn->query("SHOW TABLES");
                $output = "-- MyPortfolio SaaS Backup\n";
                $output .= "-- Database: $target_db\n";
                $output .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
                
                while ($table_row = $tables->fetch_array()) {
                    $table = $table_row[0];
                    $res = $conn->query("SHOW CREATE TABLE `$table`");
                    $create_row = $res->fetch_assoc();
                    $output .= $create_row['Create Table'] . ";\n\n";
                    
                    $data = $conn->query("SELECT * FROM `$table`");
                    while ($row = $data->fetch_assoc()) {
                        $values = array_map(fn($v) => "'" . $conn->real_escape_string($v) . "'", array_values($row));
                        $output .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $output .= "\n";
                }
                
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $target_db . '_backup_' . date('Ymd_His') . '.sql"');
                echo $output;
                exit;
            }
            $conn->close();
        }
    }
}

include_once("includes/header.php");
include_once("includes/sidebar.php");

// 1. Get all portfolio databases
$db_res = $master_link->query("SHOW DATABASES LIKE 'portfolio_%'");
$all_dbs = [];
while ($row = $db_res->fetch_array()) {
    $all_dbs[] = $row[0];
}

// 2. Count tables for each DB
$db_stats = [];
foreach ($all_dbs as $db_name) {
    $temp_link = new mysqli('mysql-8.4', 'root', '', $db_name);
    if (!$temp_link->connect_errno) {
        $table_res = $temp_link->query("SHOW TABLES");
        $db_stats[$db_name] = [
            'tables' => $table_res->num_rows,
            'status' => 'Healthy'
        ];
        $temp_link->close();
    } else {
        $db_stats[$db_name] = [
            'tables' => 0,
            'status' => 'Error'
        ];
    }
}
?>

<main class="main-content">
    <header class="header">
        <div class="header-info">
            <h1>Ma'lumotlar bazasi boshqaruvi</h1>
            <p style="color: var(--text-secondary);">Tizimdagi barcha mijozlar bazalari holati va tahlili.</p>
        </div>
        
        <div>
            <button class="btn-action" style="background: var(--accent-primary); border: none;">
                <i class="fas fa-sync-alt me-1"></i> Global sxemani sinxronlash
            </button>
        </div>
    </header>

    <!-- Alert Message -->
    <?php if(!empty($msg)): ?>
        <div class="alert alert-<?= $msg_type ?> animatsiya1" style="padding: 1rem; border-radius: 12px; margin-bottom: 2rem; background: rgba(<?= $msg_type === 'success' ? '16, 185, 129' : '239, 68, 68' ?>, 0.1); color: var(--<?= $msg_type === 'success' ? 'success' : 'danger' ?>); border: 1px solid rgba(<?= $msg_type === 'success' ? '16, 185, 129' : '239, 68, 68' ?>, 0.2);">
            <i class="fas fa-<?= $msg_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue">
                <i class="fas fa-database"></i>
            </div>
            <div class="stat-value"><?= count($all_dbs) ?></div>
            <div class="stat-label">Jami bazalar</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon icon-green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value"><?= count(array_filter($db_stats, fn($d) => $d['status'] == 'Healthy')) ?></div>
            <div class="stat-label">Holati yaxshi</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon icon-red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-value"><?= count(array_filter($db_stats, fn($d) => $d['status'] == 'Error')) ?></div>
            <div class="stat-label">Xatoliklar topildi</div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-title">Ma'lumotlar bazasi tugunlari</div>
        <table>
            <thead>
                <tr>
                    <th>Baza nomi</th>
                    <th>Mijoz</th>
                    <th>Jadvallar</th>
                    <th>Umumiy holat</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_dbs as $db_name): ?>
                <?php $username = str_replace('portfolio_', '', $db_name); ?>
                <tr>
                    <td>
                        <span style="font-weight: 500; font-family: monospace; color: var(--accent-secondary);"><?= $db_name ?></span>
                    </td>
                    <td>
                        <div style="font-weight: 600; font-size: 0.9rem;">@<?= htmlspecialchars($username) ?></div>
                    </td>
                    <td><?= $db_stats[$db_name]['tables'] ?> jadvallar</td>
                    <td>
                        <?php if($db_stats[$db_name]['status'] == 'Healthy'): ?>
                            <span class="status-badge status-active">Online</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">Ulanishda xato</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="optimize">
                                <input type="hidden" name="db_name" value="<?= $db_name ?>">
                                <button type="submit" class="btn-action" title="Optimizatsiya qilish"><i class="fas fa-magic"></i></button>
                            </form>
                            
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="verify">
                                <input type="hidden" name="db_name" value="<?= $db_name ?>">
                                <button type="submit" class="btn-action" title="Butunligini tekshirish"><i class="fas fa-shield-virus"></i></button>
                            </form>
                            
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="backup">
                                <input type="hidden" name="db_name" value="<?= $db_name ?>">
                                <button type="submit" class="btn-action" style="color: var(--accent-secondary);" title="Zaxira nusxa (SQL) yuklash"><i class="fas fa-download"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

</div> <!-- End .admin-container -->
</body>
</html>

</div> <!-- End .admin-container -->
</body>
</html>
