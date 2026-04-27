<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}


$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
    $stmt = $link->prepare("DELETE FROM messages WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $uid);
    if ($stmt->execute()) {
        header("Location: list_messages.php?res=ok");
    } else {
        header("Location: list_messages.php?res=error");
    }
    $stmt->close();
} else {
    header("Location: list_messages.php");
}
$link->close();
?>

