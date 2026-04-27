<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

if (empty($_GET['id'])) {
    header("Location: list_carousel.php");
    exit;
}

$id  = (int)$_GET['id'];
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

if (isset($link) && $link instanceof mysqli) {
    $stmt = $link->prepare("SELECT * FROM nashr_carousel WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
    } else {
        header("Location: list_carousel.php");
        exit;
    }
} else {
    header("Location: list_carousel.php");
    exit;
}
?>

<?php
$oylar = ['', 'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr'];

$s_dt = !empty($row['sana']) ? new DateTime($row['sana']) : null;
$s_oy = $s_dt ? (int)$s_dt->format('n') : 0;
$s_yil = $s_dt ? (int)$s_dt->format('Y') : 0;
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel tahrirlash</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
<?php include_once("menu.php"); ?>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-images text-primary me-2"></i> Carousel rasmini tahrirlash</h2>
        <a href="list_carousel.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="form-card animatsiya1">
                <form action="edit_carousel_check.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">

                    <!-- TUR -->
                    <div class="mb-5">
                        <label class="form-label fw-bold"><i class="fa-solid fa-layer-group text-primary"></i> Faoliyat turi <span class="required-asterisk">*</span></label>
                        <div class="d-flex flex-column flex-sm-row gap-3 mt-2">
                            <label class="d-flex align-items-center gap-3 radio-karta" id="label_asosiy">
                                <input type="radio" name="tur" value="asosiy" id="tur_asosiy" class="form-check-input" 
                                       <?= $row['tur'] === 'asosiy' ? 'checked' : '' ?> onchange="turOzgardi()">
                                <span class="fs-5">🟢 <b>Asosiy</b> faoliyat</span>
                            </label>
                            <label class="d-flex align-items-center gap-3 radio-karta" id="label_qoshimcha">
                                <input type="radio" name="tur" value="qoshimcha" id="tur_qoshimcha" class="form-check-input"
                                       <?= $row['tur'] === 'qoshimcha' ? 'checked' : '' ?> onchange="turOzgardi()">
                                <span class="fs-5">🔵 <b>Qo'shimcha</b> faoliyat</span>
                            </label>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- JORIY RASM -->
                        <div class="col-12 col-lg-5">
                            <label class="form-label fw-bold"><i class="fa-solid fa-image text-primary"></i> Joriy rasm</label>
                            <div class="p-2 border rounded text-center">
                                <img src="../nashr_carousel/<?= htmlspecialchars($row['rasm']) ?>"
                                     class="preview-img-box mb-2" style="width:100%; height:200px;" alt="Hozirgi rasm">
                                <div class="small text-muted mt-1">Hozirgi rasm saqlanib turibdi</div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-7">
                            <div class="row g-4">
                                <!-- YANGI RASM -->
                                <div class="col-12">
                                    <label class="form-label fw-bold"><i class="fa-solid fa-upload text-danger"></i> Yangi rasm yuklash</label>
                                    <input type="file" name="rasm" id="rasm_input" class="form-control"
                                           accept="image/*" onchange="rasmKorsat(this)">
                                    <small class="text-muted-small mt-1 d-block">JPG, PNG, WEBP — Bo'sh qolsa eski rasm qoladi</small>
                                    <img id="preview_rasm" class="preview-img-box mt-3" style="display: none;" src="" alt="Preview">
                                </div>

                                <!-- NOM -->
                                <div class="col-12">
                                    <label class="form-label fw-bold"><i class="fa-solid fa-pen-to-square text-success"></i> Nomi <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span></label>
                                    <input type="text" name="nom_uz" class="form-control"
                                           value="<?= htmlspecialchars($row['nom_uz']) ?>" placeholder="O'zbek tilida..." required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold"><i class="fa-solid fa-earth-americas text-secondary"></i> Name <span class="text-muted-small fw-normal">(English)</span></label>
                                    <input type="text" name="nom_en" class="form-control"
                                           value="<?= htmlspecialchars($row['nom_en']) ?>" placeholder="English title...">
                                    <small class="text-muted-small mt-1 d-block">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SANA -->
                    <div class="row mb-4 border-top pt-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-calendar-days text-warning"></i> Sana <span class="required-asterisk">*</span></label>
                            <div class="d-flex gap-3">
                                <select name="sana_oy" class="form-select flex-grow-1" required>
                                    <option value="">Oyni tanlang</option>
                                    <?php foreach ($oylar as $i => $oy): ?>
                                        <?php if ($i === 0) continue; ?>
                                        <option value="<?= $i ?>" <?= $s_oy === $i ? 'selected' : '' ?>>
                                            <?= $oy ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="sana_yil" class="form-select flex-grow-1" required>
                                    <option value="">Yilni tanlang</option>
                                    <?php for ($y = date('Y'); $y >= 1970; $y--): ?>
                                        <option value="<?= $y ?>" <?= $s_yil === $y ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
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
<script>
function rasmKorsat(input) {
    const preview = document.getElementById('preview_rasm');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
function turOzgardi() {
    const asosiy    = document.getElementById('label_asosiy');
    const qoshimcha = document.getElementById('label_qoshimcha');
    asosiy.classList.remove('active-asosiy', 'active-qoshimcha');
    qoshimcha.classList.remove('active-asosiy', 'active-qoshimcha');

    if (document.getElementById('tur_asosiy').checked) {
        asosiy.classList.add('active-asosiy');
    } else {
        qoshimcha.classList.add('active-qoshimcha');
    }
}
turOzgardi();
</script>
</body>
</html>

