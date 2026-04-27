<?php
include_once("includes/auth.php");
include_once("includes/db.php");

// --- 0. Handle Export Action ---
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    // Clear any previous output
    if (ob_get_level()) ob_end_clean();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="portfolios_export_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    // Add BOM for Excel UTF-8 support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header row
    fputcsv($output, ['ID', 'First Name', 'Last Name', 'Username', 'Email', 'Created At', 'Verified', 'Role']);
    
    $query = "SELECT id, firstname, lastname, username, email, created_at, email_verified, role FROM users ORDER BY id ASC";
    $res = $master_link->query($query);
    
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['firstname'],
            $row['lastname'],
            $row['username'],
            $row['email'],
            $row['created_at'],
            $row['email_verified'] ? 'Yes' : 'No',
            $row['role'] == 1 ? 'Super Admin' : 'User'
        ]);
    }
    fclose($output);
    exit;
}




// --- 2. Handle Portfolio Deletion (Move to Trash) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_portfolio') {
    $target_id = (int)$_POST['user_id'];
    
    // Security: Prevent deleting self
    if ($target_id === $_SESSION['super_admin_id']) {
        $msg = "O'zingizni tizimdan o'chira olmaysiz!";
        $msg_type = "danger";
    } else {
        $del_stmt = $master_link->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        $del_stmt->bind_param("i", $target_id);
        if ($del_stmt->execute()) {
            $msg = "Portfolio korzinkaga o'tkazildi. Uni 30 kun ichida qayta tiklash imkoniyati bor.";
            $msg_type = "warning";
        } else {
            $msg = "Xatolik yuz berdi: " . $master_link->error;
            $msg_type = "danger";
        }
    }
}

// --- 3. Handle Subscription Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_subscription') {
    $target_id = (int)$_POST['user_id'];
    $months = (int)$_POST['months'];
    
    // Fetch current expiry
    $u_res = $master_link->query("SELECT subscription_expires_at FROM users WHERE id = $target_id");
    if ($u_res && $u_data = $u_res->fetch_assoc()) {
        $current_expiry = strtotime($u_data['subscription_expires_at'] ?? 'now');
        if ($current_expiry < time()) $current_expiry = time();
        
        $new_expiry = date('Y-m-d H:i:s', strtotime("+$months months", $current_expiry));
        $plan_id = ($months == 1) ? '1' : (($months == 3) ? '3' : '12');

        $stmt = $master_link->prepare("UPDATE users SET subscription_plan = ?, subscription_expires_at = ? WHERE id = ?");
        $stmt->bind_param("ssi", $plan_id, $new_expiry, $target_id);
        
        if ($stmt->execute()) {
            $msg = "Foydalanuvchi obunasi $months oyga muvaffaqiyatli uzaytirildi.";
            $msg_type = "success";
        } else {
            $msg = "Xatolik yuz berdi: " . $master_link->error;
            $msg_type = "danger";
        }
    }
}

// --- 4. Handle Subscription Reset ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_subscription') {
    $target_id = (int)$_POST['user_id'];
    $stmt = $master_link->prepare("UPDATE users SET subscription_plan = 'free', subscription_expires_at = NULL WHERE id = ?");
    $stmt->bind_param("i", $target_id);
    if ($stmt->execute()) {
        $msg = "Foydalanuvchi obunasi bekor qilindi va sinov muddatiga qaytarildi.";
        $msg_type = "warning";
    } else {
        $msg = "Xatolik yuz berdi.";
        $msg_type = "danger";
    }
}

include_once("includes/header.php");
include_once("includes/sidebar.php");

// Search & Filter
$search = $_GET['search'] ?? '';
$where_clause = "WHERE (role = 0 OR role IS NULL) AND deleted_at IS NULL";
if (!empty($search)) {
    $s = $master_link->real_escape_string($search);
    $where_clause .= " AND (username LIKE '%$s%' OR email LIKE '%$s%')";
}

$all_users = $master_link->query("SELECT * FROM users $where_clause ORDER BY created_at DESC");
?>

