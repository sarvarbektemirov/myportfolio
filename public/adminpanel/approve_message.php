<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}


$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$status = filter_input(INPUT_GET, 's', FILTER_SANITIZE_SPECIAL_CHARS);

if ($id && in_array($status, ['approved', 'pending'])) {
    $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
    $stmt = $link->prepare("UPDATE messages SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $status, $id, $uid);
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

