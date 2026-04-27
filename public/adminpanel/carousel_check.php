<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

include_once("ktl.php");


$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tur    = trim($_POST['tur'] ?? '');
    $nom_uz = trim(str_replace(["\r\n", "\r", "\n"], ' ', $_POST['nom_uz'] ?? ''));
    $nom_en = trim(str_replace(["\r\n", "\r", "\n"], ' ', $_POST['nom_en'] ?? ''));

    $sana_oy  = (int)($_POST['sana_oy'] ?? 0);
    $sana_yil = (int)($_POST['sana_yil'] ?? 0);
    $sana     = ($sana_yil && $sana_oy)
                ? sprintf('%04d-%02d-01', $sana_yil, $sana_oy)
                : '';

    // Tekshiruv
    if (!in_array($tur, ['asosiy', 'qoshimcha'])) {
        $xato = "Tur noto'g'ri tanlangan!";
    } elseif (empty($nom_uz)) {
        $xato = "Nomi (O'zbek) bo'sh bo'lishi mumkin emas!";
    } elseif (empty($sana)) {
        $xato = "Sana kiritilmagan!";
    }

    // Rasm
    $rasm_nomi = "";
    if (empty($xato)) {
        if (empty($_FILES['rasm']['name'])) {
            $xato = "Rasm tanlanmagan!";
        } else {
            $ruxsat = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($_FILES['rasm']['type'], $ruxsat)) {
                $xato = "Faqat JPG, PNG, WEBP yoki GIF yuklash mumkin!";
            } elseif ($_FILES['rasm']['size'] > 3 * 1024 * 1024) {
                $xato = "Rasm hajmi 3MB dan oshmasligi kerak!";
            } else {
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/nashr_carousel/";
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                $kengaytma = pathinfo($_FILES['rasm']['name'], PATHINFO_EXTENSION);
                $rasm_nomi = time() . '_' . uniqid() . '.' . $kengaytma;
                if (!move_uploaded_file($_FILES['rasm']['tmp_name'], $upload_dir . $rasm_nomi)) {
                    $xato = "Rasmni yuklashda xatolik!";
                }
            }
        }
    }

    // Tarjima
    if (empty($xato)) {
        if (empty($nom_en)) {
            include_once("translate.php");
            $nom_en = trim(str_replace(["\r\n", "\r", "\n"], ' ',
                translateText($nom_uz, 'uz', 'en')
            ));
        }
    }

    // Bazaga yozish
    if (empty($xato)) {
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $t = $link->prepare("
            INSERT INTO nashr_carousel (tur, rasm, nom_uz, nom_en, sana, user_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $t->bind_param("sssssi", $tur, $rasm_nomi, $nom_uz, $nom_en, $sana, $uid);

        if ($t->execute()) {
            $t->close();
            $link->close();
            header("Refresh: 1; URL=add_carousel.php");
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
                    <i class='fa-solid fa-circle-check me-2'></i> Carousel rasmi muvaffaqiyatli qo'shildi!
                </div>
            </body>
            </html>
            <?php
            exit;
        } else {
            $xato = "Bazaga yozishda xatolik: " . $t->error;
            $t->close();
            $link->close();
        }
    }

    if (!empty($xato)) {
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
            <div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'>
                <i class='fa-solid fa-triangle-exclamation me-2'></i> <?= htmlspecialchars($xato) ?>
            </div>
            <?php include_once("add_carousel.php"); ?>
        </body>
        </html>
        <?php
    }

} else {
    include_once("add_carousel.php");
}

