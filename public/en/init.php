<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $current_lang = $_GET['lang'] === 'uz' ? 'uz' : 'en';
    $_SESSION['lang'] = $current_lang;
    $_SESSION['til']  = $current_lang;
}

$lang = $_SESSION['lang'] ?? $_SESSION['til'] ?? 'en';
$til  = $lang;
$_SESSION['lang'] = $lang;
$_SESSION['til']  = $til;

if (!isset($link) || !($link instanceof mysqli)) {
    // Rely on dirname(__DIR__) for absolute path consistency
    $db_path = dirname(__DIR__) . "/adminpanel/db.php";
    if (file_exists($db_path)) {
        include_once($db_path);
    }
}

// SAAS: Ommaviy portfolioni tanib olish
$portfolio_user_id = 1; // Asosiy/Default admin id si

if (isset($_GET['u']) && !empty(trim($_GET['u'])) && isset($master_link)) {
    $u = $master_link->real_escape_string(trim($_GET['u']));
    $ures = $master_link->query("SELECT id, username FROM users WHERE username = '$u'");
    if ($ures && $ures->num_rows > 0) {
        $found = $ures->fetch_assoc();
        $portfolio_user_id = (int)$found['id'];
        $portfolio_username = $found['username'];
        $_SESSION['current_portfolio_id'] = $portfolio_user_id;
        $_SESSION['current_portfolio_user'] = $portfolio_username;
        
        // Connect to the specific User Database
        connect_user_db($portfolio_username);

        // --- Visitor Tracking ---
        if (!isset($_SESSION['viewed_portfolios'])) $_SESSION['viewed_portfolios'] = [];
        if (!in_array($portfolio_user_id, $_SESSION['viewed_portfolios'])) {
            $master_link->query("UPDATE users SET views = views + 1 WHERE id = $portfolio_user_id");
            $_SESSION['viewed_portfolios'][] = $portfolio_user_id;
        }
    }
} elseif (isset($_SESSION['current_portfolio_user'])) {
    // If already in session, ensure we are connected to the right DB
    connect_user_db($_SESSION['current_portfolio_user']);
    $portfolio_user_id = (int)($_SESSION['current_portfolio_id'] ?? 1);
}

// Fallback: agar $link hali ham null bo'lsa, master DB dan birinchi userni topib ulanamiz
if (!isset($link) || !($link instanceof mysqli)) {
    if (isset($master_link) && $master_link instanceof mysqli) {
        $default_user_res = $master_link->query("SELECT id, username FROM users ORDER BY id ASC LIMIT 1");
        if ($default_user_res && $default_user_res->num_rows > 0) {
            $default_row = $default_user_res->fetch_assoc();
            $default_user = $default_row['username'];
            $portfolio_user_id = (int)$default_row['id'];
            $_SESSION['current_portfolio_id']   = $portfolio_user_id;
            $_SESSION['current_portfolio_user'] = $default_user;
            connect_user_db($default_user);
        }
    }
}

// Standardized Link mapping (works for both UZ and EN folders)
$p_links = [
    'home'  => 'home.php',
    'edu'   => 'education.php',
    'exp'   => 'experience.php',
    'pub'   => 'publications.php',
    'teach' => 'teaching.php',
    'stud'  => 'students.php',
    'other' => 'other.php',
    'conn'  => 'connection.php'
];

// --- PUBLIC PORTFOLIO ACCESS CONTROL ---
if (isset($portfolio_user_id) && !isset($_SESSION['super_admin_id'])) {
    $p_res = $master_link->query("SELECT subscription_expires_at FROM users WHERE id = $portfolio_user_id");
    if ($p_res && $p_data = $p_res->fetch_assoc()) {
        $p_expiry = $p_data['subscription_expires_at'];
        if ($p_expiry && strtotime($p_expiry) < time()) {
            ?>
            <!DOCTYPE html>
            <html lang="uz">
            <head>
                <meta charset="UTF-8">
                <title>Obuna muddati tugagan | Portfolio</title>
                <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
                <style>
                    :root { --bg: #030509; --primary: #ef4444; }
                    body { margin: 0; background: var(--bg); color: white; font-family: 'Outfit', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; text-align: center; }
                    .card { padding: 3rem; max-width: 500px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 30px; backdrop-filter: blur(10px); }
                    .icon { font-size: 4rem; margin-bottom: 1.5rem; color: var(--primary); }
                    h1 { font-size: 1.8rem; margin-bottom: 1rem; }
                    p { color: #94a3b8; line-height: 1.6; }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="icon">⌛</div>
                    <h1>Obuna muddati tugagan</h1>
                    <p>Ushbu portfolioning obuna muddati yakunlangan. Portfolio egasi obunani uzaytirgandan so'ng sayt qayta faollashadi.</p>
                </div>
            </body>
            </html>
            <?php
            exit;
        }
    }
}

// Ensure database schema is up to date
include_once(dirname(__DIR__) . "/adminpanel/auto_migrate.php");
?>
