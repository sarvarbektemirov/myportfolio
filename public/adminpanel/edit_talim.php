<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

if (empty($_GET['id'])) {
    header("Location: list_talim.php");
    exit;
}

$id = (int)$_GET['id'];
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$row = null;

$result = $link->prepare("SELECT * FROM talim WHERE id = ? AND user_id = ?");
$result->bind_param("ii", $id, $uid);
$result->execute();
$res = $result->get_result();

if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
} else {
    header("Location: list_talim.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ta'limni tahrirlash</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>

<body>
<?php include_once("menu.php"); ?>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-graduation-cap text-primary me-2"></i> Ta'lim ma'lumotlarini tahrirlash</h2>
        <a href="list_talim.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="form-card animatsiya1">
                <form action="edit_talim_check.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">

                    <!-- Asosiy Ma'lumotlar -->
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <h5 class="text-muted-small fw-bold text-uppercase border-bottom pb-2 mb-3" style="letter-spacing: 0.5px;">
                                <i class="fa-solid fa-circle-info me-2 text-primary"></i> Asosiy Ma'lumotlar
                            </h5>
                        </div>


                        <!-- BOSQICH SELECT -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-layer-group text-primary"></i> Ta'lim bosqichi <span class="required-asterisk">*</span>
                            </label>
                            <select name="bosqich" id="bosqich" class="form-select" onchange="toggleManualInput(this.value)">
                                <?php
                                $bosqichlar = [
                                    'maktab|school'       => 'Maktab',
                                    'kollej|college'      => 'Kollej',
                                    'bakalavr|bachelor'   => 'Bakalavr',
                                    'magistr|master'      => 'Magistr',
                                    'phd|phd'             => 'PhD',
                                ];
                                $is_custom = true;
                                foreach ($bosqichlar as $val => $nom) {
                                    if (explode('|', $val)[0] === $row['bosqich']) {
                                        $is_custom = false;
                                        break;
                                    }
                                }
                                ?>
                                <option value="">-- Tanlang --</option>
                                <?php foreach ($bosqichlar as $val => $nom): 
                                    $selected = (explode('|', $val)[0] === $row['bosqich']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $val ?>" <?= $selected ?>><?= $nom ?></option>
                                <?php endforeach; ?>
                                <option value="other" <?= $is_custom ? 'selected' : '' ?>>Boshqa (qo'lda kiritish)</option>
                            </select>

                            <div id="manual_input_container" class="mt-3" style="display: <?= $is_custom ? 'block' : 'none' ?>;">
                                <label for="bosqich_manual" class="form-label small text-primary fw-bold">Ta'lim nomini yozing</label>
                                <input type="text" name="bosqich_manual" id="bosqich_manual" class="form-control" placeholder="Masalan: Kurs, Til kursi..." value="<?= $is_custom ? htmlspecialchars($row['bosqich']) : '' ?>">
                            </div>
                        </div>

                        <script>
                            function toggleManualInput(val) {
                                const container = document.getElementById('manual_input_container');
                                const input = document.getElementById('bosqich_manual');
                                if (val === 'other') {
                                    container.style.display = 'block';
                                    input.required = true;
                                    input.focus();
                                } else {
                                    container.style.display = 'none';
                                    input.required = false;
                                }
                            }
                        </script>

                        <!-- RASM -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-image text-danger"></i> Rasm yuklash
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (!empty($row['rasm'])): ?>
                                    <img src="../talimrasm/<?= htmlspecialchars($row['rasm']) ?>"
                                        class="rounded shadow-sm"
                                        style="width:80px; height:80px; object-fit:cover; border: 2px solid var(--border-color);">
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <input type="file" name="rasm" id="rasm" class="form-control">
                                    <small class="text-muted-small d-block mt-1">Yangi rasm yuklasangiz, eski rasm o'chiriladi</small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Tavsiflar va Matnlar -->
                    <div class="row g-4 mb-4 border-top pt-4">
                        <div class="col-12">
                            <h5 class="text-muted-small fw-bold text-uppercase border-bottom pb-2 mb-3" style="letter-spacing: 0.5px;">
                                <i class="fa-solid fa-align-left me-2 text-info"></i> Tavsiflar va Matnlar
                            </h5>
                        </div>

                        <!-- TAVSIF UZ -->
                        <div class="col-12 col-md-6">
                            <label for="tavsif_uz" class="form-label fw-semibold">
                                <i class="fa-solid fa-align-left text-info"></i> Tavsif <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span>
                            </label>
                            <textarea name="tavsif_uz" id="tavsif_uz" class="form-control" rows="6" placeholder="O'zbek tilida..."><?= htmlspecialchars($row['tavsif_uz']) ?></textarea>
                        </div>

                        <!-- TAVSIF EN -->
                        <div class="col-12 col-md-6">
                            <label for="tavsif_en" class="form-label fw-semibold">
                                <i class="fa-solid fa-globe text-secondary"></i> Description <span class="text-muted-small fw-normal">(English)</span>
                            </label>
                            <small class="text-muted-small d-block mb-2">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                            <textarea name="tavsif_en" id="tavsif_en" class="form-control" rows="6" placeholder="English description..."><?= htmlspecialchars($row['tavsif_en']) ?></textarea>
                        </div>
                    </div>

                    <div class="text-center mt-5">
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

