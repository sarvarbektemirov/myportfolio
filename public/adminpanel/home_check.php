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
    $malumot_en = trim($_POST['b_en'] ?? '');
    $skills_en  = trim($_POST['skills_en'] ?? '');

    if (empty($malumot_uz)) {
        $xato = "O'zbek tilidagi ma'lumot maydoni bo'sh bo'lishi mumkin emas!";
    }

    // --- 2. Rasm tekshiruvi (name="a") ---
    $rasm_nomi = "";
    if (empty($xato)) {
        if (empty($_FILES['a']['name'])) {
            $xato = "Rasm tanlanmagan!";
        } else {
            $ruxsat_etilgan = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fayl_turi = $_FILES['a']['type'];
            $fayl_hajmi = $_FILES['a']['size'];

            if (!in_array($fayl_turi, $ruxsat_etilgan)) {
                $xato = "Faqat JPG, PNG, WEBP yoki GIF rasm yuklash mumkin!";
            } elseif ($fayl_hajmi > 2 * 1024 * 1024) {
                $xato = "Rasm hajmi 2MB dan oshmasligi kerak!";
            } else {
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/files/";
                $kengaytma = pathinfo($_FILES['a']['name'], PATHINFO_EXTENSION);
                $rasm_nomi = time() . '_' . uniqid() . '.' . $kengaytma;

                if (!move_uploaded_file($_FILES['a']['tmp_name'], $upload_dir . $rasm_nomi)) {
                    $xato = "Rasmni yuklashda xatolik yuz berdi!";
                }
            }
        }
    }

    // --- 3. Xato yo'q bo'lsa — tarjima va bazaga yozish ---
    if (empty($xato)) {
        include_once("translate.php");

        // Inglizcha bo'sh bo'lsa — avtomatik tarjima
        if (empty($malumot_en)) {
            $malumot_en = translateText($malumot_uz, 'uz', 'en');
        }

        if (empty($skills_en) && !empty($skills_uz)) {
            $skills_en = translateText($skills_uz, 'uz', 'en');
        }

        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $t_surov = $link->prepare("
            INSERT INTO home (rasm, malumot_uz, malumot_en, skills_uz, skills_en, user_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $t_surov->bind_param("sssssi", $rasm_nomi, $malumot_uz, $malumot_en, $skills_uz, $skills_en, $uid);

        if ($t_surov->execute()) {
            $t_surov->close();
            $link->close();
            header("Refresh: 1; URL=add_home.php");
            echo "<div class='alert alert-success animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-circle-check me-2'></i> Ma'lumotlar muvaffaqiyatli qo'shildi!</div>";
            exit;
        } else {
            $xato = "Bazaga yozishda xatolik: " . $t_surov->error;
            $t_surov->close();
            $link->close();
        }
    }

    // --- 4. Xato bo'lsa ko'rsatish ---
    if (!empty($xato)) {
        echo "<div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> " . htmlspecialchars($xato) . "</div>";
        include_once("add_home.php");
    }
} else {
    include_once("add_home.php");
}
?>
