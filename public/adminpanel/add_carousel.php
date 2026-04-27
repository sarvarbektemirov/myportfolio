<?php
session_start();
include_once("menu.php");
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel qo'shish</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-images text-primary me-2"></i> Carousel rasmi qo'shish</h2>
        <a href="list_carousel.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="form-card animatsiya1">
                <form action="carousel_check.php" method="post" enctype="multipart/form-data">

                    <!-- TUR -->
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

                    <div class="row g-4 mb-4">
                        <!-- RASM -->
                        <div class="col-12 col-lg-5">
                            <label class="form-label"><i class="fa-solid fa-image text-danger"></i> Rasm <span class="required-asterisk">*</span></label>
                            <input type="file" name="rasm" id="rasm_input" class="form-control"
                                   accept="image/*" onchange="rasmKorsat(this)" required>
                            <img id="preview_rasm" class="preview-img-box mt-3" style="display: none;" src="" alt="Preview">
                        </div>

                        <div class="col-12 col-lg-7">
                            <div class="row g-4">
                                <!-- NOM -->
                                <div class="col-12">
                                    <label class="form-label"><i class="fa-solid fa-pen-to-square text-success"></i> Nomi <span class="text-muted-small fw-normal">(O'zbek)</span> <span class="required-asterisk">*</span></label>
                                    <input type="text" name="nom_uz" class="form-control"
                                           placeholder="Masalan: Xalqaro konferensiya" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"><i class="fa-solid fa-earth-americas text-secondary"></i> Name <span class="text-muted-small fw-normal">(English)</span></label>
                                    <input type="text" name="nom_en" class="form-control"
                                           placeholder="e.g. International Conference">
                                    <small class="text-muted-small mt-1 d-block">Bo'sh qolsa avtomatik tarjima qilinadi</small>
                                </div>

                                <!-- SANA — faqat oy va yil -->
                                <div class="col-12 mt-4">
                                    <label class="form-label"><i class="fa-solid fa-calendar-days text-warning"></i> Sana <span class="required-asterisk">*</span></label>
                                    <div class="d-flex gap-3">
                                        <select name="sana_oy" class="form-select flex-grow-1" required>
                                            <option value="">Oyni tanlang</option>
                                            <?php
                                            $oylar = ['Yanvar','Fevral','Mart','Aprel','May','Iyun',
                                                      'Iyul','Avgust','Sentabr','Oktabr','Noyabr','Dekabr'];
                                            foreach ($oylar as $i => $oy) {
                                                echo "<option value='" . ($i+1) . "'>$oy</option>";
                                            }
                                            ?>
                                        </select>
                                        <select name="sana_yil" class="form-select flex-grow-1" required>
                                            <option value="">Yilni tanlang</option>
                                            <?php for ($y = date('Y'); $y >= 1970; $y--): ?>
                                                <option value="<?= $y ?>"><?= $y ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-submit">
                            <i class="fa-solid fa-plus me-2"></i> Saqlash
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
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
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

