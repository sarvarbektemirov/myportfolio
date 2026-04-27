<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$carousel = [];
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

if (isset($link) && $link instanceof mysqli) {
    $result = $link->query("SELECT * FROM nashr_carousel WHERE user_id = $uid ORDER BY sana DESC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $carousel[] = $row;
        }
    }
}

$oylar = ['','Yanvar','Fevral','Mart','Aprel','May','Iyun',
          'Iyul','Avgust','Sentabr','Oktabr','Noyabr','Dekabr'];
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel ro'yxati</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
<?php include_once("menu.php"); ?>

    <?php if (isset($_GET['res']) || isset($_GET['xabar'])): ?>
        <div class="animatsiya1 container mt-4 mb-4 p-0">
            <?php if (($_GET['res'] ?? '') === 'ok' || ($_GET['xabar'] ?? '') === 'ok'): ?>
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                    <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                    <div>Ma'lumotlar muvaffaqiyatli saqlandi!</div>
                </div>
            <?php elseif (($_GET['xabar'] ?? '') === 'ochirildi'): ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                    <i class="fa-solid fa-trash-can fs-4 me-3"></i>
                    <div>Ma'lumot muvaffaqiyatli o'chirildi.</div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <div>
            <h2 class="mb-0 fw-bold"><i class="fa-solid fa-images text-primary me-2"></i> Carousel</h2>
            <p class="text-muted small mb-0">Asosiy va qo'shimcha rasmlar boshqaruvi</p>
        </div>
        <a href="add_carousel.php" class="btn btn-submit px-4">
            <i class="fa-solid fa-plus me-2"></i> Yangi rasm qo'shish
        </a>
    </div>

    <?php if (!empty($carousel)): ?>
    <div class="form-card animatsiya1 p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Rasm</th>
                        <th>Tur</th>
                        <th>Nomi</th>
                        <th>Sana</th>
                        <th class="text-center pe-4">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carousel as $i => $c):
                        $s_dt  = !empty($c['sana']) ? new DateTime($c['sana']) : null;
                        $s_oy  = $s_dt ? (int)$s_dt->format('n') : 0;
                        $s_yil = $s_dt ? $s_dt->format('Y') : '';
                    ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?= $i + 1 ?></td>
                            <td>
                                <img src="../nashr_carousel/<?= htmlspecialchars($c['rasm']) ?>"
                                     class="carousel-preview" alt="Carousel">
                            </td>
                            <td>
                                <?php if ($c['tur'] === 'asosiy'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                        <i class="fa-solid fa-star me-1 small"></i> Asosiy
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                        <i class="fa-solid fa-layer-group me-1 small"></i> Qo'shimcha
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($c['nom_uz']) ?></td>
                            <td>
                                <span class="text-muted small">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    <?= ($s_oy > 0 ? $oylar[$s_oy] : 'Nomalum') . ' ' . $s_yil ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit_carousel.php?id=<?= $c['id'] ?>"
                                       class="btn btn-sm btn-outline-warning icon-link" title="Tahrirlash">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="delete_carousel.php?id=<?= $c['id'] ?>"
                                       class="btn btn-sm btn-outline-danger icon-link" title="O'chirish">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
        <div class="form-card animatsiya1 text-center py-5">
            <i class="fa-solid fa-image-slash fs-1 text-muted opacity-50 mb-3 d-block"></i>
            <h5 class="text-muted">Carousel rasmlari topilmadi!</h5>
            <a href="add_carousel.php" class="btn btn-submit mt-3">Birinchi rasm qo'shish</a>
        </div>
    <?php endif; ?>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>
