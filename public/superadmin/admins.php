<?php
include_once("includes/auth.php");
include_once("includes/db.php");

// --- 1. Handle Role Management Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_role') {
    $target_id = (int)$_POST['user_id'];
    $current_role = (int)$_POST['current_role'];
    $new_role = ($current_role === 1) ? 0 : 1;

    // Security: Prevent self-demotion
    if ($target_id === $_SESSION['super_admin_id']) {
        $msg = "O'zingizning huquqingizni o'zgartira olmaysiz!";
        $msg_type = "danger";
    } else {
        $stmt = $master_link->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_role, $target_id);
        if ($stmt->execute()) {
            $msg = "Foydalanuvchi rolingiz muvaffaqiyatli yangilandi.";
            $msg_type = "success";
        } else {
            $msg = "Xatolik yuz berdi.";
            $msg_type = "danger";
        }
    }
}

include_once("includes/header.php");
include_once("includes/sidebar.php");

// Search & Filter - ONLY ADMINS (role = 1)
$search = $_GET['search'] ?? '';
$where_clause = "WHERE role = 1";
if (!empty($search)) {
    $s = $master_link->real_escape_string($search);
    $where_clause .= " AND (username LIKE '%$s%' OR email LIKE '%$s%')";
}

$all_admins = $master_link->query("SELECT * FROM users $where_clause ORDER BY created_at DESC");
?>

<main class="main-content">
    <header class="header">
        <div class="header-info">
            <h1>Tizim administratorlari</h1>
            <p style="color: var(--text-secondary);">Platformani to'liq boshqarish huquqiga ega foydalanuvchilar.</p>
        </div>
        
        <form action="" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" placeholder="Qidirish..." value="<?= htmlspecialchars($search) ?>" 
                   style="background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px 16px; border-radius: 12px; outline: none; width: 250px;">
            <button type="submit" class="btn-action" style="background: var(--accent-primary); border: none;">Qidirish</button>
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
            Faol administratorlar (<?= $all_admins->num_rows ?>)
            <div>
                <button class="btn-action"><i class="fas fa-key me-1"></i> Ruxsatlar</button>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Admin ma'lumotlari</th>
                    <th>Email</th>
                    <th>Ruxsat darajasi</th>
                    <th>Status</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php if($all_admins->num_rows > 0): ?>
                    <?php while($user = $all_admins->fetch_assoc()): ?>
                    <tr>
                        <td style="color: var(--text-secondary);">#<?= $user['id'] ?></td>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--accent-secondary);">@<?= htmlspecialchars($user['username']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                             <span style="font-size: 0.65rem; background: var(--accent-primary); color: white; padding: 2px 8px; border-radius: 4px; font-weight: 700; letter-spacing: 0.5px;">SUPER ADMIN</span>
                        </td>
                        <td>
                            <?php if($user['email_verified']): ?>
                                <span class="status-badge status-active">Tasdiqlangan</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">Kutilmoqda</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <form action="" method="POST" onsubmit="return confirm('Rostdan ham ushbu adminni oddiy foydalanuvchiga tushirmoqchimisiz?');" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_role">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="current_role" value="<?= $user['role'] ?>">
                                    <button type="submit" class="btn-action" style="color: var(--accent-secondary);" title="Huquqni cheklash">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </form>
                                
                                <button class="btn-action" title="Loglarni ko'rish"><i class="fas fa-history"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">Administratorlar topilmadi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

</div> <!-- End .admin-container -->
</body>
</html>
