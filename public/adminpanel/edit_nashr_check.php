<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

include_once("ktl.php");


$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['id'])) {

    $id          = (int)$_POST['id'];
    $tur         = trim($_POST['tur'] ?? '');
    $lavozim_uz  = trim(str_replace(["\r\n","\r","\n"], ' ', $_POST['lavozim_uz']  ?? ''));
    $lavozim_en  = trim(str_replace(["\r\n","\r","\n"], ' ', $_POST['lavozim_en']  ?? ''));
    $ish_joyi_uz = trim(str_replace(["\r\n","\r","\n"], ' ', $_POST['ish_joyi_uz'] ?? ''));
    $ish_joyi_en = trim(str_replace(["\r\n","\r","\n"], ' ', $_POST['ish_joyi_en'] ?? ''));
    $hozirgi     = isset($_POST['hozirgi']) ? 1 : 0;

    $boshlanish_oy  = (int)($_POST['boshlanish_oy']  ?? 0);
    $boshlanish_yil = (int)($_POST['boshlanish_yil'] ?? 0);
    $boshlanish     = ($boshlanish_yil && $boshlanish_oy)
                      ? sprintf('%04d-%02d-01', $boshlanish_yil, $boshlanish_oy)
                      : '';

    $tugash_oy  = (int)($_POST['tugash_oy']  ?? 0);
    $tugash_yil = (int)($_POST['tugash_yil'] ?? 0);
    $tugash_db  = ($hozirgi || !$tugash_yil || !$tugash_oy)
                  ? null
                  : sprintf('%04d-%02d-01', $tugash_yil, $tugash_oy);

    $faoliyat_uz_arr = array_filter(array_map(function($v) {
        return trim(str_replace(["\r\n","\r","\n"], ' ', $v));
    }, $_POST['faoliyat_uz'] ?? []), fn($v) => $v !== '');

    $faoliyat_en_arr = array_filter(array_map(function($v) {
        return trim(str_replace(["\r\n","\r","\n"], ' ', $v));
    }, $_POST['faoliyat_en'] ?? []), fn($v) => $v !== '');

    // Tekshiruv
    if (!in_array($tur, ['asosiy', 'qoshimcha'])) {
        $xato = "Tur noto'g'ri!";
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

    // Tarjima mantiqi uchun eski ma'lumotlarni olish
    if (empty($xato)) {
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $eski_surov = $link->prepare("SELECT lavozim_uz, lavozim_en, ish_joyi_uz, ish_joyi_en, faoliyat_uz, faoliyat_en FROM nashrlar WHERE id = ? AND user_id = ?");
        $eski_surov->bind_param("ii", $id, $uid);
        $eski_surov->execute();
        $eski = $eski_surov->get_result()->fetch_assoc();
        $eski_faoliyat_uz = json_decode($eski['faoliyat_uz'] ?? '[]', true);
        $eski_faoliyat_en = json_decode($eski['faoliyat_en'] ?? '[]', true);
        $eski_surov->close();

        include_once("translate.php");

        // Lavozim
        if (empty($lavozim_en) || ($lavozim_uz !== ($eski['lavozim_uz'] ?? '') && $lavozim_en === ($eski['lavozim_en'] ?? ''))) {
            $lavozim_en = trim(str_replace(["\r\n","\r","\n"], ' ', translateText($lavozim_uz, 'uz', 'en')));
        }
        
        // Ish joyi
        if (empty($ish_joyi_en) || ($ish_joyi_uz !== ($eski['ish_joyi_uz'] ?? '') && $ish_joyi_en === ($eski['ish_joyi_en'] ?? ''))) {
            $ish_joyi_en = trim(str_replace(["\r\n","\r","\n"], ' ', translateText($ish_joyi_uz, 'uz', 'en')));
        }

        // Faoliyat (Array/JSON)
        // Agar o'zbekcha ro'yxat o'zgargan bo'lsa va inglizcha ro'yxat foydalanuvchi tomonidan o'zgartirilmagan bo'lsa
        if (empty($faoliyat_en_arr) || ($faoliyat_uz_arr !== $eski_faoliyat_uz && $faoliyat_en_arr === $eski_faoliyat_en)) {
            $faoliyat_en_arr = [];
            foreach ($faoliyat_uz_arr as $f) {
                $faoliyat_en_arr[] = trim(str_replace(["\r\n","\r","\n"], ' ', translateText($f, 'uz', 'en')));
            }
        }

        $faoliyat_uz_json = json_encode(array_values($faoliyat_uz_arr), JSON_UNESCAPED_UNICODE);
        $faoliyat_en_json = json_encode(array_values($faoliyat_en_arr), JSON_UNESCAPED_UNICODE);
    }

    // Bazaga yozish
    if (empty($xato)) {
        $t = $link->prepare("
            UPDATE nashrlar
            SET tur=?, lavozim_uz=?, lavozim_en=?, ish_joyi_uz=?, ish_joyi_en=?,
                faoliyat_uz=?, faoliyat_en=?, boshlanish=?, tugash=?, hozirgi=?
            WHERE id=? AND user_id=?
        ");
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $t->bind_param("sssssssssiii",
            $tur, $lavozim_uz, $lavozim_en, $ish_joyi_uz, $ish_joyi_en,
            $faoliyat_uz_json, $faoliyat_en_json, $boshlanish, $tugash_db, $hozirgi, $id, $uid
        );

        if ($t->execute()) {
            $t->close();
            $link->close();
            header("Refresh: 1; URL=list_nashr.php");
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
                    <i class='fa-solid fa-circle-check me-2'></i> Ma'lumotlar muvaffaqiyatli tahrirlandi!
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
    header("Location: list_nashr.php");
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
            <a href='list_nashr.php' class='btn btn-secondary btn-back'>
                <i class='fa-solid fa-arrow-left me-1'></i> Orqaga qaytish
            </a>
        </div>
    </body>
    </html>
    <?php
}

