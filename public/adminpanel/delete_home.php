<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$surov = $link->query("SELECT * FROM home WHERE user_id = $uid ORDER BY id DESC LIMIT 1");
$malumot = $surov->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O'chirishni tasdiqlash</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <style>
        .delete-warning-card {
            max-width: 500px;
            margin: auto;
            border: none;
            overflow: hidden;
        }
        .warning-icon-box {
            font-size: 4rem;
            color: #ef4444;
            margin-bottom: 20px;
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .item-preview-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include_once("menu.php"); ?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="form-card delete-warning-card animatsiya1 text-center p-5">
                <div class="warning-icon-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                
                <h2 class="fw-bold mb-3 text-dark">Ma'lumotni o'chirish?</h2>
                <p class="text-muted mb-4 fs-5">Haqiqatan ham "Asosiy sahifa" ma'lumotlarini o'chirib tashlamoqchimisiz? Bu amalni qaytarib bo'lmaydi.</p>

                <?php
include_once('db.php'); if (!empty($malumot['rasm'])): ?>
                    <div class="mb-4">
                        <img src="../files/<?= htmlspecialchars($malumot['rasm']) ?>"
                             class="item-preview-img border" alt="Preview">
                        <?php if (!empty($malumot['malumot_uz'])): ?>
                            <div class="small fw-bold text-muted"><?= htmlspecialchars(mb_strimwidth($malumot['malumot_uz'], 0, 50, "...")) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                    <a href="delete_home1.php"
                       class="btn btn-danger btn-lg px-4 shadow-sm"
                       onclick="return confirm('Haqiqatan ham o\'chirmoqchimisiz?')">
                        <i class="fa-solid fa-trash-can me-2"></i> Ha, o'chirilsin
                    </a>
                    <a href="edit_home.php" class="btn btn-light btn-lg px-4 border shadow-sm">
                        <i class="fa-solid fa-xmark me-2"></i> Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>

