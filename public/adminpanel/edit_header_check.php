<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

if (empty($_POST['id'])) {
    header("Location: edit_header.php");
    exit;
}

$id = (int)$_POST['id'];
$stmt = $link->prepare("SELECT * FROM header WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    header("Location: edit_header.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <title>Header tahrirlash</title>
</head>
<body>
    <?php include_once("menu.php"); ?>
    
    <div class="container py-5 mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4 page-header">
            <h2 class="mb-0"><i class="fa-solid fa-address-card text-primary me-2"></i> Header ma'lumotlarini tahrirlash</h2>
            <a href="edit_header.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Orqaga qaytish</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="form-card animatsiya1">
                    <form action="edit_header_check1.php" method="post" class="container-fluid">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="row g-4 mb-4">
                            <div class="col-12"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">O'zbekcha Ma'lumotlar</h5></div>
                            
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user text-primary"></i> Familiya (UZ) <span class="required-asterisk">*</span></label>
                                <input type="text" name="b" class="form-control" required oninput="faqatHarf(this)" value="<?= htmlspecialchars($row['familiya'], ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user-tag text-primary"></i> Ism (UZ) <span class="required-asterisk">*</span></label>
                                <input type="text" name="a" class="form-control" required oninput="faqatHarf(this)" value="<?= htmlspecialchars($row['ism'], ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user-graduate text-primary"></i> Ilmiy Daraja (UZ) <span class="required-asterisk">*</span></label>
                                <input type="text" name="c" class="form-control" required value="<?= htmlspecialchars($row['daraja'], ENT_QUOTES) ?>">
                            </div>

                            <div class="col-12 mt-5"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">English Information</h5></div>
                            
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user text-secondary"></i> Surname (EN)</label>
                                <input type="text" name="b_en" class="form-control" oninput="faqatHarf(this)" value="<?= htmlspecialchars($row['familiya_en'], ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user-tag text-secondary"></i> Name (EN)</label>
                                <input type="text" name="a_en" class="form-control" oninput="faqatHarf(this)" value="<?= htmlspecialchars($row['ism_en'], ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user-graduate text-secondary"></i> Degree (EN)</label>
                                <input type="text" name="c_en" class="form-control" value="<?= htmlspecialchars($row['daraja_en'], ENT_QUOTES) ?>">
                            </div>

                            <div class="col-12 mt-5"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">Aloqa Ma'lumotlari</h5></div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold"><i class="fa-solid fa-phone text-success"></i> Telefon raqami <span class="required-asterisk">*</span></label>
                                <input type="text" name="d" id="d" class="form-control" required placeholder="+998901234567" maxlength="13" oninput="faqatTelefon(this)" value="<?= htmlspecialchars($row['tel'], ENT_QUOTES) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold"><i class="fa-solid fa-envelope text-danger"></i> Email <span class="required-asterisk">*</span></label>
                                <input type="email" name="e" class="form-control" required placeholder="example@mail.uz" value="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>">
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
        function faqatHarf(input) {
            input.value = input.value.replace(/[^a-zA-ZʻʼА-Яа-яЎўҒғ'\s]/g, "");
        }
        function faqatTelefon(input) {
            let val = input.value;
            val = val.replace(/[^\d+]/g, "");
            val = val.replace(/(?!^)\+/g, "");
            if (val.length > 13) val = val.slice(0, 13);
            input.value = val;
        }
    </script>
</body>
</html>
