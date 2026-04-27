<?php
/**
 * Super Admin Master DB Connection
 */
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Master Connection (portfolio database)
$master_link = new mysqli('mysql-8.4', 'root', '', 'portfolio');

if ($master_link->connect_error) {
    die("Master DB ulanishda xato: " . $master_link->connect_error);
}

// --- FORCED LOGOUT CHECK ---
if (isset($_SESSION['id']) && !isset($_SESSION['super_admin_id'])) {
    $r_res = $master_link->query("SELECT setting_value FROM system_settings WHERE setting_key = 'last_session_reset'");
    $last_reset = ($r_res && $row = $r_res->fetch_assoc()) ? (int)$row['setting_value'] : 0;
    
    if ($last_reset > 0 && (!isset($_SESSION['auth_time']) || $_SESSION['auth_time'] < $last_reset)) {
        session_destroy();
        header("Location: login.php?reason=session_reset");
        exit;
    }
}

// --- SCHEMA UPDATE: Add deleted_at if not exists (Version compatible check) ---
$check_col = $master_link->query("SHOW COLUMNS FROM users LIKE 'deleted_at'");
if ($check_col && $check_col->num_rows === 0) {
    $master_link->query("ALTER TABLE users ADD deleted_at DATETIME DEFAULT NULL");
}

// Add views column for portfolio analytics
$check_views = $master_link->query("SHOW COLUMNS FROM users LIKE 'views'");
if ($check_views && $check_views->num_rows === 0) {
    $master_link->query("ALTER TABLE users ADD views INT DEFAULT 0");
}

// Add last_portfolio_update column
$check_update = $master_link->query("SHOW COLUMNS FROM users LIKE 'last_portfolio_update'");
if ($check_update && $check_update->num_rows === 0) {
    $master_link->query("ALTER TABLE users ADD last_portfolio_update DATETIME DEFAULT CURRENT_TIMESTAMP");
}

// --- SCHEMA UPDATE: Create system_messages table ---
$master_link->query("CREATE TABLE IF NOT EXISTS system_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sender_role TINYINT(1) DEFAULT 0,
    subject VARCHAR(255) DEFAULT '',
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Ensure sender_role exists if table already existed
$check_role = $master_link->query("SHOW COLUMNS FROM system_messages LIKE 'sender_role'");
if ($check_role && $check_role->num_rows === 0) {
    $master_link->query("ALTER TABLE system_messages ADD sender_role TINYINT(1) DEFAULT 0 AFTER user_id");
}

// --- GLOBAL MAINTENANCE MODE CHECK ---
$m_res = $master_link->query("SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'");
$m_mode = ($m_res && $row = $m_res->fetch_assoc()) ? $row['setting_value'] : '0';

if ($m_mode === '1' && !isset($_SESSION['super_admin_id'])) {
    // Do not block the SuperAdmin login and panel itself
    $current_path = $_SERVER['PHP_SELF'];
    if (strpos($current_path, '/superadmin/') === false) {
        ?>
        <!DOCTYPE html>
        <html lang="uz">
        <head>
            <meta charset="UTF-8">
            <title>Texnik ishlar | Platform</title>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
            <style>
                :root { --bg: #030509; --primary: #8b5cf6; --secondary: #06b6d4; }
                body { margin: 0; padding: 0; background-color: var(--bg); color: white; font-family: 'Outfit', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; text-align: center; }
                .container { padding: 3rem; max-width: 550px; border-radius: 40px; background: rgba(18, 22, 33, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6); }
                .logo-icon { font-size: 5rem; margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
                h1 { font-size: 2.2rem; margin-bottom: 1.2rem; }
                p { color: #94a3b8; font-size: 1.1rem; line-height: 1.7; margin-bottom: 2.5rem; }
                .badge { display: inline-block; padding: 6px 16px; background: rgba(139, 92, 246, 0.1); color: var(--primary); border-radius: 100px; font-size: 0.8rem; font-weight: 600; margin-bottom: 1.5rem; text-transform: uppercase; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="badge">Platform Maintenance</div>
                <div class="logo-icon">✨</div>
                <h1>Texnik ishlar ketmoqda</h1>
                <p>Mijozlarimizga yanada yaxshi xizmat ko'rsatish maqsadida platformada profilaktika ishlari olib borilmoqda. Tez orada barchasi odatdagidek ishlaydi.</p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Global functions for super admin
function getSetting($db, $key, $default = "") {
    $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) return $row['setting_value'];
    return $default;
}

function setSetting($db, $key, $value) {
    $stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->bind_param("sss", $key, $value, $value);
    return $stmt->execute();
}

function getTotalUsers($db) {
    // Only count regular users (role 0 or null)
    $res = $db->query("SELECT COUNT(*) as count FROM users WHERE (role = 0 OR role IS NULL)");
    $row = $res->fetch_assoc();
    return $row['count'] ?? 0;
}

function getRecentUsers($db, $limit = 5) {
    // Only show regular users in recent registrations
    return $db->query("SELECT * FROM users WHERE (role = 0 OR role IS NULL) ORDER BY created_at DESC LIMIT $limit");
}
?>
