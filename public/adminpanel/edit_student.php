<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list_student.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$stmt = $link->prepare("SELECT * FROM students WHERE id=? AND user_id=? LIMIT 1");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    header("Location: list_student.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studentni tahrirlash</title>
    <link rel="icon" href="rasmlar/logo.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include_once("menu.php"); ?>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-user-graduate text-primary me-2"></i> Studentni tahrirlash</h2>
        <a href="list_student.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <?php if (isset($_GET['xabar']) && $_GET['xabar'] === 'xato'): ?>
        <div class="alert alert-danger animatsiya1 mb-4 shadow-sm border-0">
            <i class="fa-solid fa-circle-xmark me-2"></i> Saqlashda xatolik yuz berdi. Iltimos, barcha maydonlarni tekshirib qayta urinib ko'ring.
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="form-card animatsiya1">
                <form action="update_student.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="eski_rasm" value="<?= htmlspecialchars($row['rasm'] ?? '') ?>">

                    <div class="row g-4 mb-4">
                        <!-- ISM FAMILIYA -->
                        <div class="col-12 col-md-5">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-id-card text-primary"></i> Ism Familiya <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" name="ism" class="form-control" placeholder="Masalan: Alisher Navoiy" required
                                   value="<?= htmlspecialchars($row['ism']) ?>">
                        </div>

                        <!-- TOIFA -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-tags text-warning"></i> Toifa <span class="required-asterisk">*</span>
                            </label>
                            <select name="toifa" class="form-select" required>
                                <?php
                                $toifalar = ['toifa_1' => 'BMI', 'toifa_2' => 'Magistr', 'toifa_3' => 'Boshqa'];
                                foreach ($toifalar as $val => $nom): ?>
                                    <option value="<?= $val ?>" <?= $row['toifa'] === $val ? 'selected' : '' ?>>
                                        <?= $nom ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- RASM -->
                        <div class="col-12 col-sm-6 col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-image text-danger"></i> Student rasmi
                            </label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <?php if (!empty($row['rasm'])): ?>
                                    <img src="../student_rasmlar/<?= htmlspecialchars($row['rasm']) ?>" class="preview-img-box" alt="Hozirgi rasm">
                                <?php endif; ?>
                                <div>
                                    <input type="file" name="rasm" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                    <small class="text-muted-small d-block mt-1">Bo'sh qolsa — eski rasm qoladi</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QISQA MA'LUMOT -->
                    <div class="row g-4 mb-4 border-top pt-4">
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-quote-left text-info"></i> Qisqa ma'lumot <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span>
                            </label>
                            <textarea name="qisqa_malumot_uz" class="form-control" rows="3" placeholder="O'zbek tilida..." required><?= htmlspecialchars($row['qisqa_malumot_uz'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-quote-right text-secondary"></i> Short info <span class="text-muted-small fw-normal">(English)</span>
                            </label>
                            <textarea name="qisqa_malumot_en" class="form-control" rows="3" placeholder="English description or leave for auto-translate..."><?= htmlspecialchars($row['qisqa_malumot_en'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- TO'LIQ MA'LUMOT -->
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-align-left text-primary"></i> To'liq ma'lumot <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span>
                            </label>
                            <textarea name="tolik_malumot_uz" class="form-control" rows="6" placeholder="Barcha ma'lumotlarni batafsil kiriting..." required><?= htmlspecialchars($row['tolik_malumot_uz'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-align-justify text-secondary"></i> Full information <span class="text-muted-small fw-normal">(English)</span>
                            </label>
                            <textarea name="tolik_malumot_en" class="form-control" rows="6" placeholder="English details or leave for auto-translate..."><?= htmlspecialchars($row['tolik_malumot_en'] ?? '') ?></textarea>
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

<script>
    // Dinamik dizayn elementlari yoki validatsiya kerak bo'lsa shu yerga
</script>
</body>
</html>

