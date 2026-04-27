<?php
include_once('db.php');
session_start();
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: index.php");
    exit;
}
include_once("menu.php");
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student qo'shish</title>
    <link rel="icon" href="rasmlar/logo.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-user-graduate text-primary me-2"></i> Talabalar ma'lumotlarini qo'shish</h2>
        <a href="list_student.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <?php
include_once('db.php'); if (isset($_GET['xabar'])): ?>
        <?php
include_once('db.php'); if ($_GET['xabar'] === 'ok'): ?>
            <div class="alert alert-success mt-2 mb-4">✅ Student muvaffaqiyatli qo'shildi!</div>
        <?php
include_once('db.php'); elseif ($_GET['xabar'] === 'mavjud'): ?>
            <div class="alert alert-warning mt-2 mb-4">⚠️ Bu student oldin qo'shilgan!</div>
        <?php
include_once('db.php'); elseif ($_GET['xabar'] === 'rasm_xato'): ?>
            <div class="alert alert-danger mt-2 mb-4">❌ Rasm yuklanmadi. Qayta urinib ko'ring.</div>
        <?php
include_once('db.php'); endif; ?>
    <?php
include_once('db.php'); endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="form-card animatsiya1">
                <form action="student_check.php" method="post" enctype="multipart/form-data">

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label"><i class="fa-solid fa-user text-primary"></i> Ism Familiya 
                                <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" name="ism" class="form-control" required
                                   placeholder="Familiya Ism shaklida kiriting">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label"><i class="fa-solid fa-layer-group text-success"></i> Toifa
                                <span class="required-asterisk">*</span>
                            </label>
                            <select name="toifa" class="form-select" required>
                                <option value="toifa_1">BMI bitiruvchilar</option>
                                <option value="toifa_2">Magistrlar</option>
                                <option value="toifa_3">Boshqalar</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label"><i class="fa-solid fa-image text-danger"></i> Rasm
                                <span class="required-asterisk">*</span>
                            </label>
                            <input type="file" name="rasm" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-6">
                            <label class="form-label"><i class="fa-solid fa-align-left text-info"></i> Qisqa ma'lumot (O'zbek)
                                <span class="required-asterisk">*</span>
                            </label>
                            <textarea name="qisqa_malumot_uz" class="form-control" rows="3" required
                                      placeholder="Kartada ko'rinadigan qisqa ma'lumot (o'zbek tilida)"></textarea>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label"><i class="fa-solid fa-globe text-secondary"></i> Qisqa ma'lumot (Ingliz)
                            </label>
                            <small class="text-muted-small d-block mb-2">Bo'sh qoldirsangiz avtomatik tarjima qilinadi</small>
                            <textarea name="qisqa_malumot_en" class="form-control" rows="3"
                                      placeholder="Inglizcha qisqa ma'lumot (ixtiyoriy)"></textarea>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-6">
                            <label class="form-label"><i class="fa-solid fa-file-lines text-primary"></i> To'liq ma'lumot (O'zbek)
                                <span class="required-asterisk">*</span>
                            </label>
                            <textarea name="tolik_malumot_uz" class="form-control" rows="5" required
                                      placeholder="Modal oynada ko'rinadigan to'liq ma'lumot (o'zbek tilida)"></textarea>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label"><i class="fa-solid fa-earth-americas text-secondary"></i> To'liq ma'lumot (Ingliz)
                            </label>
                            <small class="text-muted-small d-block mb-2">Bo'sh qoldirsangiz avtomatik tarjima qilinadi</small>
                            <textarea name="tolik_malumot_en" class="form-control" rows="5"
                                      placeholder="Inglizcha to'liq ma'lumot (ixtiyoriy)"></textarea>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-submit">
                            <i class="fa-solid fa-plus me-2"></i> Talabani bazaga qo'shish
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

