<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}



$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id         = intval($_POST['id'] ?? 0);
    $nom        = trim($_POST['nom']        ?? '');
    $anatatsiya = trim($_POST['anatatsiya'] ?? '');
    $muallif    = trim($_POST['muallif']    ?? '');
    $jurnal     = trim($_POST['jurnal']     ?? '');
    $yil        = intval($_POST['yil']      ?? 0);
    $uyil       = trim($_POST['uyil']       ?? '');
    $doi        = trim($_POST['doi']        ?? '');
    $sahifa     = trim($_POST['sahifa']     ?? '');
    $til        = trim($_POST['til']        ?? '');
    $baza       = trim($_POST['baza']       ?? '');
    $tur        = trim($_POST['tur']        ?? '');
    $cite       = trim($_POST['cite']       ?? '');
    $cite_f     = trim($_POST['cite_f']     ?? '');

    $eski_fayl1 = trim($_POST['eski_fayl1'] ?? '');
    $eski_fayl2 = trim($_POST['eski_fayl2'] ?? '');
    $eski_fayl3 = trim($_POST['eski_fayl3'] ?? '');

    // --- 1. Majburiy maydonlar ---
    if ($id <= 0) {
        $xato = "Noto'g'ri so'rov!";
    } elseif (empty($nom)) {
        $xato = "Adabiyot (maqola) nomi bo'sh bo'lishi mumkin emas!";
    } elseif (empty($anatatsiya)) {
        $xato = "Annotatsiya maydoni bo'sh bo'lishi mumkin emas!";
    }

    // --- 2. Fayllarni qayta ishlash ---
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/my_files/";

    // fayl1
    $fayl1_nomi = $eski_fayl1; // default: eskisi
    if (!empty($_FILES['fayl1']['name']) && $_FILES['fayl1']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['fayl1']['size'] > 20 * 1024 * 1024) {
            $xato = "fayl1 hajmi 20MB dan oshmasligi kerak!";
        } else {
            $ext1       = pathinfo($_FILES['fayl1']['name'], PATHINFO_EXTENSION);
            $fayl1_nomi = $id . "_asl." . $ext1;
            if (!move_uploaded_file($_FILES['fayl1']['tmp_name'], $upload_dir . $fayl1_nomi)) {
                $xato = "PDF faylni saqlashda xatolik!";
                $fayl1_nomi = $eski_fayl1;
            }
        }
    }

    // fayl2
    $fayl2_nomi = $eski_fayl2;
    if (empty($xato) && !empty($_FILES['fayl2']['name']) && $_FILES['fayl2']['error'] === UPLOAD_ERR_OK) {
        $ext2       = pathinfo($_FILES['fayl2']['name'], PATHINFO_EXTENSION);
        $fayl2_nomi = $id . "_word." . $ext2;
        if (!move_uploaded_file($_FILES['fayl2']['tmp_name'], $upload_dir . $fayl2_nomi)) {
            $fayl2_nomi = $eski_fayl2;
        }
    }

    // fayl3
    $fayl3_nomi = $eski_fayl3;
    if (empty($xato) && !empty($_FILES['fayl3']['name']) && $_FILES['fayl3']['error'] === UPLOAD_ERR_OK) {
        $ext3       = pathinfo($_FILES['fayl3']['name'], PATHINFO_EXTENSION);
        $fayl3_nomi = $id . "_tarjima." . $ext3;
        if (!move_uploaded_file($_FILES['fayl3']['tmp_name'], $upload_dir . $fayl3_nomi)) {
            $fayl3_nomi = $eski_fayl3;
        }
    }

    // --- 3. Bazaga yozish ---
    if (empty($xato)) {
        $stmt = $link->prepare("
            UPDATE publication SET
                nom=?, anatatsiya=?, muallif=?, jurnal=?,
                yil=?, uyil=?, sahifa=?, doi=?,
                til=?, baza=?, tur=?, cite=?, cite_f=?,
                fayl1=?, fayl2=?, fayl3=?
            WHERE id=? AND user_id=?
        ");
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $stmt->bind_param(
            "ssssississsssssii",
            $nom,
            $anatatsiya,
            $muallif,
            $jurnal,
            $yil,
            $uyil,
            $sahifa,
            $doi,
            $til,
            $baza,
            $tur,
            $cite,
            $cite_f,
            $fayl1_nomi,
            $fayl2_nomi,
            $fayl3_nomi,
            $id,
            $uid
        );
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: list_publication.php?xabar=ok");
            exit;
        } else {
            $xato = "Bazaga yozishda xatolik: " . $stmt->error;
            $stmt->close();
        }
    }

    // --- 4. Xato ko'rsatish ---
    if (!empty($xato)) {
        echo "<div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> " . htmlspecialchars($xato) . "</div>";
        // Formaga qaytish
        header("Location: edit_publication.php?id=$id&xabar=xato");
        exit;
    }
} else {
    header("Location: list_publication.php");
    exit;
}

