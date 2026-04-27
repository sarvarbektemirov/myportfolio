<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$id  = (int)($_GET['id'] ?? 0);
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$row = null;

if (isset($link) && $link instanceof mysqli) {
    $stmt = $link->prepare("SELECT * FROM nashrlar WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $row['faoliyat_uz'] = json_decode($row['faoliyat_uz'], true) ?? [];
        $row['faoliyat_en'] = json_decode($row['faoliyat_en'], true) ?? [];
    } else {
        header("Location: list_nashr.php");
        exit;
    }
} else {
    header("Location: list_nashr.php");
    exit;
}
?>

<?php
$oylar = ['', 'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr'];

$b_dt = !empty($row['boshlanish']) ? new DateTime($row['boshlanish']) : null;
$b_oy = $b_dt ? (int)$b_dt->format('n') : 0;
$b_yil = $b_dt ? (int)$b_dt->format('Y') : 0;

$t_dt = !empty($row['tugash']) ? new DateTime($row['tugash']) : null;
$t_oy = $t_dt ? (int)$t_dt->format('n') : 0;
$t_yil = $t_dt ? (int)$t_dt->format('Y') : 0;
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nashrni tahrirlash</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
<?php include_once("menu.php"); ?>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-briefcase text-primary me-2"></i> Nashrni tahrirlash</h2>
        <a href="list_nashr.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="form-card animatsiya1">
                <form action="edit_nashr_check.php" method="post">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">

                    <!-- TUR -->
                    <div class="mb-5">
                        <label class="form-label fw-bold"><i class="fa-solid fa-layer-group text-primary"></i> Faoliyat turi <span class="required-asterisk">*</span></label>
                        <div class="d-flex flex-column flex-sm-row gap-3 mt-2">
                            <label class="d-flex align-items-center gap-2 radio-karta" id="label_asosiy">
                                <input type="radio" name="tur" value="asosiy" id="tur_asosiy" class="form-check-input"
                                       <?= $row['tur'] === 'asosiy' ? 'checked' : '' ?> onchange="turOzgardi()">
                                <span>🟢 <b>Asosiy</b> faoliyat</span>
                            </label>
                            <label class="d-flex align-items-center gap-2 radio-karta" id="label_qoshimcha">
                                <input type="radio" name="tur" value="qoshimcha" id="tur_qoshimcha" class="form-check-input"
                                       <?= $row['tur'] === 'qoshimcha' ? 'checked' : '' ?> onchange="turOzgardi()">
                                <span>🔵 <b>Qo'shimcha</b> faoliyat</span>
                            </label>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- LAVOZIM -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-id-badge text-success"></i> Lavozim <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span></label>
                            <input type="text" name="lavozim_uz" class="form-control"
                                   value="<?= htmlspecialchars($row['lavozim_uz']) ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-at text-secondary"></i> Position <span class="text-muted-small fw-normal">(English)</span></label>
                            <input type="text" name="lavozim_en" class="form-control"
                                   value="<?= htmlspecialchars($row['lavozim_en']) ?>">
                            <small class="text-muted-small">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                        </div>

                        <!-- ISH JOYI -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-building text-info"></i> Ish joyi <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span></label>
                            <input type="text" name="ish_joyi_uz" class="form-control"
                                   value="<?= htmlspecialchars($row['ish_joyi_uz']) ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-city text-secondary"></i> Workplace <span class="text-muted-small fw-normal">(English)</span></label>
                            <input type="text" name="ish_joyi_en" class="form-control"
                                   value="<?= htmlspecialchars($row['ish_joyi_en']) ?>">
                            <small class="text-muted-small">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                        </div>
                    </div>

                    <div class="row g-4 mb-4 border-top pt-4">
                        <!-- FAOLIYATLAR UZ -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-list-check text-primary"></i> Qilgan ishlari <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span></label>
                            <div id="faoliyat_uz_list">
                                <?php foreach ($row['faoliyat_uz'] as $f): ?>
                                <div class="faoliyat-qator">
                                    <input type="text" name="faoliyat_uz[]" class="form-control"
                                           value="<?= htmlspecialchars($f) ?>">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="qatorOchir(this)"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2 rounded-pill"
                                    onclick="qatorQosh('faoliyat_uz_list','faoliyat_uz[]','O\'zbek tilida...')">
                                <i class="fa-solid fa-plus me-1"></i> Qator qo'shish
                            </button>
                        </div>

                        <!-- FAOLIYATLAR EN -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-earth-americas text-secondary"></i> Activities <span class="text-muted-small fw-normal">(English)</span></label>
                            <small class="text-muted-small d-block mb-2">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                            <div id="faoliyat_en_list">
                                <?php foreach ($row['faoliyat_en'] as $f): ?>
                                <div class="faoliyat-qator">
                                    <input type="text" name="faoliyat_en[]" class="form-control"
                                           value="<?= htmlspecialchars($f) ?>">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="qatorOchir(this)"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2 rounded-pill"
                                    onclick="qatorQosh('faoliyat_en_list','faoliyat_en[]','In English...')">
                                <i class="fa-solid fa-plus me-1"></i> Add activity
                            </button>
                        </div>
                    </div>

                    <!-- VAQT -->
                    <div class="row g-4 mb-4 border-top pt-4 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-calendar-check text-warning"></i> Boshlanish <span class="required-asterisk">*</span></label>
                            <div class="d-flex gap-2">
                                <select name="boshlanish_oy" class="form-select" required>
                                    <option value="">Oy</option>
                                    <?php foreach ($oylar as $i => $oy): ?>
                                        <?php if ($i === 0) continue; ?>
                                        <option value="<?= $i ?>" <?= $b_oy === $i ? 'selected' : '' ?>>
                                            <?= $oy ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="boshlanish_yil" class="form-select" required>
                                    <option value="">Yil</option>
                                    <?php for ($y = date('Y'); $y >= 1970; $y--): ?>
                                        <option value="<?= $y ?>" <?= $b_yil === $y ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-4" id="tugash_blok">
                            <label class="form-label fw-bold"><i class="fa-solid fa-calendar-xmark text-danger"></i> Tugash</label>
                            <div class="d-flex gap-2">
                                <select name="tugash_oy" id="tugash_oy" class="form-select"
                                        <?= $row['hozirgi'] ? 'disabled' : '' ?>>
                                    <option value="">Oy</option>
                                    <?php foreach ($oylar as $i => $oy): ?>
                                        <?php if ($i === 0) continue; ?>
                                        <option value="<?= $i ?>" <?= $t_oy === $i ? 'selected' : '' ?>>
                                            <?= $oy ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="tugash_yil" id="tugash_yil" class="form-select"
                                        <?= $row['hozirgi'] ? 'disabled' : '' ?>>
                                    <option value="">Yil</option>
                                    <?php for ($y = date('Y'); $y >= 1970; $y--): ?>
                                        <option value="<?= $y ?>" <?= $t_yil === $y ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="hozirgi" id="hozirgi" value="1"
                                       class="form-check-input"
                                       <?= $row['hozirgi'] ? 'checked' : '' ?>
                                       onchange="hozirgiOzgardi()">
                                <label for="hozirgi" class="form-check-label fw-bold text-success" style="cursor:pointer;">
                                    Hozirgi kungacha ishlayman
                                </label>
                            </div>
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
<script>
function qatorQosh(listId, name, placeholder) {
    const list = document.getElementById(listId);
    const div  = document.createElement('div');
    div.className = 'faoliyat-qator';
    div.innerHTML = `
        <input type="text" name="${name}" class="form-control" placeholder="${placeholder}">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="qatorOchir(this)"><i class="fa-solid fa-xmark"></i></button>
    `;
    list.appendChild(div);
}
function qatorOchir(btn) {
    const list    = btn.closest('[id$="_list"]');
    const qatorlar = list.querySelectorAll('.faoliyat-qator');
    if (qatorlar.length > 1) btn.closest('.faoliyat-qator').remove();
}
function hozirgiOzgardi() {
    const oy  = document.getElementById('tugash_oy');
    const yil = document.getElementById('tugash_yil');
    const band = document.getElementById('hozirgi').checked;
    oy.disabled  = band;
    yil.disabled = band;
    if (band) { oy.value = ''; yil.value = ''; }
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

