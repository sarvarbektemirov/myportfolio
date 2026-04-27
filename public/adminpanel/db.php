<?php
/**
 * Dynamic DB Connection for Multi-Database SaaS
 */
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
#error_reporting(0);
#ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0); // Productionda 0 bo'lishi kerak

date_default_timezone_set('Asia/Tashkent');

$db_host = getenv('MYSQLHOST') ?: 'mysql-8.4';
$db_user = getenv('MYSQLUSER') ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: '';
$db_name = getenv('MYSQLDATABASE') ?: 'portfolio';
$db_port = getenv('MYSQLPORT') ?: '3306';

$master_link = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($master_link->connect_errno) {
    die("Master DB ulanishda xato: " . $master_link->connect_error);
}

// Ensure columns exist in master database
$check_v = $master_link->query("SHOW COLUMNS FROM users LIKE 'views'");
if ($check_v && $check_v->num_rows === 0) {
    $master_link->query("ALTER TABLE users ADD views INT DEFAULT 0");
}
$check_l = $master_link->query("SHOW COLUMNS FROM users LIKE 'last_portfolio_update'");
if ($check_l && $check_l->num_rows === 0) {
    $master_link->query("ALTER TABLE users ADD last_portfolio_update DATETIME DEFAULT CURRENT_TIMESTAMP");
}

// 2. Dynamic Connection ($link)
$link = null;

/**
 * Connects to a specific user's database
 * If the database doesn't exist, it creates and initializes it.
 * @param string $username
 * @return mysqli|false
 */
function connect_user_db($username) {
    global $link, $master_link;
    if (empty($username)) return false;
    
    $user_db = "portfolio_" . $username;
    
    // 1. First, verify the user exists in the master 'users' table
    $safe_user = $master_link->real_escape_string($username);
    $uid_res = $master_link->query("SELECT id FROM users WHERE username = '$safe_user' LIMIT 1");
    if (!$uid_res || $uid_res->num_rows === 0) {
        return false; // User not found in master database
    }
    
    $user_data = $uid_res->fetch_assoc();
    $_SESSION['current_user_id'] = (int)$user_data['id'];

    // 2. Check if $link is already connected to this user database
    if (isset($link) && $link instanceof mysqli && !(@$link->connect_errno)) {
        $curr_db_res = $link->query("SELECT DATABASE()");
        if ($curr_db_res) {
            $curr_db = $curr_db_res->fetch_row()[0];
            if ($curr_db === $user_db) return $link;
        }
    }

    // 3. Attempt to connect to the user database
    try {
        // Temporarily disable exception throwing for the connection attempt to handle it manually or via catch
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $temp_link = new mysqli($db_host, $db_user, $db_pass, $user_db, $db_port);
    } catch (mysqli_sql_exception $e) {
        // If error is 1049 (Unknown database), create it
        if ($e->getCode() === 1049) {
            $create_query = "CREATE DATABASE `$user_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if ($master_link->query($create_query)) {
                // Re-attempt connection to the newly created database
                $temp_link = new mysqli($db_host, $db_user, $db_pass, $user_db, $db_port);
                
                // Initialize the database with required tables
                $schema_path = __DIR__ . "/schema.php";
                if (file_exists($schema_path)) {
                    include_once($schema_path);
                    if (function_exists('initialize_user_db')) {
                        initialize_user_db($temp_link);
                    }
                }
            } else {
                return false; // Failed to create database
            }
        } else {
            // Re-throw other types of exceptions
            throw $e;
        }
    }
    
    if ($temp_link->connect_errno) {
        return false;
    }
    
    $link = $temp_link;
    
    // Run auto-migrations if connected
    if ($link) {
        include_once(__DIR__ . "/auto_migrate.php");
    }

    return $link;
}

// Global initialization logic: Auto-connect if Admin is logged in
if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    $conn_res = connect_user_db($_SESSION['username']);
    
    // If connection failed and we are not on a public/login page, prevent fatal errors
    if (!$conn_res) {
        $current_script = basename($_SERVER['PHP_SELF']);
        $allowed_without_db = ['login.php', 'register.php', 'checkout.php', 'logout.php'];
        
        if (!in_array($current_script, $allowed_without_db)) {
            header("Location: login.php?error=db_error");
            exit;
        }
    }
}

