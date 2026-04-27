<?php
session_start();
include_once("menu.php");
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nashr qo'shish</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-briefcase text-primary me-2"></i> Ish tajribasi qo'shish</h2>
        <a href="list_nashr.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="form-card animatsiya1">
                <form action="nashr_check.php" method="post">

                    <!-- TUR TANLASH -->
                    <div class="mb-5">
                        <label class="form-label"><i class="fa-solid fa-layer-group text-primary"></i> Tur tanlang <span class="required-asterisk">*</span></label>
                        <div class="d-flex flex-column flex-sm-row gap-3 mt-2">
                            <label class="d-flex align-items-center gap-3 radio-karta" id="label_asosiy">
                                <input type="radio" name="tur" value="asosiy" id="tur_asosiy" class="form-check-input" checked onchange="turOzgardi()">
                                <span class="fs-5">🟢 <b>Asosiy</b> faoliyat</span>
                            </label>
                            <label class="d-flex align-items-center gap-3 radio-karta" id="label_qoshimcha">
                                <input type="radio" name="tur" value="qoshimcha" id="tur_qoshimcha" class="form-check-input" onchange="turOzgardi()">
                                <span class="fs-5">🔵 <b>Qo'shimcha</b> faoliyat</span>
                            </label>
                        </div>
                    </div>

                    <!-- LAVOZIM -->
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label"><i class="fa-solid fa-user-tie text-success"></i> Lavozim <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span></label>
                            <input type="text" name="lavozim_uz" class="form-control" placeholder="Masalan: Dotsent" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label"><i class="fa-solid fa-earth-americas text-secondary"></i> Position <span class="text-muted-small fw-normal">(English)</span></label>
                            <input type="text" name="lavozim_en" class="form-control" placeholder="e.g. Associate Professor">
                            <small class="text-muted-small mt-1 d-block">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                        </div>
                    </div>

                    <!-- ISH JOYI -->
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label"><i class="fa-solid fa-building text-info"></i> Ish joyi <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span></label>
                            <input type="text" name="ish_joyi_uz" class="form-control" placeholder="Masalan: Samarqand davlat universiteti" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label"><i class="fa-solid fa-earth-americas text-secondary"></i> Workplace <span class="text-muted-small fw-normal">(English)</span></label>
                            <input type="text" name="ish_joyi_en" class="form-control" placeholder="e.g. Samarkand State University">
                            <small class="text-muted-small mt-1 d-block">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                        </div>
                    </div>

                    <!-- FAOLIYATLAR -->
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-list-check text-warning"></i> Qilgan ishlari <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span></label>
                            <div id="faoliyat_uz_list">
                                <div class="faoliyat-qator">
                                    <input type="text" name="faoliyat_uz[]" class="form-control form-control-sm" placeholder="Masalan: Talabalar uchun ma'ruzalar o'tkazish" required>
                                    <button type="button" class="btn btn-danger btn-sm ochiruv-btn" onclick="qatorOchir(this)"><i class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="qatorQosh('faoliyat_uz_list', 'faoliyat_uz[]', 'O\'zbek tilida...')">
                                <i class="fa-solid fa-plus me-1"></i> Qo'shish
                            </button>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-list-check text-secondary"></i> Activities <span class="text-muted-small fw-normal">(English)</span></label>
                            <small class="text-muted-small d-block mb-1">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                            <div id="faoliyat_en_list">
                                <div class="faoliyat-qator">
                                    <input type="text" name="faoliyat_en[]" class="form-control form-control-sm" placeholder="e.g. Conducting lectures for students">
                                    <button type="button" class="btn btn-danger btn-sm ochiruv-btn" onclick="qatorOchir(this)"><i class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="qatorQosh('faoliyat_en_list', 'faoliyat_en[]', 'In English...')">
                                <i class="fa-solid fa-plus me-1"></i> Add more
                            </button>
                        </div>
                    </div>

                    <!-- VAQT — faqat oy va yil -->
                    <div class="row g-4 mb-4">
                        <!-- Boshlanish -->
                        <div class="col-12 col-md-4">
                            <label class="form-label"><i class="fa-solid fa-calendar text-primary"></i> Boshlanish <span class="required-asterisk">*</span></label>
                            <div class="d-flex gap-2">
                                <select name="boshlanish_oy" class="form-select" required>
                                    <option value="">Oy</option>
                                    <?php
                                    $oylar = ['Yanvar','Fevral','Mart','Aprel','May','Iyun',
                                              'Iyul','Avgust','Sentabr','Oktabr','Noyabr','Dekabr'];
                                            foreach ($oylar as $i => $oy) {
                                                echo "<option value='" . ($i+1) . "'>$oy</option>";
                                            }
                                    ?>
                                </select>
                                <select name="boshlanish_yil" class="form-select" required>
                                    <option value="">Yil</option>
                                    <?php for ($y = date('Y'); $y >= 1970; $y--): ?>
                                        <option value="<?= $y ?>"><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tugash -->
                        <div class="col-12 col-md-4" id="tugash_blok">
                            <label class="form-label"><i class="fa-solid fa-calendar-check text-success"></i> Tugash</label>
                            <div class="d-flex gap-2">
                                <select name="tugash_oy" id="tugash_oy" class="form-select">
                                    <option value="">Oy</option>
                                    <?php
                                    foreach ($oylar as $i => $oy) {
                                        echo "<option value='" . ($i+1) . "'>$oy</option>";
                                    }
                                    ?>
                                </select>
                                <select name="tugash_yil" id="tugash_yil" class="form-select">
                                    <option value="">Yil</option>
                                    <?php for ($y = date('Y'); $y >= 1970; $y--): ?>
                                        <option value="<?= $y ?>"><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Hozirgi kungacha -->
                        <div class="col-12 col-md-4 d-flex align-items-end pb-2">
                            <div class="form-check">
                                <input type="checkbox" name="hozirgi" id="hozirgi" value="1"
                                    class="form-check-input border-secondary" style="width:1.5em; height:1.5em;" onchange="hozirgiOzgardi()">
                                <label for="hozirgi" class="form-check-label ms-2 pt-1 fw-semibold text-primary">
                                    Hozirgi kungacha
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-submit">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Saqlash
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
    const div = document.createElement('div');
    div.className = 'faoliyat-qator';
    div.innerHTML = `
        <input type="text" name="${name}" class="form-control form-control-sm" placeholder="${placeholder}">
        <button type="button" class="btn btn-danger btn-sm ochiruv-btn" onclick="qatorOchir(this)"><i class="fa-solid fa-times"></i></button>
    `;
    list.appendChild(div);
}

function qatorOchir(btn) {
    const list = btn.closest('[id$="_list"]');
    const qatorlar = list.querySelectorAll('.faoliyat-qator');
    if (qatorlar.length > 1) {
        btn.closest('.faoliyat-qator').remove();
    }
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

