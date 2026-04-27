<?php
include_once('db.php');
if (empty($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}



if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
    
    // Statusni 'read' holatiga o'tkazish
    // Agar allaqachon read bo'lsa, 'pending' qilish (toggle)
    $res = $link->query("SELECT status FROM messages WHERE id = $id AND user_id = $uid");
    if ($row = $res->fetch_assoc()) {
        $new_status = ($row['status'] === 'read') ? 'pending' : 'read';
        $link->query("UPDATE messages SET status = '$new_status' WHERE id = $id AND user_id = $uid");
    }
}

header("Location: list_messages.php");
exit;
?>

