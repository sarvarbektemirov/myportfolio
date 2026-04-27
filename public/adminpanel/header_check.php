<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}
include_once("ktl.php");

$t = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST"
    && !empty($_POST['a'])
    && !empty($_POST['b'])
    && !empty($_POST['c'])
    && !empty($_POST['d'])
    && !empty($_POST['e'])) {

    // FILTER_SANITIZE_FULL_SPECIAL_CHARS o'rniga trim ishlatamiz
    $a = trim($_POST['a']);          // ism (o'zbek)
    $a = kirillToLotin($a);          // kirill bo'lsa lotinga o'tkazish

    $b = trim($_POST['b']);          // familiya (o'zbek)
    $b = kirillToLotin($b);

    $c = trim($_POST['c']);          // daraja (o'zbek)
    $c = kirillToLotin($c);

    $d = trim($_POST['d']);          // tel
    if (!preg_match('/^\+998[-\s]?\d{2}[-\s]?\d{3}[-\s]?\d{2}[-\s]?\d{2}$/', $d)) {
        echo "Telefon raqami darchasiga xato ma`lumot kiritilgan";
        include_once("add_header.php");
    } else {
        $e = trim($_POST['e']);      // email
        if (!(filter_var($e, FILTER_VALIDATE_EMAIL))) {
            echo "Email darchasiga xato ma`lumot kiritilgan";
            include_once("add_header.php");
        } else {
            $t = 1;
        }
    }
} else {
    include_once("add_header.php");
    echo "Formadagi ma'lumotlar to'liq emas.";
}

if ($t == 1) {
    include_once("translate.php");

    // English versiyalari - manual kiritilgan bo'lsa uni olamiz, aks holda fallback
    $a_en = !empty($_POST['a_en']) ? trim($_POST['a_en']) : $a;
    $b_en = !empty($_POST['b_en']) ? trim($_POST['b_en']) : $b;
    
    if (!empty($_POST['c_en'])) {
        $c_en = trim($_POST['c_en']);
    } else {
        $c_en = translateText($c, 'uz', 'en');
    }

    $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
    $t_surov = $link->prepare("
        INSERT INTO header (ism, ism_en, familiya, familiya_en, daraja, daraja_en, tel, email, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $t_surov->bind_param("ssssssssi", $b, $b_en, $a, $a_en, $c, $c_en, $d, $e, $uid);

    if ($t_surov->execute()) {
        $t_surov->close();
        $link->close();
        header("Refresh: 1; URL=add_header.php");
        ?>
        <!DOCTYPE html>
        <html lang="uz">
        <head>
            <meta charset="UTF-8">
            <link rel="stylesheet" href="css/bootstrap.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="css/extra.css">
        </head>
        <body class="bg-light">
            <div class='alert alert-success animatsiya1 container mt-4 border-0 shadow-sm'>
                <i class='fa-solid fa-circle-check me-2'></i> Header muvaffaqiyatli qo'shildi!
            </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        echo "Xatolik: " . $t_surov->error;
        $t_surov->close();
        $link->close();
        include_once("add_header.php");
    }
}

