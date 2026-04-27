<?php
include_once("init.php");
include_once("../adminpanel/translate.php");
include_once("../adminpanel/ktl.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['ism'] ?? $_POST['a'] ?? 'Anonim');
    $email = trim($_POST['email'] ?? $_POST['b'] ?? '');
    $rel = trim($_POST['bogliq'] ?? '');
    $msg_uz = trim($_POST['xabar'] ?? '');

    // Capture type and scores
    $msg_type = $_POST['msg_type'] ?? 'message';
    $r_content = isset($_POST['q_content']) ? (int)$_POST['q_content'] : null;
    $r_design  = isset($_POST['q_design'])  ? (int)$_POST['q_design']  : null;
    $r_func    = isset($_POST['q_func'])    ? (int)$_POST['q_func']    : null;

    if ($msg_type === 'rating') {
        $scores = ($lang === 'en') 
            ? "\n\n--- Website Rating ---\nContent: $r_content/5\nDesign: $r_design/5\nFunctionality: $r_func/5"
            : "\n\n--- Sayt bahosi ---\nMazmun: $r_content/5\nDizayn: $r_design/5\nFunksionallik: $r_func/5";
        $msg_uz .= $scores;
    }

    // Convert to Latin if Kirill
    $name = kirillToLotin($name);
    $msg_uz = kirillToLotin($msg_uz);

    // Auto-translate to English
    $msg_en = translateText($msg_uz, 'uz', 'en');
    $p_uid = $portfolio_user_id ?? 1;

    // Self-healing: Check if columns exist
    $columns_to_check = [
        'relationship' => "VARCHAR(100) AFTER email",
        'message_uz' => "TEXT AFTER relationship",
        'message_en' => "TEXT AFTER message_uz"
    ];

    foreach ($columns_to_check as $col => $definition) {
        $check = $link->query("SHOW COLUMNS FROM messages LIKE '$col'");
        if ($check && $check->num_rows == 0) {
            $link->query("ALTER TABLE messages ADD COLUMN $col $definition");
        }
    }

    $stmt = $link->prepare("INSERT INTO messages (user_id, msg_type, name, email, relationship, message_uz, message_en, status, r_content, r_design, r_func) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?)");
    $stmt->bind_param("issssssiii", $p_uid, $msg_type, $name, $email, $rel, $msg_uz, $msg_en, $r_content, $r_design, $r_func);

    if ($stmt->execute()) {
        $u_param = isset($_SESSION['current_portfolio_user']) ? '?u=' . urlencode($_SESSION['current_portfolio_user']) : '';
        header("Location: connection.php" . $u_param . (strpos($u_param, '?') !== false ? '&' : '?') . "status=success");
    } else {
        $u_param = isset($_SESSION['current_portfolio_user']) ? '?u=' . urlencode($_SESSION['current_portfolio_user']) : '';
        header("Location: connection.php" . $u_param . (strpos($u_param, '?') !== false ? '&' : '?') . "status=error");
    }
    $stmt->close();
    $link->close();
} else {
    header("Location: connection.php");
}
?>
