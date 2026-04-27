<?php
include_once("db.php");
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['del']) && is_array($_POST['del'])) {
    $del_ids = $_POST['del'];
    $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
    
    $t_surov = $link->prepare("DELETE FROM footer WHERE id = ? AND user_id = ?");

    foreach ($del_ids as $id) {
        $id = (int)$id;
        $t_surov->bind_param("ii", $id, $uid);
        $t_surov->execute();
    }

    $t_surov->close();
    header("Location: edit_footer.php?res=ok");
    exit;
} else {
    header("Location: delete_footer.php");
    exit;
}
