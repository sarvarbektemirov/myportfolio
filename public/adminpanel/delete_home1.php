<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

$surov = $link->query("SELECT * FROM home WHERE user_id = $uid ORDER BY id DESC LIMIT 1");
$malumot = $surov->fetch_assoc();

if ($malumot) {
    $id = $malumot['id'];
    $rasm = $malumot['rasm'];

    // Rasmni fayldan o'chirish
    if (!empty($rasm)) {
        $rasm_yol = $_SERVER['DOCUMENT_ROOT'] . "/files/" . $rasm;
        if (file_exists($rasm_yol)) {
            unlink($rasm_yol);
        }
    }

    // Bazadan o'chirish
    $t_surov = $link->prepare("DELETE FROM home WHERE id=? AND user_id=?");
    $t_surov->bind_param("ii", $id, $uid);

    if ($t_surov->execute()) {
        $t_surov->close();
        header("Location: edit_home.php?res=ok");
        exit;
    } else {
        echo "<div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> O'chirishda xatolik yuz berdi!</div>";
        echo "<div class='container mt-2'><a href='edit_home.php' class='btn btn-secondary btn-back'><i class='fa-solid fa-arrow-left me-1'></i> Orqaga qaytish</a></div>";
        $t_surov->close();
        exit;
    }
} else {
    header("Location: edit_home.php");
    exit;
}
