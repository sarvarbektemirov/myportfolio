<?php
include_once("db.php");
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

if (empty($_POST['id'])) {
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
            <i class='fa-solid fa-triangle-exclamation me-2'></i> ID topilmadi!
        </div>
    </body>
    </html>
    <?php
    exit;
}

$id              = (int)$_POST['id'];
$bio_uz          = trim($_POST['bio_uz'] ?? '');
$bio_en          = trim($_POST['bio_en'] ?? '');
$status_uz       = trim($_POST['status_uz'] ?? '');
$status_en       = trim($_POST['status_en'] ?? '');
$copyright_uz    = trim($_POST['copyright_uz'] ?? '');
$copyright_en    = trim($_POST['copyright_en'] ?? '');

$orcid           = htmlspecialchars(trim($_POST['orcid'] ?? ''), ENT_QUOTES);
$tg_link         = htmlspecialchars(trim($_POST['tg_link'] ?? ''), ENT_QUOTES);
$wa_link         = htmlspecialchars(trim($_POST['wa_link'] ?? ''), ENT_QUOTES);
$scopus_link     = htmlspecialchars(trim($_POST['scopus_link'] ?? ''), ENT_QUOTES);
$scholar_link    = htmlspecialchars(trim($_POST['scholar_link'] ?? ''), ENT_QUOTES);
$university_link = htmlspecialchars(trim($_POST['university_link'] ?? ''), ENT_QUOTES);
$site_launch_date = $_POST['site_launch_date'] ?? '';
$cv_fayl         = $_POST['old_cv_fayl'] ?? '';

// Yangi CV fayl yuklansa
if (!empty($_FILES['cv_fayl']['name'])) {
    $upload_dir = "../files/"; 
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    $file_name = time() . "_" . preg_replace("/[^A-Za-z0-9_\-\.]/", "_", basename($_FILES['cv_fayl']['name']));
    $file_path = $upload_dir . $file_name;
    if (move_uploaded_file($_FILES['cv_fayl']['tmp_name'], $file_path)) {
        $cv_fayl = "https://myportfolio.local/files/" . $file_name;
    }
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$stmt = $link->prepare("UPDATE footer SET 
    bio_uz = ?, 
    bio_en = ?, 
    status_uz = ?, 
    status_en = ?, 
    copyright_uz = ?, 
    copyright_en = ?, 
    orcid = ?, 
    cv_fayl = ?, 
    site_launch_date = ?,
    tg_link = ?, 
    wa_link = ?, 
    scopus_link = ?, 
    scholar_link = ?, 
    university_link = ? 
    WHERE id = ? AND user_id = ?");

if (!$stmt) {
    die("Prepare xatosi: " . $link->error);
}

$stmt->bind_param("ssssssssssssssii",
    $bio_uz, $bio_en, $status_uz, $status_en, $copyright_uz, $copyright_en,
    $orcid, $cv_fayl, $site_launch_date, $tg_link, $wa_link,
    $scopus_link, $scholar_link, $university_link, $id, $uid
);

if ($stmt->execute()) {
    $stmt->close();
    $link->close();
    header("Location: edit_footer.php?xabar=ok");
    exit;
} else {
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
            <i class='fa-solid fa-triangle-exclamation me-2'></i> Xatolik: <?= htmlspecialchars($stmt->error) ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}