<main class="main-content">
    <header class="header">
        <div class="header-info">
            <h1>Portfolio Management</h1>
            <p style="color: var(--text-secondary);">Manage all registered tenant portfolios and their databases.</p>
        </div>
        
        <form action="" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" placeholder="Search portfolios..." value="<?= htmlspecialchars($search) ?>" 
                   style="background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px 16px; border-radius: 12px; outline: none; width: 250px;">
            <button type="submit" class="btn-action" style="background: var(--accent-primary); border: none;">Search</button>
        </form>
    </header>

    <!-- Alert Message -->
    <?php if(isset($msg)): ?>
        <div class="alert alert-<?= $msg_type ?> animatsiya1" style="padding: 1rem; border-radius: 12px; margin-bottom: 2rem; background: rgba(<?= $msg_type === 'success' ? '16, 185, 129' : '239, 68, 68' ?>, 0.1); color: var(--<?= $msg_type === 'success' ? 'success' : 'danger' ?>); border: 1px solid rgba(<?= $msg_type === 'success' ? '16, 185, 129' : '239, 68, 68' ?>, 0.2);">
            <i class="fas fa-<?= $msg_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <div class="panel-title">
            All Portfolios (<?= $all_users->num_rows ?>)
            <div>
                <a href="?action=export_csv" class="btn-action" style="text-decoration: none;"><i class="fas fa-download me-1"></i> Export</a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foydalanuvchi</th>
                    <th>Email</th>
                    <th>Obuna</th>
                    <th>Ro'yxatdan o'tdi</th>
                    <th>Holat</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php if($all_users->num_rows > 0): ?>
                    <?php while($user = $all_users->fetch_assoc()): ?>
                    <tr>
                        <td style="color: var(--text-secondary);">#<?= $user['id'] ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
                                    <div style="font-size: 0.8rem; color: var(--accent-secondary);">@<?= htmlspecialchars($user['username']) ?></div>
                                </div>
                                <?php if($user['role'] == 1): ?>
                                    <span style="font-size: 0.65rem; background: var(--accent-primary); color: white; padding: 2px 8px; border-radius: 4px; font-weight: 700; letter-spacing: 0.5px;">SUPER ADMIN</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php 
                            $expiry = strtotime($user['subscription_expires_at'] ?? '');
                            if ($expiry && $expiry > time()) {
                                $days = ceil(($expiry - time()) / 86400);
                                echo '<div style="font-weight: 600; color: var(--success);">' . $days . ' kun qoldi</div>';
                                echo '<div style="font-size: 0.75rem; color: var(--text-secondary);">' . date('d.m.Y', $expiry) . '</div>';
                            } else {
                                echo '<span style="color: var(--danger); font-size: 0.85rem;">Muddati tugagan</span>';
                            }
                            ?>
                        </td>
                        <td style="color: var(--text-secondary);"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if($user['email_verified']): ?>
                                <span class="status-badge status-active">Verified</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="../en/home.php?u=<?= $user['username'] ?>" target="_blank" class="btn-action" title="View Portfolio"><i class="fas fa-external-link-alt"></i></a>
                                
                                <!-- Subscription Update Action -->
                                <div style="position: relative; display: inline-block;">
                                    <button class="btn-action" onclick="this.nextElementSibling.style.display = (this.nextElementSibling.style.display === 'block' ? 'none' : 'block')" title="Obunani uzaytirish">
                                        <i class="fas fa-calendar-plus"></i>
                                    </button>
                                    <div class="sub-menu" style="display:none; position: absolute; top: 100%; right: 0; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 15px; z-index: 100; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 200px;">
                                        <h6 style="margin-bottom: 12px; font-size: 0.8rem; font-weight: 700; color: var(--accent-primary);">UZAYTIRISH</h6>
                                        <form action="" method="POST" style="display: flex; flex-direction: column; gap: 8px;" onsubmit="return confirm('Rostdan ham ushbu foydalanuvchi obunasini uzaytirmoqchimisiz?');">
                                            <input type="hidden" name="action" value="update_subscription">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" name="months" value="1" class="btn-action" style="width: 100%; justify-content: start; background: rgba(255,255,255,0.05);">+ 1 Oy (<?= getSetting($master_link, 'price_1_month', '49,000') ?>)</button>
                                            <button type="submit" name="months" value="3" class="btn-action" style="width: 100%; justify-content: start; background: rgba(255,255,255,0.05);">+ 3 Oy (<?= getSetting($master_link, 'price_3_month', '129,000') ?>)</button>
                                            <button type="submit" name="months" value="12" class="btn-action" style="width: 100%; justify-content: start; background: rgba(255,255,255,0.05);">+ 1 Yil (<?= getSetting($master_link, 'price_12_month', '399,000') ?>)</button>
                                        </form>
                                        
                                        <div style="border-top: 1px solid var(--border-color); margin: 8px 0;"></div>
                                        
                                        <form action="" method="POST" onsubmit="return confirm('Rostdan ham ushbu foydalanuvchi obunasini BEKOR qilmoqchimisiz?');">
                                            <input type="hidden" name="action" value="reset_subscription">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn-action" style="width: 100%; justify-content: start; color: var(--danger); background: rgba(239,68,68,0.05);">
                                                <i class="fas fa-undo-alt me-2"></i> Bekor qilish
                                            </button>
                                        </form>
                                    </div>
                                </div>



                                <!-- Move to Trash Action -->
                                <form action="" method="POST" onsubmit="return confirm('Ushbu portfolioni korzinkaga o\'tkazmoqchimisiz?');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_portfolio">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn-action" style="color: var(--danger);" title="Korzinkaga o'tkazish">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">No portfolios found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

</div> <!-- End .admin-container -->
</body>
</html>
