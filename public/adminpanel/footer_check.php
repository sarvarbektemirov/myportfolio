<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}



// POST orqali kelgan ma'lumotlarni tekshirish
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Xavfsiz kiritish va sanitizatsiya
    $bio_uz          = trim($_POST['bio_uz'] ?? '');
    $bio_en          = trim($_POST['bio_en'] ?? '');
    $status_uz       = trim($_POST['status_uz'] ?? '');
    $status_en       = trim($_POST['status_en'] ?? '');
    $copyright_uz    = trim($_POST['copyright_uz'] ?? '');
    $copyright_en    = trim($_POST['copyright_en'] ?? '');

    $orcid           = filter_input(INPUT_POST, "orcid", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $site_launch_date = filter_input(INPUT_POST, "site_launch_date", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $tg_link         = filter_input(INPUT_POST, "tg_link", FILTER_SANITIZE_URL);
    $wa_link         = filter_input(INPUT_POST, "wa_link", FILTER_SANITIZE_URL);
    $scopus_link     = filter_input(INPUT_POST, "scopus_link", FILTER_SANITIZE_URL);
    $scholar_link    = filter_input(INPUT_POST, "scholar_link", FILTER_SANITIZE_URL);
    $university_link = filter_input(INPUT_POST, "university_link", FILTER_SANITIZE_URL);

    // Fayl yuklash uchun tayyor
    $cv_fayl = null;

    if (!empty($_FILES['cv_fayl']['name'])) {

        $fayl_nomi      = basename($_FILES['cv_fayl']['name']);
        $fayl_nomi_safe = preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $fayl_nomi); // bo'shliq va maxsus belgilar o'zgartirildi
        $fayl_kengaytma = strtolower(pathinfo($fayl_nomi_safe, PATHINFO_EXTENSION));

        $ruxsat_etilgan = ['pdf', 'docx'];
        if (!in_array($fayl_kengaytma, $ruxsat_etilgan)) {
            echo "<h3 style='color:red;'>Faqat PDF yoki DOCX fayl yuklang!</h3>";
            include_once("add_footer.php");
            exit;
        }

        if ($_FILES['cv_fayl']['size'] > 10 * 1024 * 1024) {
            echo "<h3 style='color:red;'>Fayl hajmi 10MB dan oshmasligi kerak!</h3>";
            include_once("add_footer.php");
            exit;
        }

        // Faylni serverdagi files papkasiga saqlash
        $targetDir = __DIR__ . "/../files/"; // Corrected path
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); 
        }
        $targetPath = $targetDir . $fayl_nomi_safe;

        if (move_uploaded_file($_FILES['cv_fayl']['tmp_name'], $targetPath)) {
            $cv_fayl = "https://myportfolio.local/files/" . $fayl_nomi_safe;
        } else {
            echo "<h3 style='color:red;'>Faylni yuklashda xatolik!</h3>";
            include_once("add_footer.php");
            exit;
        }
    }

    // Tayyor so'rov
    $uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
    
    try {
        $stmt = $link->prepare("
            INSERT INTO footer 
            (bio_uz, bio_en, status_uz, status_en, copyright_uz, copyright_en, orcid, cv_fayl, site_launch_date, tg_link, wa_link, scopus_link, scholar_link, university_link, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'Unknown column') !== false) {
            // Manual column existence check to support different MySQL versions
            $cols_to_check = [
                'orcid' => "VARCHAR(255) AFTER copyright_en",
                'cv_fayl' => "VARCHAR(255) AFTER orcid",
                'site_launch_date' => "DATE AFTER cv_fayl",
                'tg_link' => "VARCHAR(255) AFTER site_launch_date",
                'wa_link' => "VARCHAR(255) AFTER tg_link",
                'scopus_link' => "VARCHAR(255) AFTER wa_link",
                'scholar_link' => "VARCHAR(255) AFTER scopus_link",
                'university_link' => "VARCHAR(255) AFTER scholar_link"
            ];

            foreach ($cols_to_check as $col => $def) {
                $check = $link->query("SHOW COLUMNS FROM footer LIKE '$col'");
                if ($check && $check->num_rows == 0) {
                    $link->query("ALTER TABLE footer ADD COLUMN $col $def");
                }
            }
            
            // Retry the prepare after fixes
            $stmt = $link->prepare("
                INSERT INTO footer 
                (bio_uz, bio_en, status_uz, status_en, copyright_uz, copyright_en, orcid, cv_fayl, site_launch_date, tg_link, wa_link, scopus_link, scholar_link, university_link, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
        } else {
            throw $e;
        }
    }

    $stmt->bind_param(
        "ssssssssssssssi",
        $bio_uz, $bio_en, $status_uz, $status_en, $copyright_uz, $copyright_en,
        $orcid, $cv_fayl, $site_launch_date, $tg_link, $wa_link,
        $scopus_link, $scholar_link, $university_link, $uid
    );

    if ($stmt->execute()) {
        $stmt->close();
        $link->close();
        header("Refresh: 1; URL=add_footer.php");
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
        echo "<div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> " . htmlspecialchars($stmt->error) . "</div>";
        $stmt->close();
        $link->close();
        include_once("add_footer.php");
        exit;
    }
} else {
    echo "<h3 style='color:red;'>Formadagi ma'lumotlar to‘liq emas!</h3>";
    include_once("add_footer.php");
    exit;
}

