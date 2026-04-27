<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

include_once("ktl.php");


$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Bosqich tekshiruvi ---
    $bosqich_raw = trim($_POST['bosqich'] ?? '');
    if (empty($bosqich_raw)) {
        $xato = "Ta'lim bosqichini tanlang!";
    } elseif ($bosqich_raw === 'other') {
        $bosqich = trim($_POST['bosqich_manual'] ?? '');
        if (empty($bosqich)) {
            $xato = "Ta'lim nomini kiriting!";
        } else {
            include_once("translate.php");
            $bosqich_en = translateText($bosqich, 'uz', 'en');
        }
    } else {
        // "maktab|school" → ["maktab", "school"]
        $bosqich_arr = explode('|', $bosqich_raw);
        $bosqich    = $bosqich_arr[0] ?? '';
        $bosqich_en = $bosqich_arr[1] ?? '';
    }

    // --- 2. Tavsif tekshiruvi ---
    if (empty($xato)) {
        $tavsif_uz = trim($_POST['tavsif_uz'] ?? '');
        $tavsif_en = trim($_POST['tavsif_en'] ?? '');

        if (empty($tavsif_uz)) {
            $xato = "O'zbek tilidagi tavsif bo'sh bo'lishi mumkin emas!";
        }
    }

    // --- 3. Rasm tekshiruvi ---
    $rasm_nomi = "";
    if (empty($xato)) {
        if (!empty($_FILES['rasm']['name'])) {
            $ruxsat_etilgan = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fayl_turi  = $_FILES['rasm']['type'];
            $fayl_hajmi = $_FILES['rasm']['size'];

            if (!in_array($fayl_turi, $ruxsat_etilgan)) {
                $xato = "Faqat JPG, PNG, WEBP yoki GIF rasm yuklash mumkin!";
            } elseif ($fayl_hajmi > 2 * 1024 * 1024) {
                $xato = "Rasm hajmi 2MB dan oshmasligi kerak!";
            } else {
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/talimrasm/";
                $kengaytma  = pathinfo($_FILES['rasm']['name'], PATHINFO_EXTENSION);
                $rasm_nomi  = time() . '_' . uniqid() . '.' . $kengaytma;

                if (!move_uploaded_file($_FILES['rasm']['tmp_name'], $upload_dir . $rasm_nomi)) {
                    $xato = "Rasmni yuklashda xatolik yuz berdi!";
                }
            }
        }
        // Rasm majburiy emas — bo'sh qolsa ham saqlanadi
    }

    // --- 4. Tarjima va bazaga yozish ---
    if (empty($xato)) {
        include_once("translate.php");

        // Inglizcha bo'sh bo'lsa — avtomatik tarjima
        if (empty($tavsif_en)) {
            $tavsif_en = translateText($tavsif_uz, 'uz', 'en');
        }

        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $t_surov = $link->prepare("
            INSERT INTO talim (bosqich, bosqich_en, rasm, tavsif_uz, tavsif_en, user_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $t_surov->bind_param("sssssi", $bosqich, $bosqich_en, $rasm_nomi, $tavsif_uz, $tavsif_en, $uid);

        if ($t_surov->execute()) {
            $t_surov->close();
            $link->close();
            header("Location: add_talim.php?status=success");
            exit;
        } else {
            $xato = "Bazaga yozishda xatolik: " . $t_surov->error;
            $t_surov->close();
            $link->close();
        }
    }

    // --- 5. Xato bo'lsa ko'rsatish ---
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
        <?php include_once("add_talim.php"); ?>
    </body>
    </html>
    <?php
}

} else {
    include_once("add_talim.php");
}

