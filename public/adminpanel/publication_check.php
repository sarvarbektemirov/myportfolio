<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}



$xato = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Majburiy matn maydonlarni tekshirish ---
    $nom       = trim($_POST['nom']        ?? '');
    $anatatsiya= trim($_POST['anatatsiya'] ?? '');
    $muallif   = trim($_POST['muallif']    ?? '');
    $jurnal    = trim($_POST['jurnal']     ?? '');
    $yil       = intval($_POST['yil']      ?? 0);
    $uyil      = trim($_POST['uyil']       ?? '');
    $doi       = trim($_POST['doi']        ?? '');
    $sahifa    = trim($_POST['sahifa']     ?? '');
    $til       = trim($_POST['til']        ?? '');
    $baza      = trim($_POST['baza']       ?? '');
    $tur       = trim($_POST['tur']        ?? '');
    $cite      = trim($_POST['cite']       ?? '');
    $cite_f    = trim($_POST['cite_f']     ?? '');

    if (empty($nom)) {
        $xato = "Adabiyot (maqola) nomi maydoni bo'sh bo'lishi mumkin emas!";
    } elseif (empty($anatatsiya)) {
        $xato = "Annotatsiya maydoni bo'sh bo'lishi mumkin emas!";
    }

    // --- 2. PDF fayl tekshiruvi (fayl1 majburiy) ---
    $fayl1_nomi = "";
    $fayl2_nomi = "";
    $fayl3_nomi = "";

    if (empty($xato)) {
        if (empty($_FILES['fayl1']['name'])) {
            $xato = "PDF fayl tanlanmagan!";
        } else {
            $ruxsat_pdf  = ['application/pdf', 'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $fayl1_turi  = $_FILES['fayl1']['type'];
            $fayl1_hajmi = $_FILES['fayl1']['size'];

            if (!in_array($fayl1_turi, $ruxsat_pdf)) {
                $xato = "Faqat PDF, DOC yoki DOCX fayl yuklash mumkin!";
            } elseif ($fayl1_hajmi > 20 * 1024 * 1024) {
                $xato = "Fayl hajmi 20MB dan oshmasligi kerak!";
            }
        }
    }

    // --- 3. Xato yo'q bo'lsa — takror tekshirish ---
    if (empty($xato)) {
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $check = $link->prepare("SELECT id FROM publication WHERE nom=? AND muallif=? AND user_id=? LIMIT 1");
        $check->bind_param("ssi", $nom, $muallif, $uid);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $xato = "Bu ma'lumotlar oldin yuklangan!";
        }
        $check->close();
    }

    // --- 4. Xato yo'q bo'lsa — fayl nomlash va yuklash ---
    if (empty($xato)) {
        $res     = $link->query("SELECT MAX(id) as max_id FROM publication");
        $row     = $res->fetch_assoc();
        $next_id = ($row['max_id'] ?? 0) + 1;

        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/my_files/";

        // fayl1 (PDF/DOC — majburiy)
        $ext1      = pathinfo($_FILES['fayl1']['name'], PATHINFO_EXTENSION);
        $fayl1_nomi = $next_id . "_asl." . $ext1;
        if (!move_uploaded_file($_FILES['fayl1']['tmp_name'], $upload_dir . $fayl1_nomi)) {
            $xato = "PDF faylni yuklashda xatolik yuz berdi!";
            $fayl1_nomi = "";
        }
    }

    // fayl2 (Word — ixtiyoriy)
    if (empty($xato) && !empty($_FILES['fayl2']['name']) && $_FILES['fayl2']['error'] === UPLOAD_ERR_OK) {
        $res     = $link->query("SELECT MAX(id) as max_id FROM publication");
        $row     = $res->fetch_assoc();
        $next_id = ($row['max_id'] ?? 0) + 1;

        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/my_files/";
        $ext2      = pathinfo($_FILES['fayl2']['name'], PATHINFO_EXTENSION);
        $fayl2_nomi = $next_id . "_word." . $ext2;
        if (!move_uploaded_file($_FILES['fayl2']['tmp_name'], $upload_dir . $fayl2_nomi)) {
            $fayl2_nomi = ""; // ixtiyoriy, xatoni to'xtatmaymiz
        }
    }

    // fayl3 (Tarjima — ixtiyoriy)
    if (empty($xato) && !empty($_FILES['fayl3']['name']) && $_FILES['fayl3']['error'] === UPLOAD_ERR_OK) {
        $res     = $link->query("SELECT MAX(id) as max_id FROM publication");
        $row     = $res->fetch_assoc();
        $next_id = ($row['max_id'] ?? 0) + 1;

        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/my_files/";
        $ext3      = pathinfo($_FILES['fayl3']['name'], PATHINFO_EXTENSION);
        $fayl3_nomi = $next_id . "_tarjima." . $ext3;
        if (!move_uploaded_file($_FILES['fayl3']['tmp_name'], $upload_dir . $fayl3_nomi)) {
            $fayl3_nomi = ""; // ixtiyoriy
        }
    }

    // --- 5. Xato yo'q bo'lsa — bazaga yozish ---
    if (empty($xato)) {
        $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
        $stmt = $link->prepare("
            INSERT INTO publication
                (nom, anatatsiya, muallif, jurnal, yil, uyil, sahifa, doi, til, baza, tur, cite, cite_f, fayl1, fayl2, fayl3, user_id)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssisssssssssssi",
            $nom, $anatatsiya, $muallif, $jurnal,
            $yil, $uyil, $sahifa, $doi,
            $til, $baza, $tur, $cite, $cite_f,
            $fayl1_nomi, $fayl2_nomi, $fayl3_nomi,
            $uid
        );

        if ($stmt->execute()) {
            $stmt->close();
            $link->close();
            header("Refresh: 1; URL=add_publication.php");
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
                    <i class='fa-solid fa-circle-check me-2'></i> Ma'lumotlar muvaffaqiyatli qo'shildi!
                </div>
            </body>
            </html>
            <?php
            exit;
        } else {
            $xato = "Bazaga yozishda xatolik: " . $stmt->error;
            $stmt->close();
            $db->close();
        }
    }

    // --- 6. Xato bo'lsa — xatoni ko'rsatib formaga qaytish ---
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
            <?php include_once("add_publication.php"); ?>
        </body>
        </html>
        <?php
    }

} else {
    include_once("add_publication.php");
}
?>

