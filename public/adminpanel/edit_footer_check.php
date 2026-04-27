<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <title>Footer tahrirlash</title>
</head>

<body>
    <?php
    include_once("menu.php");
    

    if (empty($_POST['id'])) {
        echo "<div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> ID topilmadi!</div>";
        exit;
    }

    $id = (int)$_POST['id'];
    $stmt = $link->prepare("SELECT * FROM footer WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "<div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-circle-xmark me-2'></i> Ma'lumot topilmadi!</div>";
        exit;
    }
    ?>

    <div class="container py-5 mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4 page-header">
            <h2 class="mb-0"><i class="fa-solid fa-circle-info text-primary me-2"></i> Footer ma'lumotlarini tahrirlash</h2>
            <a href="edit_footer.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Orqaga qaytish</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="form-card animatsiya1">
                    <form action="edit_footer_check1.php" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="row g-4 mb-4">
                            <!-- O'zbekcha Tavsif -->
                            <div class="col-12"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">O'zbekcha Ma'lumotlar</h5></div>
                            
                            <div class="col-12 col-md-6">
                                <label for="bio_uz" class="form-label fw-bold"><i class="fa-solid fa-align-left text-primary"></i> Qisqa Bio (UZ) <span class="required-asterisk">*</span></label>
                                <textarea name="bio_uz" id="bio_uz" class="form-control" rows="4" required placeholder="O'zbek tilida qisqa ma'lumot..."><?= htmlspecialchars($row['bio_uz'] ?? '', ENT_QUOTES) ?></textarea>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="status_uz" class="form-label fw-bold"><i class="fa-solid fa-signal text-success"></i> Status (UZ)</label>
                                <input type="text" name="status_uz" id="status_uz" class="form-control" placeholder="Masalan: Faol rivojlanish" value="<?= htmlspecialchars($row['status_uz'] ?? '', ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="copyright_uz" class="form-label fw-bold"><i class="fa-solid fa-copyright text-secondary"></i> Copyright (UZ)</label>
                                <input type="text" name="copyright_uz" id="copyright_uz" class="form-control" placeholder="Masalan: Barcha huquqlar himoyalangan" value="<?= htmlspecialchars($row['copyright_uz'] ?? '', ENT_QUOTES) ?>">
                            </div>

                            <!-- English Description -->
                            <div class="col-12 mt-5"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">English Information</h5></div>
                            
                            <div class="col-12 col-md-6">
                                <label for="bio_en" class="form-label fw-bold"><i class="fa-solid fa-align-left text-secondary"></i> Short Bio (EN)</label>
                                <textarea name="bio_en" id="bio_en" class="form-control" rows="4" placeholder="Short bio in English..."><?= htmlspecialchars($row['bio_en'] ?? '', ENT_QUOTES) ?></textarea>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="status_en" class="form-label fw-bold"><i class="fa-solid fa-signal text-secondary"></i> Status (EN)</label>
                                <input type="text" name="status_en" id="status_en" class="form-control" placeholder="e.g. Active Development" value="<?= htmlspecialchars($row['status_en'] ?? '', ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="copyright_en" class="form-label fw-bold"><i class="fa-solid fa-copyright text-secondary"></i> Copyright (EN)</label>
                                <input type="text" name="copyright_en" id="copyright_en" class="form-control" placeholder="e.g. All Rights Reserved" value="<?= htmlspecialchars($row['copyright_en'] ?? '', ENT_QUOTES) ?>">
                            </div>

                            <!-- Links & Files -->
                            <div class="col-12 mt-5"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">Havolalar va Fayllar</h5></div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-brands fa-orcid text-success"></i> Orcid ID:</label>
                                <input type="text" name="orcid" class="form-control" placeholder="0000-0000-0000-0000" value="<?= htmlspecialchars($row['orcid'] ?? '', ENT_QUOTES) ?>">
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label fw-bold"><i class="fa-solid fa-file-pdf text-danger"></i> CV Fayl:</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-grow-1">
                                        <input type="file" name="cv_fayl" class="form-control">
                                    </div>
                                    <?php
include_once('db.php'); if (!empty($row['cv_fayl'])): ?>
                                        <a href="<?= htmlspecialchars($row['cv_fayl']) ?>" target="_blank" class="btn btn-outline-primary shadow-sm" title="Hozirgi faylni ko'rish">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    <?php
include_once('db.php'); endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-12 col-md-3">
                                <label for="site_launch_date" class="form-label fw-bold"><i class="fa-solid fa-calendar-days text-info"></i> Ishga tushgan sana:</label>
                                <input type="date" name="site_launch_date" id="site_launch_date" class="form-control" value="<?= htmlspecialchars($row['site_launch_date'] ?? '', ENT_QUOTES) ?>">
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <!-- SOCIAL LINKS -->
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-brands fa-telegram text-info"></i> Telegram:</label>
                                <input type="text" name="tg_link" class="form-control" placeholder="https://t.me/username" value="<?= htmlspecialchars($row['tg_link'] ?? '', ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-brands fa-whatsapp text-success"></i> WhatsApp:</label>
                                <input type="text" name="wa_link" class="form-control" placeholder="https://wa.me/number" value="<?= htmlspecialchars($row['wa_link'] ?? '', ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-magnifying-glass text-warning"></i> Scopus:</label>
                                <input type="text" name="scopus_link" class="form-control" placeholder="Scopus link..." value="<?= htmlspecialchars($row['scopus_link'] ?? '', ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-sm-6 col-md-6">
                                <label class="form-label fw-bold"><i class="fa-solid fa-graduation-cap text-danger"></i> Google Scholar:</label>
                                <input type="text" name="scholar_link" class="form-control" placeholder="Scholar link..." value="<?= htmlspecialchars($row['scholar_link'] ?? '', ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-sm-6 col-md-6">
                                <label class="form-label fw-bold"><i class="fa-solid fa-university text-primary"></i> Universitet:</label>
                                <input type="text" name="university_link" class="form-control" placeholder="University link..." value="<?= htmlspecialchars($row['university_link'] ?? '', ENT_QUOTES) ?>">
                            </div>
                        </div>

                        <div class="text-center mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-submit px-5 shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i> O'zgarishlarni saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.js"></script>
</body>

</html>

