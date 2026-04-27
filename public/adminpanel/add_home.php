<?php
session_start();
include_once("db.php");
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id']);
$check_home = $link->query("SELECT id FROM home WHERE user_id = $uid LIMIT 1");
if ($check_home && $check_home->num_rows > 0) {
    header("Location: edit_home.php");
    exit;
}
include_once("menu.php");
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home ma'lumotlari</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>

<body>

<div class="container py-5 mt-3">
    <h2 class="page-header"><i class="fa-solid fa-house-chimney-window text-primary me-2"></i> Asosiy sahifa ma'lumotlari</h2>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="form-card">
                <form action="home_check.php" method="post" enctype="multipart/form-data">
                    
                    <!-- 1-QATOR: Rasm -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="a" class="form-label">
                                <i class="fa-solid fa-image text-primary"></i> Shaxsiy rasmingiz
                            </label>
                            <input type="file" name="a" id="a" class="form-control" accept="image/jpeg, image/png, image/webp">
                            <small class="text-muted-small mt-2 d-block"><i class="fa-solid fa-circle-info me-1"></i> Tavsiya etiladi: JPG, PNG, WEBP — max 2MB</small>
                        </div>
                    </div>

                    <!-- 2-QATOR: Matnlar -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6 d-flex flex-column">
                            <label for="b" class="form-label">
                                <i class="fa-solid fa-language text-success"></i> O'zingiz haqingizda (O'zbek)
                                <span class="required-asterisk">*</span>
                            </label>
                            <small class="text-muted-small mb-2 pt-1">Asosiy sahifada chiqadigan ma'lumotlarni o'zbek tilida kiriting</small>
                            <textarea
                                name="b" id="b"
                                class="form-control flex-grow-1"
                                style="min-height:220px; resize:vertical;"
                                placeholder="O'zbek tilida yozing..."
                                onkeydown="if(event.key==='Enter'){event.preventDefault(); this.value+='. ';}"></textarea>
                        </div>

                        <div class="col-md-6 d-flex flex-column">
                            <label for="b_en" class="form-label">
                                <i class="fa-solid fa-earth-americas text-info"></i> About yourself (English)
                            </label>
                            <small class="text-muted-small mb-2 pt-1">Bo'sh qolsa o'zbek matndan avtomatik tarjima qilinadi</small>
                            <textarea
                                name="b_en" id="b_en"
                                class="form-control flex-grow-1"
                                style="min-height:220px; resize:vertical;"
                                placeholder="Write in English or leave empty..."
                                onkeydown="if(event.key==='Enter'){event.preventDefault(); this.value+='. ';}"></textarea>
                        </div>
                    </div>

                    <!-- 3-QATOR: Skilllar -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6 d-flex flex-column">
                            <label for="skills_uz" class="form-label">
                                <i class="fa-solid fa-bolt text-warning"></i> Skillaringiz (O'zbek)
                            </label>
                            <small class="text-muted-small mb-2 pt-1">Faqat skill nomlarini vergul bilan ajratib yozing (masalan: PHP, Laravel, Photoshop). Ortiqcha gap-so'zlar shart emas.</small>
                            <textarea
                                name="skills_uz" id="skills_uz"
                                class="form-control"
                                style="min-height:100px; resize:vertical;"
                                placeholder="PHP, JavaScript, Teamwork..."></textarea>
                        </div>

                        <div class="col-md-6 d-flex flex-column">
                            <label for="skills_en" class="form-label">
                                <i class="fa-solid fa-bolt text-warning"></i> Your Skills (English)
                            </label>
                            <small class="text-muted-small mb-2 pt-1">Bo'sh qolsa o'zbek matndan avtomatik tarjima qilinadi</small>
                            <textarea
                                name="skills_en" id="skills_en"
                                class="form-control"
                                style="min-height:100px; resize:vertical;"
                                placeholder="PHP, JavaScript, Teamwork..."></textarea>
                        </div>
                    </div>

                    <!-- TUGMA -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-submit">
                            <i class="fa-solid fa-cloud-arrow-up me-2"></i> Saqlash
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

