<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}


$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id                = intval($_POST['id']               ?? 0);
    $ism               = trim($_POST['ism']               ?? '');
    $toifa             = trim($_POST['toifa']             ?? '');
    $qisqa_malumot_uz  = trim($_POST['qisqa_malumot_uz']  ?? '');
    $qisqa_malumot_en  = trim($_POST['qisqa_malumot_en']  ?? '');
    $tolik_malumot_uz  = trim($_POST['tolik_malumot_uz']  ?? '');
    $tolik_malumot_en  = trim($_POST['tolik_malumot_en']  ?? '');
    $eski_rasm         = trim($_POST['eski_rasm']         ?? '');

    // --- 1. Majburiy maydonlar ---
    if ($id <= 0) {
        $xato = "Noto'g'ri so'rov!";
    } elseif (empty($ism)) {
        $xato = "Ism Familiya bo'sh bo'lishi mumkin emas!";
    } elseif (empty($qisqa_malumot_uz)) {
        $xato = "Qisqa ma'lumot (o'zbek) bo'sh bo'lishi mumkin emas!";
    } elseif (empty($tolik_malumot_uz)) {
        $xato = "To'liq ma'lumot (o'zbek) bo'sh bo'lishi mumkin emas!";
    }

    // --- 2. Rasm (ixtiyoriy yangilash) ---
    $rasm_nomi = $eski_rasm;
    if (empty($xato) && !empty($_FILES['rasm']['name']) && $_FILES['rasm']['error'] === UPLOAD_ERR_OK) {
        $ruxsat = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($_FILES['rasm']['type'], $ruxsat)) {
            $xato = "Faqat JPG, PNG, WEBP yoki GIF rasm yuklash mumkin!";
        } elseif ($_FILES['rasm']['size'] > 2 * 1024 * 1024) {
            $xato = "Rasm hajmi 2MB dan oshmasligi kerak!";
        } else {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/student_rasmlar/";
            $kengaytma  = pathinfo($_FILES['rasm']['name'], PATHINFO_EXTENSION);
            $yangi_rasm = time() . '_' . uniqid() . '.' . $kengaytma;

            if (move_uploaded_file($_FILES['rasm']['tmp_name'], $upload_dir . $yangi_rasm)) {
                // Eski rasmni o'chirish
                if (!empty($eski_rasm) && file_exists($upload_dir . $eski_rasm)) {
                    unlink($upload_dir . $eski_rasm);
                }
                $rasm_nomi = $yangi_rasm;
            } else {
                $xato = "Rasmni yuklashda xatolik!";
            }
        }
    }

    // --- 3. Auto-tarjima ---
    if (empty($xato)) {
        include_once("translate.php");
        if (empty($qisqa_malumot_en)) {
            $qisqa_malumot_en = translateText($qisqa_malumot_uz, 'uz', 'en');
        }
        if (empty($tolik_malumot_en)) {
            $tolik_malumot_en = translateText($tolik_malumot_uz, 'uz', 'en');
        }
    }

    // --- 4. Bazaga yozish ---
    if (empty($xato)) {
        $stmt = $link->prepare("
            UPDATE students SET
                ism=?, rasm=?, toifa=?,
                qisqa_malumot_uz=?, qisqa_malumot_en=?,
                tolik_malumot_uz=?, tolik_malumot_en=?
            WHERE id=? AND user_id=?
        ");
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $stmt->bind_param(
            "sssssssii",
            $ism, $rasm_nomi, $toifa,
            $qisqa_malumot_uz, $qisqa_malumot_en,
            $tolik_malumot_uz, $tolik_malumot_en,
            $id, 
            $uid
        );

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: list_student.php?xabar=ok");
            exit;
        } else {
            $xato = "Bazaga yozishda xatolik: " . $stmt->error;
            $stmt->close();
        }
    }

    // --- 5. Xato ---
    if (!empty($xato)) {
        echo "<div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> " . htmlspecialchars($xato) . "</div>";
        header("Location: edit_student.php?id=$id&xabar=xato");
        exit;
    }

} else {
    header("Location: list_student.php");
    exit;
}
?>

