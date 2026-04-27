<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$surov = $link->query("SELECT * FROM home WHERE user_id = $uid LIMIT 1");
$malumot = $surov->fetch_assoc();
if (!$malumot) {
    header("Location: add_home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asosiy sahifa tahrirlash</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>

<body class="bg-light">
    <?php include_once("menu.php"); ?>

    <div class="container py-5 mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4 page-header">
            <h2 class="mb-0 fw-bold"><i class="fa-solid fa-house-user text-primary me-2"></i> Asosiy sahifa ma'lumotlarini tahrirlash</h2>
            <a href="edit_home.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Orqaga qaytish</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="form-card">
                    <form action="edit_home_check1.php" method="post" enctype="multipart/form-data">

                        <!-- Mavjud rasm -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa-solid fa-image text-primary"></i> Mavjud rasm
                                </label>
                                <div class="mb-3 p-2 border rounded bg-light text-center">
                                    <?php if (!empty($malumot['rasm'])): ?>
                                        <img src="../files/<?= htmlspecialchars($malumot['rasm']) ?>"
                                            alt="Mavjud rasm" style="max-width: 100%; height: 200px; object-fit: cover;" class="rounded shadow-sm">
                                    <?php else: ?>
                                        <div class="py-4 text-muted">
                                            <i class="fa-solid fa-image-slash fa-2x mb-2 d-block"></i>
                                            Rasm yuklanmagan
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <label for="a" class="form-label mt-2">
                                    <i class="fa-solid fa-upload text-success"></i> Yangi rasm yuklash 
                                    <span class="text-muted-small fw-normal ms-1">(o'zgartirmasangiz bo'sh qoldiring)</span>
                                </label>
                                <input type="file" name="a" id="a" class="form-control" accept="image/*">
                            </div>

                            <!-- Ma'lumot UZ -->
                            <div class="col-md-6 d-flex flex-column">
                                <label for="b" class="form-label">
                                    <i class="fa-solid fa-language text-info"></i> Ma'lumot (UZ)
                                </label>
                                <textarea name="b" id="b" class="form-control flex-grow-1" rows="8" placeholder="Ma'lumotlarni o'zbek tilida kiriting..."><?= htmlspecialchars($malumot['malumot_uz'] ?? '') ?></textarea>
                            </div>

                            <!-- Ma'lumot EN -->
                            <div class="col-md-6 d-flex flex-column">
                                <label for="b_en" class="form-label">
                                    <i class="fa-solid fa-globe text-secondary"></i> Information (EN)
                                </label>
                                <small class="text-muted-small d-block mb-2">Bo'sh qolsa yoki o'zgartirmasangiz avtomatik yangilanadi</small>
                                <textarea name="b_en" id="b_en" class="form-control flex-grow-1" rows="8" placeholder="Information in English..."><?= htmlspecialchars($malumot['malumot_en'] ?? '') ?></textarea>
                            </div>

                            <!-- Skills UZ -->
                            <div class="col-md-6 d-flex flex-column mt-4">
                                <label for="skills_uz" class="form-label">
                                    <i class="fa-solid fa-bolt text-warning"></i> Skillaringiz (UZ)
                                </label>
                                <small class="text-muted-small d-block mb-2">Faqat skill nomlarini vergul bilan ajratib yozing (masalan: PHP, Laravel, Photoshop). Ortiqcha gap-so'zlar shart emas.</small>
                                <textarea name="skills_uz" id="skills_uz" class="form-control" rows="4" placeholder="PHP, Laravel, MySQL..."><?= htmlspecialchars($malumot['skills_uz'] ?? '') ?></textarea>
                            </div>

                            <!-- Skills EN -->
                            <div class="col-md-6 d-flex flex-column mt-4">
                                <label for="skills_en" class="form-label">
                                    <i class="fa-solid fa-bolt text-warning"></i> Skills (EN)
                                </label>
                                <small class="text-muted-small d-block mb-2">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                                <textarea name="skills_en" id="skills_en" class="form-control" rows="4" placeholder="PHP, Laravel, MySQL..."><?= htmlspecialchars($malumot['skills_en'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- TUGMALAR -->
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

