<?php
include_once("includes/auth.php");
include_once("includes/db.php");

// --- 1. Handle Restore Action ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'restore_user') {
    $target_id = (int)$_POST['user_id'];
    $stmt = $master_link->prepare("UPDATE users SET deleted_at = NULL WHERE id = ?");
    $stmt->bind_param("i", $target_id);
    if ($stmt->execute()) {
        $msg = "Foydalanuvchi muvaffaqiyatli tiklandi.";
        $msg_type = "success";
    } else {
        $msg = "Xatolik yuz berdi.";
        $msg_type = "danger";
    }
}

// --- 2. Handle Permanent Delete Action ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'permanent_delete') {
    $target_id = (int)$_POST['user_id'];
    $target_username = $_POST['username'];
    
    // Step A: Drop the tenant database
    $db_name = "portfolio_" . $target_username;
    $drop_sql = "DROP DATABASE IF EXISTS `$db_name`";
    
    if ($master_link->query($drop_sql)) {
        // Step B: Delete from users table
        $del_stmt = $master_link->prepare("DELETE FROM users WHERE id = ?");
        $del_stmt->bind_param("i", $target_id);
        if ($del_stmt->execute()) {
            $msg = "Portfolio va unga tegishli '$db_name' bazasi butunlay o'chirildi.";
            $msg_type = "success";
        } else {
            $msg = "Xatolik yuz berdi: " . $master_link->error;
            $msg_type = "danger";
        }
    }
}

// --- 3. Auto-Cleanup Logic (Delete items older than 30 days) ---
$cleanup_sql = "SELECT id, username FROM users WHERE deleted_at IS NOT NULL AND deleted_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
$to_cleanup = $master_link->query($cleanup_sql);
while ($row = $to_cleanup->fetch_assoc()) {
    $tid = $row['id'];
    $tuser = $row['username'];
    $tdb = "portfolio_" . $tuser;
    $master_link->query("DROP DATABASE IF EXISTS `$tdb`");
    $master_link->query("DELETE FROM users WHERE id = $tid");
}

include_once("includes/header.php");
include_once("includes/sidebar.php");

$deleted_users = $master_link->query("SELECT * FROM users WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
?>

<main class="main-content">
    <header class="header">
        <div class="header-info">
            <h1>Korzinka</h1>
            <p style="color: var(--text-secondary);">O'chirilgan portfoliolar ro'yxati. Bu yerdagi ma'lumotlar 30 kundan keyin butunlay o'chib ketadi.</p>
        </div>
    </header>

    <!-- Alert Message -->
    <?php if(isset($msg)): ?>
        <div class="alert alert-<?= $msg_type ?> animatsiya1" style="padding: 1rem; border-radius: 12px; margin-bottom: 2rem; background: rgba(<?= $msg_type === 'success' ? '16, 185, 129' : '239, 68, 68' ?>, 0.1); color: var(--<?= $msg_type === 'success' ? 'success' : 'danger' ?>); border: 1px solid rgba(<?= $msg_type === 'success' ? '16, 185, 129' : '239, 68, 68' ?>, 0.2);">
            <i class="fas fa-<?= $msg_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <div class="panel-title">
            O'chirilganlar (<?= $deleted_users->num_rows ?>)
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foydalanuvchi</th>
                    <th>Email</th>
                    <th>O'chirilgan vaqt</th>
                    <th>Qolgan muddat</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php if($deleted_users->num_rows > 0): ?>
                    <?php while($user = $deleted_users->fetch_assoc()): ?>
                    <tr>
                        <td style="color: var(--text-secondary);">#<?= $user['id'] ?></td>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--accent-secondary);">@<?= htmlspecialchars($user['username']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($user['deleted_at'])) ?></td>
                        <td>
                            <?php 
                            $deleted_time = strtotime($user['deleted_at']);
                            $expiry_time = strtotime("+30 days", $deleted_time);
                            $remaining_days = ceil(($expiry_time - time()) / 86400);
                            
                            if ($remaining_days > 0) {
                                echo '<span class="status-badge status-pending">' . $remaining_days . ' kun</span>';
                            } else {
                                echo '<span class="status-badge status-danger">Yaqinda o\'chadi</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <!-- Restore Action -->
                                <form action="" method="POST" onsubmit="return confirm('Ushbu foydalanuvchini tiklamoqchimisiz?');">
                                    <input type="hidden" name="action" value="restore_user">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn-action" style="color: var(--success);" title="Tiklash">
                                        <i class="fas fa-undo"></i> Tiklash
                                    </button>
                                </form>

                                <!-- Permanent Delete Action -->
                                <form action="" method="POST" onsubmit="return confirm('DIQQAT! Ushbu portfolioni BUTUNLAY o\'chirib tashlamoqchimisiz? Bu amalni ortga qaytarib bo\'lmaydi!');">
                                    <input type="hidden" name="action" value="permanent_delete">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="username" value="<?= $user['username'] ?>">
                                    <button type="submit" class="btn-action" style="color: var(--danger);" title="Butunlay o'chirish">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">Korzinka bo'sh.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

</div> <!-- End .admin-container -->
</body>
</html>
