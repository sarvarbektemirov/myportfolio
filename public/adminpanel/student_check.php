<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}



$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Majburiy maydonlar ---
    $ism               = trim($_POST['ism']               ?? '');
    $toifa             = trim($_POST['toifa']             ?? '');
    $qisqa_malumot_uz  = trim($_POST['qisqa_malumot_uz']  ?? '');
    $qisqa_malumot_en  = trim($_POST['qisqa_malumot_en']  ?? '');
    $tolik_malumot_uz  = trim($_POST['tolik_malumot_uz']  ?? '');
    $tolik_malumot_en  = trim($_POST['tolik_malumot_en']  ?? '');

    if (empty($ism)) {
        $xato = "Ism Familiya maydoni bo'sh bo'lishi mumkin emas!";
    } elseif (empty($qisqa_malumot_uz)) {
        $xato = "Qisqa ma'lumot (o'zbek) maydoni bo'sh bo'lishi mumkin emas!";
    } elseif (empty($tolik_malumot_uz)) {
        $xato = "To'liq ma'lumot (o'zbek) maydoni bo'sh bo'lishi mumkin emas!";
    }

    // --- 2. Rasm tekshiruvi ---
    $rasm_nomi = "";
    if (empty($xato)) {
        if (empty($_FILES['rasm']['name']) || $_FILES['rasm']['error'] !== UPLOAD_ERR_OK) {
            $xato = "Rasm tanlanmagan!";
        } else {
            $ruxsat = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fayl_turi  = $_FILES['rasm']['type'];
            $fayl_hajmi = $_FILES['rasm']['size'];

            if (!in_array($fayl_turi, $ruxsat)) {
                $xato = "Faqat JPG, PNG, WEBP yoki GIF rasm yuklash mumkin!";
            } elseif ($fayl_hajmi > 2 * 1024 * 1024) {
                $xato = "Rasm hajmi 2MB dan oshmasligi kerak!";
            } else {
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/student_rasmlar/";
                $kengaytma  = pathinfo($_FILES['rasm']['name'], PATHINFO_EXTENSION);
                $rasm_nomi  = time() . '_' . uniqid() . '.' . $kengaytma;

                if (!move_uploaded_file($_FILES['rasm']['tmp_name'], $upload_dir . $rasm_nomi)) {
                    $xato = "Rasmni yuklashda xatolik! /student_rasmlar/ papkasi mavjudligini tekshiring.";
                    $rasm_nomi = "";
                }
            }
        }
    }

    // --- 3. Takror tekshiruvi ---
    if (empty($xato)) {
        $check = $link->prepare("SELECT id FROM students WHERE ism=? AND toifa=? AND user_id=? LIMIT 1");
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $check->bind_param("ssi", $ism, $toifa, $uid);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $xato = "Bu student bu toifada oldin qo'shilgan!";
        }
        $check->close();
    }

    // --- 4. Auto-tarjima ---
    if (empty($xato)) {
        include_once("translate.php");

        if (empty($qisqa_malumot_en)) {
            $qisqa_malumot_en = translateText($qisqa_malumot_uz, 'uz', 'en');
        }
        if (empty($tolik_malumot_en)) {
            $tolik_malumot_en = translateText($tolik_malumot_uz, 'uz', 'en');
        }
    }

    // --- 5. Bazaga yozish ---
    if (empty($xato)) {
        $stmt = $link->prepare("
            INSERT INTO students (ism, rasm, toifa, qisqa_malumot_uz, qisqa_malumot_en, tolik_malumot_uz, tolik_malumot_en, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $stmt->bind_param(
            "sssssssi",
            $ism, $rasm_nomi, $toifa,
            $qisqa_malumot_uz, $qisqa_malumot_en,
            $tolik_malumot_uz, $tolik_malumot_en,
            $uid
        );

        if ($stmt->execute()) {
            $stmt->close();
            header("Refresh: 1; URL=add_student.php?xabar=ok");
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
                    <i class='fa-solid fa-circle-check me-2'></i> Student muvaffaqiyatli qo'shildi!
                </div>
            </body>
            </html>
            <?php
            exit;
        } else {
            $xato = "Bazaga yozishda xatolik: " . $stmt->error;
            $stmt->close();
        }
    }

    // --- 6. Xato ko'rsatish ---
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
            <?php include_once("add_student.php"); ?>
        </body>
        </html>
        <?php
    }

} else {
    include_once("add_student.php");
}
?>

