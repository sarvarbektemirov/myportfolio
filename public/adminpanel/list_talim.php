<?php
include_once("db.php");
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$result = $link->query("SELECT * FROM talim WHERE user_id = $uid ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ta'lim ro'yxati</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
<?php include_once("menu.php"); ?>

    <?php if (isset($_GET['res']) || isset($_GET['xabar'])): ?>
        <div class="animatsiya1 container mt-4 mb-0 p-0">
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
            <h2 class="mb-0 fw-bold"><i class="fa-solid fa-graduation-cap text-primary me-2"></i> Ta'lim Bosqichlari</h2>
            <p class="text-muted small mb-0">O'quv bosqichlari va tavsiflari ro'yxati</p>
        </div>
        <a href="add_talim.php" class="btn btn-submit px-4">
            <i class="fa-solid fa-plus me-2"></i> Yangi qo'shish
        </a>
    </div>

    <div class="form-card animatsiya1 p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Bosqich</th>
                        <th>Rasm</th>
                        <th style="width: 40%;">Tavsif (UZ)</th>
                        <th class="text-center pe-4">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php $i = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?= $i++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-link bg-primary-subtle text-primary me-3 shadow-sm border border-primary-subtle" style="width: 42px; height: 42px; border-radius: 12px; font-size: 1.1rem;">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-capitalize fs-6" style="letter-spacing: -0.2px;"><?= htmlspecialchars($row['bosqich']) ?></div>
                                            <div class="text-muted-small opacity-75" style="font-size: 0.7rem; font-weight: 500; text-uppercase: none;">Ta'lim bosqichi</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($row['rasm'])): ?>
                                        <img src="../talimrasm/<?= htmlspecialchars($row['rasm']) ?>" class="talim-img" alt="Talim">
                                    <?php else: ?>
                                        <div class="talim-img bg-light d-flex align-items-center justify-content-center border">
                                            <i class="fa-solid fa-image text-muted opacity-50"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted-small" style="line-height: 1.6; max-width: 300px;">
                                    <?= htmlspecialchars(mb_strimwidth($row['tavsif_uz'], 0, 150, "...")) ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="edit_talim.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning icon-link" title="Tahrirlash">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="delete_talim.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger icon-link" title="O'chirish">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open d-block fs-1 mb-2 opacity-50"></i>
                                Ma'lumot topilmadi!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>

</html>

