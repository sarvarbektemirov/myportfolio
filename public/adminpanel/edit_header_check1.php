<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}
include_once("ktl.php");
include_once("translate.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['id'])) {

    $id = (int)$_POST['id'];
    $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

    // Eski ma'lumotlarni olish (Sinxron tahrir uchun)
    $eski_surov = $link->prepare("SELECT * FROM header WHERE id = ? AND user_id = ?");
    $eski_surov->bind_param("ii", $id, $uid);
    $eski_surov->execute();
    $eski = $eski_surov->get_result()->fetch_assoc();
    $eski_surov->close();

    $a = trim($_POST['a'] ?? '');
    $a = kirillToLotin($a);
    $b = trim($_POST['b'] ?? '');
    $b = kirillToLotin($b);
    $c = trim($_POST['c'] ?? '');
    $c = kirillToLotin($c);
    $d = trim($_POST['d'] ?? '');
    $e = trim($_POST['e'] ?? '');

    if (empty($a) || empty($b) || empty($c) || empty($d) || empty($e)) {
    ?>
    <!DOCTYPE html>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> Ma'lumotlar to'liq emas!</div>
    <?php
    exit;
    }

    $a_en = !empty($_POST['a_en']) ? trim($_POST['a_en']) : '';
    $b_en = !empty($_POST['b_en']) ? trim($_POST['b_en']) : '';
    $c_en = !empty($_POST['c_en']) ? trim($_POST['c_en']) : '';

    // Smart Translation
    // Ism
    if (empty($a_en) || ($a !== $eski['ism'] && $a_en === $eski['ism_en'])) {
        $a_en = $a; // Ismlar odatda tarjima qilinmaydi, shunchaki ko'chiriladi
    }
    // Familiya
    if (empty($b_en) || ($b !== $eski['familiya'] && $b_en === $eski['familiya_en'])) {
        $b_en = $b;
    }
    // Ilmiy daraja
    if (empty($c_en) || ($c !== $eski['daraja'] && $c_en === $eski['daraja_en'])) {
        $c_en = translateText($c, 'uz', 'en');
    }

    $t_surov = $link->prepare("
        UPDATE header 
        SET ism=?, ism_en=?, familiya=?, familiya_en=?, daraja=?, daraja_en=?, tel=?, email=? 
        WHERE id=? AND user_id=?
    ");
    $t_surov->bind_param("ssssssssii", $a, $a_en, $b, $b_en, $c, $c_en, $d, $e, $id, $uid);

    if ($t_surov->execute()) {
        $t_surov->close();
        header("Location: edit_header.php?xabar=ok");
        exit;
    } else {
    ?>
    <!DOCTYPE html>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> Xatolik: <?= htmlspecialchars($t_surov->error) ?></div>
    <?php
    exit;
    }
} else {
    header("Location: edit_header.php");
    exit;
}
