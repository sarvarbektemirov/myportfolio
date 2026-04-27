<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

include_once("ktl.php");


$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['id'])) {

    $id     = (int)$_POST['id'];
    $tur    = trim($_POST['tur'] ?? '');
    $nom_uz = trim(str_replace(["\r\n","\r","\n"], ' ', $_POST['nom_uz'] ?? ''));
    $nom_en = trim(str_replace(["\r\n","\r","\n"], ' ', $_POST['nom_en'] ?? ''));

    $sana_oy  = (int)($_POST['sana_oy']  ?? 0);
    $sana_yil = (int)($_POST['sana_yil'] ?? 0);
    $sana     = ($sana_yil && $sana_oy)
                ? sprintf('%04d-%02d-01', $sana_yil, $sana_oy)
                : '';

    // Tekshiruv
    if (!in_array($tur, ['asosiy', 'qoshimcha'])) {
        $xato = "Tur noto'g'ri!";
    } elseif (empty($nom_uz)) {
        $xato = "Nomi (O'zbek) bo'sh bo'lishi mumkin emas!";
    } elseif (empty($sana)) {
        $xato = "Sana kiritilmagan!";
    }

    // Eski rasmni olish
    $rasm_nomi = "";
    if (empty($xato)) {
        // Eski ma'lumotlarni olish
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $eski_surov = $link->prepare("SELECT rasm, nom_uz, nom_en FROM nashr_carousel WHERE id = ? AND user_id = ?");
        $eski_surov->bind_param("ii", $id, $uid);
        $eski_surov->execute();
        $eski = $eski_surov->get_result()->fetch_assoc();
        $rasm_nomi = $eski['rasm'] ?? '';
        $eski_surov->close();

        // Yangi rasm yuklangan bo'lsa
        if (!empty($_FILES['rasm']['name'])) {
            $ruxsat = ['image/jpeg','image/png','image/webp','image/gif'];
            if (!in_array($_FILES['rasm']['type'], $ruxsat)) {
                $xato = "Faqat JPG, PNG, WEBP yoki GIF yuklash mumkin!";
            } elseif ($_FILES['rasm']['size'] > 3 * 1024 * 1024) {
                $xato = "Rasm hajmi 3MB dan oshmasligi kerak!";
            } else {
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/nashr_carousel/";
                $kengaytma  = pathinfo($_FILES['rasm']['name'], PATHINFO_EXTENSION);
                $yangi_rasm = time() . '_' . uniqid() . '.' . $kengaytma;

                if (move_uploaded_file($_FILES['rasm']['tmp_name'], $upload_dir . $yangi_rasm)) {
                    if (!empty($rasm_nomi) && file_exists($upload_dir . $rasm_nomi)) {
                        unlink($upload_dir . $rasm_nomi);
                    }
                    $rasm_nomi = $yangi_rasm;
                } else {
                    $xato = "Rasmni yuklashda xatolik!";
                }
            }
        }
    }

    // Tarjima
    if (empty($xato)) {
        include_once("translate.php");
        
        // Smart Translation: Agar o'zbekcha o'zgargan bo'lsa va inglizchaga teginilmagan bo'lsa (yoki bo'sh bo'lsa)
        if (empty($nom_en) || ($nom_uz !== $eski['nom_uz'] && $nom_en === $eski['nom_en'])) {
            $nom_en = trim(str_replace(["\r\n","\r","\n"], ' ', translateText($nom_uz, 'uz', 'en')));
        }
    }

    // Bazaga yozish
    if (empty($xato)) {
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $t = $link->prepare("
            UPDATE nashr_carousel
            SET tur=?, rasm=?, nom_uz=?, nom_en=?, sana=?
            WHERE id=? AND user_id=?
        ");
        $t->bind_param("sssssii", $tur, $rasm_nomi, $nom_uz, $nom_en, $sana, $id, $uid);

        if ($t->execute()) {
            $t->close();
            $link->close();
            header("Refresh: 1; URL=list_carousel.php");
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
                    <i class='fa-solid fa-circle-check me-2'></i> Muvaffaqiyatli tahrirlandi!
                </div>
            </body>
            </html>
            <?php
            exit;
        } else {
            $xato = "Xatolik: " . $t->error;
            $t->close();
            $link->close();
        }
    }

} else {
    header("Location: list_carousel.php");
    exit;
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
        <div class='container mt-2'>
            <a href='list_carousel.php' class='btn btn-secondary btn-back'>
                <i class='fa-solid fa-arrow-left me-1'></i> Orqaga qaytish
            </a>
        </div>
    </body>
    </html>
    <?php
}

