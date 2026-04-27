<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

include_once("ktl.php");


$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST"
    && !empty($_POST['id'])
    && !empty($_POST['bosqich'])) {

    $id = (int)$_POST['id'];

    // Bosqich
    $bosqich_raw = trim($_POST['bosqich'] ?? '');
    if ($bosqich_raw === 'other') {
        $bosqich = trim($_POST['bosqich_manual'] ?? '');
        if (empty($bosqich)) {
            $xato = "Ta'lim nomini kiriting!";
        } else {
            include_once("translate.php");
            $bosqich_en = translateText($bosqich, 'uz', 'en');
        }
    } else {
        $bosqich_arr = explode('|', $bosqich_raw);
        $bosqich     = $bosqich_arr[0] ?? '';
        $bosqich_en  = $bosqich_arr[1] ?? '';
    }

    // Tavsif
    $tavsif_uz = trim($_POST['tavsif_uz'] ?? '');
    $tavsif_en = trim($_POST['tavsif_en'] ?? '');

    if (empty($tavsif_uz)) {
        $xato = "O'zbek tilidagi tavsif bo'sh bo'lishi mumkin emas!";
    }

        // Eski ma'lumotlarni olish
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $eski_surov = $link->prepare("SELECT rasm, tavsif_uz, tavsif_en FROM talim WHERE id = ? AND user_id = ?");
        $eski_surov->bind_param("ii", $id, $uid);
        $eski_surov->execute();
        $eski = $eski_surov->get_result()->fetch_assoc();
        $rasm_nomi = $eski['rasm'] ?? '';
        $eski_surov->close();
 
        // Yangi rasm yuklangan bo'lsa
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
                $yangi_rasm = time() . '_' . uniqid() . '.' . $kengaytma;

                if (move_uploaded_file($_FILES['rasm']['tmp_name'], $upload_dir . $yangi_rasm)) {
                    // Eski rasmni o'chirish
                    if (!empty($rasm_nomi) && file_exists($upload_dir . $rasm_nomi)) {
                        unlink($upload_dir . $rasm_nomi);
                    }
                    $rasm_nomi = $yangi_rasm;
                } else {
                    $xato = "Rasmni yuklashda xatolik yuz berdi!";
                }
            }
        }
    }

    // Tarjima va saqlash
    if (empty($xato)) {
        include_once("translate.php");

        // Smart Translation: Agar o'zbekcha o'zgargan bo'lsa va inglizchaga teginilmagan bo'lsa (yoki bo'sh bo'lsa)
        if (empty($tavsif_en) || ($tavsif_uz !== $eski['tavsif_uz'] && $tavsif_en === $eski['tavsif_en'])) {
            $tavsif_en = translateText($tavsif_uz, 'uz', 'en');
        }

        $t_surov = $link->prepare("
            UPDATE talim
            SET bosqich=?, bosqich_en=?, rasm=?, tavsif_uz=?, tavsif_en=?
            WHERE id=? AND user_id=?
        ");
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $t_surov->bind_param("sssssii",
            $bosqich, $bosqich_en, $rasm_nomi, $tavsif_uz, $tavsif_en, $id, $uid
        );

        if ($t_surov->execute()) {
            $t_surov->close();
            $link->close();
            header("Refresh: 1; URL=list_talim.php");
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
            $xato = "Bazaga yozishda xatolik: " . $t_surov->error;
            $t_surov->close();
            $link->close();
        }
    }

 else {
    header("Location: list_talim.php");
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
            <a href='list_talim.php' class='btn btn-secondary btn-back'>
                <i class='fa-solid fa-arrow-left me-1'></i> Orqaga qaytish
            </a>
        </div>
    </body>
    </html>
    <?php
}