// --- SUBSCRIPTION EXPIRY CHECK ---
if (isset($_SESSION['id']) && !isset($_SESSION['super_admin_id'])) {
    $uid = (int)$_SESSION['id'];
    $u_res = $master_link->query("SELECT subscription_expires_at FROM users WHERE id = $uid");
    if ($u_res && $u_data = $u_res->fetch_assoc()) {
        $expiry = $u_data['subscription_expires_at'];
        $current_script = basename($_SERVER['PHP_SELF']);
        $allowed_pages = ['billing.php', 'checkout.php', 'logout.php', 'login.php'];
        
        if ($expiry && strtotime($expiry) < time() && !in_array($current_script, $allowed_pages)) {
            header("Location: billing.php?error=expired");
            exit;
        }
    }
}

// --- GLOBAL MAINTENANCE MODE CHECK ---
if (isset($master_link) && !isset($_SESSION['super_admin_id'])) {
    $m_res = $master_link->query("SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'");
    $m_mode = ($m_res && $row = $m_res->fetch_assoc()) ? $row['setting_value'] : '0';
    
    if ($m_mode === '1') {
        ?>
        <!DOCTYPE html>
        <html lang="uz">
        <head>
            <meta charset="UTF-8">
            <title>Texnik ishlar | Platform</title>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
            <style>
                :root {
                    --bg: #030509;
                    --primary: #8b5cf6;
                    --secondary: #06b6d4;
                }
                body {
                    margin: 0;
                    padding: 0;
                    background-color: var(--bg);
                    background-image: 
                        radial-gradient(circle at 15% 15%, rgba(139, 92, 246, 0.08) 0%, transparent 40%),
                        radial-gradient(circle at 85% 85%, rgba(6, 182, 212, 0.08) 0%, transparent 40%);
                    color: var(--text-primary, white);
                    font-family: 'Outfit', sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    text-align: center;
                    overflow: hidden;
                }
                .container {
                    padding: 3rem;
                    max-width: 550px;
                    border-radius: 40px;
                    background: var(--card-bg, rgba(18, 22, 33, 0.6));
                    backdrop-filter: blur(20px);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
                    animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
                }
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(40px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .logo-icon {
                    font-size: 5rem;
                    margin-bottom: 2rem;
                    background: linear-gradient(135deg, var(--primary), var(--secondary));
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    animation: float 4s ease-in-out infinite;
                }
                @keyframes float {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-10px); }
                }
                h1 {
                    font-size: 2.2rem;
                    font-weight: 600;
                    margin: 0 0 1.2rem 0;
                    letter-spacing: -1px;
                }
                p {
                    color: var(--text-muted, #94a3b8);
                    font-size: 1.1rem;
                    line-height: 1.7;
                    margin-bottom: 2.5rem;
                }
                .badge {
                    display: inline-block;
                    padding: 6px 16px;
                    background: rgba(139, 92, 246, 0.1);
                    color: var(--primary);
                    border-radius: 100px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    border: 1px solid rgba(139, 92, 246, 0.2);
                    margin-bottom: 1.5rem;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .loader {
                    width: 40px;
                    height: 40px;
                    border: 3px solid rgba(255,255,255,0.05);
                    border-top: 3px solid var(--primary);
                    border-radius: 50%;
                    margin: 0 auto;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="badge">Platform Maintenance</div>
                <div class="logo-icon">✨</div>
                <h1>Texnik ishlar ketmoqda</h1>
                <p>Mijozlarimizga yanada yaxshi xizmat ko'rsatish maqsadida platformada profilaktika ishlari olib borilmoqda. Tez orada barchasi odatdagidek ishlaydi.</p>
                <div class="loader"></div>
            </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        // --- If maintenance is ON but user is Admin, show a WARNING BANNER ---
        if ($m_mode === '1') {
            echo '<div class="maintenance-banner">
                    <i class="fas fa-exclamation-triangle"></i> 
                    DIQQAT: Tizimda tanaffus rejimi yoqilgan. Oddiy foydalanuvchilar saytni ko\'ra olmaydi!
                  </div>';
        }
    }
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

// --- AUTOMATIC LAST UPDATE TRACKING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id'])) {
    $upd_uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id']);
    if (isset($master_link) && $upd_uid > 0) {
        $now = date('Y-m-d H:i:s');
        $master_link->query("UPDATE users SET last_portfolio_update = '$now' WHERE id = $upd_uid");
    }
}
?>
