<?php
session_start();
include_once("db.php");
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id']);
$check_footer = $link->query("SELECT id FROM footer WHERE user_id = $uid LIMIT 1");
if ($check_footer && $check_footer->num_rows > 0) {
    header("Location: edit_footer.php");
    exit;
}
include_once("menu.php");
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer ma'lumotlari</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>

<div class="container py-5 mt-3">
    <h2 class="page-header"><i class="fa-solid fa-shoe-prints text-primary me-2"></i> Footer ma'lumotlarini qo'shish</h2>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="form-card animatsiya1">
                <form action="footer_check.php" method="post" enctype="multipart/form-data">
                    <div class="row g-4 mb-4">
                        <!-- O'zbekcha Tavsif -->
                        <div class="col-12"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">O'zbekcha Ma'lumotlar</h5></div>
                        
                        <div class="col-12 col-md-6">
                            <label for="bio_uz" class="form-label fw-bold"><i class="fa-solid fa-align-left text-primary"></i> Qisqa Bio (UZ) <span class="required-asterisk">*</span></label>
                            <textarea name="bio_uz" id="bio_uz" class="form-control" rows="4" required placeholder="O'zbek tilida qisqa ma'lumot..."></textarea>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="status_uz" class="form-label fw-bold"><i class="fa-solid fa-signal text-success"></i> Status (UZ)</label>
                            <input type="text" name="status_uz" id="status_uz" class="form-control" placeholder="Masalan: Faol rivojlanish">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="copyright_uz" class="form-label fw-bold"><i class="fa-solid fa-copyright text-secondary"></i> Copyright (UZ)</label>
                            <input type="text" name="copyright_uz" id="copyright_uz" class="form-control" placeholder="Masalan: Barcha huquqlar himoyalangan">
                        </div>

                        <!-- English Description -->
                        <div class="col-12 mt-5"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">English Information</h5></div>
                        
                        <div class="col-12 col-md-6">
                            <label for="bio_en" class="form-label fw-bold"><i class="fa-solid fa-align-left text-secondary"></i> Short Bio (EN)</label>
                            <textarea name="bio_en" id="bio_en" class="form-control" rows="4" placeholder="Short bio in English..."></textarea>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="status_en" class="form-label fw-bold"><i class="fa-solid fa-signal text-secondary"></i> Status (EN)</label>
                            <input type="text" name="status_en" id="status_en" class="form-control" placeholder="e.g. Active Development">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="copyright_en" class="form-label fw-bold"><i class="fa-solid fa-copyright text-secondary"></i> Copyright (EN)</label>
                            <input type="text" name="copyright_en" id="copyright_en" class="form-control" placeholder="e.g. All Rights Reserved">
                        </div>

                        <!-- Links & Files -->
                        <div class="col-12 mt-5"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">Havolalar va Fayllar</h5></div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="orcid" class="form-label fw-bold">
                                <i class="fa-brands fa-orcid text-success text-center" style="width:20px;"></i> Orcid ID
                            </label>
                            <input type="text" name="orcid" id="orcid" class="form-control" placeholder="xxxx-xxxx-xxxx-xxxx">
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="cv_fayl" class="form-label fw-bold">
                                <i class="fa-solid fa-file-pdf text-danger text-center" style="width:20px;"></i> CV fayl
                            </label>
                            <input type="file" name="cv_fayl" id="cv_fayl" class="form-control">
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="site_launch_date" class="form-label fw-bold">
                                <i class="fa-solid fa-calendar-days text-info text-center" style="width:20px;"></i> Ishga tushgan sana
                            </label>
                            <input type="date" name="site_launch_date" id="site_launch_date" class="form-control">
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="tg_link" class="form-label fw-bold">
                                <i class="fa-brands fa-telegram text-primary text-center" style="width:20px;"></i> Telegram
                            </label>
                            <input type="url" name="tg_link" id="tg_link" class="form-control" placeholder="https://t.me/username">
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="wa_link" class="form-label fw-bold">
                                <i class="fa-brands fa-whatsapp text-success text-center" style="width:20px;"></i> WhatsApp
                            </label>
                            <input type="url" name="wa_link" id="wa_link" class="form-control" placeholder="https://wa.me/number">
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="scopus_link" class="form-label fw-bold">
                                <i class="fa-solid fa-book-open text-warning text-center" style="width:20px;"></i> Scopus
                            </label>
                            <input type="url" name="scopus_link" id="scopus_link" class="form-control" placeholder="https://www.scopus.com/...">
                        </div>

                        <div class="col-12 col-sm-6 col-md-6">
                            <label for="scholar_link" class="form-label fw-bold">
                                <i class="fa-brands fa-google text-danger text-center" style="width:20px;"></i> Google Scholar
                            </label>
                            <input type="url" name="scholar_link" id="scholar_link" class="form-control" placeholder="https://scholar.google.com/...">
                        </div>

                        <div class="col-12 col-sm-6 col-md-6">
                            <label for="university_link" class="form-label fw-bold">
                                <i class="fa-solid fa-building-columns text-primary text-center" style="width:20px;"></i> Universitet
                            </label>
                            <input type="url" name="university_link" id="university_link" class="form-control" placeholder="https://www.samdu.uz/...">
                        </div>

                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-submit px-5 shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Ma'lumotlarni Saqlash
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

