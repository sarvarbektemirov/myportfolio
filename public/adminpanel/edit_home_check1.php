<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

include_once("ktl.php");


$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Ma'lumot tekshiruvi ---
    $malumot_uz = trim($_POST['b'] ?? '');
    $skills_uz  = trim($_POST['skills_uz'] ?? '');

    if (empty($malumot_uz)) {
        $xato = "Ma'lumot maydoni bo'sh bo'lishi mumkin emas!";
    }

    // --- 2. Bazadan mavjud yozuvni olish ---
    $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
    $surov = $link->query("SELECT * FROM home WHERE user_id = $uid LIMIT 1");
    $eski = $surov->fetch_assoc();
    if (!$eski) {
        header("Location: add_home.php");
        exit;
    }
    $rasm_nomi = $eski['rasm'];
    $id = $eski['id'];

    // --- 3. Rasm tekshiruvi ---
    if (empty($xato) && !empty($_FILES['a']['name'])) {
        $ruxsat_etilgan = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $fayl_turi = $_FILES['a']['type'];
        $fayl_hajmi = $_FILES['a']['size'];

        if (!in_array($fayl_turi, $ruxsat_etilgan)) {
            $xato = "Faqat JPG, PNG, WEBP yoki GIF rasm yuklash mumkin!";
        } elseif ($fayl_hajmi > 5 * 1024 * 1024) {
            $xato = "Rasm hajmi 5MB dan oshmasligi kerak!";
        } else {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/files/";

            $kengaytma = pathinfo($_FILES['a']['name'], PATHINFO_EXTENSION);
            $yangi_rasm = time() . '_' . uniqid() . '.' . $kengaytma;

            if (move_uploaded_file($_FILES['a']['tmp_name'], $upload_dir . $yangi_rasm)) {
                if (!empty($eski['rasm']) && file_exists($upload_dir . $eski['rasm'])) {
                    unlink($upload_dir . $eski['rasm']);
                }
                $rasm_nomi = $yangi_rasm;
            } else {
                $xato = "Rasmni yuklashda xatolik yuz berdi!";
            }
        }
    }

    // --- 4. Xato yo'q bo'lsa — tarjima va bazaga yozish ---
    if (empty($xato)) {
        include_once("translate.php");

        $malumot_en = trim($_POST['b_en'] ?? '');
        $skills_en = trim($_POST['skills_en'] ?? '');

        // Smart Translation for Bio
        if (empty($malumot_en) || ($malumot_uz !== $eski['malumot_uz'] && $malumot_en === $eski['malumot_en'])) {
            $malumot_en = translateText($malumot_uz, 'uz', 'en');
        }

        // Smart Translation for Skills
        if (empty($skills_en) || ($skills_uz !== $eski['skills_uz'] && $skills_en === $eski['skills_en'])) {
            if (!empty($skills_uz)) {
                $skills_en = translateText($skills_uz, 'uz', 'en');
            } else {
                $skills_en = "";
            }
        }

        $t_surov = $link->prepare("
            UPDATE home SET rasm=?, malumot_uz=?, malumot_en=?, skills_uz=?, skills_en=?
            WHERE id=? AND user_id=?
        ");
        $t_surov->bind_param("sssssii", $rasm_nomi, $malumot_uz, $malumot_en, $skills_uz, $skills_en, $id, $uid);

        if ($t_surov->execute()) {
            $t_surov->close();
            $link->close();
            header("Refresh: 1; URL=edit_home.php");
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

    // --- 4. Xato bo'lsa ko'rsatish ---
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
            <?php include_once("edit_home_check.php"); ?>
        </body>
        </html>
        <?php
    }

} else {
    include_once("edit_home_check.php");
}
?>
