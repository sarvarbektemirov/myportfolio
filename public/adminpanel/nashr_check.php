<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

include_once("ktl.php");


$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Ma'lumotlarni olish va tozalash ---
    $tur         = trim($_POST['tur'] ?? '');
    $lavozim_uz  = trim(str_replace(["\r\n", "\r", "\n"], ' ', $_POST['lavozim_uz'] ?? ''));
    $lavozim_en  = trim(str_replace(["\r\n", "\r", "\n"], ' ', $_POST['lavozim_en'] ?? ''));
    $ish_joyi_uz = trim(str_replace(["\r\n", "\r", "\n"], ' ', $_POST['ish_joyi_uz'] ?? ''));
    $ish_joyi_en = trim(str_replace(["\r\n", "\r", "\n"], ' ', $_POST['ish_joyi_en'] ?? ''));
    $hozirgi     = isset($_POST['hozirgi']) ? 1 : 0;

    // Sanalarni oy + yildan birlashtirish
    $boshlanish_oy  = (int)($_POST['boshlanish_oy'] ?? 0);
    $boshlanish_yil = (int)($_POST['boshlanish_yil'] ?? 0);
    $boshlanish     = ($boshlanish_yil && $boshlanish_oy)
                      ? sprintf('%04d-%02d-01', $boshlanish_yil, $boshlanish_oy)
                      : '';

    $tugash_oy  = (int)($_POST['tugash_oy'] ?? 0);
    $tugash_yil = (int)($_POST['tugash_yil'] ?? 0);
    $tugash_db  = ($hozirgi || !$tugash_yil || !$tugash_oy)
                  ? null
                  : sprintf('%04d-%02d-01', $tugash_yil, $tugash_oy);

    // Faoliyatlar — massiv, tozalab filtrlash
    $faoliyat_uz_arr = array_filter(array_map(function($v) {
        return trim(str_replace(["\r\n", "\r", "\n"], ' ', $v));
    }, $_POST['faoliyat_uz'] ?? []), fn($v) => $v !== '');

    $faoliyat_en_arr = array_filter(array_map(function($v) {
        return trim(str_replace(["\r\n", "\r", "\n"], ' ', $v));
    }, $_POST['faoliyat_en'] ?? []), fn($v) => $v !== '');

    // --- 2. Tekshiruv ---
    if (!in_array($tur, ['asosiy', 'qoshimcha'])) {
        $xato = "Tur noto'g'ri tanlangan!";
    } elseif (empty($lavozim_uz)) {
        $xato = "Lavozim (O'zbek) bo'sh bo'lishi mumkin emas!";
    } elseif (empty($ish_joyi_uz)) {
        $xato = "Ish joyi (O'zbek) bo'sh bo'lishi mumkin emas!";
    } elseif (empty($faoliyat_uz_arr)) {
        $xato = "Kamida bitta faoliyat kiriting!";
    } elseif (empty($boshlanish)) {
        $xato = "Boshlanish oyi va yili kiritilmagan!";
    } elseif (!$hozirgi && (!$tugash_yil || !$tugash_oy)) {
        $xato = "Tugash oyini va yilini kiriting yoki 'Hozirgi kungacha' ni belgilang!";
    }

    // --- 3. Tarjima ---
    if (empty($xato)) {
        include_once("translate.php");

        if (empty($lavozim_en)) {
            $lavozim_en = trim(str_replace(["\r\n", "\r", "\n"], ' ',
                translateText($lavozim_uz, 'uz', 'en')
            ));
        }

        if (empty($ish_joyi_en)) {
            $ish_joyi_en = trim(str_replace(["\r\n", "\r", "\n"], ' ',
                translateText($ish_joyi_uz, 'uz', 'en')
            ));
        }

        if (empty($faoliyat_en_arr)) {
            $faoliyat_en_arr = [];
            foreach ($faoliyat_uz_arr as $faoliyat) {
                $faoliyat_en_arr[] = trim(str_replace(["\r\n", "\r", "\n"], ' ',
                    translateText($faoliyat, 'uz', 'en')
                ));
            }
        }

        $faoliyat_uz_json = json_encode(array_values($faoliyat_uz_arr), JSON_UNESCAPED_UNICODE);
        $faoliyat_en_json = json_encode(array_values($faoliyat_en_arr), JSON_UNESCAPED_UNICODE);
    }

    // --- 4. Bazaga yozish ---
    if (empty($xato)) {
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $t_surov = $link->prepare("
            INSERT INTO nashrlar
                (tur, lavozim_uz, lavozim_en, ish_joyi_uz, ish_joyi_en,
                 faoliyat_uz, faoliyat_en, boshlanish, tugash, hozirgi, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $t_surov->bind_param(
            "sssssssssii",
            $tur,
            $lavozim_uz,
            $lavozim_en,
            $ish_joyi_uz,
            $ish_joyi_en,
            $faoliyat_uz_json,
            $faoliyat_en_json,
            $boshlanish,
            $tugash_db,
            $hozirgi,
            $uid
        );

        if ($t_surov->execute()) {
            $t_surov->close();
            $link->close();
            header("Refresh: 1; URL=add_nashr.php");
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
                    <i class='fa-solid fa-circle-check me-2'></i> Ma'lumotlar muvaffaqiyatli saqlandi!
                </div>
            </body>
            </html>
            <?php
            exit;
        } else {
            $xato = "Bazaga yozishda xatolik: " . $t_surov->error;
            $t_surov->close();
            $link->close();
        }
    }

    // --- 5. Xato bo'lsa ---
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
            <?php include_once("add_nashr.php"); ?>
        </body>
        </html>
        <?php
    }

} else {
    include_once("add_nashr.php");
}

