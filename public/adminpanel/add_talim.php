<?php
session_start();
include_once("menu.php");
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ta'lim ma'lumotlari</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-graduation-cap text-primary me-2"></i> Ta'lim ma'lumotlarini qo'shish</h2>
        <a href="list_talim.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> Ma'lumotlar muvaffaqiyatli qo'shildi!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="form-card animatsiya1">
                <form action="talim_check.php" method="post" enctype="multipart/form-data">
                    <div class="row g-4 mb-4">

                        <!-- BOSQICH SELECT -->
                        <div class="col-12 col-sm-6">
                            <label for="bosqich" class="form-label">
                                <i class="fa-solid fa-layer-group text-primary"></i> Ta'lim bosqichi <span class="required-asterisk">*</span>
                            </label>
                            <select name="bosqich" id="bosqich" class="form-select" onchange="toggleManualInput(this.value)">
                                <option value="">-- Tanlang --</option>
                                <option value="maktab|school">Maktab</option>
                                <option value="kollej|college">Kollej</option>
                                <option value="bakalavr|bachelor">Bakalavr</option>
                                <option value="magistr|master">Magistr</option>
                                <option value="phd|phd">PhD</option>
                                <option value="other">Boshqa (qo'lda kiritish)</option>
                            </select>

                            <div id="manual_input_container" class="mt-3" style="display: none;">
                                <label for="bosqich_manual" class="form-label small text-primary fw-bold">Ta'lim nomini yozing</label>
                                <input type="text" name="bosqich_manual" id="bosqich_manual" class="form-control" placeholder="Masalan: Kurs, Til kursi, Sertifikat...">
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
                        <div class="col-12 col-sm-6">
                            <label for="rasm" class="form-label">
                                <i class="fa-solid fa-image text-danger"></i> Rasm
                            </label>
                            <input type="file" name="rasm" id="rasm" class="form-control">
                        </div>

                    </div>

                    <div class="row g-4 mb-4">
                        <!-- TAVSIF UZ -->
                        <div class="col-12 col-sm-6">
                            <label for="tavsif_uz" class="form-label">
                                <i class="fa-solid fa-align-left text-info"></i> Tavsif <span class="text-muted-small fw-normal">(O'zbek)</span>
                                <span class="required-asterisk">*</span>
                            </label>
                            <small class="text-muted-small d-block mb-2">Ta'lim to'g'risidagi malumotlaringizni kiriting</small>
                            <textarea name="tavsif_uz" id="tavsif_uz" class="form-control" rows="5"
                                placeholder="O'zbek tilida yozing..."></textarea>
                        </div>

                        <!-- TAVSIF EN -->
                        <div class="col-12 col-sm-6">
                            <label for="tavsif_en" class="form-label">
                                <i class="fa-solid fa-globe text-secondary"></i> Description <span class="text-muted-small fw-normal">(English)</span>
                            </label>
                            <small class="text-muted-small d-block mb-2">Bo'sh qolsa avtomatik tarjima qilinadi. Tarjima xato bo'lishi mumkin</small>
                            <textarea name="tavsif_en" id="tavsif_en" class="form-control" rows="5"
                                placeholder="Write in English or leave empty..."></textarea>
                        </div>
                    </div>

                    <div class="text-center mt-4">
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
</body>
</html>

