<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

if (isset($link) && $link instanceof mysqli) {
    $surov = $link->query("SELECT * FROM home WHERE user_id = $uid LIMIT 1");
    $malumot = $surov ? $surov->fetch_assoc() : null;
} else {
    $malumot = null;
}

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
    <title>Asosiy sahifa ma'lumotlari</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
<?php include_once("menu.php"); ?>

<?php if (isset($_GET['res']) && $_GET['res'] === 'ok'): ?>
    <div class="alert alert-success animatsiya1 container mt-4 border-0 shadow-sm">
        <i class="fa-solid fa-circle-check me-2"></i> Ma'lumotlar muvaffaqiyatli tahrirlandi!
    </div>
<?php endif; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0 fw-bold"><i class="fa-solid fa-house-user text-primary me-2"></i> Asosiy sahifa ma'lumotlari</h2>
        <a href="edit_home_check.php" class="btn btn-submit px-4 shadow">
            <i class="fa-solid fa-pen-to-square me-2"></i> Tahrirlash
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="form-card animatsiya1 p-4">
                <div class="row align-items-center">
                    <!-- Rasm -->
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        <div class="info-label text-center mb-3">Mavjud rasm</div>
                        <?php if (!empty($malumot['rasm'])): ?>
                            <img src="../files/<?= htmlspecialchars($malumot['rasm']) ?>" class="preview-img-box" alt="Main Image">
                        <?php else: ?>
                            <div class="preview-img-box bg-light d-flex align-items-center justify-content-center mx-auto border border-dashed">
                                <i class="fa-solid fa-image fa-3x text-muted opacity-50"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Ma'lumot matni -->
                    <div class="col-md-8">
                        <div class="mb-4">
                            <div class="info-label"><i class="fa-solid fa-language me-1"></i> Ma'lumot (UZ)</div>
                            <div class="p-3 rounded-3 border">
                                <?= !empty($malumot['malumot_uz']) ? nl2br(htmlspecialchars($malumot['malumot_uz'])) : '<span class="text-muted italic">Ma\'lumot kiritilmagan</span>' ?>
                            </div>
                        </div>
                        <div>
                            <div class="info-label"><i class="fa-solid fa-globe me-1"></i> Ma'lumot (EN)</div>
                            <div class="p-3 rounded-3 border">
                                <?= !empty($malumot['malumot_en']) ? nl2br(htmlspecialchars($malumot['malumot_en'])) : '<span class="text-muted italic">Information not entered</span>' ?>
                            </div>
                        </div>

                        <!-- Skills -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="info-label"><i class="fa-solid fa-bolt text-warning me-1"></i> Skilllar (UZ)</div>
                                <div class="p-3 rounded-3 border bg-light">
                                    <?= !empty($malumot['skills_uz']) ? htmlspecialchars($malumot['skills_uz']) : '<span class="text-muted italic">Kiritilmagan</span>' ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="info-label"><i class="fa-solid fa-bolt text-warning me-1"></i> Skills (EN)</div>
                                <div class="p-3 rounded-3 border bg-light">
                                    <?= !empty($malumot['skills_en']) ? htmlspecialchars($malumot['skills_en']) : '<span class="text-muted italic">Not entered</span>' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end text-muted small">
                    <i class="fa-solid fa-hashtag me-1"></i> Identifikator: <strong><?= htmlspecialchars($malumot['id'] ?? 'N/A') ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>
